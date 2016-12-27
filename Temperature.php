<?php

function setTemp($conn, $id, $temp){
  if ($temp >= 16 && $temp <= 50) {
    $get_temp = getTemp($conn, $id);
    $last_temp = $get_temp['status'] == 'true' ? $get_temp['data']['temp'] : null;
    $sql = "UPDATE ac SET temp='$temp' where id='$id'";
    $result = $conn->query($sql);
    if ($last_temp && $result) {
      $last_temp = intval($last_temp);
      $output['status'] = 'true';
      $output['data']['id'] = $id;
      $output['data']['last_temp'] = $last_temp;
      $output['data']['message'] = "berhasil set temperatur menjadi ".$temp;
    } else {
      $output['status'] = 'false';
      $output['data']['id'] = $id;
      $output['data']['message'] = "Error: " . $sql . mysqli_error($conn);
    }
    return $output;
  } else {
    $output['status'] = 'false';
    $output['data']['id'] = $id;
    $output['data']['message'] = "Temperature ".$temp." derajat Celcius tidak vald";
    return $output;
  }
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
    $result = intval($result);
    $output['status'] = 'true';
    $output['data']['id'] = $id;
    $output['data']['temp'] = $result;
	}
	return $output;
}
function setSystemTemperature($conn, $temperature){
	$sql = "UPDATE system SET temperature='$temperature'";
	$result = $conn->query($sql);
	if ($result) {
		$output['status'] = 'true';
		$output['data']['message'] = "berhasil set temperature ".$temperature;
	} else {
		$output['status'] = 'false';
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);;
	}
  return $output;
}

function updateSystemTemperature($conn){
  //itung suhu rata-rata
  $sql = "SELECT temp FROM ac WHERE status='on'";
  $result = $conn->query($sql);

  $sumTemp = 0;
  $countTemp = 0;

  while($row = mysqli_fetch_row($result)) {
    $inResult = count($row) > 0 ? $row : "404";
    
    if ($inResult == '404') {
      exit();
    }else {
      $sumTemp = $sumTemp + $inResult[0];
      $countTemp = $countTemp + 1;
    }
  }

  $avgTemp = $sumTemp / $countTemp;

  //ambil system temperature
  $sql = "SELECT temperature FROM system";
  $result = $conn->query($sql);
  $row = mysqli_fetch_row($result);
  $result = count($row) > 0 ? $row[0] : "404";
  $systemTemp = 0;

  if($result == '404') {
    exit();
  } else {
    $systemTemp = $result;
  }

  //compare suhu ac dan suhu ruangan
  if ($avgTemp > $systemTemp){
    setSystemTemperature($conn, $systemTemp + 1);
    setSystemTime($conn, date("Y-m-d H:i:s"));
  } elseif ($avgTemp < $systemTemp) {
    setSystemTemperature($conn, $systemTemp - 1);
    setSystemTime($conn, date("Y-m-d H:i:s"));
  }

}
