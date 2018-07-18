#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 22.01.17
 * Time: 13:58
 */

require_once (__DIR__ . '/drop.php');

date_default_timezone_set('UTC');

$config = json_decode(file_get_contents(__DIR__ . '/../../app/Config/local.json'), true);

$connect = new mysqli($config['dbs.options']['local']['host'], $config['dbs.options']['local']['user'], $config['dbs.options']['local']['password']);

if ($connect->connect_error) die('Connection failed: ' . $connect->connect_error);

$sql = sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET UTF8 COLLATE utf8_general_ci', $config['dbs.options']['local']['dbname']);

echo $connect->query($sql) === true ? 'Database created successfully' . PHP_EOL : 'Error creating database: ' . $connect->error . PHP_EOL;

$connect->close();

$connect = new mysqli($config['dbs.options']['local']['host'], $config['dbs.options']['local']['user'], $config['dbs.options']['local']['password'], $config['dbs.options']['local']['dbname']);

if ($connect->connect_error) die('Connection failed: ' . $connect->connect_error);

$commands = file_get_contents(__DIR__ . '/../../tmp/db/scheme.sql');

echo $connect->multi_query($commands) === true ? 'Scheme database create successfully' . PHP_EOL : 'Error creating database: ' . $connect->error . PHP_EOL;

$connect->close();
