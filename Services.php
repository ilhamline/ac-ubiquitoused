<?php

	function pingChair($baseServer){
		$result = file_get_contents('http://10.10.100.206:9000/?fungsi=getOccupiedSeats');
		$res = json_decode($result,true);

		$i = $res['status'] == 'true' ? true : false;

		if (!$i) {
			$hasil['status'] = 'false';
			$hasil['data']['message'] = "Error saat melakukan ping";
			return $hasil;
		}

		$hasil['status'] = 'true';
		$apa = $res['data'] > 0 ? true : false;

		if(!$apa){
			//standby
			$out = file_get_contents('http://'.$baseServer.'/index?fungsi=set_status&arg1=standby');
			$output = json_decode($out, true);

			if($output[0]['status'] == 'false') {
				$hasil['status'] = 'false';
				$hasil['data']['message'] = "Gagal set standby : " . $output['data']['message'];
				return $hasil;
			}

			$hasil['data']['message'] = "Ruangan kosong";
		} else {
			//on
			$out = file_get_contents('http://'.$baseServer.'/index?fungsi=set_status&arg1=on');
			$temp = file_get_contents('http://'.$baseServer.'/index?fungsi=set_temperature&arg1=20');
			$output = json_decode($out, true);
			$temperature = json_decode($temp, true);

			if($output[0]['status'] == 'false') {
				$hasil['status'] = 'false';
				$hasil['data']['message'] = "Gagal set on : " . $output[0]['data']['message'];
				return $hasil;
			}

			if($temperature[0]['status'] == 'false') {
				$hasil['status'] = 'false';
				$hasil['data']['message'] = "Gagal set temperature : " . $temperature['data']['message'];
				return $hasil;
			}

			$hasil['data']['message'] = "Ruangan terisi";
		}

		return $hasil;
	}
?>
