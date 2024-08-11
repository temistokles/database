<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Database\Driver\PDO;

use Nette\Database\ConnectionException;
use Nette\Database\Driver;


class Connection implements Driver\Connection
{
	public static function createPDO(
		string $dsn,
		?string $username = null,
		#[\SensitiveParameter]
		?string $password = null,
		?array $options = null,
	): \PDO
	{
		try {
			$pdo = new \PDO($dsn, $username, $password, $options);
			$pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, [Result::class, []]);
			return $pdo;
		} catch (\PDOException $e) {
			throw ConnectionException::from($e);
		}
	}


	public function __construct(
		private \PDO $pdo,
	) {

	}


	public function __call(string $name, array $args): mixed
	{
		return $this->pdo->$name(...$args);
	}
}
