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

require 'Status.php';
require 'Temperature.php';
require 'Time.php';
require 'Cron.php';
require 'Services.php';

updateSystemTime($conn);

//cron update sytem temperature
checkTemp($baseServer);

//cron auto on jam 7 pagi
autoOn7am($baseServer);

//cron auto off jam 9 malam
autoOff9pm($baseServer);

//cron auto ping ke kursi
autoPingChair($baseServer);

$app->get('/', function () use ($conn, $baseServer){
	$app = \Slim\Slim::getInstance();
	$systemTemp = getSystemTemperature($conn);

	$stat = file_get_contents('http://'.$baseServer.'/index?fungsi=get_status');
	$status = json_decode($stat, true);

	$chair = file_get_contents('http://10.10.100.206:9000/?fungsi=getOccupiedSeats');
	$chairJSON = json_decode($chair,true);
	$c = $chairJSON['status'] == 'true' ? true : false;
	
	if (!$c) {
		$chairRes = false;
	} else {
		$chairRes = count($chairJSON['data']) > 0 ? true : false;
	}

	$app->render('home.html', 
		array('systemTemp' => $systemTemp,
			'date' => date("g:i A"),
			'status' => $status,
			'chair' => $chairRes));
});

$app->get('/index', function () use ($conn, $baseServer){
	$app = \Slim\Slim::getInstance();
	$func = $app->request->get('fungsi');
	$id = intval($app->request->get('id_device'));
	$app->response()->headers->set('Content-Type', 'application/json');
	switch ($func) {
		case 'get_status':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, getStatus($conn, $i));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
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
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, setStatus($conn, $i, $status));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
			echo json_encode(setStatus($conn, $id, $status), JSON_PRETTY_PRINT);
			break;
		case 'set_temperature':
			$temp = intval($app->request->get('arg1'));
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, setTemp($conn, $i, $temp ));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
			echo json_encode(setTemp($conn, $id, $temp ), JSON_PRETTY_PRINT);
			break;
		case 'get_temperature':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, getTemp($conn, $i));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
			echo json_encode(getTemp($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'set_timer':
			$action = $app->request->get('arg1');
			$time = intval($app->request->get('arg2'));
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, setTimer($conn, $i, $action, $time, $baseServer));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
			echo json_encode(setTimer($conn, $id, $action, $time, $baseServer), JSON_PRETTY_PRINT);
			break;
		case 'reset_timer':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, resetTimer($conn, $i, $baseServer));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
			echo json_encode(resetTimer($conn, $id, $baseServer), JSON_PRETTY_PRINT);
			break;
		case 'get_timer':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, getTimer($conn, $i));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
			echo json_encode(getTimer($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'minus_timer':
		  	echo json_encode(minusTimer($conn, $id, $baseServer), JSON_PRETTY_PRINT);
			break;
		case 'get_running_time':
			if ($id == 0) {
				$sql = "SELECT id FROM ac";
				$result = $conn->query($sql);
				$num_rows = $result->num_rows;
				$output = [];
				for ($i=1; $i <= $num_rows; $i++) {
					array_push($output, getRunningTime($conn, $i));
				}
				echo json_encode($output, JSON_PRETTY_PRINT);
				return;
			}
		  echo json_encode(getRunningTime($conn, $id), JSON_PRETTY_PRINT);
			break;
		case 'set_system_time':
			$time = $app->request->get('arg1');
		  echo json_encode(setSystemTime($conn, $time), JSON_PRETTY_PRINT);
			break;
		case 'set_system_temperature':
			$temperature = intval($app->request->get('arg1'));
		  echo json_encode(setSystemTemperature($conn, $temperature), JSON_PRETTY_PRINT);
			break;
		case 'get_system_temperature':
		  echo json_encode(getSystemTemperature($conn), JSON_PRETTY_PRINT);
			break;
		case 'update_system_temperature':
			echo json_encode(updateSystemTemperature($conn), JSON_PRETTY_PRINT);
			break;
		case 'ping_chair':
			echo json_encode(pingChair($baseServer), JSON_PRETTY_PRINT);
			break;
	}
});

$app->get('/startupnp/', function () {
	$cmd = '/usr/bin/node node_modules/node-ssdp/ubi/server.js';
	$outputfile = 'output.txt';
	$pidfile = 'pid.txt';
	exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
});

$app->get('/upnp/', function () {
	$cmd = '/usr/bin/node node_modules/node-ssdp/ubi/client.js';
	exec($cmd, $output);
	echo json_encode($output, JSON_PRETTY_PRINT);
});

$app->get('/service/', function () use ($conn, $projectfolder) {
	$result = mysqli_query($conn,"SELECT * FROM services");
	echo "<table border='1'>";

	$i = 0;
	while($row = $result->fetch_assoc())
	{
    if ($i == 0) {
      echo "<tr>";
      foreach ($row as $key => $value) {
        echo "<th>" . $key . "</th>";
      }
      echo "</tr>";
    }
    $i++;
    echo "<tr>";
    foreach ($row as $value) {
      echo "<td>" . $value . "</td>";
    }
    echo "<td>";
    echo '<a href="/'.$projectfolder.'/service/'.$i.'"><button>edit</button></a>';
    echo "</td>";
    echo "</tr>";
	}
	echo "</table>";
  echo '<br><a href="/'.$projectfolder.'"><button>home</button></a>';
	mysqli_close($conn);
})->name('service');

$app->get('/service/:id/', function ($id) use ($conn) {
	$result = mysqli_query($conn,"SELECT * FROM services WHERE id=$id");
	while($row = $result->fetch_assoc())
	{
  	echo '
			<form action="" method="POST">
	    Nama: <input type="text" name="name" value="'.$row['name'].'">';
	  echo '
	    Baseserver: <input type="text" name="baseserver" value="'.$row['baseserver'].'" >
	    <input type="submit" name="submit" value="SUBMIT" >
			</form>';
	}
	mysqli_close($conn);
});

$app->post('/service/:id', function ($id) use ($conn) {
	$app = \Slim\Slim::getInstance();
	$name = $app->request->post('name');
	$base = $app->request->post('baseserver');
	$sql = "UPDATE services SET name='$name', baseserver='$base' WHERE id=$id";
	if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
	} else {
  	echo "Error updating record: " . $conn->error;
	}
	$conn->close();
	$link = $app->urlFor('service');
	echo '<br><a href="'.$link.'"><button>back to list of services</button></a>';
});

$app->run();
