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
require 'Time.php';

$app->get('/index', function () use ($conn){
	$app = \Slim\Slim::getInstance();
	$func = $app->request->get('fungsi');
	$id = $app->request->get('id');
	$app->response()->headers->set('Content-Type', 'application/json');
	switch ($func) {
		case 'get_status':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(getStatus($conn, $i), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(getStatus($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'set_status':
			$status = $app->request->get('arg1');
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(setStatus($conn, $i, $status), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(setStatus($conn, $id, $status), JSON_PRETTY_PRINT);
			break;
		case 'set_temperature':
			$temp = $app->request->get('arg1');
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(setTemp($conn, $i, $temp ), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(setTemp($conn, $id, $temp ), JSON_PRETTY_PRINT);
			break;
		case 'get_temperature':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(getTemp($conn, $i), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(getTemp($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'set_timer':
			$action = $app->request->get('arg1');
			$time = $app->request->get('arg2');
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(setTimer($conn, $i, $action, $time), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(setTimer($conn, $id, $action, $time), JSON_PRETTY_PRINT);
			break;
		case 'reset_timer':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(resetTimer($conn, $i), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(resetTimer($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'get_timer':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(getTimer($conn, $i), JSON_PRETTY_PRINT);
				}
				return;
			}
			echo json_encode(getTimer($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'get_running_time':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				for ($i=1; $i <= $num_rows; $i++) {
					echo json_encode(getRunningTime($conn, $i), JSON_PRETTY_PRINT);
				}
				return;
			}
		  echo json_encode(getRunningTime($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'set_system_time':
			$time = $app->request->get('arg1');
		  echo json_encode(setSystemTime($conn, $time), JSON_PRETTY_PRINT);
			break;
		case 'set_system_temperature':
			$temperature = $app->request->get('arg1');
		  echo json_encode(setSystemTemperature($conn, $temperature), JSON_PRETTY_PRINT);
			break;
	}
});

$app->run();
