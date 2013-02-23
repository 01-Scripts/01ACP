<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Installationsdatei
	#fv.122#
-->
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />

<title>01acp - Installation</title>

<link rel="stylesheet" type="text/css" href="system/default.css" />
<script src="system/js/javas.js" type="text/javascript"></script>

</head>

<body>

<div id="content" style="width:800px;">

<h1>01acp - Installation</h1>

<?PHP
@date_default_timezone_set("Europe/Berlin");
// Variablen, Flags & Co.
$flag_stopinstall = FALSE;



// Installation: Step 7 (Grundeinstellungen & Admin-Account speichern/anlegen)
if(isset($_REQUEST['step']) && $_REQUEST['step'] == 7 &&
   isset($_POST['absolut_url']) && !empty($_POST['absolut_url']) &&
   isset($_POST['kontaktemail']) && !empty($_POST['kontaktemail']) &&
   isset($_POST['username']) && !empty($_POST['username']) &&
   isset($_POST['email']) && !empty($_POST['email']) &&
   isset($_POST['passwort1']) && !empty($_POST['passwort1']) &&
   isset($_POST['passwort2']) && $_POST['passwort1'] == $_POST['passwort2']){

    // Include
	$flag_acp = TRUE;
	include_once("system/headinclude.php");
	include_once("system/functions.php");
	
	if($settings['installed'] != 1){
		$mysqli->query("UPDATE ".$mysql_tables['settings']." SET wert = '".$mysqli->escape_string($_POST['absolut_url'])."' WHERE idname = 'absolut_url' LIMIT 1");
		$mysqli->query("UPDATE ".$mysql_tables['settings']." SET wert = '".$mysqli->escape_string($_POST['kontaktemail'])."' WHERE idname = 'email_absender' LIMIT 1");
		
		// Eintrag in DB vornehmen
		$sql_insert = "INSERT INTO ".$mysql_tables['user']." (username,mail,password,level,lastlogin,sperre,01acp_rights,01acp_profil,01acp_upload,01acp_dateimanager,01acp_settings,01acp_userverwaltung,01acp_signatur,01acp_addsettings,01acp_devmode,01acp_module,01acp_editcomments) VALUES (
						'".$mysqli->escape_string($_POST['username'])."',
						'".$mysqli->escape_string($_POST['email'])."',
						'".pwhashing($_POST['passwort1'])."',
						'10',
						'0',
						'0',
						'1',
						'1',
						'1',
						'2',
						'1',
						'2',
						'',
						'1',
						'0',
						'1',
						'1'
						)";
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		$mysqli->query("UPDATE ".$mysql_tables['settings']." SET wert = '1' WHERE idname = 'installed' LIMIT 1");
		}
?>
<h1>Installation beendet</h1>

<p class="meldung_ok">
	<b>Herzlichen Gl&uuml;ckwunsch!</b><br />
	Die Installation des <b>01acp</b> wurde erfolgreich beendet.<br />
	Sie können sich nun mit Ihrem Account in <a href="index.php">den Administrationsbereich einloggen</a>
	und ein erstes Modul installieren.<br />
</p>
<p class="meldung_hinweis">
	<b>Aus Sicherheitsgr&uuml;nden sollten Sie die Dateien install.php und install_sql.sql umgehend mit Ihrem
	FTP-Programm l&ouml;schen!</b>
</p>

<?PHP
	}
// Installation: Step 7 (Fehlermeldungen)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 7){
	echo "<p class=\"meldung_error\"><b>Fehler:</b> Sie haben nicht alle n&ouml;tigen Felder 
			ausgef&uuml;llt oder die beiden eingegebenen Passw&ouml;rter stimmen nicht &uuml;berein!<br />
			<a href=\"javascript:history.back();\">&laquo; Zur&uuml;ck</a></p>";
	}
// Installation: Step 6 (Grundeinstellungen & Admin-Account)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 6){
?>
<h1>Schritt 6 - Installation fertigstellen</h1>

<p class="meldung_ok"><b>Die MySQL-Tabellen wurden erfolgreich angelegt!</b></p>

<form action="install.php" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

	<tr>
		<td class="trb" colspan="2"><h2>Grundeinstellungen</h2></td>
	</tr>

    <tr>
        <td class="tra" width="60%">
			<b>Absolute URL</b><br />
			Bitte geben Sie die absolute URL (inkl. http://) zum Verzeichnis 01scripts/ ein.
			Beispiel: http://www.domainname.de/pfad/01scripts/</td>
        <td class="tra"><input type="text" name="absolut_url" size="50" /></td>
    </tr>
	
    <tr>
        <td class="trb"><b>Kontakt-E-Mail-Adresse</b></td>
        <td class="trb"><input type="text" name="kontaktemail" size="25" /></td>
    </tr>
	
	<tr>
		<td class="tra" colspan="2"><h2>Administrator-Account erstellen</h2></td>
	</tr>
	
    <tr>
        <td class="trb"><b>Benutzername</b></td>
        <td class="trb"><input type="text" name="username" size="25" /></td>
    </tr>
	
    <tr>
        <td class="tra"><b>E-Mail-Adresse</b></td>
        <td class="tra"><input type="text" name="email" size="25" /></td>
    </tr>
	
    <tr>
        <td class="trb"><b>Passwort</b></td>
        <td class="trb"><input type="password" name="passwort1" size="25" /></td>
    </tr>
	
    <tr>
        <td class="tra"><b>Passwort wiederholen</b></td>
        <td class="tra"><input type="password" name="passwort2" size="25" /></td>
    </tr>
	
    <tr>
        <td class="trb" colspan="2" align="right">
            <input type="hidden" name="step" value="7" />
            <input type="submit" value="Installation abschlie&szlig;en" class="input" />
        </td>
    </tr>	
</table>
</form>
<?PHP	
	}
// Installation: Step 5 (MySQL-Zugangsdaten überprüfen, in Datei schreiben und MySQL-Tabellen anlegen)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 5 &&
   isset($_POST['mysql_host']) && !empty($_POST['mysql_host']) &&
   isset($_POST['mysql_username']) && !empty($_POST['mysql_username']) &&
   isset($_POST['mysql_datenbank']) && !empty($_POST['mysql_datenbank']) &&
   isset($_POST['install_nummer']) && !empty($_POST['install_nummer']) &&
   isset($_POST['warnung_gelesen']) && $_POST['warnung_gelesen'] == 1){

    // Funktionen
	function create_Salt(){
	$zahl = mt_rand(1111, 9999999999999);

	$salt = md5(md5(md5($zahl)));
	$salt = md5($salt.microtime());
	$salt = md5($salt.time().$salt.microtime());
	$salt = md5(md5($salt));
	$salt = md5($salt.microtime().$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].microtime().$salt);
	$salt = md5(md5(md5(md5($salt))));

	return $salt;
	}

	function parse_SQLLines($dumpline,$modulnr,$modulidname){
	global $instnr;

	$dumpline = ereg_replace("\r\n$", "\n", $dumpline);
	$dumpline = ereg_replace("\r$", "\n", $dumpline);
	$dumpline = ereg_replace("01prefix_", "01_".$instnr."_", $dumpline);

	return $dumpline;
	}
   
    // Versuchen eine Verbindung zur Datenbank aufzubauen:
	$mysqli = new mysqli($host, $user, $passw, $database);
	if ($mysqli->connect_errno) {
	    echo "<p class=\"meldung_error\"><b>Fehler:</b> Es konnte keine Verbindung zum MySQL-Server 
				und/oder der MySQL-Datenbank aufgebaut werden. Bitte gehen Sie zur&uuml;ck und &uuml;berpr&uuml;fen
				Sie Ihre Zugangsdaten!<br />
				<a href=\"javascript:history.back();\">&laquo; Zur&uuml;ck</a></p>";
	}
	// Verbindung wurde aufgebaut -> weitere Ausführung
	else{
		//Zugangsdaten in Datei schreiben:
        $datei = "../01_config.php";
		$configfile = fopen($datei,"w");
        $writetext = "<?PHP
\$user = \"".$_POST['mysql_username']."\";
\$passw = \"".$_POST['mysql_passwort']."\";
\$host = \"".$_POST['mysql_host']."\";
\$database = \"".$_POST['mysql_datenbank']."\";
\$instnr = \"".$_POST['install_nummer']."\";
\$salt = \"".create_Salt()."\";
?>";
        $wrotez = fwrite($configfile, $writetext);
        fclose($configfile);

        @clearstatcache();
        @chmod($datei, 0777);
		
		// Weitere Ausführung
		if($wrotez > 0){
			echo "<p class=\"meldung_ok\">Die Zugangsdaten wurden erfolgreich in ".$datei." geschrieben<br />
					<br />
					MySQL-Tabellen werden nun angelegt. Bitte warten...<br />
					...</p>";
			
			$instnr = $_POST['install_nummer'];
			$sql_filename 	= "install_sql.sql";     // Specify the dump filename to suppress the file selection dialog
			$sql_erfolglink = "<a href=\"install.php?step=6\">Weiter &raquo;</a>";
			include_once("system/sql_bigdump.php");
			}
		// Fehlermeldung
		else{
			echo "<p class=\"meldung_error\"><b>Fehler:</b> Zugangsdaten konnten nicht in die Datei 
					".$datei." geschrieben werden. Bitte &uuml;berpr&uuml;fen Sie die chmod-Schreibrechte<br />
					<a href=\"javascript:history.back();\">&laquo; Zur&uuml;ck</a></p>";
			}
		}
	}
// Installation: Step 5 (Fehlermeldungen bei fehlenden Eingaben)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 5){
	echo "<p class=\"meldung_error\"><b>Fehler:</b> Sie haben nicht alle n&ouml;tigen Felder ausgef&uuml;llt!<br />
			<a href=\"javascript:history.back();\">&laquo; Zur&uuml;ck</a></p>";
	}
// Installation: Step 4 (MySQL-Zugangsdaten abfragen)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 4){
?>
<h1>Schritt 4 - MySQL-Zugangsdaten</h1>

<p class="meldung_hinweis">Wenn Sie Daten aus dem 01-Newsscript V 2.x.x.x importieren m&ouml;chten muss das
<b>01acp</b> in die gleiche Datenbank installiert werden, in dem sich auch die MySQL-Tabellen des 01-Newsscripts
befinden!</p>

<form action="install.php" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

	<tr>
		<td class="trb" colspan="2"><h2>MySQL-Zugangsdaten</h2></td>
	</tr>
	
	<tr>
		<td class="tra" colspan="2">
			Bitte geben Sie nachfolgend die MySQL-Zugangsdaten f&uuml;r die MySQL-Datenbank ein.<br />
			Die Zugangsdaten erhalten Sie normalerweise von Ihrem Provider.
		</td>
	</tr>
	

    <tr>
        <td class="trb" width="40%"><b>MySQL-Host:</b></td>
        <td class="trb"><input type="text" name="mysql_host" value="localhost" size="25" /></td>
    </tr>

    <tr>
        <td class="tra"><b>MySQL-Benutzername:</b></td>
        <td class="tra"><input type="text" name="mysql_username" value="" size="25" /></td>
    </tr>

    <tr>
        <td class="trb"><b>MySQL-Passwort:</b></td>
        <td class="trb"><input type="password" name="mysql_passwort" value="" size="25" /></td>
    </tr>

    <tr>
        <td class="tra"><b>MySQL-Datenbank:</b></td>
        <td class="tra"><input type="text" name="mysql_datenbank" value="" size="25" /></td>
    </tr>

    <tr>
        <td class="trb"><b>Installationsnummer:</b></td>
        <td class="trb"><input type="text" name="install_nummer" value="1" size="3" /></td>
    </tr>

    <tr>
        <td class="tra" colspan="2">
            Nach einem Klick auf <i>Weiter &raquo;</i> wird versucht mit den eingegebenen Zugangsdaten
			eine Verbindung zur MySQL-Datenbank herzustellen.<br />
			Wenn dies gelingt werden anschlie&szlig;end die Zugangsdaten in die Datei <i>../01_config.php</i> geschrieben.
			Anschlie&szlig;end werden die f&uuml;r das <b>01acp</b> n&ouml;tigen MySQL-Tabellen erstellt
            und mit Standardwerten gef&uuml;llt.<br />
			<b>Dieser Vorgang kann einige Zeit dauern (bis zu 60s). Bitte brechen Sie ihn nicht ab!</b><br />
			<br />
            <b style="color:red;">ACHTUNG: Bitte legen Sie ein Backup Ihrer kompletten Datenbank an um einem unbeabsichtigten Datenverlust vorzubeugen!</b>
        </td>
    </tr>

    <tr>
        <td class="trb"><b>Hinweis gelesen:</b> <input type="checkbox" name="warnung_gelesen" value="1" /></td>
        <td class="trb" align="right">
            <input type="hidden" name="step" value="5" />
            <input type="submit" value="Weiter &raquo;" class="input" />
        </td>
    </tr>	
	
</table>
</form>
<?PHP
   }
// Installation: Step 3 (Chmode-Rechte)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 3 &&
   isset($_REQUEST['lizenz']) && $_REQUEST['lizenz'] == 1){

	echo "<h1>Schritt 3 - Schreibrechte (chmod)</h1>";
	
	echo "<form action=\"install.php\" method=\"get\">
<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">

	<tr>
		<td class=\"trb\" colspan=\"2\"><h2>Schreibrechte vergeben</h2>
		Bitte vergeben Sie f&uuml;r folgende Dateien und Verzeichnisse mit Ihrem FTP-Programm die 
		<b>Schreibrechte 0777</b> (<a href=\"http://de.wikipedia.org/wiki/Chmod\" target=\"_blank\">chmod</a>).<br />
		Sollten Sie dabei Probleme haben schlagen Sie bitte im Handbuch Ihres FTP-Programms nach oder Fragen Sie unter Nennung Ihres Programms
        im <a href=\"http://board.01-scripts.de\" target=\"_blank\">Supportforum</a> nach.<br />
		<br />
		Wenn Ihr <b>Server(!)</b> unter einem Windows Betriebssystem l&auml;uft k&ouml;nnen Sie diesen Schritt &uuml;berspringen.
		</td>
	</tr>";
	
	$chmodfiles = array("cache/","../01_config.php","../01files/","../01pics/","../01pics/catpics/");
	
	foreach($chmodfiles as $datei){
		clearstatcache();  
		@chmod ($datei, 0777);
		$chmod = decoct(fileperms($datei));
		$cchmod = strchr($chmod, "777");

		if($cchmod == 777)
			echo "    <tr>
		<td class=\"tr_green\"><b>".$datei."</b></td>
		<td class=\"tr_green\"><b>Rechte 0777 OK</b></td>
	</tr>";
		else
			echo "    <tr>
		<td class=\"tr_red\"><b>".$datei."</b></td>
		<td class=\"tr_red\"><b>Bitte die Chmod-Rechte 0777 vergeben</b></td>
	</tr>";
		}
		
	echo "    <tr>
		<td class=\"tra\">
			<input type=\"button\" value=\"Seite neu laden\" class=\"input\" onclick=\"location.reload();\" />
		</td>
		<td class=\"tra\" align=\"right\">
			<input type=\"hidden\" name=\"step\" value=\"4\" />
			<input type=\"submit\" name=\"submit\" value=\"Weiter &raquo;\" class=\"input\" />
		</td>
	</tr>";
		
	echo "\n</table>
</form>";
	}
// Installation: Step 3 (Lizenzbedigungen NICHT akzeptiert)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 3){
   echo "<p class=\"meldung_error\">Fehler: Sie haben die Lizenzbedigungen nicht akzeptiert!<br />
			<a href=\"javascript:history.back();\">&laquo; Zur&uuml;ck</a></p>";
   }
// Installation: Step 2 (Lizenzbestimmungen)
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 2){
?>

<h1>Schritt 2 - Lizenzbestimmungen</h1>

<form action="install.php" method="get">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

	<tr>
		<td class="trb" colspan="2"><h2>Lizenzbestimmungen</h2></td>
	</tr>

	<tr>
		<td class="tra" colspan="2">
			<p>Das <b>01acp</b> und alle von <a href="http://www.01-scripts.de" target="_blank"><b>01-Scripts.de</b></a>
			ver&ouml;ffentlichten Module werden unter der Creative-Commons-Lizenz 
			"<i>Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland</i>" ver&ouml;ffentlicht.</p>
			
			<p align="center"><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/" target="_blank"><img src="images/layout/cc_licence.gif" alt="Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland" title="Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland" /></a></p>
			
			<p><b class="green">Es ist Ihnen gestattet:</b></p>
			<ul>
				<li>das Werk vervielf&auml;ltigen, verbreiten und &ouml;ffentlich zug&auml;nglich machen</li>
				<li>Abwandlungen bzw. Bearbeitungen des Inhaltes anfertigen</li>
			</ul>

			<p><b class="red">Zu den folgenden Bedingungen:</b></p>
			<ul>
				<li><b>Namensnennung</b>. Sie m&uuml;ssen den Namen des Autors/Rechteinhabers in der von ihm festgelegten Weise nennen.</li>
				<li><b>Keine kommerzielle Nutzung</b>. Dieses Werk darf nicht f&uuml;r kommerzielle Zwecke verwendet werden (Firmenseiten inbegriffen).</li>
				<li><b>Weitergabe unter gleichen Bedingungen</b>. Wenn Sie den lizenzierten Inhalt bearbeiten oder in anderer Weise umgestalten, ver&auml;ndern oder als Grundlage f&uuml;r einen anderen Inhalt verwenden, d&uuml;rfen Sie den neu entstandenen Inhalt nur unter Verwendung von Lizenzbedingungen weitergeben, die mit denen dieses Lizenzvertrages identisch oder vergleichbar sind.</li>
			</ul>
			<br />
			<ul>
				<li>Im Falle einer Verbreitung m&uuml;ssen Sie anderen die Lizenzbedingungen, unter welche dieses Werk f&auml;llt, mitteilen. Am Einfachsten ist es, einen Link auf <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/" target="_blank">diese Seite</a> einzubinden.</li>
				<li>Jede der vorgenannten Bedingungen kann aufgehoben werden, sofern Sie die Einwilligung des Rechteinhabers dazu erhalten.</li>
				<li>Diese Lizenz l&auml;sst die Urheberpers&ouml;nlichkeitsrechte unberührt.</li>
			</ul>
			
			<p><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/" target="_blank"><b>Rechtlich g&uuml;ltigen Lizenzvertrag aufrufen &raquo;</b></a></p>
			
			<p class="meldung_hinweis">
			F&uuml;r einen <b>kommerziellen Einsatz</b> oder den Einsatz auf Firmenseiten erwerben Sie bitte eine 
			<b><i>Lizenz zur kommerziellen Nutzung</i></b>.<br />
			Wenn Sie den sichtbaren <b>Urheberrechtshinweis entfernen</b> m&ouml;chten ist der Erwerb einer 
			<b><i>Non-Copyright-Lizenz</i></b> nötig.<br />
			Weitere Informationen zu den Lizenzen und den Lizenzpreisen entnehmen Sie bitte 
			<a href="http://www.01-scripts.de/preise.php" target="_blank">dieser Seite</a>.<br />
			Bei Fragen oder Problemen stehe ich Ihnen jederzeit gerne <a href="http://www.01-scripts.de/contact.php" target="_blank">zur Verfügung</a>.
			</p>
		</td>
	</tr>
	
	<tr>
		<td class="trb" align="right"><b class="green">Ich habe die Lizenzbestimmungen gelesen und bin damit einverstanden.</b></td>
		<td class="trb" align="center" width="10%"><input type="checkbox" name="lizenz" value="1" /></td>
	</tr>
	
	<tr>
		<td class="tra" colspan="2" align="right">
			<input type="hidden" name="step" value="3" />
			<input type="submit" name="submit" value="Installation fortsetzen &raquo;" class="input" />
		</td>
	</tr>

</table>
</form>

<?PHP
	}
else{
?>

<h1>Schritt 1 - Systemvoraussetzungen</h1>

<p class="meldung_hinweis">Diese Installationsroutine wird Sie durch den Installationsprozess für das <b>01acp</b> begleiten.<br />
Folgen Sie bitte einfach den Anweisungen auf dem Bildschirm.<br />
<br />
Halten Sie für die weitere Installations Ihr FTP-Programm sowie Ihre MySQL-Zugangsdaten bereit!</p>

<?PHP
$flag_error = FALSE;

// PHP-Version
if(PHP_VERSION >= 5.2)
	$phpv_class = "tr_green";
else{
	$phpv_class = "tr_red";
	$flag_error = TRUE;
	}
	
// SQL-Installationsdateien
if(file_exists("install_sql.sql")){
	$sql = "<b>JA</b>";
	$sql_class = "tr_green";
	}
else{
	$sql = "<b>Nein</b>, install_sql.sql fehlt";
	$sql_class = "tr_red";
	//$flag_error = TRUE;
	}
	
// SQL-Importer (bigdump)
if(file_exists("system/sql_bigdump.php")){
	$sqli = "<b>JA</b>";
	$sqli_class = "tr_green";
	}
else{
	$sqli = "<b>Nein</b>, system/sql_bigdump.php fehlt";
	$sqli_class = "tr_red";
	$flag_error = TRUE;
	}
	
// functions.php
if(file_exists("system/functions.php")){
	$fd = "<b>JA</b>";
	$fd_class = "tr_green";
	}
else{
	$fd = "<b>Nein</b>, system/functions.php fehlt";
	$fd_class = "tr_red";
	$flag_error = TRUE;
	}
	
// Config-Datei
if(file_exists("../01_config.php")){
	$config = "<b>JA</b>";
	$config_class = "tr_green";
	}
else{
	$config = "<b>Nein</b>, ../01_config.php fehlt";
	$config_class = "tr_red";
	$flag_error = TRUE;
	}
?>

<form action="install.php" method="get">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

	<tr>
		<td class="trb" colspan="2"><h2>Systemvoraussetzungen</h2></td>
	</tr>
	
	<tr>
		<td width="70%" class="<?PHP echo $phpv_class; ?>"><b>PHP-Version</b> (Version 5.2 oder höher)</td>
		<td class="<?PHP echo $phpv_class; ?>"><b><?PHP echo PHP_VERSION; ?></b></td>
	</tr>

	<tr>
		<td width="70%" class="trb"><b>MySQL-Version</b> (Version 4.1.x oder h&ouml;her n&ouml;tig; Informationen zu Version 4.0.x finden Sie <a href="http://www.01-scripts.de/board/thread.php?threadid=955" target="_blank">hier</a>)</td>
		<td class="trb"><b>n.a.</b></td>
	</tr>

	<tr>
		<td class="<?PHP echo $sql_class; ?>"><b>SQL-Installationsdatei vorhanden?</b></td>
		<td class="<?PHP echo $sql_class; ?>"><?PHP echo $sql; ?></td>
	</tr>
	
	<tr>
		<td class="<?PHP echo $sqli_class; ?>"><b>SQL-Importer vorhanden?</b></td>
		<td class="<?PHP echo $sqli_class; ?>"><?PHP echo $sqli; ?></td>
	</tr>
	
	<tr>
		<td class="<?PHP echo $fd_class; ?>"><b>Funktionsdatei vorhanden?</b></td>
		<td class="<?PHP echo $fd_class; ?>"><?PHP echo $fd; ?></td>
	</tr>
	
	<tr>
		<td class="<?PHP echo $config_class; ?>"><b>Konfigurationsdatei vorhanden?</b></td>
		<td class="<?PHP echo $config_class; ?>"><?PHP echo $config; ?></td>
	</tr>
	
<?PHP if(!$flag_error){ ?>
	<tr>
		<td class="tra" colspan="2" align="right">
			<input type="hidden" name="step" value="2" />
			<input type="submit" name="submit" value="Installation fortsetzen &raquo;" class="input" />
		</td>
	</tr>
<?PHP } ?>

	<tr>
		<td class="trb" colspan="2"><h2>Credits</h2></td>
	</tr>
	
	<tr>
		<td class="tra" colspan="2">
		<?php include("system/includes/credits.html"); ?>
		</td>
	</tr>	

</table>
</form>

<?PHP
	}
?>

</div>

<div id="footer" style="width:800px;">
	<p>&copy; 2006-<?PHP echo date("Y"); ?> by <a href="http://www.01-scripts.de" target="_blank">01-Scripts.de</a></p>
</div>

</body>
</html>