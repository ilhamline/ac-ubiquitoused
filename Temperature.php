<?php
$app->post('/set_temperature', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
	$temp = $app->request->get('arg1');
  $app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode(setTemp($conn, $id, $temp), JSON_PRETTY_PRINT);
});
$app->get('/get_temperature', function () use ($conn){
	$app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
  $app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode(getTemp($conn, $id), JSON_PRETTY_PRINT);
});

function setTemp($conn, $id, $temp){
  $get_temp = getTemp($conn, $id);
  $last_temp = $get_temp['status'] == 'true' ? $get_temp['data']['temp'] : null;
  $sql = "UPDATE ac SET temp='$temp' where id='$id'";
  $result = $conn->query($sql);
  if ($last_temp && $result) {
    $output['status'] = 'true';
    $output['data']['id'] = $id;
    $output['data']['last_temp'] = $last_temp;
		$output['data']['message'] = "berhasil set temperatur menjadi ".$temp;
  } else {
    $output['status'] = 'false';
    $output['data']['id'] = $id;
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
  }
  return $output;
}
function getTemp($conn, $id){
	$sql = "SELECT temp FROM ac WHERE id = '".$id."'";
	$result = $conn->query($sql);
	$row = mysqli_fetch_row($result);
  $result = count($row) > 0 ? $row[0] : "404";
  if ($result == '404') {
    $output['status'] = 'false';
    $output['data']['id'] = $id;
		$output['data']['message'] = 'gak ketemu gan!';
	}else {
    $output['status'] = 'true';
    $output['data']['id'] = $id;
    $output['data']['temp'] = $result;
	}
	return $output;
}
