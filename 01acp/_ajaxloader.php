<?PHP
/* 
	01ACP - Copyright 2008-2009 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo: 	Verteilt eingehende Ajax-Request an die entsprechenden Verarbeitungsdateien (Modulspezifisch oder vom 01ACP)
	#fv.1101#
*/

//Session starten:
if(isset($_GET['SID']) && !empty($_GET['SID']))
	session_id($_GET['SID']);		// Session-ID aus URL-String für Fancy-Uploader
@session_start();

// Config-Dateien
include("system/main.php");

if(isset($userdata['id']) && $userdata['id'] >= 1){

	if(file_exists($modulpath."_ajax.php") && !is_dir($modulpath."_ajax.php"))
		include_once($modulpath."_ajax.php");
	else
		include_once("_ajax.php");
	}
else{
	echo "<script> Failed_delfade(); </script>";
	}



// 01ACP Copyright 2008-2009 by Michael Lorer - 01-Scripts.de
?>