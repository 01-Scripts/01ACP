<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	#fv.1002#
	
	Design by Free CSS Templates http://www.freecsstemplates.org - Noncopyright-Lizenz erworben von Michael Lorer, 01-Scripts.de
	
	Modul:		01ACP
	Dateiinfo:	Layout für ACP
	#fv.1102#
-->
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />

<title>01acp - <?PHP echo $sitetitle; ?></title>

<link rel="stylesheet" type="text/css" href="system/default.css" />

<?PHP
// Benötigte MooTools laden
if(isset($mootools_use) && is_array($mootools_use)){
	foreach($mootools_use as $use){
		if(isset($mootools[$use])){
			foreach($mootools[$use] as $include){
				echo $include."\n";
				}
			}
		}
	}
	
// DOMReady ausgeben
if(isset($mootools_use) && is_array($mootools_use)){
	echo "<script type=\"text/javascript\">
	window.addEvent('domready',function(){
	";
	foreach($mootools_use as $use){
		if(isset($domready[$use])){
			foreach($domready[$use] as $include){
				include_once($include);
				echo "\n\n";
				}
			}
		}
	include("system/js/domready-javas.js");
	echo "
    });
</script>\n";
	}
?>
<script src="system/js/javas.js" type="text/javascript"></script>
<!-- 2559ad821dde361560dbf967c3406f51 -->
<?PHP
// modulspezifische JavaScript-Datei
if(isset($addJSFile) && file_exists($modulpath.$addJSFile) && !is_dir($modulpath.$addJSFile) && !empty($addJSFile))
	echo "<script src=\"".$modulpath.$addJSFile."\" type=\"text/javascript\"></script>";

// modulspezifische CSS-Datei
if(isset($addCSSFile) && file_exists($modulpath.$addCSSFile) && !is_dir($modulpath.$addCSSFile) && !empty($addCSSFile))
	echo "<link href=\"".$modulpath.$addCSSFile."\" rel=\"stylesheet\" type=\"text/css\" />";
?>
</head>

<body>
<?php 
if(strchr($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0") && $flag_showIE6Warning)
	echo "<p align=\"center\" class=\"meldung_error\" style=\"margin-top: 0;\">
	Sie verwenden einen <b>veralteten Browser</b> (Internet Explorer 6) mit <b>Sicherheitsschwachstellen</b>
	und <b>können nicht alle Funktionen dieser Webseite nutzen</b>.<br /><a href=\"http://browser-update.org/update.php\" target=\"_blank\">Hier erfahren Sie, wie 
	einfach Sie Ihren Browser aktualisieren können.</a></p>";
?>

<div id="header">
	<ul id="menu">
		<?PHP
		if(isset($userdata['id']) && $userdata['id'] >= 1) 
			echo "        <li><a href=\"index.php?action=logout\" title=\"Aus dem ACP ausloggen\"><img src=\"images/icons/menue_abmelden.gif\" alt=\"Abmelden\" title=\"Aus dem ACP ausloggen\" style=\"border:0; margin-right:3px;\" align=\"left\" /> Abmelden</a></li>\n";
		else
			echo "        <li>&nbsp;</li>\n";
		if(isset($userdata['id']) && $userdata['id'] >= 1 && $userdata['profil'] == 1){
			echo "        <li><a href=\"users.php?action=profil\" accesskey=\"4\" title=\"Eigenes Profil bearbeiten\"><img src=\"images/icons/menue_profil.gif\" alt=\"Profil\" title=\"Eigenes Profil\" style=\"border:0; margin-right:3px;\" align=\"left\" /> Profil</a></li>\n";
			echo "        <li><a href=\"javascript:popup('notepad','','','',500,400);\" accesskey=\"5\" title=\"Notizblock aufrufen\" style=\"margin-right:40px;\"><img src=\"images/icons/notebook.png\" alt=\"Notizblock\" title=\"Notizblock aufrufen\" style=\"border:0;\" align=\"left\" /></a></li>\n";
			}
		if(isset($userdata['id']) && $userdata['settings'] == 1) 
			echo "        <li><a href=\"settings.php?action=settings\" accesskey=\"1\" title=\"Einstellungen ändern\"><img src=\"images/icons/menue_einstellungen.gif\" alt=\"Einstellungen\" title=\"Einstellungen\" style=\"border:0; margin-right:3px;\" align=\"left\" /> Einstellungen</a></li>\n";
		if(isset($userdata['id']) && $userdata['userverwaltung'] >= 1) 
			echo "        <li><a href=\"users.php?action=edit_users\" accesskey=\"2\" title=\"Bernutzerverwaltung\"><img src=\"images/icons/menue_benutzer.gif\" alt=\"Benutzerverwaltung\" title=\"Benutzerverwaltung\" style=\"border:0; margin-right:3px;\" align=\"left\" /> Benutzer</a></li>\n";
		if(isset($userdata['id']) && ($userdata['dateimanager'] >= 1)) 
			echo "        <li><a href=\"filemanager.php\" accesskey=\"3\" title=\"Datei- und Bildmanager\"><img src=\"images/icons/menue_dateien.gif\" alt=\"Datei- und Bildmanager\" title=\"Datei- und Bildmanager\" style=\"border:0; margin-right:3px;\" align=\"left\" />Dateien</a></li>";
		?>
	</ul>
	<form id="rightbox" action="_loader.php" method="get">
		<?PHP
		if(isset($userdata['id']) && $userdata['id'] >= 1){
		?>
		<fieldset>
		<select name="modul" size="1" class="input_select2" onchange="location.href='_loader.php?modul='+this.options[this.selectedIndex].value+''">
			<?PHP echo create_ModulDropDown(TRUE); ?>
		</select>
		<input name="input2" type="submit" class="input2" value="Modul ausw&auml;hlen" />
		</fieldset>
		<?PHP
		}
		?>
	</form>
</div>

<div id="content">
	<div id="colOne">
		<div id="logo">
			<?PHP if(isset($userdata['id']) && $userdata['id'] >= 1){ ?>
			<h1 onclick="javascript:location.href='acp.php'" title="Zur Startseite"><span style="color:#F8A000;">01</span>ACP</h1>
			<?PHP }else{ ?>
			<h1 onclick="javascript:location.href='index.php'" title="Zur Startseite"><span style="color:#F8A000;">01</span>ACP</h1>
			<?PHP } ?>
			<h2>by 01-scripts.de</h2>
		</div>
		<div class="box">
			<?PHP echo create_menue($menuecat,0); ?>
		</div>
	</div>
	<div id="mainContent">
	
<noscript><p class="meldung_error"><b>Bitte aktivieren Sie f&uuml;r den Administrationsbereich JAVASCRIPT!</b><br />
Sie k&ouml;nnen ansonsten NICHT den vollen Funktionsumfang des ACP nutzen!<br />
<a href="http://www.01-scripts.de/board/thread.php?threadid=913" target="_blank">Hier</a> finden Sie Informationen 
um JavaScript in Ihrem Browser zu aktivieren.</p></noscript>

	<div id="ajax_hiddenresponse" class="moo_hiddenelements"></div>
	<div class="ajax_box ajax_erfolg">OK</div>
	<div class="ajax_box ajax_error">Fehler</div>
	<div class="ajax_box ajax_loading">Lade...</div>
	<div class="ajax_box ajax_meldung"></div>