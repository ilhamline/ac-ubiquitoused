<?php
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ubi-slim';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$app->get('/', function () use ($conn){
  echo "Hi!";
});

$app->post('/set_active', function () use ($conn){
  // $app = \Slim\Slim::getInstance();
  // $json = json_decode($app->request->getBody());
  // $action = $app->request->get('action');
  // $id = $app->request->get('id');
  // if ($action == 'on') {
  //   $sql = "UPDATE ac SET active='$action' where id='$id'";
  // }elseif ($action == 'off') {
  //   # code...
  // }
  // $result = $conn->query($sql);
  // if ($result) {
  //   echo "A record updated successfully";
  // } else {
  //   echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  // }
});

$app->post('/set_temperature', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $json = json_decode($app->request->getBody());
  $temp = $app->request->get('temp');
  $id = $app->request->get('id');
  $sql = "UPDATE ac SET temp='$temp' where id='$id'";
  $result = $conn->query($sql);
  if ($result) {
    echo "A record updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
});

$app->get('/get_temperature', function () use ($conn){
	$app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
  $result = getTemp($conn, $id);
  if ($result == '404') {
    $output["status"] = '404';
  }else {
    $output["status"] = '200';
    $output["temperature"] = $result;
  }
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
});

function getTemp($conn, $id){
	$app = \Slim\Slim::getInstance();
	$sql = "SELECT temp FROM ac WHERE id = '".$id."'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_row($result);
	if (count($row) > 0) {
		return $row[0];
	} else {
		return "404";
	}
}

$app->run();
