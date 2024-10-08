<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Database\Driver\PDO;

use Nette\Database\Driver;


/**
 * PDO MS SQL database driver.
 */
class MSSQLDriver extends Driver\MSSQLDriver
{
	public function connect(
		#[\SensitiveParameter]
		array $params,
	): Connection
	{
		return new Connection(Connection::createPDO(...$params));
	}
}
