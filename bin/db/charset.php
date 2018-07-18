#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 22.01.17
 * Time: 14:43
 */

echo 'Charset default: ' . $connect->character_set_name() . PHP_EOL;

if (!$connect->set_charset($config['dbs.options']['local']['charset']))
    die('Ошибка при загрузке набора символов ' . $config['dbs.options']['local']['charset'] . ' : ' . $connect->error . PHP_EOL);

echo 'Edit сharset to ' . $connect->character_set_name() . PHP_EOL;
