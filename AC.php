<?php
function getAllAC($conn)
{
	$sql = "SELECT * FROM ac";
	$result = $conn->query($sql);
	while($row = mysqli_fetch_row($result)) {
		 $inResult = count($row) > 0 ? $row : "404";

		if ($inResult == '404') {
	    	$output['status'] = 'false';
			$output['data']['message'] = 'gak ada ac gan!';
			break;
		}else {
	    	$output['status'] = 'true';
	    	$output['data']['ac-'.$inResult[0].'']['id'] = $inResult[0];
	    	$output['data']['ac-'.$inResult[0].'']['status'] = $inResult[1];
	    	$output['data']['ac-'.$inResult[0].'']['last_update'] = $inResult[2];
	    	$output['data']['ac-'.$inResult[0].'']['timer'] = $inResult[3];
	    	$output['data']['ac-'.$inResult[0].'']['set_timer_at'] = $inResult[4];
	    	$output['data']['ac-'.$inResult[0].'']['timer_action'] = $inResult[5];
	    	$output['data']['ac-'.$inResult[0].'']['temperature'] = $inResult[6];
			$output['data']['ac-'.$inResult[0].'']['message'] = $inResult[1] == '1' ? 'nyala gan' : 'mati gan' ;
		}
	}
	
	return $output;
}