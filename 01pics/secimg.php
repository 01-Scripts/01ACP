<?PHP
session_start();
/* 
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Captcha-Funktion
	#fv.122#
*/

function rand_String($laenge){
	$zahl = mt_rand(1000, 9999);

	$passzahl = md5($zahl);
	$newpass = substr($passzahl,0,$laenge);

	return $newpass;
	}


$secCode = rand_String(6);
$_SESSION['antispam01'] = md5($secCode);

$im = imagecreatefromjpeg("sec.jpg");

$font = "verdanab.ttf";
$fontSize = 12;
$fontColor = imagecolorallocate($im, 70, 70, 70);

imagettftext($im, $fontSize, 10, 5, 25, $fontColor, $font, $secCode);
header("Content-Type: image/jpeg");
imagejpeg($im,NULL,100);
?>