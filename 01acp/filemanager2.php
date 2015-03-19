<?PHP
/* 
    01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
    Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
    Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
    
    Modul:      01ACP
    Dateiinfo:  Datei- & Bildverwaltung (Hochgeladenen Bilder und andere Dateien verwalten sowie neue Dateien hochladen)
    #fv.130#
*/

$menuecat = "01acp_filemanager";
$sitetitle = "Datei- &amp; Bildverwaltung";
$filename = $_SERVER['PHP_SELF'];

// Config-Dateien
include("system/main.php");
include("system/head.php");

// Sicherheitsabfrage: Login & Benutzerrechte für Dateimanager
if(isset($userdata['id']) && $userdata['id'] > 0 && $userdata['dateimanager'] >= 1){

?>

<iframe src="fileman/index.html?integration=custom&type=files&txtFieldId=txtSelectedFile" style="width:100%;height:800px" frameborder="0"></iframe>

<?PHP

}else $flag_loginerror = true;
include("system/foot.php");

?>