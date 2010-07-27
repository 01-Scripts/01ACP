<?PHP
/* 
	01ACP - Copyright 2008-2010 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Datei- & Bildverwaltung (Hochgeladenen Bilder und andere Dateien verwalten sowie neue Dateien hochladen)
	#fv.1200#
*/

$menuecat = "01acp_filemanager";
$sitetitle = "Datei- &amp; Bildverwaltung";
$filename = $_SERVER['PHP_SELF'];
$filename2 = $filename."?type=".$_REQUEST['type']."&amp;search=".$_REQUEST['search']."&amp;dir=".$_REQUEST['dir'];
$mootools_use = array("moo_core","moo_more","moo_remooz","moo_request","moo_dragdrop");

// Config-Dateien
include("system/main.php");
include("system/head.php");

if(!isset($_REQUEST['uid'])) $_REQUEST['uid'] = "";

// Sicherheitsabfrage: Login & Benutzerrechte für Dateimanager
if(isset($userdata['id']) && $userdata['id'] > 0 && $userdata['dateimanager'] >= 1){

	if(!isset($_REQUEST['type'])) $_REQUEST['type'] = "";
	switch($_REQUEST['type']){
	  case "pic":
	    echo "<h1>Bildverwaltung</h1>";
	  break;
	  case "file":
	    echo "<h1>Dateiverwaltung</h1>";
	  break;
	  default:
	    echo "<h1>Datei- &amp; Bildverwaltung</h1>";
	  break;
	  }

	echo "<p>";
	// Darf neues Verzeichnis erstellen?
	if($userdata['dateimanager'] == 2){
?>
	<a href="javascript: hide_unhide('adddir');" class="actionbutton">
		<img src="images/icons/add.gif" alt="Plus-Icon" title="Neues Verzeichnis anlegen" style="border:0; margin-right:10px;" />Neues Verzeichnis anlegen
	</a>
<?PHP
		}
	
	// Berechtigung für Upload vorhanden?
	if($userdata['upload'] == 1){
?>
	<!-- <a href="javascript: hide_unhide('uploadbox');" class="actionbutton"> -->
	<a href="javascript:popup('fmanageruploader','','','',620,480);" class="actionbutton">
		<img src="images/icons/icon_upload.gif" alt="Neue Datei/Bild hochladen" style="border:0; margin-right:10px;" />Neue Datei / Bild hochladen
	</a>
	<?PHP } ?>
</p>

<?PHP
	// Berechtigung für Upload vorhanden?
	if($userdata['dateimanager'] == 2){
	
	$display = "none";
	// Verzeichnis anlegen
	if(isset($_POST['create_new_dir']) && $_POST['create_new_dir'] == 1 &&
	   isset($_POST['new_verzname']) && !empty($_POST['new_verzname'])){
		//Eintragung in Datenbank vornehmen:
		$sql_insert = "INSERT INTO ".$mysql_tables['filedirs']." (parentid,timestamp,name,uid) VALUES (
					'".mysql_real_escape_string($_POST['parentfiledirid'])."',
					'".time()."',
					'".mysql_real_escape_string($_POST['new_verzname'])."',
					'".$userdata['id']."'
					)";
		mysql_query($sql_insert) OR die(mysql_error());
		
		$display = "block";
		echo "
<script type=\"text/javascript\">
Success_standard();
</script>";
		}
	elseif(isset($_POST['create_new_dir']) && $_POST['create_new_dir'] == 1){
		$display = "block";
		echo "
<script type=\"text/javascript\">
ShowAjaxError('<b>Fehler:</b><br />Sie haben nicht alle ben&ouml;tigen Felder ausgef&uuml;llt!');
</script>";		
		}
		
	// Markierte Dateien löschen
	if(isset($_POST['delselected']) && $_POST['delselected'] == 1 &&
	   isset($_POST['delfiles'])){
		if(!is_array($_POST['delfiles']))
		    $_POST['delfiles'] = array($_POST['delfiles']);
		
		$cup = 0;
		foreach($_POST['delfiles'] as $fileid){    
			$list = mysql_query("SELECT type,name,uid FROM ".$mysql_tables['files']." WHERE id='".mysql_real_escape_string($fileid)."' LIMIT 1");
			while($row = mysql_fetch_assoc($list)){
				
				if($userdata['dateimanager'] == 2 || $userdata['dateimanager'] == 1 && $row['uid'] == $userdata['id']){
					switch($row['type']){
					  case "pic":
					    if(delfile($picuploaddir,$row['name']))
					        $cup++;
					    break;
					  case "file":
					    if(delfile($attachmentuploaddir,$row['name']))
					        $cup++;
					    break;
					  }
					}
				}
		    }
		echo "<p class=\"meldung_erfolg\">
		Es wurden ".$cup." Dateien erfolgreich gel&ouml;scht.
		</p>";
		}
	elseif(isset($_POST['delfiles']))
	    echo "<p class=\"meldung_hinweis\">
		<b>Bitte markieren Sie die <span style=\"color:#B00;\">Checkbox</span> unten, um die Dateien wirklich zu l&ouml;schen!</b>
		</p>";
?>

<div id="adddir" style="display: <?PHP echo $display; ?>;">
<form action="filemanager.php" method="post">
	<input type="text" size="34" name="new_verzname" class="input_text" />
	<select name="parentfiledirid" size="1" class="input_select">
		<option value="0">Kein Unterverzeichnis</option>
		<?PHP echo getFileVerz_Rek(0,0,-1,"echo_FileVerz_select",0); ?>
	</select>
	<input type="submit" value="Anlegen &raquo;" class="input" />
	<input type="hidden" name="create_new_dir" value="1" />
</form>
</div>

<?PHP
		}

	if(isset($_GET['sort']) && $_GET['sort'] == "desc") $sortorder = "DESC";
	else{ $sortorder = "ASC"; $_GET['sort'] = "ASC"; }
	
	if(isset($_REQUEST['search']) && !empty($_REQUEST['search']) && $_REQUEST['search'] != "Nach Dateiennamen suchen") $where = " WHERE orgname LIKE '%".mysql_real_escape_string($_REQUEST['search'])."%'";
	else{ $where = " WHERE 1 = 1"; $_GET['search'] = ""; }
	
	if(isset($_REQUEST['dir']) && !empty($_REQUEST['dir']) && is_numeric($_REQUEST['dir'])) $where .= " AND dir = '".mysql_real_escape_string($_REQUEST['dir'])."' ";
	else{ $_REQUEST['dir'] = 0; $where .= " AND dir = '0' "; }
	
	if($userdata['dateimanager'] == 1) $where .= " AND uid='".$userdata['id']."' ";
	
	if(isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) && $userdata['dateimanager'] == 2) $where .= " AND uid='".mysql_real_escape_string($_REQUEST['uid'])."' ";
	
	if(isset($_REQUEST['type']) && ($_REQUEST['type'] == "pic" || $_REQUEST['type'] == "file")) $where .= " AND type = '".mysql_real_escape_string($_REQUEST['type'])."'";
	else $_GET['type'] = "";

	if(!isset($_GET['orderby'])) $_GET['orderby'] = "";
	switch($_GET['orderby']){
	  case "downloads":
	    $orderby = "downloads+0 ".$sortorder.",orgname,id";
	  break;
	  case "filename":
	    $orderby = "orgname,id";
	  break;
	  case "size":
	    $orderby = "size+0 ".$sortorder.",orgname,id";
	  break;
	  case "date":
	    $orderby = "timestamp ".$sortorder.",orgname,id";
	  break;  
	  default:
	    $orderby = "timestamp DESC,orgname,id";
	  break;
	  }

	$sites = 0;
	$query = "SELECT * FROM ".$mysql_tables['files']."".$where." ORDER BY ".$orderby." ".$sortorder;
	$query = makepages($query,$sites,"site",ACP_PER_PAGE);

	// Fehlermeldung bei erfolgloser Suche
	if($sites == 0 && isset($_GET['search']) && !empty($_GET['search']))
		echo "<p class=\"meldung_error\">Es konnte leider kein Benutzer zu Ihrer Sucheingabe \"".$_GET['search']."\" gefunden werden!<br />
			Bitte probieren Sie es erneut.</p>";
?>
<form action="<?PHP echo $filename; ?>" method="get">
	<p><input type="text" name="search" value="Nach Dateiennamen suchen" size="30" onfocus="clearField(this);" onblur="checkField(this);" class="input_search" /> 
	<select name="type" size="1" class="input_select"><option value="">Dateien &amp; Bilder</option><option value="pic">Nur Bilder</option><option value="file">Nur Dateien</option></select>
	<input type="submit" value="Suchen &raquo;" class="input" /></p>
</form>

<p class="meldung_erfolg" id="del_erfolgsmeldung" style="display:none;">
	Die Datei wurde erfolgreich gel&ouml;scht.
</p>

<p class="meldung_erfolg" id="ers_erfolgsmeldung" style="display:none;">
	Die Datei wurde erfolgreich ersetzt!
</p>

<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
		<td class="tra" width="10" align="center"><!--Mehrfach-Löschen--></td>
		<td class="tra" width="70" align="center"><img src="images/icons/refresh.gif" alt="Refresh-Icon" title="Seite neu laden" onclick="document.location.reload(true);" /></td>
        <td class="tra"><b>Dateiname</b>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=asc&amp;orderby=filename"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=desc&amp;orderby=filename"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
			<span style="float:right;">
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=asc&amp;orderby=downloads"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=desc&amp;orderby=downloads"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
			</span>
		</td>
		<td class="tra" width="90"><b>Größe</b>			
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=asc&amp;orderby=size"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=desc&amp;orderby=size"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra" width="90"><b>Datum</b>			
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=asc&amp;orderby=date"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=desc&amp;orderby=date"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra" width="130"><b>Benutzer</b></td>
		<td class="tra" width="20">&nbsp;<!--Ersetzen--></td>
		<td class="tra" width="20" align="center"><!--Löschen--><img src="images/icons/icon_trash.gif" alt="M&uuml;lleimer" title="Datei l&ouml;schen" /></td>
    </tr>

<?PHP
echo getFilelist($query,$filename2."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."",TRUE,TRUE,TRUE,TRUE,"");
?>

    <tr>
		<td class="tra" align="center" style="border: 1px solid #B00;"><input type="checkbox" name="delselected" value="1" /></td>
		<td class="tra" colspan="7" align="left">
			<input type="submit" name="sending" value="Markierte Dateien l&ouml;schen" class="input" />
			Es erfolgt <b>keine</b> weitere Abfrage!
		</td>
	</tr>

</form>
</table>
<br />

<?PHP
echo echopages($sites,"80%","site","search=".$_GET['search']."&amp;type=".$_GET['type']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."&amp;dir=".$_REQUEST['dir']."")."<br />";

}else $flag_loginerror = true;
include("system/foot.php");

?>