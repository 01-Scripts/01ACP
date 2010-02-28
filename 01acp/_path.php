<?PHP
/* 
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	L�dt Dateien aus Unterverzeichnissen, damit die Pfadangaben auf den normalen 01acp-Ordner bezogen werden k�nnen. F�r Popup und IFrames
*/

// Session starten:
@session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>01acp</title>

<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />
<?PHP
// Config-Dateien
include("system/main.php");
?>
<link rel="stylesheet" type="text/css" href="system/default.css" />

</head>

<body style="padding:0 10px;">

<div class="contentbox">

<?PHP
switch($_REQUEST['include']){
  case "uploader":
	$filename = "_path.php?include=uploader&amp;";
	include_once("system/uploader.php");
  break;
  }
?>

</div>

<!-- 01ACP Copyright 2008 by Michael Lorer - 01-Scripts.de -->
</body>
</html>