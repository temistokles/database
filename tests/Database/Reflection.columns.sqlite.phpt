<?php

/**
 * Test: Nette\Database\Connection: reflection
 * @dataProvider? databases.ini  sqlite
 */

declare(strict_types=1);

use Nette\Database\Type;
use Tester\Assert;

require __DIR__ . '/connect.inc.php'; // create $connection

Nette\Database\Helpers::loadFromFile($connection, __DIR__ . '/files/sqlite-nette_test3.sql');


$reflection = $connection->getReflection();
$columns = $reflection->getTable('types')->columns;

$expectedColumns = [
	'int' => [
		'name' => 'int',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'integer' => [
		'name' => 'integer',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'tinyint' => [
		'name' => 'tinyint',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'smallint' => [
		'name' => 'smallint',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'mediumint' => [
		'name' => 'mediumint',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'bigint' => [
		'name' => 'bigint',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'unsigned_big_int' => [
		'name' => 'unsigned_big_int',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'int2' => [
		'name' => 'int2',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'int8' => [
		'name' => 'int8',
		'table' => 'types',
		'type' => Type::Integer,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'character_20' => [
		'name' => 'character_20',
		'table' => 'types',
		'type' => Type::Text,
		'size' => 20,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'varchar_255' => [
		'name' => 'varchar_255',
		'table' => 'types',
		'type' => Type::Text,
		'size' => 255,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'varying_character_255' => [
		'name' => 'varying_character_255',
		'table' => 'types',
		'type' => Type::Text,
		'size' => 255,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'nchar_55' => [
		'name' => 'nchar_55',
		'table' => 'types',
		'type' => Type::Text,
		'size' => 55,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'native_character_70' => [
		'name' => 'native_character_70',
		'table' => 'types',
		'type' => Type::Text,
		'size' => 70,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'nvarchar_100' => [
		'name' => 'nvarchar_100',
		'table' => 'types',
		'type' => Type::Text,
		'size' => 100,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'text' => [
		'name' => 'text',
		'table' => 'types',
		'type' => Type::Text,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'clob' => [
		'name' => 'clob',
		'table' => 'types',
		'type' => Type::Text,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'blob' => [
		'name' => 'blob',
		'table' => 'types',
		'type' => Type::Binary,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'real' => [
		'name' => 'real',
		'table' => 'types',
		'type' => Type::Float,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'double' => [
		'name' => 'double',
		'table' => 'types',
		'type' => Type::Float,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'double precision' => [
		'name' => 'double precision',
		'table' => 'types',
		'type' => Type::Float,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'float' => [
		'name' => 'float',
		'table' => 'types',
		'type' => Type::Float,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'numeric' => [
		'name' => 'numeric',
		'table' => 'types',
		'type' => Type::Decimal,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'decimal_10_5' => [
		'name' => 'decimal_10_5',
		'table' => 'types',
		'type' => Type::Decimal,
		'size' => 10,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'boolean' => [
		'name' => 'boolean',
		'table' => 'types',
		'type' => Type::Boolean,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'date' => [
		'name' => 'date',
		'table' => 'types',
		'type' => Type::Date,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
	'datetime' => [
		'name' => 'datetime',
		'table' => 'types',
		'type' => Type::DateTime,
		'size' => null,
		'nullable' => true,
		'default' => null,
		'autoIncrement' => false,
		'primary' => false,
	],
];

Assert::same(
	$expectedColumns,
	array_map(fn($c) => [
		'name' => $c->name,
		'table' => $c->table->name,
		'type' => $c->type,
		'size' => $c->size,
		'nullable' => $c->nullable,
		'default' => $c->default,
		'autoIncrement' => $c->autoIncrement,
		'primary' => $c->primary,
	], $columns),
);