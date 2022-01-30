<?php
$serverName = "localhost";
$connectionInfo = array( "Database"=>"MOVIE_RESERVATION", "UID"=>"sa", "PWD"=>"");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
?>