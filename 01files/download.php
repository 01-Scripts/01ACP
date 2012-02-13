<?php
/*
	01ACP - Copyright 2008-2012 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo:	File-Download-Funktion
	#fv.122#
*/

$flag_acp = false;
$subfolder = "../";
include("../01acp/system/headinclude.php");

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
	ini_set('zlib.output_compression', 'Off');

if(isset($_GET['fileid']) && !empty($_GET['fileid']) && is_numeric($_GET['fileid'])){
	$list = mysql_query("SELECT id,type,orgname,name FROM ".$mysql_tables['files']." WHERE id='".mysql_real_escape_string($_GET['fileid'])."' LIMIT 1");
	while($row = mysql_fetch_assoc($list)){
		switch($row['type']){
		  case "pic":
		    $folder = $picuploaddir;
		    break;
		  case "file":
		    $folder = "";
		    break;
		  }
		
		if(!file_exists($folder.$row['name'])){
  			echo "Fehler: Datei nicht gefunden.";
  			exit;
			}
		else{
			// downloads+1
			if(!isset($_GET['nocount']))
				mysql_query("UPDATE ".$mysql_tables['files']." SET downloads = downloads+1 WHERE id = '".mysql_real_escape_string($_GET['fileid'])."' LIMIT 1");
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=\"".basename($row['orgname'])."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($folder.$row['name']));
			readfile($folder.$row['name']);
			exit();
			}
		}
	if(!isset($folder))
		echo "Fehler: Es konnte keine zur &uuml;bergebenen ID passende Datei gefunden werden!";
	}
else
	echo "Fehler: Keine korrekte Datei-ID &uuml;bergeben (fileid = empty)";

?>