<?php
require 'Slim/Slim.php';
require 'config.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
date_default_timezone_set('ASIA/JAKARTA');

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$app->get('/', function () use ($conn){
  echo "Hi!";
});

require 'Status.php';
require 'Temperature.php';
require 'RunningTime.php';

$app->run();
