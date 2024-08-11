<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Database\Driver\PDO;

use Nette\Database\Driver;


/**
 * PDO SQLite3 database driver.
 */
class SQLiteDriver extends Driver\SQLiteDriver
{
	public function connect(
		#[\SensitiveParameter]
		array $params,
	): Connection
	{
		return (fn(
			string $dsn,
			?string $username = null,
			#[\SensitiveParameter]
			?string $password = null,
			?array $options = null,
			string $formatDateTime = 'U',
		) => new Connection(Connection::createPDO($dsn, $username, $password, $options)))(...$params);
	}
}
