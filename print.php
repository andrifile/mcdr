<?php
include_once("mysql.php"); include_once("header.php"); include_once("sugarsoap.php");

set_time_limit(0);
	/*
	$link = mysql_connect('cdr.domain', 'root', '');
	mysql_select_db('dslam');
	$query = "INSERT INTO `fatura` VALUES ( NULL, CURRENT_TIMESTAMP, 'http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."')";
	mysql_query($query);
	mysql_close($link);
	*/

if(isset($_GET['file'])) {
	include_once('header.php');
	print '<div style="margin-top:300px; margin-left:500px;"><strong>';

	if (isset($_GET['trunk'])) {
		if (isset($_GET['year'])) {
			$trunk = str_ireplace('/', '_', $_GET['trunk']);
			$provider = provider($trunk);
			if (!file_exists('generated/'.$provider.'-'.$_GET['year'].$_GET['month'].'.pdf')) {
				exec('./wkhtmltopdf-i386 http://cdr.domain/mcdr/trunk.php?year='.$_GET['year'].'\&month='.$_GET['month'].'\&trunk='.$trunk.'\&print=1\&filter=1 ./generated/'.$provider.'-'.$_GET['year'].$_GET['month'].'.pdf');
				print 'Fatura u ruajt ne <br /><a href="generated/'.$provider.'-'.$_GET['year'].$_GET['month'].'.pdf">'.'/generated/'.$provider.'-'.$_GET['year'].$_GET['month'].'.pdf'."</a>";
			} else {
				print 'Kjo fature egziston.<br /><a href="generated/'.$provider.'-'.$_GET['year'].$_GET['month'].'.pdf">'.'/generated/'.$provider.'-'.$_GET['year'].$_GET['month'].'.pdf'."</a>";
			}
		} else {
			if (!file_exists('generated/'.$provider.'.pdf')) {
				exec('./wkhtmltopdf-i386 http://cdr.domain/mcdr/trunk.php?trunk='.$trunk.'\&print=1\&filter=1 ./generated/'.$provider.'.pdf');
				print 'Fatura u ruajt ne <br /><a href="generated/'.$provider.'.pdf">'.'/generated/'.$provider.'.pdf'."</a>";
			} else {
				print 'Kjo fature egziston.<br /><a href="generated/'.$provider.'.pdf">'.'/generated/'.$provider.'-'.'.pdf'."</a>";
			}
		}
		print '</strong></div>';
	} else if (isset($_GET['ext'])) {
		
		if (!file_exists('generated/'.$_GET['ext'].'-'.$_GET['year'].$_GET['month'].'.pdf')) {
			exec('./wkhtmltopdf-i386 http://cdr.domain/mcdr/cdr.php?year='.$_GET['year'].'\&month='.$_GET['month'].'\&ext='.$_GET['ext'].'\&print=1\&filter=1 ./generated/'.$_GET['ext'].'-'.$_GET['year'].$_GET['month'].'.pdf');
			print 'Fatura u ruajt ne <br /><a href="generated/'.$_GET['ext'].'-'.$_GET['year'].$_GET['month'].'.pdf">'.'/generated/'.$_GET['ext'].'-'.$_GET['year'].$_GET['month'].'.pdf'."</a>";
		} else {
			print 'Kjo fature egziston.<br /><a href="generated/'.$_GET['ext'].'-'.$_GET['year'].$_GET['month'].'.pdf">'.'/generated/'.$_GET['ext'].'-'.$_GET['year'].$_GET['month'].'.pdf'."</a>";
		}
	} else if (!isset($_GET['year'])){
		if (isset($_GET['trunk'])) {
			if (!file_exists('generated/'.$_GET['trunk'].'.pdf')) {
				exec('./wkhtmltopdf-i386 http://cdr.domain/mcdr/cdr.php?trunk='.$_GET['trunk'].'\&print=1\&filter=1 ./generated/'.$_GET['trunk'].'.pdf');
				print 'Fatura u ruajt ne <br /><a href="generated/'.$_GET['trunk'].'.pdf">'.'/generated/'.$_GET['trunk'].'.pdf'."</a>";
			} else {
				print 'Kjo fature egziston.<br /><a href="generated/'.$_GET['trunk'].'.pdf">'.'/generated/'.$_GET['trunk'].'-'.'.pdf'."</a>";
			}

		} else {
			if (!file_exists('generated/'.$_GET['ext'].'.pdf')) {
				exec('./wkhtmltopdf-i386 http://cdr.domain/mcdr/cdr.php?ext='.$_GET['ext'].'\&print=1\&filter=1 ./generated/'.$_GET['ext'].'.pdf');
				print 'Fatura u ruajt ne <br /><a href="generated/'.$_GET['ext'].'.pdf">'.'/generated/'.$_GET['ext'].'.pdf'."</a>";
			} else {
				print 'Kjo fature egziston.<br /><a href="generated/'.$_GET['ext'].'.pdf">'.'/generated/'.$_GET['ext'].'-'.'.pdf'."</a>";
			}
		}
	}
	print '</strong></div>';
}	
?>