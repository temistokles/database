<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Database;

use JetBrains\PhpStorm\Language;
use Nette\Utils\Arrays;
use PDO;
use PDOException;


/**
 * Represents a connection between PHP and a database server.
 */
class Connection
{
	private const Drivers = [
		'pdo-mssql' => Driver\PDO\MSSqlDriver::class,
		'pdo-mysql' => Driver\PDO\MySQLDriver::class,
		'pdo-oci' => Driver\PDO\OCIDriver::class,
		'pdo-odbc' => Driver\PDO\ODBCDriver::class,
		'pdo-pgsql' => Driver\PDO\PgSQLDriver::class,
		'pdo-sqlite' => Driver\PDO\SQLiteDriver::class,
		'pdo-sqlsrv' => Driver\PDO\SQLSrvDriver::class,
	];

	/** @var array<callable(self): void>  Occurs after connection is established */
	public array $onConnect = [];

	/** @var array<callable(self, ResultSet|DriverException): void>  Occurs after query is executed */
	public array $onQuery = [];
	private Driver\Driver $driver;
	private SqlPreprocessor $preprocessor;
	private ?Driver\Connection $conn = null;

	/** @var callable(array, ResultSet): array */
	private $rowNormalizer = [Helpers::class, 'normalizeRow'];
	private ?string $sql = null;
	private int $transactionDepth = 0;


	public function __construct(
		private readonly string $dsn,
		#[\SensitiveParameter]
		private readonly ?string $user = null,
		#[\SensitiveParameter]
		private readonly ?string $password = null,
		private readonly array $options = [],
	) {
		if (empty($options['lazy'])) {
			$this->connect();
		}
	}


	public function connect(): void
	{
		if ($this->conn) {
			return;
		}

		$this->conn = new Driver\PDO\Connection(Driver\PDO\Connection::createPDO($this->dsn, $this->user, $this->password, $this->options));
		$class = empty($this->options['driverClass'])
			? self::Drivers['pdo-' . $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME)]
			: $this->options['driverClass'];
		$this->driver = new $class;
		$this->preprocessor = new SqlPreprocessor($this);
		$this->driver->initialize($this, $this->options);
		Arrays::invoke($this->onConnect, $this);
	}


	public function reconnect(): void
	{
		$this->disconnect();
		$this->connect();
	}


	public function disconnect(): void
	{
		$this->conn = null;
	}


	public function getDsn(): string
	{
		return $this->dsn;
	}


	public function getPdo(): Driver\Connection
	{
		$this->connect();
		return $this->conn;
	}


	public function getDriver(): Driver\Driver
	{
		$this->connect();
		return $this->driver;
	}


	/** @deprecated use getDriver() */
	public function getSupplementalDriver(): Driver\Driver
	{
		trigger_error(__METHOD__ . '() is deprecated, use getDriver()', E_USER_DEPRECATED);
		$this->connect();
		return $this->driver;
	}


	public function getReflection(): Reflection
	{
		return new Reflection($this->getDriver());
	}


	public function setRowNormalizer(?callable $normalizer): static
	{
		$this->rowNormalizer = $normalizer;
		return $this;
	}


	public function getInsertId(?string $sequence = null): string
	{
		try {
			$res = $this->getPdo()->lastInsertId($sequence);
			return $res === false ? '0' : $res;
		} catch (PDOException $e) {
			throw $this->driver->convertException($e);
		}
	}


	public function quote(string $string, int $type = PDO::PARAM_STR): string
	{
		try {
			return $this->getPdo()->quote($string, $type);
		} catch (PDOException $e) {
			throw DriverException::from($e);
		}
	}


	public function beginTransaction(): void
	{
		if ($this->transactionDepth !== 0) {
			throw new \LogicException(__METHOD__ . '() call is forbidden inside a transaction() callback');
		}

		$this->query('::beginTransaction');
	}


	public function commit(): void
	{
		if ($this->transactionDepth !== 0) {
			throw new \LogicException(__METHOD__ . '() call is forbidden inside a transaction() callback');
		}

		$this->query('::commit');
	}


	public function rollBack(): void
	{
		if ($this->transactionDepth !== 0) {
			throw new \LogicException(__METHOD__ . '() call is forbidden inside a transaction() callback');
		}

		$this->query('::rollBack');
	}


	public function transaction(callable $callback): mixed
	{
		if ($this->transactionDepth === 0) {
			$this->beginTransaction();
		}

		$this->transactionDepth++;
		try {
			$res = $callback($this);
		} catch (\Throwable $e) {
			$this->transactionDepth--;
			if ($this->transactionDepth === 0) {
				$this->rollback();
			}

			throw $e;
		}

		$this->transactionDepth--;
		if ($this->transactionDepth === 0) {
			$this->commit();
		}

		return $res;
	}


	/**
	 * Generates and executes SQL query.
	 * @param  literal-string  $sql
	 */
	public function query(#[Language('SQL')] string $sql, #[Language('GenericSQL')] ...$params): ResultSet
	{
		[$this->sql, $params] = $this->preprocess($sql, ...$params);
		try {
			$result = new ResultSet($this, $this->sql, $params, $this->rowNormalizer);
		} catch (PDOException $e) {
			Arrays::invoke($this->onQuery, $this, $e);
			throw $e;
		}

		Arrays::invoke($this->onQuery, $this, $result);
		return $result;
	}


	/** @deprecated  use query() */
	public function queryArgs(string $sql, array $params): ResultSet
	{
		trigger_error(__METHOD__ . '() is deprecated, use query()', E_USER_DEPRECATED);
		return $this->query($sql, ...$params);
	}


	/**
	 * @param  literal-string  $sql
	 * @return array{string, array}
	 */
	public function preprocess(string $sql, ...$params): array
	{
		$this->connect();
		return $params
			? $this->preprocessor->process(func_get_args())
			: [$sql, []];
	}


	public function getLastQueryString(): ?string
	{
		return $this->sql;
	}


	/********************* shortcuts ****************d*g**/


	/**
	 * Shortcut for query()->fetch()
	 * @param  literal-string  $sql
	 */
	public function fetch(#[Language('SQL')] string $sql, #[Language('GenericSQL')] ...$params): ?Row
	{
		return $this->query($sql, ...$params)->fetch();
	}


	/**
	 * Shortcut for query()->fetchField()
	 * @param  literal-string  $sql
	 */
	public function fetchField(#[Language('SQL')] string $sql, #[Language('GenericSQL')] ...$params): mixed
	{
		return $this->query($sql, ...$params)->fetchField();
	}


	/**
	 * Shortcut for query()->fetchFields()
	 * @param  literal-string  $sql
	 */
	public function fetchFields(#[Language('SQL')] string $sql, #[Language('GenericSQL')] ...$params): ?array
	{
		return $this->query($sql, ...$params)->fetchFields();
	}


	/**
	 * Shortcut for query()->fetchPairs()
	 * @param  literal-string  $sql
	 */
	public function fetchPairs(#[Language('SQL')] string $sql, #[Language('GenericSQL')] ...$params): array
	{
		return $this->query($sql, ...$params)->fetchPairs();
	}


	/**
	 * Shortcut for query()->fetchAll()
	 * @param  literal-string  $sql
	 */
	public function fetchAll(#[Language('SQL')] string $sql, #[Language('GenericSQL')] ...$params): array
	{
		return $this->query($sql, ...$params)->fetchAll();
	}


	public static function literal(string $value, ...$params): SqlLiteral
	{
		return new SqlLiteral($value, $params);
	}
}
