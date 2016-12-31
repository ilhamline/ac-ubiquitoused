<?php

function setStatus($conn, $id, $status){
	$get_status = getStatus($conn, $id);
	$last_status = $get_status['status'] == 'true' ? $get_status['data']['status'] : null;

	if ($status == 'on' && (date('H') > 21 || date('H') < 7)) {
		$output['status'] = 'false';
		$output['data']['id'] = $id;
		$output['data']['message'] = "Tidak boleh menyalakan ac pada jam 07.00-21.00";
		return $output;
	}

	if ($status == 'on' || $status == 'off') {
		$sql = "UPDATE ac SET status='$status', last_onoff_at=now() where id='$id'";
	} elseif ($status == 'standby') {
		$sql = "UPDATE ac SET status='$status' where id='$id'";

		$set_temp = setTemp($conn, $id, 25);

		if ($set_temp['status'] == 'false') {
			$output['status'] = 'false';
			$output['data']['id'] = $id;
			$output['data']['message'] = "Error set temp standby : " . $set_temp['data']['message'];
			return $output;
		}

	} else {
		$output['status'] = 'false';
		$output['data']['id'] = $id;
		$output['data']['message'] = "Action tidak valid";
		return $output;
	}

	$result = $conn->query($sql);
	
	if ($last_status && $result) {
		$output['status'] = 'true';
		$output['data']['id'] = $id;
		$output['data']['last_status'] = $last_status;
		$output['data']['message'] = "berhasil set status menjadi ".$status;
	} else {
		$output['status'] = 'false';
		$output['data']['id'] = $id;
		$output['data']['message'] = "Error: " . $sql . mysqli_error($conn);
	}
	return $output;
}
function getStatus($conn, $id){
	$sql = "SELECT status FROM ac WHERE id = '".$id."'";
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
		$output['data']['message'] = "AC dengan id ".$id." memiliki status ".$result;
	}
	return $output;
}
