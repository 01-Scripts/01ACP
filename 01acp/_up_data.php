<?PHP
/* Datei enthält alle für das 01ACP zuständigen Update-Anweisungen und wird in die Datei
   update.php includiert.
   Ein manueller Aufruf ist nicht möglich:
*/
if(!$update_ok) exit;

if(isset($_POST['update']) && $_POST['update'] == "1200_zu_121"){
	// Neue Einstellung anlegen:
	$sql_insert = "INSERT INTO ".$mysql_tables['settings']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES
				('01acp', 0, 1, 4, 'acp_captcha4login', 'Captcha bei ACP-Login verwenden?', '', 'Ja|Nein', '1|0', '', '0', '0', 1, 0);";
	$result = $mysqli->query($sql_insert) OR die($mysqli->error);
	
	// Versionsnummer aktualisieren
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.2.1', wert = '1.2.1' WHERE idname = 'acpversion' LIMIT 1");
	
?>
<div class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf <b>Version 1.2.1 des 01ACP</b> wurde erfolgreich beendet.<br />
	<br />
	<b>Folgende Dateien &amp; Verzeichnisse werden nicht mehr ben&ouml;tigt und k&ouml;nnen gel&ouml;scht werden:</b>
	<ul>
	<li>01acp/images/icons/f&auml;rben.txt</li>
	<li>01acp/system/tiny_mce/plugins/layer/*</li>
	<li>01acp/system/tiny_mce/plugins/nonbreaking/*</li>
	<li>01acp/system/tiny_mce/plugins/style/*</li>
	<li>01acp/system/tiny_mce/plugins/visualchars/*</li>
	<li>01acp/system/tiny_mce/plugins/xhtmlxtras/*</li>
	</ul>
</div>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1101_zu_1200"){
	// Extra Feld für Timestamp hinzufügen und mit Werten aus "name" füllen
	$mysqli->query("ALTER TABLE `".$mysql_tables['files']."` ADD `timestamp` INT( 10 ) NULL AFTER `modul`");
	$mysqli->query("ALTER TABLE `".$mysql_tables['files']."` ADD `downloads` INT( 10 ) NOT NULL DEFAULT '0'");	
	$list = $mysqli->query("SELECT id,name FROM ".$mysql_tables['files']."");
	while($row = $list->fetch_assoc()){
		$split = explode(".",$row['name'],2);
		$mysqli->query("UPDATE ".$mysql_tables['files']." SET timestamp = '".$mysqli->escape_string($split[0])."' WHERE id = '".$row['id']."' LIMIT 1");
		}
		
	// Neue Einstellungen anlegen:
	$sql_insert = "INSERT INTO ".$mysql_tables['settings']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES
				('01acp','0','3','7','comments_zensur','Zensur aktivieren?','','Ja|Nein','1|0','','0','1','0','0'),
				('01acp','0','3','8','comments_badwords','Zu zensierende W&ouml;rter','Pro Zeile ein Wort eingeben, welches zensiert werden soll.','textarea','5|50','','','','0','0'),
				('01acp','0','3','9','comments_zensurlimit','Kommentar abweisen ab','-1 weist keine Kommentare ab','text','4','erkannten W&ouml;rtern.','5','5','0','0');";
	$result = $mysqli->query($sql_insert) OR die($mysqli->error);
	
	// Neues "Recht" einfügen:
	$sql_insert = "INSERT INTO ".$mysql_tables['rights']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,nodelete,hide,in_profile) VALUES
				( '01acp', '0', '2', '3', 'notepad', 'Notizblock', '', 'textarea', '5|50', '', '', '0', '0', '1')";
	$result = $mysqli->query($sql_insert) OR die($mysqli->error);
	$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` ADD `01acp_notepad` TEXT NOT NULL default ''");
	//$mysqli->query("UPDATE `".$mysql_tables['user']."` SET `01acp_notepad` = '1'");
	
	// Diverse MySQL-Tabellenänderungen:
	$mysqli->query("ALTER TABLE `".$mysql_tables['module']."` CHANGE `serialized_data` `serialized_data` MEDIUMBLOB NULL DEFAULT NULL COMMENT 'use unserialize() to get data back'");
	$mysqli->query("ALTER TABLE `".$mysql_tables['comments']."` CHANGE `modul` `modul` VARCHAR( 25 ) NULL ,
CHANGE `postid` `postid` VARCHAR( 255 ) NOT NULL DEFAULT '0',
CHANGE `uid` `uid` VARCHAR( 32 ) NOT NULL DEFAULT '0',
CHANGE `frei` `frei` TINYINT( 1 ) NOT NULL DEFAULT '0',
CHANGE `timestamp` `timestamp` INT( 10 ) NOT NULL DEFAULT '0',
CHANGE `ip` `ip` VARCHAR( 15 ) NULL ,
CHANGE `autor` `autor` VARCHAR( 50 ) NULL ,
CHANGE `email` `email` VARCHAR( 75 ) NULL ,
CHANGE `url` `url` VARCHAR( 100 ) NULL ,
CHANGE `comment` `comment` TEXT NULL ,
CHANGE `smilies` `smilies` TINYINT( 1 ) NOT NULL DEFAULT '0',
CHANGE `bbc` `bbc` TINYINT( 1 ) NOT NULL DEFAULT '0'");
	$mysqli->query("ALTER TABLE `".$mysql_tables['filedirs']."` CHANGE `parentid` `parentid` INT( 10 ) NOT NULL DEFAULT '0',
CHANGE `timestamp` `timestamp` INT( 10 ) NOT NULL DEFAULT '0',
CHANGE `name` `name` VARCHAR( 50 ) NULL ,
CHANGE `uid` `uid` INT( 10 ) NOT NULL DEFAULT '0'");
	$mysqli->query("ALTER TABLE `".$mysql_tables['files']."` CHANGE `type` `type` VARCHAR( 4 ) NULL COMMENT 'Dateityp \"pic\" oder \"file\"',
CHANGE `modul` `modul` VARCHAR( 25 ) NULL");
	$mysqli->query("ALTER TABLE `".$mysql_tables['menue']."` CHANGE `name` `name` VARCHAR( 50 ) NULL ,
CHANGE `link` `link` VARCHAR( 100 ) NULL ,
CHANGE `modul` `modul` VARCHAR( 25 ) NULL ,
CHANGE `sicherheitslevel` `sicherheitslevel` INT( 3 ) NOT NULL DEFAULT '0',
CHANGE `rightname` `rightname` VARCHAR( 40 ) NULL ,
CHANGE `rightvalue` `rightvalue` VARCHAR( 255 ) NULL ,
CHANGE `sortorder` `sortorder` INT( 3 ) NOT NULL DEFAULT '0',
CHANGE `subof` `subof` INT( 10 ) NOT NULL DEFAULT '0'");
	$mysqli->query("ALTER TABLE `".$mysql_tables['module']."` CHANGE `nr` `nr` INT( 3 ) NOT NULL DEFAULT '0',
CHANGE `modulname` `modulname` VARCHAR( 25 ) NULL COMMENT 'Gibt die Modulart z.B. \"01news\" \"01gallery\" an.',
CHANGE `instname` `instname` VARCHAR( 50 ) NULL COMMENT 'Individueller, vom Benutzer eingegebene Name',
CHANGE `idname` `idname` VARCHAR( 25 ) NULL COMMENT 'entspricht dem Verzeichnisnamen',
CHANGE `version` `version` VARCHAR( 10 ) NULL");
	$mysqli->query("ALTER TABLE `".$mysql_tables['rights']."` CHANGE `modul` `modul` VARCHAR( 25 ) NULL ,
CHANGE `catid` `catid` TINYINT( 2 ) NOT NULL DEFAULT '0',
CHANGE `idname` `idname` VARCHAR( 40 ) NULL COMMENT 'rightname',
CHANGE `formename` `formename` TEXT NULL ,
CHANGE `formwerte` `formwerte` VARCHAR( 255 ) NULL");
	$mysqli->query("ALTER TABLE `".$mysql_tables['settings']."` CHANGE `modul` `modul` VARCHAR( 25 ) NULL ,
CHANGE `catid` `catid` TINYINT( 2 ) NOT NULL DEFAULT '0',
CHANGE `idname` `idname` VARCHAR( 25 ) NULL ,
CHANGE `formename` `formename` TEXT NULL ,
CHANGE `formwerte` `formwerte` VARCHAR( 255 ) NULL");
	$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` CHANGE `username` `username` VARCHAR( 50 ) NULL ,
CHANGE `mail` `mail` VARCHAR( 50 ) NULL ,
CHANGE `password` `password` VARCHAR( 40 ) NULL ,
CHANGE `level` `level` INT( 3 ) NOT NULL DEFAULT '0',
CHANGE `01acp_signatur` `01acp_signatur` TEXT NOT NULL DEFAULT '' COMMENT 'Manuell von varchar auf text geändert'");
	
	
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.2.0.0', wert = '1.2.0.0' WHERE idname = 'acpversion' LIMIT 1");
?>
<div class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf <b>Version 1.2.0.0 des 01ACP</b> wurde erfolgreich beendet.<br />
	<br />
	<b>Folgende Dateien &amp; Verzeichnisse werden nicht mehr ben&ouml;tigt und k&ouml;nnen gel&ouml;scht werden:</b>
	<ul>
	<li>01acp/system/fancyupload.php</li>
	<li>01acp/system/tt.php</li>
	<li>01acp/system/tiny_mce/plugins/advhr/*</li>
	<li>01acp/system/tiny_mce/plugins/pagebreak/*</li>
	<li>01acp/system/tiny_mce/plugins/fullpage/*</li>
	<li>01acp/system/tiny_mce/plugins/paste/css/*</li>
	<li>01acp/system/tiny_mce/plugins/paste/blank.htm</li>
	</ul>
</div>

<div class="meldung_ok">
	<b>Es stehen neue Funktionen zur Verf&uuml;gung:</b>
	<ul>
	<li>Nutzen Sie den neuen Notizblock um Details f&uuml;r zuk&uuml;nftige Artikel zu hinterlegen</li>
	<li>Es k&ouml;nnen nun mehrere Dateien gleichzeitig durch die Nutzung von Checkboxen gel&ouml;scht werden</li>
	<li>Multiupload f&uuml;r die Dateiverwaltung des Administrationsbereichs</li>
	<li>Zensurfunktion f&uuml;r Kommentarsystem (siehe Einstellungen)</li>
	<li>Z&auml;hlfunktion f&uuml;r Datei-Downloads</li>
	<li>Sowie viele weiteren Verbesserungen und Bugfixes...</li>
	</ul>
</div>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1100_zu_1101"){
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.1.0.1', wert = '1.1.0.1' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.1.0.1 des <b>01acp</b> wurde erfolgreich beendet.
</p>
<?PHP
	}
elseif(isset($_POST['update']) && $_POST['update'] == "1002_zu_1100"){
	$mysqli->query("ALTER TABLE `".$mysql_tables['comments']."` ADD `subpostid` VARCHAR( 255 ) NOT NULL DEFAULT '0' AFTER `postid`");
	$mysqli->query("ALTER TABLE `".$mysql_tables['module']."` ADD `serialized_data` MEDIUMTEXT NOT NULL COMMENT 'use unserialize() to get data back' AFTER `version`");
	$mysqli->query("ALTER TABLE `".$mysql_tables['files']."` ADD `dir` INT( 10 ) NOT NULL DEFAULT '0' AFTER `modul`");
	$mysqli->query("CREATE TABLE IF NOT EXISTS `".$mysql_tables['filedirs']."` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parentid` int(10) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `uid` int(10) NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `sperre` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;");

	$mysqli->query("UPDATE ".$mysql_tables['rights']." SET exp = 'Benutzer, die Zugriff auf alle Dateien &amp; Bilder haben, k&ouml;nnen auch Verzeichnisse anlegen und bearbeiten.' WHERE idname = 'dateimanager' LIMIT 1");
	$mysqli->query("UPDATE ".$mysql_tables['menue']." SET name = 'Bilder verwalten' WHERE link = 'filemanager.php?type=pic'");
	$mysqli->query("UPDATE ".$mysql_tables['menue']." SET name = 'Dateien verwalten' WHERE link = 'filemanager.php?type=file'");

	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.1.0.0', wert = '1.1.0.0' WHERE idname = 'acpversion' LIMIT 1");
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
	$mysqli->query("UPDATE `".$mysql_tables['settings']."` SET `hide` = '1' WHERE `idname` = 'thumbwidth' LIMIT 1");
	$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` ADD `startpage` VARCHAR( 25 ) NOT NULL DEFAULT '01acp' AFTER `lastlogin`");
	$mysqli->query("ALTER TABLE `".$mysql_tables['user']."` ADD `cookiehash` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `startpage`");

	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.0.0.2', wert = '1.0.0.2' WHERE idname = 'acpversion' LIMIT 1");
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
	$mysqli->query("UPDATE ".$mysql_tables['settings']." SET standardwert = '1.0.0.1', wert = '1.0.0.1' WHERE idname = 'acpversion' LIMIT 1");
?>
<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Das Update auf Version 1.0.0.1 des <b>01acp</b> wurde erfolgreich beendet.
</p>
<?PHP
	}
?>