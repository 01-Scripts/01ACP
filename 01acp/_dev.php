<?PHP
/* 
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	�bersicht �ber zentral zur Verf�gung stehende Arrays
*/

$menuecat = "01acp_start";
$sitetitle = "Dev-�bersicht";
$filename = $_SERVER['SCRIPT_NAME'];

// Config-Dateien
include("system/main.php");
include("system/head.php");

// Sicherheitsabfrage: Login
if(isset($userdata['id']) && $userdata['id'] > 0 && $userdata['level'] == 10){
?>

<b>$userdata</b><br /><code>
<?PHP echo print_r($userdata); ?>
</code>

<br /><br />

<b>$settings</b><br /><code>
<?PHP echo print_r($settings); ?>
</code>

<br /><br />

<b>$module</b><br /><code>
<?PHP echo print_r($module); ?>
</code>

<br /><br />

<b>$inst_module</b><br /><code>
<?PHP echo print_r($inst_module); ?>
</code>

<br /><br />

<b>$mysql_tables</b><br /><code>
<?PHP echo print_r($mysql_tables); ?>
</code>

<br /><br />

<b>$picendungen</b><br /><code>
<?PHP echo print_r($picendungen); ?>
</code>

<br /><br />

<b>$attachmentendungen</b><br /><code>
<?PHP echo print_r($attachmentendungen); ?>
</code>

<br /><br />

<?PHP
}else $flag_loginerror = true;
include("system/foot.php");

// 01ACP Copyright 2008 by Michael Lorer - 01-Scripts.de
?>