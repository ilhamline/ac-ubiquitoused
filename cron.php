#!/usr/bin/php
<?php

$on  = "*/1 * * * * curl -i -H \"Accept: application/json\" -H \"Content-Type: application/json\" -X GET \"http://localhost/ac-ubiquitoused/index?fungsi=minus_time&id=1\"\n";
$off = "#*/1 * * * * curl -i -H \"Accept: application/json\" -H \"Content-Type: application/json\" -X GET \"http://localhost/ac-ubiquitoused/index?fungsi=minus_time&id=1\"\n";

$param    = isset( $argv[1] ) ? $argv[1] : '';
$filename = isset( $argv[2] ) ? $argv[2] : '';

if( in_array( $param, array( 'on', 'off' ) ) )
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