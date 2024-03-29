<?PHP
/* 
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Formular & Logik f�r Dateiupload + Ausgabe innerhalb des TinyMCE-Editor
	#fv.130#
*/

if(!isset($filename)) $filename = $_SERVER['SCRIPT_NAME'];
if(!isset($_REQUEST['formname'])) $_REQUEST['formname'] = "";
if(!isset($_REQUEST['type'])) $_REQUEST['type'] = "";
if(!isset($_REQUEST['returnvalue'])) $_REQUEST['returnvalue'] = "";
if(!isset($_REQUEST['formfield'])) $_REQUEST['formfield'] = "";
if(!isset($_REQUEST['look'])) $_REQUEST['look'] = "";
if(!isset($_REQUEST['delfileid'])) $_REQUEST['delfileid'] = "";
if(!isset($_REQUEST['delfile'])) $_REQUEST['delfile'] = "";
	
// Parameter an URL anh�ngen
$filename .= "type=".$_REQUEST['type']."&amp;returnvalue=".$_REQUEST['returnvalue']."&amp;formname=".$_REQUEST['formname']."&amp;formfield=".$_REQUEST['formfield']."&amp;look=".$_REQUEST['look']."&amp;delfileid=".$_REQUEST['delfileid']."&amp;delfile=".$_REQUEST['delfile'];

$flag_showformular = TRUE;
$flag_showlist = TRUE;

// Sicherheitsabfrage: Login & Berechtigung f�r Upload
if(isset($userdata['id']) && $userdata['id'] > 0){
	
	// Upload ausf�hren
	if(isset($_POST['upload']) && $_POST['upload'] == 1 && isset($_FILES['new_datei']['name']) && $_FILES['new_datei']['name'] != "" && $userdata['upload'] == 1){
		
		if(isset($_REQUEST['delfileid']) && !empty($_REQUEST['delfileid'])){
			if($userdata['dateimanager'] == 1) $query = "SELECT id,filetype,name FROM ".$mysql_tables['files']." WHERE uid = '".$userdata['id']."' AND id='".$mysqli->escape_string($_REQUEST['delfileid'])."' LIMIT 1";
			elseif($userdata['dateimanager'] == 2) $query = "SELECT id,filetype,name FROM ".$mysql_tables['files']." WHERE id='".$mysqli->escape_string($_REQUEST['delfileid'])."' LIMIT 1";
			
			$list = $mysqli->query($query);
			if($list->num_rows == 1){
				while($row = $list->fetch_assoc()){
					$fupload = uploadfile($_FILES['new_datei']['name'],$_FILES['new_datei']['size'],$_FILES['new_datei']['tmp_name'],$_REQUEST['type'],"01acp",$row['name'],$_REQUEST['filedirid']);
					$fupload['msg'] = "Die Datei <i>".$_REQUEST['delfile']."</i> wurde erfolgreich ersetzt.";
					}
				}
			else
				echo "<p class=\"meldung_error\"><b>Fehler:</b> Die Datei konnte nicht gefunden werden oder
						Sie haben nicht die n&ouml;tige Berechtigung um die Datei zu ersetzen!<br />
						<br />
						<a href=\"javascript:window.close();\">Fenster schlie&szlig;en</a>";
			}
		else
			$fupload = uploadfile($_FILES['new_datei']['name'],$_FILES['new_datei']['size'],$_FILES['new_datei']['tmp_name'],$_REQUEST['type'],"01acp","",$_REQUEST['filedirid']);
		
		// Ausgabe der Meldungen / R�ckgabewerte
		if($fupload['success'] == 1){
			$flag_showformular = FALSE;
			
			if(isset($_REQUEST['delfileid']) && !empty($_REQUEST['delfileid']) && $flag_ispopup){
				echo "
<script type=\"text/javascript\">
opener.location.reload(true);
window.setTimeout(\"opener.document.getElementById('ers_erfolgsmeldung').style.display = 'block'\", 2500);
</script>";
				}
				
			echo "<p class=\"meldung_ok\"><b>".$fupload['msg']."</b><br />";
			
			// JavaScript - Bilder
			if($fupload['fileart'] == "pic" && ($_REQUEST['returnvalue'] == "js" && !empty($_REQUEST['formname']) && !empty($_REQUEST['formfield']) || 
				$_REQUEST['returnvalue'] == "php" || empty($_REQUEST['returnvalue']))){
				
				if($_REQUEST['returnvalue'] == "js" && !empty($_REQUEST['formname']) && !empty($_REQUEST['formfield']))
					echo "<br />Bild einf&uuml;gen:<br />
					<br />
					<input type=\"button\" value=\"Einf&uuml;gen\" class=\"input\" onclick=\"opener.document.".$_REQUEST['formname'].".".$_REQUEST['formfield'].".value='".stripslashes($fupload['name'])."'; window.close();\" />";

				echo "</p><div style=\"float:left;\">";				
				echo "<a href=\"".$picuploaddir.$fupload['name']."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$fupload['name']."&amp;size=".ACP_TB_WIDTH200."&amp;hidegif=normal\" alt=\"Hochgeladenes Bild\" /></a>";
				echo "</div>";
				
				echo "<div style=\"float:right;\">
				<a href=\"".$filename."&amp;deltype=pic&amp;del=1&amp;file=".$fupload['name']."\" style=\"color:red;\"><img src=\"images/icons/icon_delete.gif\" alt=\"Icon: L&ouml;schen\" title=\"Bild l&ouml;schen\" style=\"border:0; margin-right:8px;\" />Bild l&ouml;schen &raquo;</a><br />
				<a href=\"".$filename."\"><img src=\"images/icons/icon_upload.gif\" alt=\"Weitere Datei hochladen\" style=\"border:0; margin-right:8px;\" />Weitere Datei hochladen &raquo;</a></div>";
				}
			// JavaScript - Dateien
			elseif($fupload['fileart'] == "file" && ($_REQUEST['returnvalue'] == "js" && !empty($_REQUEST['formname']) && !empty($_REQUEST['formfield']) || 
				$_REQUEST['returnvalue'] == "php" || empty($_REQUEST['returnvalue']))){
				
				if($_REQUEST['returnvalue'] == "js" && !empty($_REQUEST['formname']) && !empty($_REQUEST['formfield']))
					echo "<br /><b>Datei einf&uuml;gen:</b><br />
					<input type=\"button\" value=\"Einf&uuml;gen\" class=\"input\" onclick=\"opener.document.".$_REQUEST['formname'].".".$_REQUEST['formfield'].".value='".stripslashes($fupload['name'])."'; window.close();\" />
					<br /><br />";
				
				echo "
				<a href=\"".$filename."&amp;deltype=file&amp;del=1&amp;file=".$fupload['name']."\" style=\"color:red;\"><img src=\"images/icons/icon_delete.gif\" alt=\"Icon: L&ouml;schen\" title=\"Datei l&ouml;schen\" style=\"border:0; margin-right:8px;\" />Datei l&ouml;schen</a><br />
				<a href=\"".$filename."\"><img src=\"images/icons/icon_upload.gif\" alt=\"Weitere Datei hochladen\" style=\"border:0; margin-right:8px;\" />Weitere Datei hochladen &raquo;</a>
				</p>";
				}
			// TinyMCE - Bilder
			elseif($fupload['fileart'] == "pic" && $_REQUEST['returnvalue'] == "tinymce" && 
				!empty($_REQUEST['formname']) && !empty($_REQUEST['formfield'])){
                $info = getimagesize($picuploaddir.$fupload['name']);
				if($info[0] >= $info[1]){ $bigside = $info[0]; }else{ $bigside = $info[1]; }
				
				if($bigside > $settings['thumbwidth']){ $c1 = ""; $c2 = " checked=\"checked\""; }
				else{ $c1 = " checked=\"checked\""; $c2 = ""; }
				
				echo "<br />Bild einf&uuml;gen:<br />
				<span class=\"small\"><input type=\"radio\" name=\"usesize\" value=\"org\"".$c1." />Original Datei (".$info[0]."x".$info[1]." px) einf&uuml;gen<br />
                <input type=\"radio\" name=\"usesize\" value=\"small\"".$c2." />Verkleinerte Version mit <input type=\"text\" name=\"maxsize\" value=\"".$settings['thumbwidth']."\" size=\"5\" />px Kantenl&auml;nge einf&uuml;gen<br />
				<input type=\"checkbox\" name=\"link\" value=\"1\" checked=\"checked\" />Link auf original Datei (mit maximale Aufl&ouml;sung) setzen</span>
				<input type=\"button\" value=\"Einf&uuml;gen\" class=\"input\" onclick=\"FileDialog.insertpic('".$picuploaddir."','".$fupload['name']."');\" />
				</p>
				
				<div style=\"float:left;\">";				
				echo "<a href=\"".$picuploaddir.$fupload['name']."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$fupload['name']."&amp;size=".ACP_TB_WIDTH200."&amp;hidegif=normal\" alt=\"Hochgeladenes Bild\" /></a>";
				echo "</div>";
				
				echo "<div style=\"float:right;\">
				<a href=\"".$filename."&amp;deltype=pic&amp;del=1&amp;file=".$fupload['name']."\" style=\"color:red;\"><img src=\"images/icons/icon_delete.gif\" alt=\"Icon: L&ouml;schen\" title=\"Bild l&ouml;schen\" style=\"border:0; margin-right:8px;\" />Bild l&ouml;schen &raquo;</a><br />
				<a href=\"".$filename."\"><img src=\"images/icons/icon_upload.gif\" alt=\"Weitere Datei hochladen\" style=\"width:16px; margin-right:8px;\" />Weitere Datei hochladen &raquo;</a></div>";
				}
			// TinyMCE - Dateien
			elseif($fupload['fileart'] == "file" && $_REQUEST['returnvalue'] == "tinymce" && 
				!empty($_REQUEST['formname']) && !empty($_REQUEST['formfield'])){
				echo "<br /><b>Datei einf&uuml;gen:</b><br />
				<input type=\"button\" value=\"Einf&uuml;gen\" class=\"input\" onclick=\"FileDialog.insertfile('".$attachmentuploaddir."','".$fupload['name']."','".$fupload['orgname']."');\" />
				<br /><br />
				
				<a href=\"".$filename."&amp;deltype=file&amp;del=1&amp;file=".$fupload['name']."\" style=\"color:red;\"><img src=\"images/icons/icon_delete.gif\" alt=\"Icon: L&ouml;schen\" title=\"Datei l&ouml;schen\" style=\"border:0; margin-right:8px;\" />Datei l&ouml;schen</a><br />
				<a href=\"".$filename."\"><img src=\"images/icons/icon_upload.gif\" alt=\"Weitere Datei hochladen\" style=\"width:16px; margin-right:8px;\" />Weitere Datei hochladen &raquo;</a></p>";
				}
				
			}
		else{
			$flag_showformular = TRUE;
			echo "<p class=\"meldung_error\">".$fupload['msg']."</p>";
			}
		}
	elseif(isset($_POST['upload']) && $_POST['upload'] == 1 && $userdata['upload'] == 1){
		$flag_showformular = TRUE;
		echo "<p class=\"meldung_error\">Sie haben keine Datei ausgew&auml;hlt!</p>";
		}
	elseif(isset($_POST['upload']) && $_POST['upload'] == 1 && $userdata['upload'] != 1){
		$flag_showformular = FALSE;
		echo "<p class=\"meldung_error\">Sie haben keine Berechtigung eine neue Datei hochzuladen.</p>";
		}






	// Abfrage: Datei l�schen
	if(isset($_GET['del']) && $_GET['del'] == 1 && isset($_GET['file']) && !empty($_GET['file']) && isset($_GET['deltype']) && !empty($_GET['deltype']) && $userdata['upload'] == 1){
		$flag_showformular = FALSE;
		
		echo "<p class=\"meldung_frage\">
		Wollen Sie die Datei <b>".$_GET['file']."</b> wirklich l&ouml;schen?
		<br /><br />
		<a href=\"".$filename."&amp;deltype=".$_GET['deltype']."&amp;del=2&amp;file=".$_GET['file']."\">Ja, Datei l&ouml;schen</a> |
		<a href=\"".$filename."\">Nein, zur&uuml;ck</a>
		</p>";
		}
	// Durchf�hrung: Datei l�schen
	elseif(isset($_GET['del']) && $_GET['del'] == 2 && isset($_GET['file']) && !empty($_GET['file']) && isset($_GET['deltype']) && !empty($_GET['deltype']) && $userdata['upload'] == 1){
		$flag_showformular = TRUE;
		
		list($fmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['files']." WHERE name='".$mysqli->escape_string($_GET['file'])."' AND uid='".$userdata['id']."'")->fetch_array(MYSQLI_NUM);
		if($fmenge == 1){
			switch($_REQUEST['deltype']){
			  case "file":
			  @delfile($attachmentuploaddir,$_GET['file']);
			  break;
			  case "pic":
			  @delfile($picuploaddir,$_GET['file']);
			  break;
			  }
			
			echo "<p class=\"meldung_ok\">Die Datei <b>".$_GET['file']."</b> wurde erfolgreich gel&ouml;scht.</p>";
			}
		else{
			echo "<p class=\"meldung_error\">Die Datei <b>".$_GET['file']."</b> konnte nicht gefunden werden oder Sie haben keine Berechtigung diese Datei zu l&ouml;schen.</p>";
			}
		}
		
if($userdata['upload'] == 1 && $flag_showformular && (isset($_REQUEST['look']) && ($_REQUEST['look'] == "upload" || $_REQUEST['look'] == "both" || empty($_REQUEST['look'])) || !isset($_REQUEST['look']))){

// Tun: Datei verschieben:
if(isset($_REQUEST['delfileid']) && !empty($_REQUEST['delfileid']) &&
   isset($_REQUEST['newdir']) && is_numeric($_REQUEST['newdir']) && $_REQUEST['newdir'] != $_REQUEST['olddir']){
	$mysqli->query("UPDATE ".$mysql_tables['files']." SET dir = '".$mysqli->escape_string($_REQUEST['newdir'])."' WHERE id = '".$mysqli->escape_string($_REQUEST['delfileid'])."' LIMIT 1");
	
	echo "
<script type=\"text/javascript\">
opener.document.getElementById('id".$_REQUEST['delfileid']."').style.display = 'none';
</script>";
	}
// Ausgabe: Datei in anderes Verzeichnis verschieben und Hinweis auf M�glichkeit der Ersetzung
if(isset($_REQUEST['delfileid']) && !empty($_REQUEST['delfileid'])){
	$list = $mysqli->query("SELECT dir FROM ".$mysql_tables['files']." WHERE id = '".$mysqli->escape_string($_REQUEST['delfileid'])."' LIMIT 1");
	$fileinfo = $list->fetch_assoc();
	
	echo "<div class=\"meldung_hinweis\"><form action=\"".$filename."\" method=\"post\">
	<b>Datei in Verzeichnis verschieben:</b> <select name=\"newdir\" size=\"1\" class=\"input_select\">
		<option value=\"0\">Kein Unterverzeichnis</option>
		".getFileVerz_Rek(0,0,-1,"echo_FileVerz_select",$fileinfo['dir'])."
	</select>
	<input type=\"hidden\" name=\"olddir\" value=\"".$fileinfo['dir']."\" />
	<input type=\"submit\" class=\"input\" value=\"Verschieben\" />
	</form></div>";
	echo "<p class=\"meldung_hinweis\">W&auml;hlen Sie eine neue Datei, aus um die Datei <b>".$_REQUEST['delfile']."</b> zu ersetzt:</p>";
	
	$flag_showlist = FALSE;
	}
?>

<?php if(!strchr($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0") && !$flag_oldfileupload &&
		 isset($_REQUEST['action']) && $_REQUEST['action'] == "fmanageruploader"){ ?>
<form action="_ajaxloader.php?SID=<?php echo htmlspecialchars(session_id(),$htmlent_flags,$htmlent_encoding_acp); ?>&amp;modul=01acp&amp;ajaxaction=fancyupload<?php if($flag_sessionbugfix){ echo "&amp;sessiondata=".urlencode(session_encode()); } ?>" method="post" enctype="multipart/form-data" id="fancy-form">

<div id="fancy-status" class="hide">
<p>
	<a href="#" id="fancy-browse">Dateien ausw&auml;hlen</a> |
	<a href="#" id="fancy-clear">Warteschlange l&ouml;schen</a> |
	<a href="#" id="fancy-upload">Dateien jetzt hochladen</a>
</p>
<p>
	<select name="filedirid" id="filedirid" size="1" class="input_select" onchange="up.setOptions({data: 'filedirid=' + $('filedirid').get('value'),})">
		<option value="0">Kein Unterverzeichnis</option>
		<?PHP echo getFileVerz_Rek(0,0,-1,"echo_FileVerz_select",0); ?>
	</select>
</p>
<div>
	<strong class="overall-title"></strong><br />
	<img src="images/fancy/bar.gif" class="progress overall-progress" />
</div>
<div>
	<strong class="current-title"></strong><br />
	<img src="images/fancy/bar.gif" class="progress current-progress" />
</div>

<div class="current-text"></div>
</div>

<ul id="fancy-list"></ul>

</form>
<?php } ?>

<!-- Fallback-L�sung! + Standard-L�sung f�r TinyMCE-Upload! -->
<div id="fancy-fallback">
<h2>Neue Datei hochladen</h2>
<form enctype="multipart/form-data" action="<?PHP echo $filename; ?>" method="post">
	<p>Bitte w&auml;hlen Sie auf Ihrer Festplatte eine Datei aus:<br />
	<input type="file" name="new_datei" class="input_text" />&nbsp;&nbsp;
	<select name="filedirid" size="1" class="input_select">
		<option value="0">Kein Unterverzeichnis</option>
		<?PHP echo getFileVerz_Rek(0,0,-1,"echo_FileVerz_select",0); ?>
	</select>&nbsp;&nbsp;
	<input type="submit" value="Hochladen" onclick="hide_unhide('loading');" class="input" /><br />
	<b class="small">(Keine Leer- oder Sonderzeichen im Dateiname!)</b></p>
	
	<div id="loading" style="display:none; float:right; margin-right:35px; text-align:center;">
		<img src="images/icons/loading.gif" alt="Lade-Animation" title="Datei wird zum Server &uuml;bertragen - bitte warten..." /><br />
		<span class="small">Datei wird &uuml;betragen...</span>
	</div>
	<p>
		<?PHP
		if(isset($_REQUEST['type']) && ($_REQUEST['type'] == "pic" || empty($_REQUEST['type'])) || !isset($_REQUEST['type'])){
		?>
		<b>Spezifikationen f�r Bilder:</b> <?PHP echo $settings['pic_end']; ?>-Datei mit max. <?PHP echo $settings['pic_size']; ?> KB<br />
		<?PHP
		}
		
		if(isset($_REQUEST['type']) && ($_REQUEST['type'] == "file" || empty($_REQUEST['type'])) || !isset($_REQUEST['type'])){
		?>
		<b>Spezifikationen f�r Dateien:</b> <?PHP echo $settings['attachment_end']; ?>-Datei mit max. <?PHP echo $settings['attachment_size']; ?> KB<br />
		<?PHP
		}
		?>
		<br />
		<input type="hidden" name="upload" value="1" />
	</p>
</form>
</div>

<?PHP
	}// Ende: $flag_showformular

// Auflistung der bereits hochgeladenen Dateien
if($flag_showlist && (isset($_REQUEST['look']) && ($_REQUEST['look'] == "list" || $_REQUEST['look'] == "both" || empty($_REQUEST['look'])) || !isset($_REQUEST['look']))){

	if(isset($_REQUEST['dir']) && !empty($_REQUEST['dir']) && is_numeric($_REQUEST['dir'])) $where = " AND dir = '".$mysqli->escape_string($_REQUEST['dir'])."' ";
	else{ $_REQUEST['dir'] = 0; $where = " AND dir = '0' "; }
	
	$sites = 0;
	if($userdata['dateimanager'] == 1) $query = "SELECT * FROM ".$mysql_tables['files']." WHERE filetype='".$mysqli->escape_string($_REQUEST['type'])."' AND uid='".$userdata['id']."'".$where." ORDER BY utimestamp DESC,orgname,id";
	elseif($userdata['dateimanager'] == 2) $query = "SELECT * FROM ".$mysql_tables['files']." WHERE filetype='".$mysqli->escape_string($_REQUEST['type'])."'".$where." ORDER BY utimestamp DESC,orgname,id";
	$query = makepages($query,$sites,"site",ACP_PER_PAGE);

    if($_REQUEST['returnvalue'] == "tinymce" && $_REQUEST['type'] == "pic"){
        echo "<h2 style=\"clear: both;\">Bild ausw&auml;hlen</h2>";
        echo "<p>Die Bildgr&ouml;&szlig;e kann nach dem Einf&uuml;gen durch einen Klick auf <img src=\"system/tiny_mce/plugins/filemanager/img/old_pics.gif\" alt=\"Bild bearbeiten\" title=\"Bild-Eigenschaften bearbeiten\" /> ge�ndert werden.</p>";
    }
    elseif($_REQUEST['returnvalue'] == "tinymce" && $_REQUEST['type'] == "file")
        echo "<h2 style=\"clear: both;\">Datei ausw&auml;hlen</h2>";
?>
<table border="0" align="center" width="95%" cellpadding="3" cellspacing="5" class="rundrahmen trab" style="margin-top: 10px;">

    <tr>
		<td style="width:70px;" align="center"><img src="images/icons/refresh.gif" alt="Refresh-Icon" title="Seite neu laden" onclick="document.location.reload(true);" /></td>
        <td><b>Dateiname</b></td>
		<td style="width:80px;" align="center"><b>Gr��e</b></td>
		<td style="width:80px;" align="center"><b>Datum</b></td>
    </tr>
	
<?PHP
echo getFilelist($query,$filename,FALSE,TRUE,TRUE,FALSE,$_REQUEST['returnvalue']);

echo "</table>\n<br />";

echo echopages($sites,"80%","site","dir=".$_REQUEST['dir'])."<br />";
	
	}// Ende: $flag_showlist

}else echo "<p class=\"meldung_error\">Fehler: Sie haben keine Berechtigung diesen Bereich zu betreten.</p>";

?>