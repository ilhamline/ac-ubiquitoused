#!/usr/bin/php
<?php

$on  = "*/1 * * * * wget http://localhost/ac-ubiquitoused/index?fungsi=minus_timer?id=1\n";
$off = "#*/1 * * * * wget http://localhost/ac-ubiquitoused/index?fungsi=minus_timer?id=1\n";

$param    = isset( $argv[1] ) ? $argv[1] : '';
$filename = isset( $argv[2] ) ? $argv[2] : '';

if ( $param == 'activate' )
{
    shell_exec( 'export EDITOR="/opt/lampp/htdocs/ac-ubiquitoused/cron.php on"; crontab -e' );
}
elseif( $param == 'deactivate' )
{
    shell_exec( 'export EDITOR="/opt/lampp/htdocs/ac-ubiquitoused/cron.php off"; crontab -e' );
}
elseif( in_array( $param, array( 'on', 'off' ) ) )
{
    if ( !is_writable( $filename ) )
        exit();

    $crontab = file( $filename );
    $key = array_search( $param == 'on' ? $off : $on, $crontab );

    $crontab[$key] = $param == 'on' ? $on : $off;
    sleep( 1 );
    file_put_contents( $filename, implode( '', $crontab ) );
}

exit();

?>