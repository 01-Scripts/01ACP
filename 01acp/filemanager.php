<?PHP
/* 
	01ACP - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Datei- & Bildverwaltung (Hochgeladenen Bilder und andere Dateien verwalten sowie neue Dateien hochladen)
	#fv.1010#
*/

$menuecat = "01acp_filemanager";
$sitetitle = "Datei- &amp; Bildverwaltung";
$filename = $_SERVER['PHP_SELF'];
$filename2 = $filename."?type=".$_REQUEST['type']."&amp;search=".$_REQUEST['search']."&amp;dir=".$_REQUEST['dir'];
$mootools_use = array("moo_core","moo_more","moo_remooz","moo_request","moo_dragdrop");

// Config-Dateien
include("system/main.php");
include("system/head.php");

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
	<a href="javascript: hide_unhide('uploadbox');" class="actionbutton">
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
	// Berechtigung für Upload vorhanden?
	if($userdata['upload'] == 1){
?>

<div id="uploadbox" style="display:none;">

<div style="position:relative; left:550px; top:50px; width:50px;">
<a href="javascript: hide_unhide('uploadbox');"><img src="images/icons/icon_exit.gif" alt="Standby-Icon" title="Upload-Formular ausblenden" /></a>
</div>

<iframe src="_path.php?include=uploader&amp;returnvalue=php&amp;look=upload" width="610" height="300" name="Dateiupload" scrolling="auto" frameborder="0">
	<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:
	Sie k&ouml;nnen die eingebettete Seite &uuml;ber den folgenden Verweis
	aufrufen: <a href="javascript:popup('fmanageruploader','','','',620,480);">Dateiupload</a></p>
</iframe>
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
	  case "filename":
	    $orderby = "orgname,id";
	  break;
	  case "size":
	    $orderby = "size+0 ".$sortorder.",orgname,id";
	  break;
	  case "date":
	    $orderby = "name+0 ".$sortorder.",orgname,id";
	  break;  
	  default:
	    $orderby = "name+0 DESC,orgname,id";
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
		<td class="tra" width="70" align="center"><img src="images/icons/refresh.gif" alt="Refresh-Icon" title="Seite neu laden" onclick="document.location.reload(true);" /></td>
        <td class="tra"><b>Dateiname</b>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=asc&amp;orderby=filename"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename2."&amp;uid=".$_REQUEST['uid']; ?>&amp;sort=desc&amp;orderby=filename"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
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
		<td class="tra" width="20" align="center"><!--Löschen--><img src="images/icons/icon_trash.gif" alt="M&uuml;lleimer" title="Benutzer l&ouml;schen" /></td>
    </tr>

<?PHP
echo getFilelist($query,$filename2."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."",TRUE,TRUE,TRUE,TRUE,"");
?>

</table>
<br />

<?PHP
echo echopages($sites,"80%","site","search=".$_GET['search']."&amp;type=".$_GET['type']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."&amp;dir=".$_REQUEST['dir']."")."<br />";

}else $flag_loginerror = true;
include("system/foot.php");

// 01ACP Copyright 2008 by Michael Lorer - 01-Scripts.de
?>