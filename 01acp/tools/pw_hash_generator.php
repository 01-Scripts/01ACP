<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
	01ACP - Copyright 2008-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo:	Passwort-Hash für 01ACP-Datenbank generieren
	#fv.122#
-->
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />

<title>01acp - Passwort-Hash-Generator</title>

<link rel="stylesheet" type="text/css" href="system/default.css" />

</head>

<body>

<?php 
if(isset($_POST['send']) && !empty($_POST['send'])){
	include("../../01_config.php");
	include("../system/functions.php");
	
	echo "<b>Passwort-Hash:</b> ".pwhashing2($_POST['password'], $_POST['uid']);
	}
?>

<form action="pw_hash_generator.php" method="post">
<b>Passwort eingeben:</b> <input type="text" name="password" size="25" /><br />
<b>Benutzer-ID:</b> <input type="text" name="uid" size="5" />
<br />
<input type="submit" name="send" value="Hash generieren" />
</form>

</body>
</html>