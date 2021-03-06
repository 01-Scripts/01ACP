<?PHP
/* 
	01ACP - Copyright 2008-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:  Nur f�r den Administrationsbereich n�tige globale Grundeinstellungen und Variablendefinitionen
				Wird auf jeder ACP-Seite als aller erste Datei includiert und enth�lt include f�r headinclude.php
	#fv.131#
*/

$flag_acp = true;
$flag_loginerror = false;

// Datei wird nur im ACP (statt direkt der headinclude.php) eingebunden
@header('Content-Type: text/html; charset=ISO-8859-1');
include_once("system/headinclude.php");

// Session-Bugfix f�r Fancyupload
if($flag_sessionbugfix && isset($_GET['sessiondata']) && !empty($_GET['sessiondata']))
	$_SESSION = unserializesession(urldecode(stripslashes($_GET['sessiondata'])));

// Userberechtigungen in Array $userdata[] einlesen:
if(!isset($hide_userdata)){
	$shasalt = sha1($salt);
	if(isset($_SESSION['01_idsession_'.$shasalt]) && isset($_SESSION['01_passsession_'.$shasalt]) && !empty($_SESSION['01_passsession_'.$shasalt]) && strlen($_SESSION['01_passsession_'.$shasalt]) == 40){
		$userdata = getUserdata("",TRUE);
		if($userdata['sperre'] == 1){
			$flag_loginerror = true;
			}
		}
	}

?>