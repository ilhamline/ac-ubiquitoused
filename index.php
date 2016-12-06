<?php
require 'Slim/Slim.php';
require 'config.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$app->get('/', function () use ($conn){
  echo "Hi!";
});

$app->post('/set_active', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $json = json_decode($app->request->getBody());
  $id = $app->request->get('id');
  $sql = "UPDATE ac SET active=1, update_active_at=now() where id='$id'";
  $result = $conn->query($sql);
  if ($result) {
    echo "A record updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
});

$app->post('/set_inactive', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $json = json_decode($app->request->getBody());
  $id = $app->request->get('id');
  $sql = "UPDATE ac SET active=0, update_active_at=now() where id='$id'";
  $result = $conn->query($sql);
  if ($result) {
    echo "A record updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
});

$app->get('/get_status', function () use ($conn){
	$app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
  $sql = "SELECT active FROM ac WHERE id = '".$id."'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_row($result);
	if (count($row) > 0) {
		$result = $row[0];
	} else {
		$result = "404";
	}
  if ($result == '404') {
    $output["status"] = '404';
  }else {
    $output["status"] = '200';
    $output["status"] = $result;
  }
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
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

$app->post('/set_timer', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $json = json_decode($app->request->getBody());
  $id = $app->request->get('id');
  $type = $app->request->get('arg1');
  $duration = $app->request->get('arg2');
  $sql = "UPDATE ac SET timer='$type', set_timer_at='$duration' WHERE id='$id'";
  $result = $conn->query($sql);
  if ($result) {
    echo "A record updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
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
