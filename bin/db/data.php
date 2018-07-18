#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 22.01.17
 * Time: 13:58
 */

date_default_timezone_set('UTC');

$config = json_decode(file_get_contents(__DIR__ . '/../../app/Config/local.json'), true);

$connect = new mysqli($config['dbs.options']['local']['host'], $config['dbs.options']['local']['user'], $config['dbs.options']['local']['password'], $config['dbs.options']['local']['dbname']);

if ($connect->connect_error) die('Connection failed: ' . $connect->connect_error);

require_once (__DIR__ . '/charset.php');

$commands = file_get_contents(__DIR__ . '/../../tmp/db/data.sql');

echo $connect->multi_query($commands) === true ? 'Data successfully add' . PHP_EOL : 'Error creating database: ' . $connect->error . PHP_EOL;

$connect->close();
