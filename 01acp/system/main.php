<?PHP
/* 
	01ACP - Copyright 2008-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:  Nur für den Administrationsbereich nötige globale Grundeinstellungen und Variablendefinitionen
				Wird auf jeder ACP-Seite als aller erste Datei includiert und enthält include für headinclude.php
	#fv.122#
*/

$flag_acp = true;
$flag_loginerror = false;

// Datei wird nur im ACP (statt direkt der headinclude.php) eingebunden
include_once("system/headinclude.php");

// Session-Bugfix für Fancyupload
if($flag_sessionbugfix && isset($_GET['sessiondata']) && !empty($_GET['sessiondata']))
	$_SESSION = unserializesession(urldecode(stripslashes($_GET['sessiondata'])));

// Userberechtigungen in Array $userdata[] einlesen:
if(!isset($hide_userdata)){
	if(isset($_SESSION['01_idsession_'.sha1($salt)]) && isset($_SESSION['01_passsession_'.sha1($salt)]) && !empty($_SESSION['01_passsession_'.sha1($salt)]) && strlen($_SESSION['01_passsession_'.sha1($salt)]) == 40){
		$userdata = getUserdata("",TRUE);
		if($userdata['sperre'] == 1){
			$flag_loginerror = true;
			break;
			}
		}
	}

?>