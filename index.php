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
	$app = \Slim\Slim::getInstance();
	$app->render('test.html');
  	echo "Hi!";
});

require 'Status.php';
require 'Temperature.php';
require 'RunningTime.php';

$app->get('/index', function () use ($conn){
	$app = \Slim\Slim::getInstance();
	$func = $app->request->get('fungsi');
	$id = $app->request->get('id');
	$app->response()->headers->set('Content-Type', 'application/json');
	switch ($func) {
		case 'set_ac':
			$status = $app->request->get('arg1') == 'on' ? 1 : 0 ;
			echo json_encode(setStatus($conn, $id, $status), JSON_PRETTY_PRINT);
			break;
		case 'set_temperature':
			$temp = $app->request->get('arg1');
			echo json_encode(setTemp($conn, $id, $temp ), JSON_PRETTY_PRINT);
			break;
		case 'get_temp':
			echo json_encode(getTemp($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'set_timer':
			$action = $app->request->get('arg1') == 'on' ? 1 : 0 ;
			$time = $app->request->get('arg2');
			echo json_encode(setTimer($conn, $id, $action, $time), JSON_PRETTY_PRINT);
			break;
		case 'get_running_time':
		  echo json_encode(getRunningTime($conn, $id), JSON_PRETTY_PRINT);
			break;
	}
});

$app->run();
