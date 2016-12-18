<?php

define('DB_HOST','localhost');
define('DB_USER','qqbot');
define('DB_PWD','qqbot');
define('DB_DBNAME','qqbot');
define('DB_CHARSET','gbk');
define('DB_TYPE','mysql'); 

require_once 'Medoo.php';

$db = new medoo([
    // required
    'database_type' => DB_TYPE,
    'database_name' => DB_DBNAME,
    'server' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PWD,
    'charset' => DB_CHARSET,

    // optional
    'port' => 3306,
    // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
    'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);