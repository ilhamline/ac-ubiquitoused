<?php

function getRunningTime($conn, $id){
  $get_status = getStatus($conn, $id);
  $status = $get_status['status'] == 'true' ? $get_status['data']['status'] : null;
  if ($status == 'on' || $status == 'standby') {
    $sql = "SELECT last_onoff_at FROM ac WHERE id = '".$id."'";
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
  } elseif ($status == 'off') {
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
    if ($action == 'on' || $action == 'off' || $action == 'standby') {
      $sql = "UPDATE ac SET timer='$time', timer_action='$action', set_timer_at=now() WHERE id='$id'";
    }else {
			$output['status'] = 'false';
			$output['data']['id'] = $id;
	    $output['data']['action'] = $action;
	    $output['data']['message'] = 'Action yang dilakukan tidak valid';
			return $output;
    }
    $result = $conn->query($sql);
    if ($result) {
      $output['status'] = 'true';
      $output['data']['id'] = $id;
			$output['data']['action'] = $action;
			$output['data']['timer'] = $time;
      $output['data']['set_timer_at'] = strftime("%Y-%m-%d %H:%M:%S", time());
      $output['data']['message'] = "berhasil set temperatur action ".$action;
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
function setSystemTime($conn, $time){
	$sql = "UPDATE system SET now='$time'";
	$result = $conn->query($sql);
	if ($result) {
		$output['status'] = 'true';
		$output['data']['message'] = "berhasil set system time ".$time;
	} else {
		$output['status'] = 'false';
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
	}
  return $output;
}
function resetTimer($conn, $id){
	$sql = "UPDATE ac SET timer=null, timer_action=null, set_timer_at=null WHERE id='$id'";
	$result = $conn->query($sql);
	if ($result) {
		$output['status'] = 'true';
		$output['data']['message'] = "berhasil reset timer";
	} else {
		$output['status'] = 'false';
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
	}
  return $output;
}
function getTimer($conn, $id){
	$sql = "SELECT timer_action, timer, set_timer_at FROM ac WHERE id = '".$id."'";
	$result = mysqli_fetch_row($conn->query($sql));
	$action = $result[0];
	$timer = $result[1];
	$set_timer_at = $result[2];
	if ($result) {
		$output['status'] = 'true';
		$output['data']['action'] = $action;
		$output['data']['timer'] = $timer;
		$output['data']['set_timer_at'] = $set_timer_at;
	} else {
		$output['status'] = 'false';
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
	}
  return $output;
}
