<?php

//入口
define('APP','fiction');
require ("../config/config.php");
$controller = $_GET['c'];
$action = $_GET['a'];

$file = $controller.".class.php";
require($file);

$connection = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PWD'], $config['DB_NAME']);
// Check connection
if (mysqli_connect_errno($connection))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$connection->query("set names utf8");

$app = new $controller($connection);
$app->$action();
