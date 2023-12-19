<?php

$hostname="127.0.0.1";
$database="asterisk";
$username="db_user";
$password="db_pass";

!$link = mysql_connect($hostname, $username, $password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db($database);
?>