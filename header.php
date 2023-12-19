<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<title>MCDR</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
function cmimi($str, $op, $opname='') {
	$str = preg_replace("@^[+|++|00]355.+@i", "0", $str);
	
	$prefix = substr($str, 0, 3);
	if(substr($prefix,1,1)=="4" || $opname=='ALBTELEKOM') {
		return $op[0];
	} else if ($prefix=="069" || $opname=='VODAFONE') { 
		return $op[1];
	} else if ($prefix=="068" || $opname=='AMC') { 
		return $op[2];
	} else if ($prefix=="067" || $opname=='EAGLE') {
		return $op[3];
	} else if ($prefix=="066" || $opname=='PLUS') {
		return $op[4];
	}
	else {
		return array("Internal", "", 0, 0, 0);
	}
}

function format_time($time) {

	$date = date_create('0000-00-00 00:00:00');
	date_add($date, date_interval_create_from_date_string("$time seconds"));
	return date_format($date, 'H:i:s');
}

function provider($str) {
	if (str_ireplace("/", "_",$str) == "DAHDI/G1") {
		return "Albtelekom";
	}
}

?>