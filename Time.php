<?php
$app->get('/get_running_time', function () use ($conn){
	$app = \Slim\Slim::getInstance();
	$id = $app->request->get('id');
  $app->response()->headers->set('Content-Type', 'application/json');
  echo json_encode(getRunningTime($conn, $id), JSON_PRETTY_PRINT);
});
$app->post('/set_timer', function () use ($conn){
  $app = \Slim\Slim::getInstance();
  $id = $app->request->get('id');
  $action = $app->request->get('arg1');
  $time = $app->request->get('arg2');
  $app->response()->headers->set('Content-Type', 'application/json');
  echo json_encode(setTimer($conn, $id, $action, $time), JSON_PRETTY_PRINT);
});

function getRunningTime($conn, $id){
  $get_status = getStatus($conn, $id);
  $status = $get_status['status'] == 'true' ? $get_status['data']['status'] : null;
  if ($status == 1) {
    $sql = "SELECT update_active_at FROM ac WHERE id = '".$id."'";
    $result = $conn->query($sql);
    $row = mysqli_fetch_row($result);
    $result = $row[0];
    $start_date = new DateTime($result);
    $start_date_epoch = strtotime($result);
    $since_start = $start_date->diff(new DateTime(date("Y-m-d H:i:s")));
    $msg = "";
    if ($since_start->y > 0) $msg .= $since_start->y.' years, ';
    if ($since_start->m > 0) $msg .= $since_start->m.' months, ';
    if ($since_start->d > 0) $msg .= $since_start->d.' days, ';
    if ($since_start->h > 0) $msg .= $since_start->h.' hours, ';
    if ($since_start->i > 0) $msg .= $since_start->i.' minutes, ';
    $msg .= $since_start->s.' seconds';
    $output['status'] = 'true';
    $output['data']['id'] = $id;
    $output['data']['running_time'] = time() - $start_date_epoch;
    $output['data']['message'] = $msg;
  } elseif ($status != null) {
    $output['status'] = 'true';
    $output['data']['id'] = $id;
    $output['data']['running_time'] = 0;
    $output['data']['message'] = 'ac mati gan';
  } else {
    $output['status'] = 'false';
    $output['data']['id'] = $id;
    $output['data']['message'] = 'ac gak ketemu gan!';
  }
  return $output;
}
function setTimer($conn, $id, $action, $time){
  $get_status = getStatus($conn, $id);
  $status = $get_status['status'] == 'true' ? $get_status['data']['status'] : null;
  if ($status) {
    if ($action == 'on') {
      $sql = "UPDATE ac SET timer='$time', set_timer_at=now() WHERE id='$id'";
    }else {
      $sql = "UPDATE ac SET timer=null, set_timer_at=now() WHERE id='$id'";
    }
    $result = $conn->query($sql);
    if ($result) {
      $output['status'] = 'true';
      $output['data']['id'] = $id;
      $output['data']['action'] = $action;
      $output['data']['message'] = "berhasil set temperatur action ".$action;
      if ($action == 'on') $output['data']['time'] = $time;
    } else {
      $output['status'] = 'false';
      $output['data']['id'] = $id;
      $output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
    }
  }else {
    $output['status'] = 'false';
    $output['data']['id'] = $id;
    $output['data']['message'] = 'ac gak ketemu gan!';
  }
  return $output;
}
