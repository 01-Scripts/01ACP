<?PHP
/* 
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Layout / Framework für Popup-Fenster
	#fv.122#
*/

// Session starten:
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>01acp - Popup</title>

<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />

<?PHP
$filename = "popups.php";
$flag_ispopup = TRUE;

// Config-Dateien
include("system/main.php");
?>
<link rel="stylesheet" type="text/css" href="system/default.css" />
<script src="system/js/javas.js" type="text/javascript"></script>
<?PHP
// Funktionen für TinyMCE-Editor
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "tiny_uploader" || $_REQUEST['action'] == "art2gal")){
?>
	<script language="javascript" type="text/javascript" src="system/tiny_mce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="system/tiny_mce/plugins/filemanager/js/filemanager.js"></script>
<?PHP
	}
// Mootools / Fancyup für Filemanager-Popup
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "fmanageruploader"){
	$mootools_use[] = "moo_core";
	$mootools_use[] = "moo_more";
	$mootools_use[] = "moo_request";
	$mootools_use[] = "moo_fancyup_fm";
	load_js_and_moo($mootools_use);
	}
?>
</head>

<body style="padding:0 10px;">

<div class="contentbox">
<?PHP if(isset($_REQUEST['returnvalue']) && $_REQUEST['returnvalue'] != "tinymce" || !isset($_REQUEST['returnvalue'])){ ?>
	<br />
	<div style="position:absolute; right:20px; top:25px;">
	<a href="javascript: window.close();"><img src="images/icons/icon_exit.gif" alt="Standby-Icon" title="Upload-Formular ausblenden" /></a>
	</div>
<?PHP
}

// Allgemeine Berechtigung überprüfen:
if(isset($userdata['id']) && $userdata['id'] > 0){


// Modul-Popup ?
if(isset($modul) && $modul != "01acp" && !empty($modul) && file_exists($modulpath."_popup.php") && !is_dir($modulpath."_popup.php") && $userdata[$modul] == 1){

	include_once($modulpath."_popup.php");

}
else{
// Wenn kein Modul-Popup normale Datei weiter includen:

// PROFIL: PASSWORT ÄNDERN
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "change_pw"){
	if(isset($_POST['send']) && $_POST['send'] == 1 &&
	isset($_POST['new_pw1']) && !empty($_POST['new_pw1']) &&
	$_POST['new_pw1'] == $_POST['new_pw2'] &&
	strlen($_POST['new_pw1']) >= PW_LAENGE &&
	isset($_POST['old_pw']) && pwhashing($_POST['old_pw']) == $userdata['userpassword']){
		// Passwort ändern, DB aktualisieren
		$mysqli->query("UPDATE ".$mysql_tables['user']." SET userpassword='".pwhashing($_POST['new_pw1'])."', cookiehash='' WHERE id='".$userdata['id']."' LIMIT 1");
		
		echo "<p class=\"meldung_ok\">Das Passwort wurde erfolgreich ge&auml;ndert.<br />
			<a href=\"javascript:window.close();\">Fenster schlie&szlig;en</a></p>";
		}
	else{
		if(isset($_POST['send']) && $_POST['send'] == 1)
			echo "<p class=\"meldung_error\"><b>Fehler:</b> Das Passwort konnte nicht ge&auml;ndert werden.
				Bitte &uuml;berpr&uuml;fen Sie Ihre Eingaben!</p>";
?>
	<h2><img src="images/icons/icon_pw.gif" alt="Icon: Schlüssel" title="Passwort &auml;ndern" /> Passwort &auml;ndern</h2>
	
	<form action="<?PHP echo $filename; ?>" method="post">
	<p style="text-align:right;">
	
		Neues Passwort: <input type="password" name="new_pw1" size="20" /><br />
		Passwort wiederholen: <input type="password" name="new_pw2" size="20" /><br />
		<span class="small"><b>Mindestl&auml;nge: <?PHP echo PW_LAENGE; ?> Zeichen</b></span><br />
		<br />
		Aktuelles Passwort: <input type="password" name="old_pw" size="20" /><br />
		<br />
		<input type="submit" value="Passwort &auml;ndern" class="input" />
	
		<input type="hidden" name="action" value="change_pw" />
		<input type="hidden" name="send" value="1" />
	
	</p>
	</form>
<?PHP	
		}
	}// Ende: Profil: Passwort ändern
	
// PROFIL: Notizblock anzeigen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "notepad" && $userdata['profil'] == 1){
	if(isset($_POST['send']) && $_POST['send'] == 1)
	    $mysqli->query("UPDATE ".$mysql_tables['user']." SET 01acp_notepad='".$mysqli->escape_string($_POST['notepad'])."' WHERE id='".$userdata['id']."' LIMIT 1");

	$list = $mysqli->query("SELECT id,01acp_notepad FROM ".$mysql_tables['user']." WHERE id='".$mysqli->escape_string($userdata['id'])."' LIMIT 1");
	while($row = $list->fetch_assoc()){
		$notepad_text = stripslashes($row['01acp_notepad']);
		}
?>
	<h2><img src="images/icons/notebook.png" alt="Icon: Notizblock" title="Notizblock" /> Pers&ouml;nlicher Notizblock</h2>

	<form action="<?PHP echo $filename; ?>" method="post">
	<p>

		<textarea name="notepad" rows="15" cols="61" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;"><?php echo $notepad_text; ?></textarea>
		<br /><br />
		<input type="reset" value="Reset" class="input" style="margin-right: 340px;" />
		<input type="submit" value="Speichern" class="input" />

		<input type="hidden" name="action" value="notepad" />
		<input type="hidden" name="send" value="1" />
	</p>
	</form>
<?PHP	
	}// Ende: Profil: Notizblock
	
// TinyMCE-UPLOADER-POPUP
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "tiny_uploader"){
	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = $_REQUEST['var1'];
	if(!isset($_REQUEST['formname'])) $_REQUEST['formname'] = $_REQUEST['var2'];
	if(!isset($_REQUEST['formfield'])) $_REQUEST['formfield'] = $_REQUEST['var3'];
	$_REQUEST['returnvalue'] = "tinymce";
	
	$filename = $filename."?action=tiny_uploader&amp;";
	include_once("system/uploader.php");
	}
	
// Filmananger (Noframe)-Uploader-Popup
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "fmanageruploader"){
	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = $_REQUEST['var1'];
	if(!isset($_REQUEST['formname'])) $_REQUEST['formname'] = $_REQUEST['var2'];
	if(!isset($_REQUEST['formfield'])) $_REQUEST['formfield'] = $_REQUEST['var3'];
	$_REQUEST['returnvalue'] = "php";
	$_REQUEST['look'] = "upload";
	
	$filename = $filename."?action=uploader&amp;";
	include_once("system/uploader.php");
	}
	
// Uploader-Popup (normal)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "uploader"){
	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = $_REQUEST['var1'];
	if(!isset($_REQUEST['formname'])) $_REQUEST['formname'] = $_REQUEST['var2'];
	if(!isset($_REQUEST['formfield'])) $_REQUEST['formfield'] = $_REQUEST['var3'];
	$_REQUEST['returnvalue'] = "js";
	
	$filename = $filename."?action=uploader&amp;";
	include_once("system/uploader.php");
	}
	
// Uploader-Popup (alte Datei überschreiben)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "reuploader"){
	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = $_REQUEST['var1'];
	if(!isset($_REQUEST['delfileid'])) $_REQUEST['delfileid'] = $_REQUEST['var2'];
	if(!isset($_REQUEST['delfile'])) $_REQUEST['delfile'] = $_REQUEST['var3'];
	$_REQUEST['returnvalue'] = "php";

	$filename = $filename."?action=reuploader&amp;";
	include_once("system/uploader.php");
	}
	
// Filelist-Popup
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "filelist"){
	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = $_REQUEST['var1'];
	if(!isset($_REQUEST['formname'])) $_REQUEST['formname'] = $_REQUEST['var2'];
	if(!isset($_REQUEST['formfield'])) $_REQUEST['formfield'] = $_REQUEST['var3'];
	$_REQUEST['returnvalue'] = "js";
	$_REQUEST['look'] = "list";
	
	$filename = $filename."?action=filelist&amp;";
	include_once("system/uploader.php");
	}
	

// Verzeichnis löschen (Abfrage)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "dir_del1" && $userdata['dateimanager'] == 2 &&
   isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) &&
   isset($_REQUEST['var2']) && !empty($_REQUEST['var2'])){

 	$list = $mysqli->query("SELECT id FROM ".$mysql_tables['filedirs']." WHERE parentid='".$mysqli->escape_string($_REQUEST['var1'])."'");
	
	if($list->num_rows == 0)
		echo "<p class=\"meldung_frage\">M&ouml;chten Sie wirklich das Verzeichnis <i>".stripslashes($_REQUEST['var2'])."</i><br />
		<b>inklusive aller enthaltenen Dateien (!!!) komplett l&ouml;schen?</b><br /><br />
			<a href=\"".$filename."?action=dir_dodel&amp;id=".$_REQUEST['var1']."\">JA</a> |
			<a href=\"javascript:window.close();\">Nein</a>
			</p>";
	else
		echo "<p class=\"meldung_error\"><b>Verzeichnisse mit Unterverzeichnisse k&ouml;nnen aus
		Sicherheitsgr&uuml;nden nicht gel&ouml;scht werden!</b><br />
		Bitte l&ouml;sche Sie zuerst alle enthaltenen Unterverzeichnisse!<br /><br />
		<a href=\"javascript:window.close();\">Fenster schlie&szlig;en</a></p>";
	}


// Verzeichnis löschen (tun)
if(isset($_GET['action']) && $_GET['action'] == "dir_dodel" && $userdata['dateimanager'] == 2 &&
	isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){

	$error = FALSE;
	$list = $mysqli->query("SELECT id,type,name FROM ".$mysql_tables['files']." WHERE dir = '".$mysqli->escape_string($_GET['id'])."'");
	while($row = $list->fetch_assoc()){
		switch($row['type']){
		  case "pic":
		    $dir = $picuploaddir;
		  break;
		  case "file":
		    $dir = $attachmentuploaddir;
		  break;
		  }
		if(delfile($dir,$row['name'])) $error = FALSE;
		else $error = TRUE;
		}

	if(!$error){
		$mysqli->query("DELETE FROM ".$mysql_tables['filedirs']." WHERE id='".$mysqli->escape_string($_GET['id'])."' LIMIT 1");
		echo "
<script type=\"text/javascript\">
opener.document.getElementById('del_erfolgsmeldung').style.display = 'block';
opener.document.getElementById('dirid".$_GET['id']."').style.display = 'none';
self.window.close();
</script>";
		}
	else
		echo "<p class=\"meldung_error\"><b>Fehler:</b> Das Verzeichnis konnte nicht gel&ouml;scht werden.</p>";
	}
	
	
// Datei löschen (Abfrage)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "file_del1" && $userdata['dateimanager'] >= 1 && 
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) &&
	isset($_REQUEST['var2']) && ($_REQUEST['var2'] == "pic" || $_REQUEST['var2'] == "file")){
	
	$filenames = explode("-3-3-",$_REQUEST['var3']);

	if($_REQUEST['var2'] == "pic") echo "<a href=\"".$picuploaddir.$filenames[0]."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$filenames[0]."&amp;size=".ACP_TB_WIDTH200."&amp;hidegif=normal\" alt=\"Hochgeladenes Bild\" /></a>";
	
	echo "<p class=\"meldung_frage\">M&ouml;chten Sie die Datei <b>".$filenames[1]."</b> wirklich l&ouml;schen?<br /><br />
			<a href=\"".$filename."?action=file_dodel&amp;id=".$_REQUEST['var1']."\">JA</a> |
			<a href=\"javascript:window.close();\">Nein</a>
			</p>";
	}
	
	
// Datei löschen (tun)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "file_dodel" && $userdata['dateimanager'] >= 1 &&
	isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
	
	if($userdata['dateimanager'] == 1) $query = "SELECT id,type,name FROM ".$mysql_tables['files']." WHERE uid = '".$userdata['id']."' AND id='".$mysqli->escape_string($_GET['id'])."' LIMIT 1";
	elseif($userdata['dateimanager'] == 2) $query = "SELECT id,type,name FROM ".$mysql_tables['files']." WHERE id='".$mysqli->escape_string($_GET['id'])."' LIMIT 1";
	
	$list = $mysqli->query($query);	
	if($list->num_rows == 1){
		while($row = $list->fetch_assoc()){
			switch($row['type']){
			  case "pic":
			    $dir = $picuploaddir;
			  break;
			  case "file":
			    $dir = $attachmentuploaddir;
			  break;
			  }
			if(delfile($dir,$row['name']))
				echo "
<script type=\"text/javascript\">
opener.document.getElementById('del_erfolgsmeldung').style.display = 'block';
opener.document.getElementById('id".$row['id']."').style.display = 'none';
self.window.close();
</script>";
			else
				echo "<p class=\"meldung_error\"><b>Fehler:</b> Die Datei konnte nicht gel&ouml;scht werden.</p>";
			}
		}
	else
		echo "<p class=\"meldung_error\"><b>Fehler:</b> Die Datei konnte nicht gefunden werden oder
				Sie haben nicht die n&ouml;tige Berechtigung um die Datei zu l&ouml;schen!<br />
				<br />
				<a href=\"javascript:window.close();\">Fenster schlie&szlig;en</a>";
	}
	

// Kommentar anzeigen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "show_comment" && $userdata['editcomments'] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	if(isset($_REQUEST['var2']) && $_REQUEST['var2'] == "erfolg")
		echo "<p class=\"meldung_erfolg\">Kommentar wurde erfolgreich bearbeitet!
				<a href=\"javascript:window.close();\">Schlie&szlig;en</a></p>";
	
	$list = $mysqli->query("SELECT id,timestamp,ip,autor,email,url,comment,smilies,bbc FROM ".$mysql_tables['comments']." WHERE id='".$mysqli->escape_string($_REQUEST['var1'])."' LIMIT 1");
	while($row = $list->fetch_assoc()){
		echo "<p>Geschrieben von <b>".stripslashes($row['autor'])."</b> (".$row['ip'].") 
				am <b>".date("d.m.Y",$row['timestamp'])."</b>, <b>".date("H:i",$row['timestamp'])."</b> Uhr</p>";
		
		echo "<p style=\"float:right;\">
		<a href=\"".$filename."?action=edit_comment&amp;var1=".$_REQUEST['var1']."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift+Papier\" title=\"Kommentar bearbeiten\" /></a>
		<img src=\"images/layout/spacer.gif\" alt=\"spacer\" width=\"15\" />
		<a href=\"".$filename."?action=del_comment&amp;var1=".$_REQUEST['var1']."\"><img src=\"images/icons/icon_delete.gif\" alt=\"rotes Kreuz\" title=\"Kommentar l&ouml;schen\" /></a>
		</p>";
		
		echo "<p>";
		if(!empty($row['email'])) echo "E-Mail: ".stripslashes($row['email'])."<br />";
		if(!empty($row['url'])) echo "Web: <a href=\"".stripslashes($row['url'])."\" target=\"_blank\">".stripslashes($row['url'])."</a><br />";
		echo "</p>";
		
		
		
		echo "<p>".bb_code_comment(stripslashes($row['comment']),1,$row['bbc'],$row['smilies'])."</p>";
		}
	}
	
	
	
	
// Kommentar bearbeiten (Formular)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_comment" && $userdata['editcomments'] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	echo "<h2>Kommentar bearbeiten</h2>";
	
	$list = $mysqli->query("SELECT id,timestamp,ip,autor,email,url,comment,smilies,bbc FROM ".$mysql_tables['comments']." WHERE id='".$mysqli->escape_string($_REQUEST['var1'])."' LIMIT 1");
	while($row = $list->fetch_assoc()){
		if($row['bbc'] == 0) $c1 = " checked=\"checked\"";
		else $c1 = "";
		if($row['smilies'] == 0) $c2 = " checked=\"checked\"";
		else $c2 = "";
		
		echo "<form action=\"".$filename."\" method=\"post\">
	<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">
		<tr>
			<td class=\"tra\"><b>Autor*</b></td>
			<td class=\"tra\"><input type=\"text\" name=\"autor\" value=\"".stripslashes($row['autor'])."\" size=\"30\" /></td>
		</tr>

		<tr>
			<td class=\"trb\"><b>E-Mail</b></td>
			<td class=\"trb\"><input type=\"text\" name=\"email\" value=\"".stripslashes($row['email'])."\" size=\"30\" /></td>
		</tr>
		
		<tr>
			<td class=\"tra\"><b>Homepage</b></td>
			<td class=\"tra\"><input type=\"text\" name=\"url\" value=\"".stripslashes($row['url'])."\" size=\"30\" /></td>
		</tr>

		<tr>
			<td colspan=\"2\" class=\"trb\">
				<textarea name=\"comment\" rows=\"10\" cols=\"65\" style=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;\">".stripslashes($row['comment'])."</textarea><br />
				<input type=\"checkbox\" value=\"1\" name=\"bbc\"".$c1." /> BB-Code <b>de</b>aktivieren<br />
				<input type=\"checkbox\" value=\"1\" name=\"smilies\"".$c2." /> Smilies <b>de</b>aktivieren
			</td>
		</tr>
		
		<tr>
			<td class=\"tra\"><input type=\"reset\" value=\"Reset\" class=\"input\" /></td>
			<td class=\"tra\" align=\"right\">
				<input type=\"hidden\" name=\"var1\" value=\"".$row['id']."\" />
				<input type=\"hidden\" name=\"action\" value=\"save_comment\" />
				<input type=\"submit\" name=\"submit\" value=\"Speichern &raquo;\" class=\"input\" />
			</td>
		</tr>
	</table>
</form>";
		}
	}
	
	
	
// Kommentar bearbeiten (Speichern)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "save_comment" && $userdata['editcomments'] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1']) &&
	isset($_REQUEST['autor']) && !empty($_REQUEST['autor']) &&
	isset($_REQUEST['comment']) && !empty($_REQUEST['comment'])){
	
	if(isset($_REQUEST['bbc']) && $_REQUEST['bbc'] == 1) $bbc = 0;
	else $bbc = 1;
	if(isset($_REQUEST['smilies']) && $_REQUEST['smilies'] == 1) $smilies = 0;
	else $smilies = 1;
	
	if(check_mail($_REQUEST['email']) && !empty($_REQUEST['email'])) $email = $_REQUEST['email'];
	else $email = "";
	
	if(substr_count($_REQUEST['url'], "http://") < 1 && !empty($_REQUEST['url'])) $url = "http://".stripslashes($_REQUEST['url']);
	elseif(!empty($_REQUEST['url'])) $url = $_REQUEST['url'];
	else $url = "";
	
	$mysqli->query("UPDATE ".$mysql_tables['comments']." SET 
				autor 	=	'".$mysqli->escape_string(htmlentities($_REQUEST['autor'], $htmlent_flags, $htmlent_encoding_acp))."',
				email 	=	'".$mysqli->escape_string($email)."',
				url		=	'".$mysqli->escape_string($url)."',
				comment =	'".$mysqli->escape_string(htmlentities($_REQUEST['comment'], $htmlent_flags, $htmlent_encoding_acp))."',
				bbc		=	'".$bbc."',
				smilies =	'".$smilies."'
				WHERE id='".$mysqli->escape_string($_REQUEST['var1'])."' LIMIT 1");

	echo "
<script type=\"text/javascript\">
document.location.href = '".$filename."?action=show_comment&var1=".$_REQUEST['var1']."&var2=erfolg';
</script>";
	}
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "save_comment" && $userdata['editcomments'] == 1)
	echo "<p class=\"meldung_error\">Bitte f&uuml;llen Sie alle n&ouml;tigen Felder aus!</p>";


	
	
// Kommentar löschen (Abfrage)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "del_comment" && $userdata['editcomments'] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	echo "<p class=\"meldung_frage\">Möchten Sie den Kommentar wirklich löschen?<br /><br />
			<a href=\"".$filename."?action=dodel_comment&amp;var1=".$_REQUEST['var1']."\">JA</a> | <a href=\"javascript:window.close();\">Nein</a></p>";
	}
	
	
	
	
// Kommentar löschen (löschen)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "dodel_comment" && $userdata['editcomments'] == 1 &&
	isset($_REQUEST['var1']) && !empty($_REQUEST['var1']) && is_numeric($_REQUEST['var1'])){
	
	$mysqli->query("DELETE FROM ".$mysql_tables['comments']." WHERE id='".$mysqli->escape_string($_REQUEST['var1'])."' LIMIT 1");
	
	echo "
<script type=\"text/javascript\">
opener.document.getElementById('del_erfolgsmeldung').style.display = 'block';
opener.document.getElementById('id".$_REQUEST['var1']."').style.display = 'none';
self.window.close();
</script>";
	}	
	
	
	
	
	
	
	
	
// Credits-Links
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "credits"){
	echo "<h2>Credits</h2>";
	
	include("system/includes/credits.html");

	}//Ende: Credits-Links










// Feedback-Formular
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "feedback"){
?>
<h2>Feedback senden / Fehler melden</h2>

<form action="<?PHP echo $filename; ?>" method="post">
<p>
	Fehler gefunden? Idee oder Kritik? - Vielen Dank für Ihre Mithilfe:<br />
	<textarea name="message" rows="8" cols="52"></textarea><br />
	<span class="small">Es wird Ihre eingegebene Nachricht und die
		zu Ihrem Benutzer-Account geh&ouml;rende E-Mail-Adresse (<?PHP echo $userdata['mail']; ?>)
		an <b><?PHP echo FEEDBACK_MAIL; ?></b> übermittelt.
	</span><br />
	
	<input type="submit" value="Absenden" class="input" />
	<input type="hidden" name="action" value="send_feedback" />
	<input type="hidden" name="send" value="1" />

</p>
</form>	
	
	
<?PHP
	}//Ende: Feedback-Formular
	
// Feedback-Formular senden
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "send_feedback" &&
   isset($_REQUEST['message']) && !empty($_REQUEST['message'])){
	
	$header 		= "From:".$userdata['mail']."<".$userdata['mail'].">\n";
	$email_betreff 	= "Neues Feedback - ID: #".md5(time().$userdata['mail'])."";
	$emailbody 	 	= preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$_REQUEST['message']);
    if(mail(FEEDBACK_MAIL,$email_betreff,$emailbody,$header))
		echo "<p class=\"meldung_ok\"><b>Vielen Dank für Ihre Mithilfe. Das Feedback wurde erfolgreich
				an ".FEEDBACK_MAIL." verschickt!</b><br />
				<br />
				<a href=\"javascript:window.close();\">Fenster schlie&szlig;en</a></p>";
	else
		echo "<p class=\"meldung_error\">Es trat ein Fehler auf. Das Feedback konnte nicht verschickt werden.<br />
				<a href=\"javascript:history.back();\">Zur&uuml;ck</a></p>";
	}


}//Ende: 01ACP-Modul-Part



}//Berechtigung überprüfen: ENDE
else{
echo "<p class=\"meldung_error\">Fehler: Sie haben keine Berechtigung diesen Bereich zu betreten.</p>";
}
?>
</div>

</body>
</html>