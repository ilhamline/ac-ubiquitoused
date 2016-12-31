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

function getSystemTemperature($conn){
  $sql = "SELECT temperature FROM system";
  $result = $conn->query($sql);
  $row = mysqli_fetch_row($result);
  $result = count($row) > 0 ? $row[0] : "404";
  if ($result == '404') {
    $output['status'] = 'false';
    $output['data']['message'] = 'system rusak gan!';
  }else {
    $result = intval($result);
    $output['status'] = 'true';
    $output['data']['system_temp'] = $result;
  }
  return $output;
}

function updateSystemTemperature($conn){
  //itung suhu rata-rata
  $sql = "SELECT temp FROM ac WHERE status='on' OR status='standby'";
  $result = $conn->query($sql);

  $sumTemp = 0;
  $countTemp = 0;

  while($row = mysqli_fetch_row($result)) {
    $inResult = count($row) > 0 ? $row : "404";
    
    if ($inResult == '404') {
      $output['status'] = 'false';
      $output['data']['message'] = "Error get ac temperature: " . $sql . mysqli_error($conn);
      return $output;
    }else {
      $sumTemp = $sumTemp + $inResult[0];
      $countTemp = $countTemp + 1;
    }
  }

  if ($countTemp == 0) {
    $output['status'] = 'false';
    $output['data']['message'] = "ac mati semua gan";
    return $output;
  }

  $avgTemp = $sumTemp / $countTemp;

  //ambil system temperature
  $get_system_temperature = getSystemTemperature($conn);
  $status = $get_system_temperature['status'] == "true" ? true : false;
  $systemTemp = 0;

  if($status) {
    $systemTemp = $get_system_temperature['data']['system_temp'];
  } else {
    $output['status'] = 'false';
    $output['data']['message'] = "Error get system temperature: " . $get_system_temperature['data']['message'];
    return $output;
  }

  //compare suhu ac dan suhu ruangan
  if ($avgTemp > $systemTemp){
    $set_system_temperature = setSystemTemperature($conn, $systemTemp + 1);
    $set_system_time = setSystemTime($conn, date("Y-m-d H:i:s"));
  } elseif ($avgTemp < $systemTemp) {
    $set_system_temperature = setSystemTemperature($conn, $systemTemp - 1);
    $set_system_time = setSystemTime($conn, date("Y-m-d H:i:s"));
  } else {
    //kalo ga berubah
    $output['status'] = 'true';
    $output['data']['temperature'] = $systemTemp;
    $output['data']['message'] = "temperature tidak berubah";
    return $output;
  }

  if ($set_system_temperature['status'] == 'false') {
    $output['status'] = 'false';
    $output['data']['message'] = "Error set system temperature: " . $set_system_temperature['data']['message'];
    return $output;
  }

  if ($set_system_time['status'] == 'false') {
    $output['status'] = 'false';
    $output['data']['message'] = "Error set system time: " . $set_system_time['data']['message'];
    return $output;
  }

  $output['status'] = 'true';
  $output['data']['old_temperature'] = $systemTemp;
  $output['data']['new_temperature'] = $set_system_temperature['data']['message'];
  $output['data']['message'] = "berhasil update temperature";
  return $output;
}
