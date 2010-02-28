<?PHP
/* 
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:  Nur für den Administrationsbereich nötige globale Grundeinstellungen und Variablendefinitionen
				Wird auf jeder ACP-Seite als aller erste Datei includiert und enthält include für headinclude.php
*/

$flag_acp = true;
$flag_loginerror = false;

// Datei wird nur im ACP (statt direkt der headinclude.php) eingebunden
include_once("system/headinclude.php");

// Userberechtigungen in Array $userdata[] einlesen:
if(!isset($hide_userdata)){
	if(isset($_SESSION['01_idsession']) && isset($_SESSION['01_passsession'])){
		$userdata = getUserdata("",TRUE);
		if($userdata['sperre'] == 1){
			$flag_loginerror = true;
			break;
			}
		}
	}

// 01ACP Copyright 2008 by Michael Lorer - 01-Scripts.de
?>