<?php
/*
	01ACP - Copyright 2008-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo:	File-Download-Funktion
	#fv.131#
*/

$flag_acp = false;
$subfolder = "../";
include("../01acp/system/headinclude.php");

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
	ini_set('zlib.output_compression', 'Off');

// More security for file download
if(isset($_GET['fileid']) && !empty($_GET['fileid']) && is_numeric($_GET['fileid']) && isset($settings['filesecid']) && $settings['filesecid'] > 0 && $_GET['fileid'] <= $settings['filesecid'] ||
   isset($_GET['fileid']) && !empty($_GET['fileid']) && is_numeric($_GET['fileid']) && !isset($settings['filesecid'])  )
	$query = "SELECT id,filetype,orgname,name FROM ".$mysql_tables['files']." WHERE id='".$mysqli->escape_string($_GET['fileid'])."' LIMIT 1";
elseif(isset($_GET['fileid']) && !empty($_GET['fileid']))
	$query = "SELECT id,filetype,orgname,name FROM ".$mysql_tables['files']." WHERE name='".$mysqli->escape_string($_GET['fileid'])."' LIMIT 1";


if(isset($query)){
	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){
		switch($row['filetype']){
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
				$mysqli->query("UPDATE ".$mysql_tables['files']." SET downloads = downloads+1 WHERE id = '".$row['id']."' LIMIT 1");
			
			$path_parts = pathinfo(basename($row['orgname']));
			if(!isset($path_parts['extension']) || empty($path_parts['extension'])) $path_parts['extension'] = getEndung($row['name']);

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=\"".$path_parts['filename'].".".strtolower($path_parts['extension'])."\"" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($folder.$row['name']));
			readfile($folder.$row['name']);
			exit();
			}
		}
	if(!isset($folder))
		echo "Fehler: Datei nicht gefunden.";
	}
else
	echo "Fehler: Keine Datei gefunden!";

?>