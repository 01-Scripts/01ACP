<?PHP
/*
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo: 	Simuliert eine Bilddatei um Cookies unabhängig von headers_already.sent zu setzen.
	#fv.1100#
*/

if(isset($_GET['cookiename']) && !empty($_GET['cookiename']) && isset($_GET['cookiewert']) && 
   isset($_GET['cookietime']) && !empty($_GET['cookietime'])){ 

	setcookie(stripslashes($_GET['cookiename']),stripslashes($_GET['cookiewert']),stripslashes($_GET['cookietime']),"/");
	}



header("Content-type: image/jpg");
$echofile_id = imagecreatetruecolor(1, 1);
imagejpeg($echofile_id);
imagedestroy($echofile_id);
?>