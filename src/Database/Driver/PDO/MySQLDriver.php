<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Database\Driver\PDO;

use Nette\Database\Driver;


/**
 * PDO MySQL database driver.
 */
class MySQLDriver extends Driver\MySQLDriver
{
	/**
	 * Driver options:
	 *   - charset => character encoding to set (default is utf8 or utf8mb4 since MySQL 5.5.3)
	 *   - sqlmode => see http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
	 *   - supportBooleans => converts INT(1) to boolean
	 */
	public function connect(
		#[\SensitiveParameter]
		array $params,
	): Connection
	{
		return (function (
			string $dsn,
			?string $username = null,
			#[\SensitiveParameter]
			?string $password = null,
			?array $options = null,
			string|false|null $charset = null,
			?string $sqlmode = null,
			bool $supportBooleans = false,
		) {
			$pdo = Connection::createPDO($dsn, $username, $password, $options);

			$charset ??= version_compare($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION), '5.5.3', '>=') ? 'utf8mb4' : 'utf8';
			if ($charset) {
				$pdo->query('SET NAMES ' . $pdo->quote($charset));
			}

			if ($sqlmode) {
				$pdo->query('SET sql_mode=' . $pdo->quote($sqlmode));
			}

			return new Connection($pdo);
		})(...$params);
	}
}
