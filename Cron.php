<?php

$checkTempActive = false;

function timerOn($id, $baseServer) {
  	exec( '(crontab -l ; echo "* * * * * curl \'http://'.$baseServer.'/index?fungsi=minus_timer&id_device='.$id.'\'") | crontab -' );
 
}
function timerOff($id, $baseServer) {
  	exec( 'crontab -l | grep -v "curl \'http://'.$baseServer.'/index?fungsi=minus_timer&id_device='.$id.'\'"  | crontab -' );
}

function checkTemp($baseServer) {
	exec( 'crontab -l | grep -v "curl \'http://'.$baseServer.'/index?fungsi=update_system_temperature\'"  | crontab -' );
	exec( '(crontab -l ; echo "* * * * * curl \'http://'.$baseServer.'/index?fungsi=update_system_temperature\'") | crontab -' );
}

function autoOn7am($baseServer) {
	exec( 'crontab -l | grep -v "curl \'http://'.$baseServer.'/index?fungsi=set_status&arg1=on\'"  | crontab -' );
	exec( '(crontab -l ; echo "0 7 * * * curl \'http://'.$baseServer.'/index?fungsi=set_status&arg1=on\'") | crontab -' );
}

function autoOff9pm($baseServer){
	exec( 'crontab -l | grep -v "curl \'http://'.$baseServer.'/index?fungsi=set_status&arg1=off\'"  | crontab -' );
	exec( '(crontab -l ; echo "0 21 * * * curl \'http://'.$baseServer.'/index?fungsi=set_status&arg1=off\'") | crontab -' );
}