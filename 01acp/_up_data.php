<?PHP
/* Datei enthält alle für das 01ACP zuständigen Update-Anweisungen und wird in die Datei
   update.php includiert.
   Ein manueller Aufruf ist nicht möglich:
*/
if(!$update_ok) exit;

if(isset($_POST['update']) && $_POST['update'] == "1101_zu_1200"){
	
	// Extra Feld für Timestamp hinzufügen und mit Werten aus "name" füllen
	mysql_query("ALTER TABLE `".$mysql_tables['files']."` ADD `timestamp` INT( 10 ) NULL AFTER `modul`");	
	$list = mysql_query("SELECT id,name FROM ".$mysql_tables['files']."");
	while($row = mysql_fetch_array($list)){
		$split = explode(".",$row['name'],2);
		mysql_query("UPDATE ".$mysql_tables['files']." SET timestamp = '".mysql_real_escape_string($split[0])."' WHERE id = '".$row['id']."' LIMIT 1");
		}
	
	mysql_query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.2.0.0', wert = '1.2.0.0' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.2.0.0 des <b>01acp</b> wurde erfolgreich beendet.
</p>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1100_zu_1101"){
	mysql_query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.1.0.1', wert = '1.1.0.1' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.1.0.1 des <b>01acp</b> wurde erfolgreich beendet.
</p>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1002_zu_1100"){
	mysql_query("ALTER TABLE `".$mysql_tables['comments']."` ADD `subpostid` VARCHAR( 255 ) NOT NULL DEFAULT '0' AFTER `postid`");
	mysql_query("ALTER TABLE `".$mysql_tables['module']."` ADD `serialized_data` MEDIUMTEXT NOT NULL COMMENT 'use unserialize() to get data back' AFTER `version`");
	mysql_query("ALTER TABLE `".$mysql_tables['files']."` ADD `dir` INT( 10 ) NOT NULL DEFAULT '0' AFTER `modul`");
	mysql_query("CREATE TABLE IF NOT EXISTS `".$mysql_tables['filedirs']."` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parentid` int(10) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `uid` int(10) NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `sperre` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;");

	mysql_query("UPDATE ".$mysql_tables['rights']." SET exp = 'Benutzer, die Zugriff auf alle Dateien &amp; Bilder haben, k&ouml;nnen auch Verzeichnisse anlegen und bearbeiten.' WHERE idname = 'dateimanager' LIMIT 1");
	mysql_query("UPDATE ".$mysql_tables['menue']." SET name = 'Bilder verwalten' WHERE link = 'filemanager.php?type=pic'");
	mysql_query("UPDATE ".$mysql_tables['menue']." SET name = 'Dateien verwalten' WHERE link = 'filemanager.php?type=file'");

	mysql_query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.1.0.0', wert = '1.1.0.0' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.1.0.0 des <b>01acp</b> wurde erfolgreich beendet.<br />
	<br />
	<b>Folgende Verzeichnisse werden nicht mehr ben&ouml;tigt und k&ouml;nnen gel&ouml;scht werden:</b><br />
	-01acp/system/tiny_mce/plugins/advhr/<br />
	-01acp/system/tiny_mce/plugins/autosave/<br />
	-01acp/system/tiny_mce/plugins/compat2x/<br />
	-01acp/system/tiny_mce/plugins/contextmenue/<br />
	-01acp/system/tiny_mce/plugins/direcionality/<br />
	-01acp/system/tiny_mce/plugins/example/<br />
	-01acp/system/tiny_mce/plugins/fullpage/<br />
	-01acp/system/tiny_mce/plugins/fullscreen/<br />
	-01acp/system/tiny_mce/plugins/iespell/<br />
	-01acp/system/tiny_mce/plugins/insertdatetime/<br />
	-01acp/system/tiny_mce/plugins/print/<br />
	-01acp/system/tiny_mce/plugins/save/<br />
	-01acp/system/tiny_mce/plugins/spellchecker/<br />
	-01acp/system/tiny_mce/plugins/tabfocus/
</p>

<p class="meldung_ok">
	Folgende neue Funktionen stehen Ihnen mit dem Update zur Verf&uuml;gung:<br />
	- Verwalten und organisieren Sie ab sofort Ihre Dateien und Bilder in <b>Verzeichnissen</b>
</p>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1001_zu_1002"){
	mysql_query("UPDATE `".$mysql_tables['settings']."` SET `hide` = '1' WHERE `idname` = 'thumbwidth' LIMIT 1");
	mysql_query("ALTER TABLE `".$mysql_tables['user']."` ADD `startpage` VARCHAR( 25 ) NOT NULL DEFAULT '01acp' AFTER `lastlogin`");
	mysql_query("ALTER TABLE `".$mysql_tables['user']."` ADD `cookiehash` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `startpage`");

	mysql_query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.0.0.2', wert = '1.0.0.2' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.0.0.2 des <b>01acp</b> wurde erfolgreich beendet.<br />
	<br />
	<b>Folgende Dateien werden nicht mehr ben&ouml;tigt und k&ouml;nnen gel&ouml;scht werden:</b><br />
	-01scripts/01acp/system/javas.js
</p>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1000_zu_1001"){
	mysql_query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.0.0.1', wert = '1.0.0.1' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.0.0.1 des <b>01acp</b> wurde erfolgreich beendet.
</p>
<?PHP
	}
?>