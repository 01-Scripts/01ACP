<?PHP
session_start();
/* 
	01ACP - Copyright 2008-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Captcha-Funktion
	#fv.131#
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

/* Für die Generierung des Captcha-Images können zwei verschiedene Sets an 
 * Schriftarten verwendet werden. Aktueller Standard ist Set 2.
 * Welches Set Sie verwenden möchten kann durch Eintragen der gewünschten Nummer
 * in nachfolgende Variable bestimmt werden.
 */ 
$use_captchaset = 2;

switch($use_captchaset){
  case 1:
	/* Captcha Set 1 */
	$font = "verdanab.ttf";
	$fontSize = 12;
	$fontColor = imagecolorallocate($im, 70, 70, 70);
  break;
  case 2:
  default:
	/* Captcha Set 2 */
	$font = "Eadui.ttf";
	$fontSize = 20;
	$fontColor = imagecolorallocate($im, 10, 20, 20);
  break;
}

imagettftext($im, $fontSize, 10, 5, 25, $fontColor, $font, $secCode);
header("Content-Type: image/jpeg");
imagejpeg($im,NULL,100);
?>