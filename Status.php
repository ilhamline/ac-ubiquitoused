<?php
$app->post('/set_active', function () use ($conn){
	$app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
	$status = 1;
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode(setStatus($conn, $id, $status), JSON_PRETTY_PRINT);
});
$app->post('/set_inactive', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
	$status = 0;
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode(setStatus($conn, $id, $status), JSON_PRETTY_PRINT);
});
$app->get('/get_status', function () use ($conn){
	$app = \Slim\Slim::getInstance();
	$id = $app->request->get('id');
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode(getStatus($conn, $id), JSON_PRETTY_PRINT);
});

function setStatus($conn, $id, $status){
  $get_status = getStatus($conn, $id);
  $last_status = $get_status['status'] == 'true' ? $get_status['data']['status'] : null;
  $sql = "UPDATE ac SET active=$status, update_active_at=now() where id='$id'";
  $result = $conn->query($sql);
  if ($last_status && $result) {
    $output['status'] = 'true';
    $output['data']['id'] = $id;
    $output['data']['last_status'] = $last_status;
		$output['data']['message'] = "berhasil set status menjadi ".$status;
  } else {
    $output['status'] = 'false';
    $output['data']['id'] = $id;
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
  }
  return $output;
}
function getStatus($conn, $id){
	$sql = "SELECT active FROM ac WHERE id = '".$id."'";
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
    $output['data']['status'] = $result;
		$output['data']['message'] = $result == '1' ? 'nyala gan' : 'mati gan' ;
	}
	return $output;
}
