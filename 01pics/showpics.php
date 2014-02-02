<?PHP
/* 
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Dynamisches Generieren von Thumbnails
	#fv.122#
*/

include("../01acp/system/functions.php");
include("../01acp/cache/thumbwidth.php");
define('ACP_TB_WIDTH', 40);
define('ACP_TB_WIDTH200', 200);
define('FILE_404_THUMB', '404thumb.gif');
define('FILE_GIF_THUMB', 'gifthumb.gif');

//Bild-Ausgabe
if(file_exists($_GET['img'])){
	if(isset($_GET['img']) && !empty($_GET['img']) && getEndung($_GET['img']) == "gif" && isset($_GET['hidegif']) && !empty($_GET['hidegif'])){
		if($_GET['hidegif'] == "complete")
			$file = fread(fopen(FILE_404_THUMB, "r"), filesize(FILE_404_THUMB));
		else
			$file = fread(fopen(FILE_GIF_THUMB, "r"), filesize(FILE_GIF_THUMB));

		header("Content-type: image/gif");
		echo $file; 
		fclose($file);
		}
	elseif(isset($_GET['img']) && !empty($_GET['img']) && getEndung($_GET['img']) == "gif"){
		$_GET['img'] = str_replace("../","",$_GET['img']);
		$_GET['img'] = str_replace(".gif","",$_GET['img']);
		$_GET['img'] = str_replace(".","",$_GET['img']);
		$_GET['img'] = str_replace("/","",$_GET['img']);
		
		$file = fread(fopen($_GET['img'].".gif", "r"), filesize($_GET['img'].".gif"));
		header("Content-type: image/gif");
		echo $file; 
		fclose($file);
		}
	elseif(isset($_GET['img']) && !empty($_GET['img']) && isset($_GET['size']) && !empty($_GET['size']) && is_numeric($_GET['size'])){
	    showpic($_GET['img'],$_GET['size']);
	    }
	elseif(isset($_GET['img']) && !empty($_GET['img'])){
	    showpic($_GET['img']);
	    }
	}
else{
	$file = fread(fopen(FILE_404_THUMB, "r"), filesize(FILE_404_THUMB));
	header("Content-type: image/gif");
	echo $file; 
	fclose($file);
	}

?>