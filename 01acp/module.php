<?PHP
/* 
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Module verwalten
	#fv.1102#
*/

$menuecat = "01acp_module";
$sitetitle = "Module verwalten";
$filename = $_SERVER['PHP_SELF'];
$flag_stopupdate = FALSE;

// Config-Dateien
include("system/main.php");
include("system/head.php");

// Sicherheitsabfrage: Login
if(isset($userdata['id']) && $userdata['id'] > 0 && $userdata['module'] == 1){

// Neue Versions-XML vom 01-Scripts.de-Server holen
if($settings['cachetime']+CACHE_TIME < time()){
	getXML2Cache(HTTP_VERSIONSINFO_DATEILINK,LOKAL_VERSIONSINFO_DATEILINK);
	}
$vinfoxml = @simplexml_load_file(LOKAL_VERSIONSINFO_DATEILINK);






// Neues Modul installieren (Step 1)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "install" && 
	isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) &&
	isset($_REQUEST['step']) && $_REQUEST['step'] == 1 &&
	file_exists($moduldir.$_REQUEST['modul']."/_info.xml")){

	$xml = simplexml_load_file($moduldir.$_REQUEST['modul']."/_info.xml",NULL,LIBXML_NOCDATA);
	
	echo "<h1>".$xml->titel." installieren</h1>";
	
	// ID-Namen auf Sonderzeichen �berpr�fen
	$err = "";
	$err2 = false;
	foreach($forbidden_chars as $char){
		if(strchr($_REQUEST['modul'],$char)) $err .= $char;
		}

	// Maximale L�nge von 25 Zeichen �berpr�fen
	if(strlen($_REQUEST['modul']) > 25) $err2 = true;

	if($xml->need01acpv <= $settings['acpversion'] && empty($err) && !$err2){
?>

<p class="meldung_hinweis">
	Der Benutzer mit dem Sie momentan angemeldet sind (<i><?PHP echo $userdata['username']; ?></i>)
	erh&auml;lt f&uuml;r das Modul alle Administrations- &amp; Benutzerrechte.<br />
	Allen anderen Benutzern m&uuml;ssen die Rechte <b>nachtr&auml;glich</b> entsprechend zugeteilt werden 
	(&uuml;ber <i>Benutzer bearbeiten</i>).
</p>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra" width="35%"><b>Installationsname*</b><br /><span class="small">Der eingegebene Name erscheint in der Dropdown-Modulauswahl (oben rechts).</span></td>
        <td class="tra"><input type="text" name="instname" value="<?PHP echo $xml->titel; ?>" size="50" maxlength="25" /></td>
    </tr>

    <tr>
        <td class="tra"><input type="reset" value="Zur&uuml;cksetzen" class="input" /></td>
        <td class="tra">
			<input type="hidden" name="action" value="install" />
			<input type="hidden" name="modul" value="<?PHP echo $_REQUEST['modul']; ?>" />
			<input type="hidden" name="step" value="2" />
			<input type="submit" value="Weiter &raquo;" class="input" />
		</td>
    </tr>

</table>
</form><br />

<?PHP
		}
	elseif(!empty($err))
		echo "<p class=\"meldung_error\">Bitte entfernen Sie alle Sonderzeichen aus dem Verzeichnisnamen des Moduls:<br />
				Aktueller Verzeichnisname: <b>".$_REQUEST['modul']."</b><br />
				Zu entfernende Zeichen: <b>".$err."</b></p>";
	elseif($err2)
		echo "<p class=\"meldung_error\">Bitte w&auml;hlen Sie einen Verzeichnisnamen mit weniger als 25 Zeichen!</p>";
	else
		echo "<p class=\"meldung_error\">Um das gew&uuml;nschte Modul zu installieren ben&ouml;tigen Sie die
				Version ".$xml->need01acpv." des 01ACP.<br />
				Bitte aktualisieren Sie zuerst das 01ACP!</p>";
	}
// Installation: Step 2
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "install" && 
	isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) &&
	isset($_REQUEST['instname']) && !empty($_REQUEST['instname']) &&
	isset($_REQUEST['step']) && $_REQUEST['step'] == 2 &&
	file_exists($moduldir.$_REQUEST['modul']."/_info.xml")){
	
	$xml = simplexml_load_file($moduldir.$_REQUEST['modul']."/_info.xml",NULL,LIBXML_NOCDATA);
	
	// N�chste Modul-Nummer aus DB holen (f�r mehrere Parallelinstallationen)
	$newnr = 0;
	$list = mysql_query("SELECT nr FROM ".$mysql_tables['module']." WHERE modulname = '".mysql_real_escape_string($xml->name)."' ORDER BY nr DESC LIMIT 1");
	while($row = mysql_fetch_array($list)){
		$newnr = $row['nr'];
		}
	$newnr = $newnr+1;
	
	// Modul in DB eintragen
	$sql_insert = "INSERT INTO ".$mysql_tables['module']." (nr,aktiv,modulname,instname,idname,version,serialized_data) VALUES (
					'".$newnr."',
					'1',
					'".mysql_real_escape_string($xml->name)."',
					'".mysql_real_escape_string($_REQUEST['instname'])."',
					'".mysql_real_escape_string($_REQUEST['modul'])."',
					'".mysql_real_escape_string($xml->version)."',
					''
					)";
	$result = mysql_query($sql_insert, $db) OR die(mysql_error());
	
	// CatID von cat_modulaccess holen:
	$list = mysql_query("SELECT catid FROM ".$mysql_tables['rights']." WHERE idname='cat_modulaccess' LIMIT 1");
	while($row = mysql_fetch_array($list)){
		$catid = $row['catid'];
		}
	
	// SortID der Datens�tze von cat_modulaccess holen:
	$sortid = 0;
	$list = mysql_query("SELECT sortid FROM ".$mysql_tables['rights']." WHERE catid='".$catid."' AND is_cat='0' ORDER BY sortid DESC LIMIT 1");
	while($row = mysql_fetch_array($list)){
		$sortid = $row['sortid'];
		}
	$sortid = $sortid+1;
	
	// Neues Benutzerrecht anlegen
	$sql_insert = "INSERT INTO ".$mysql_tables['rights']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,nodelete,hide,in_profile) VALUES (
					'01acp',
					'0',
					'".$catid."',
					'".$sortid."',
					'".mysql_real_escape_string($_REQUEST['modul'])."',
					'".mysql_real_escape_string($_REQUEST['instname'])."',
					'',
					'Zugriff erlaubt|Zugriff verboten',
					'1|0',
					'',
					'0',
					'1',
					'0',
					'0')";
	$result = mysql_query($sql_insert, $db) OR die(mysql_error());
	
	// Datenbankspalte f�r neues Recht erzeugen
	mysql_query("ALTER TABLE `".$mysql_tables['user']."` ADD `01acp_".mysql_real_escape_string($_REQUEST['modul'])."` TINYINT( 1 ) NOT NULL DEFAULT '0'");
	
	// Aktuellem Benutzer Zugriff gestatten
	mysql_query("UPDATE ".$mysql_tables['user']." SET 01acp_".mysql_real_escape_string($_REQUEST['modul'])."='1' WHERE id='".$userdata['id']."'");
?>
<h1>Installation wird fortgesetzt</h1>

<p class="meldung_hinweis"><b>Bitte legen Sie sp&auml;testens jetzt ein Backup Ihrer MySQL-Datenbank an!</b></p>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra" colspan="2">Der n&auml;chste Schritt kann einige Zeit in Anspruch nehmen (bis zu 60s).<br />
		<b>Bitte unterbrechen Sie den Vorgang nicht!</b> Es werden SQL-Befehle zur Installation des
		Moduls ausgef&uuml;hrt.</td>
    </tr>

    <tr>
        <td class="tra">&nbsp;</td>
        <td class="tra">
			<input type="hidden" name="action" value="install" />
			<input type="hidden" name="modul" value="<?PHP echo $_REQUEST['modul']; ?>" />
			<input type="hidden" name="step" value="3" />
			<input type="submit" value="Weiter &raquo;" class="input" />
		</td>
    </tr>

</table>
</form><br />
<?PHP
	}
// Installation: Step 3 (Bigdump)
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "install" && 
	isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) &&
	isset($_REQUEST['step']) && $_REQUEST['step'] == 3 &&
	file_exists($moduldir.$_REQUEST['modul']."/_info.xml") &&
	file_exists($moduldir.$_REQUEST['modul']."/_sql_install.sql")){
	
	$xml = simplexml_load_file($moduldir.$_REQUEST['modul']."/_info.xml",NULL,LIBXML_NOCDATA);
	
	// Modul-Infos aus DB holen
	$sql_modulnr = 1;
	$sql_modulidname = "";
	$list = mysql_query("SELECT nr,idname FROM ".$mysql_tables['module']." WHERE modulname = '".mysql_real_escape_string($xml->name)."' ORDER BY nr DESC LIMIT 1");
	while($row = mysql_fetch_array($list)){
		$sql_modulnr = $row['nr'];
		$sql_modulidname = $row['idname'];
		}
		
	$sql_filename 	= $moduldir.$_REQUEST['modul']."/_sql_install.sql";     // Specify the dump filename to suppress the file selection dialog
	$sql_erfolglink = "<a href=\"".$filename."?action=install&amp;modul=".$_REQUEST['modul']."&amp;step=4\">Weiter &raquo;</a>";
	include_once("system/sql_bigdump.php");
	
	}
// Installation: Step 4 (Iframe oder Ausgabe der Erfolgsmeldung)
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "install" && 
	isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) &&
	isset($_REQUEST['step']) && $_REQUEST['step'] == 4 &&
	file_exists($moduldir.$_REQUEST['modul']."/_info.xml")){
	
	// Install-Datei (wenn vorhanden) in einem IFrame anzeigen
	if(file_exists($moduldir.$_REQUEST['modul']."/_install.php"))
		include_once($moduldir.$_REQUEST['modul']."/_install.php");

		mysql_query("UPDATE ".$mysql_tables['module']." SET aktiv='1' WHERE idname='".mysql_real_escape_string($_REQUEST['modul'])."'");
		$xml = simplexml_load_file($moduldir.$_REQUEST['modul']."/_info.xml",NULL,LIBXML_NOCDATA);
		
		echo "<p class=\"meldung_ok\"><b>Das Modul wurde erfolgreich installiert!</b></p>";
		echo "<p>".$xml->includeinfo."</p>";
		
	}
	
	
	
	
	
	
	
	
	
// UPDATE: Step 1 (Passendes Update aus XML ausw�hlen)
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "update" && 
	isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) &&
	isset($_REQUEST['step']) && $_REQUEST['step'] == 1 &&
	file_exists($moduldir.$_REQUEST['modul']."/_info.xml")){
	$options = "";

	$xml = simplexml_load_file($moduldir.$_REQUEST['modul']."/_info.xml",NULL,LIBXML_NOCDATA);
	
	if($xml->need01acpv <= $settings['acpversion']){
	
		foreach($xml->updates as $updates){
			foreach($updates as $update){
				// Abfrage funktioniert aus ungekl�rter Ursache nicht ohne md5()
				if($update->startv == $module[$_REQUEST['modul']]['version'] && md5($update->zielv) == md5($xml->version))
					$options .= "<option value=\"".$update->action."\">Version ".$update->startv." nach ".$update->zielv."</option>\n";
				}
			}
		if(empty($options) || !isset($options)){
			$options = "<option value=\"0\">Es konnte keine passende Version gefunden werden</option>";
			$flag_stopupdate = TRUE;
			}
		}
	else{
		$options = "<option value=\"0\">Bitte aktualisieren Sie zuerst das 01ACP auf die Version ".$xml->need01acpv." !</option>";
		$flag_stopupdate = TRUE;
		}
?>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra">Bitte w&auml;hlen Sie das passende Update aus.<br />
			<br />
			<b>Beispiel:</b> Wenn Sie von Version 1.0.0.0 auf Version 1.0.0.3 aktualisieren m&ouml;chten,
			m&uuml;ssen <b class="red">alle dazwischenliegende Updates</b> in der richtigen Reihenfolgen
			<b class="red">einzeln(!)</b> installiert werden:<br />
			Version 1.0.0.0 -&gt; Version 1.0.0.1 (passendes Update-Paket herunterladen!)<br />
			Version 1.0.0.1 -&gt; Version 1.0.0.2 (passendes Update-Paket herunterladen!)<br />
			Version 1.0.0.2 -&gt; Version 1.0.0.3 (passendes Update-Paket herunterladen!)<br />
			<b class="red">Bitte achten Sie darauf, dass Sie mit dem ERSTEN Update-Paket (z.B. 1.0.0.0 nach 1.0.0.1) starten und wirklich alle ge&auml;nderten
			Dateien &uuml;berschreiben.<br />
			Starten Sie dann hier den ERSTEN Update-Prozess (z.B. 1.0.0.0 nach 1.0.0.1)<br />
			und nehmen Sie ERST DANN das n&auml;chste Update (z.B. 1.0.0.1 nach 1.0.0.2, etc.) vor!</b></td>
    </tr>

	<tr>
		<td class="trb"><select name="update" size="1" class="input_select2"><?PHP echo $options; ?></select></td>
	</tr>
<?PHP if(!$flag_stopupdate){ ?>
    <tr>
        <td class="tra">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="modul" value="<?PHP echo $_REQUEST['modul']; ?>" />
			<input type="hidden" name="step" value="2" />
			<input type="submit" value="Update starten &raquo;" class="input" />
		</td>
    </tr>
<?PHP } ?>
</table>
</form><br />

<?PHP	
	}
// UPDATE: Step 2 (Update-Datei mit entsprechendem $_REQUEST-Parameter f�r richtigen Einsteig aufrufen)
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "update" && 
	isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) &&
	isset($_REQUEST['update']) && !empty($_REQUEST['update']) &&
	isset($_REQUEST['step']) && $_REQUEST['step'] == 2 &&
	file_exists($moduldir.$_REQUEST['modul']."/_info.xml") &&
	file_exists($moduldir.$_REQUEST['modul']."/_updates.php")){
	
	include_once($moduldir.$_REQUEST['modul']."/_updates.php");
	
	}
	
	
	
	
	
	
	
	
	
	
// Fehlermeldungen ausgeben
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "install" ||
	isset($_REQUEST['action']) && $_REQUEST['action'] == "update"){

	echo "<p class=\"meldung_error\"><b>Fehler:</b> Es wurden nicht alle n&ouml;tigen Parameter &uuml;bergeben oder
			es konnten nicht alle n&ouml;tigen Dateien f&uuml;r den weiteren Installations/Update-Prozess gefunden werden.<br />
			Bitte &uuml;berpr&uuml;fen Sie, ob Sie alle n&ouml;tigen Dateien hochgeladen haben und starten Sie den Vorgang ggf.
			erneut.<br />
			<a href=\"module.php\">Zur&uuml;ck zur &Uuml;bersicht &raquo;</a><br />
			<br />
			Sollten auch weitern Fehler oder Probleme auftreten, wenden Sie sich bitte an den 
			<a href=\"http://board.01-scripts.de/\" target=\"_blank\">Support</a>.</p>";

	}
	
	
	
	
	
	
	
	
	
	
	
	
// Module auflisten
else{
?>

<h1>Module verwalten</h1>

<?PHP
// Module aktiv / deaktiv setzen
if(isset($_GET['aktiv']) && ($_GET['aktiv'] == 1 || $_GET['aktiv'] == 0) && $_GET['aktiv'] != "" && isset($_GET['modul']) && !empty($_GET['modul'])){
	mysql_query("UPDATE ".$mysql_tables['module']." SET aktiv='".mysql_real_escape_string($_GET['aktiv'])."' WHERE idname='".mysql_real_escape_string($_GET['modul'])."'");
	
	echo "
<script type=\"text/javascript\">
redirect('module.php')
</script>";
	}
?>

<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
		<td class="tra" width="20" align="center">&nbsp;</td>
		<td class="tra" width="35" align="center">&nbsp;</td>
        <td class="tra" width="130"><b>Modul-ID-Name</b></td>
		<td class="tra"><b>Titel</b></td>
		<td class="tra" width="100"><b>Version</b></td>
		<td class="tra" width="30">&nbsp;</td>
		<td class="tra" width="30">&nbsp;</td>
	</tr>

<?PHP
$count = 0;
$readverz = opendir($moduldir);
while($dir = readdir($readverz)){
	if($dir != "." && $dir != ".." && file_exists($moduldir.$dir."/_info.xml") && $dir != "01example")
        {
		$xml = simplexml_load_file($moduldir.$dir."/_info.xml",NULL,LIBXML_NOCDATA);
		
		$xml_modname = "_".$module[$dir]['modulname'];
		
		if(file_exists($moduldir.$dir."/".$xml->icon) && $xml->icon != "")
			$icon = "<img src=\"".$moduldir.$dir."/".$xml->icon."\" alt=\"Icon\" title=\"".$xml->titel."\" />";
		else $icon = "";
		
		if(!in_array($dir,$inst_module)){
			$install = "<a href=\"".$filename."?action=install&amp;modul=".$dir."&amp;step=1\"><img src=\"images/icons/icon_install.gif\" alt=\"Plus-Symbol\" title=\"Modul ".$xml->titel." installieren\" /></a>";
			$installed = " <b class=\"red\">*nicht installiert*</b>";
			$vcolorclass = "";
			$warning = "<img src=\"images/icons/add.gif\" alt=\"Plus-Symbol\" title=\"Modul ".$xml->titel." installieren\" />";
			$version = $xml->version;
			$titel = $xml->titel;
			$deaktiv = "";
			}
		elseif(in_array($dir,$inst_module) && $module[$dir]['version'] < $xml->version){
			$install = "<a href=\"".$filename."?action=update&amp;modul=".$dir."&amp;step=1\"><img src=\"images/icons/icon_update.gif\" alt=\"Plus-Symbol\" title=\"Modul ".$xml->titel." aktualisieren\" /></a>";
			$installed = " <b class=\"red\">*installiert - UPDATE m&ouml;glich*</b>";
			$vcolorclass = "red";
			$warning = "<img src=\"images/icons/warning.gif\" alt=\"Warnung\" title=\"Modul ".$xml->titel." aktualisieren!\" />";
			$version = $module[$dir]['version'];
			$titel = $module[$dir]['instname'];
			if($module[$dir]['aktiv'] == 1)
				$deaktiv = "<a href=\"".$filename."?aktiv=0&amp;modul=".$dir."\"><img src=\"images/icons/icon_deaktivieren.gif\" alt=\"Icon\" title=\"Modul deaktivieren (wird im ACP nicht mehr angezeigt)\" /></a>";
			else
				$deaktiv = "<a href=\"".$filename."?aktiv=1&amp;modul=".$dir."\"><img src=\"images/icons/icon_aktivieren.gif\" alt=\"Icon\" title=\"Modul aktivieren (wird im ACP wieder angezeigt)\" /></a>";
			}
		elseif(in_array($dir,$inst_module) && $module[$dir]['version'] < $vinfoxml->$xml_modname->version){
			$install = "<a href=\"".$vinfoxml->$xml_modname->updateurl."\" target=\"_blank\"><img src=\"images/icons/icon_update.gif\" alt=\"Plus-Symbol\" title=\"Modul ".$xml->titel." aktualisieren\" /></a>";
			$installed = " <a href=\"".$vinfoxml->$xml_modname->updateurl."\" target=\"_blank\"><b class=\"red\">*installiert - UPDATE vorhanden!*</b></a>";
			$vcolorclass = "red";
			$warning = "<img src=\"images/icons/warning.gif\" alt=\"Warnung\" title=\"Modul ".$xml->titel." aktualisieren!\" />";
			$version = $module[$dir]['version'];
			$titel = $module[$dir]['instname'];
			if($module[$dir]['aktiv'] == 1)
				$deaktiv = "<a href=\"".$filename."?aktiv=0&amp;modul=".$dir."\"><img src=\"images/icons/icon_deaktivieren.gif\" alt=\"Icon\" title=\"Modul deaktivieren (wird im ACP nicht mehr angezeigt)\" /></a>";
			else
				$deaktiv = "<a href=\"".$filename."?aktiv=1&amp;modul=".$dir."\"><img src=\"images/icons/icon_aktivieren.gif\" alt=\"Icon\" title=\"Modul aktivieren (wird im ACP wieder angezeigt)\" /></a>";
			}
		elseif(in_array($dir,$inst_module) && $module[$dir]['version'] >= $xml->version){
			$install = "<a href=\"javascript:hide_unhide_tr('_".$module[$dir]['idname']."');\"><img src=\"images/icons/kreis_frage.gif\" alt=\"Fragezeichen\" title=\"Informationen zum Einbinden des Moduls ein/ausblenden\" /></a>";
			$installed = " <b class=\"green\">*installiert*</b>";
			$vcolorclass = "green";
			$warning = "<img src=\"images/icons/ok.gif\" alt=\"OK-Symbol\" title=\"Alles ok\" />";
			$version = $module[$dir]['version'];
			$titel = $module[$dir]['instname'];
			if($module[$dir]['aktiv'] == 1)
				$deaktiv = "<a href=\"".$filename."?aktiv=0&amp;modul=".$dir."\"><img src=\"images/icons/icon_deaktivieren.gif\" alt=\"Icon\" title=\"Modul deaktivieren (wird im ACP nicht mehr angezeigt)\" /></a>";
			else
				$deaktiv = "<a href=\"".$filename."?aktiv=1&amp;modul=".$dir."\"><img src=\"images/icons/icon_aktivieren.gif\" alt=\"Icon\" title=\"Modul aktivieren (wird im ACP wieder angezeigt)\" /></a>";
			}


		if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
		echo "<tr>
		<td class=\"".$class."\" align=\"center\">".$warning."</td>
		<td class=\"".$class."\" align=\"center\">".$icon."</td>
		<td class=\"".$class."\">".$module[$dir]['idname']."</td>
		<td class=\"".$class."\"><b>".$titel."</b>".$installed."<br />".$xml->beschreibung."</td>
		<td class=\"".$class."\" align=\"center\"><b class=\"".$vcolorclass."\">".$version."</b></td>
		<td class=\"".$class."\" align=\"center\">".$install."</td>
		<td class=\"".$class."\" align=\"center\">".$deaktiv."</td>
</tr>";

		echo "<tr id=\"_".$module[$dir]['idname']."\" style=\"display:none;\">
		<td class=\"".$class."\" colspan=\"7\">".$xml->includeinfo."</td>
</tr>";
        }
	clearstatcache(); 
    }
?>

</table>
<br />


<?PHP
} // Ende: Module auflisten

}else $flag_loginerror = true;
include("system/foot.php");

// 01ACP Copyright 2008 by Michael Lorer - 01-Scripts.de
?>