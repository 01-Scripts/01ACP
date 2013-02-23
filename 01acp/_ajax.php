<?PHP
/* 
	01ACP - Copyright 2008-2010 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo: 	Bearbeitung von eingehenden Ajax-Requests
	#fv.122#
*/

// Fancy-Upload (Dateien hochladen)
if(isset($_GET['ajaxaction']) && $_GET['ajaxaction'] == "fancyupload" && $userdata['upload'] == 1){
	if(!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])){
		$error = 'Invalid Upload';
		}
	else{
		// Our processing, we get a hash value from the file
		$return['hash'] = md5_file($_FILES['Filedata']['tmp_name']);
		if(!isset($_REQUEST['filedirid'])) $_REQUEST['filedirid'] = 0;
		$fupload = uploadfile(utf8_decode($_FILES['Filedata']['name']),$_FILES['Filedata']['size'],$_FILES['Filedata']['tmp_name'],"","01acp","",$_REQUEST['filedirid']);
		}

	// Ausgabe der Meldungen / Rückgabewerte
	if($fupload['success'] == 1){
		$return = array('status' => '1',
						'name' => $_FILES['Filedata']['name']);
		}
	else{
		$return = array('status' => '0',
						'error' =>$fupload['msg']);
		}
	
	// Return
	if(isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml'){
		// header('Content-type: text/xml');
		echo "<response>";
		foreach ($return as $key => $value){
			echo "<$key><![CDATA[$value]]></$key>";
			}
		echo "</response>";
		}
	else{
		//header('Content-type: application/json');
		echo json_encode($return);
		}
	}
// Verzeichnisse umbenennen (Filemanager)
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "savefiledir" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) && 
   isset($_REQUEST['dirname']) && !empty($_REQUEST['dirname']) && 
   isset($_REQUEST['dir']) && is_numeric($_REQUEST['dir']) && $userdata['dateimanager'] == 2){
	
	// Verhindern, dass ein Verzeichnis sich selbst untergeordnet wird:
	if($_REQUEST['dir'] == $_REQUEST['id']){
	echo "
<script type=\"text/javascript\">
Stop_Loading_standard();
ShowAjaxError('<b>Fehler:</b><br />Verzeichnis kann nicht in sich selbst verschoben werden!');
hide_unhide('hide_showdir_".$_REQUEST['id']."');
hide_unhide('hide_editdir_".$_REQUEST['id']."');
</script>";
	echo "<a href=\"".$_REQUEST['url']."\">".$_REQUEST['olddirname']."</a>";
		}
	else{
		$mysqli->query("UPDATE ".$mysql_tables['filedirs']." SET parentid = '".$mysqli->escape_string($_REQUEST['dir'])."', name = '".$mysqli->escape_string(utf8_decode($_REQUEST['dirname']))."' WHERE id = '".$mysqli->escape_string($_REQUEST['id'])."' LIMIT 1");
		
		if($_REQUEST['dir'] != $_REQUEST['olddirid']) $hide = "Success_delfade('dirid".$_REQUEST['id']."');";
		else $hide = "Success_standard();";
	
	    echo "
	<script type=\"text/javascript\">
	Stop_Loading_standard();
	".$hide."
	hide_unhide('hide_showdir_".$_REQUEST['id']."');
	hide_unhide('hide_editdir_".$_REQUEST['id']."');
	</script>";
	
	    echo "<a href=\"".$_REQUEST['url']."\">".stripslashes($_REQUEST['dirname'])."</a>";
	    }
	}
// Fehlermeldung beim Verzeichnis umbenennen (nichts eingegeben)
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "savefiledir"){
	echo "
<script type=\"text/javascript\">
Stop_Loading_standard();
ShowAjaxError('<b>Fehler:</b><br />Sie haben nicht alle ben&ouml;tigen Felder ausgef&uuml;llt!');
hide_unhide('hide_showdir_".$_REQUEST['id']."');
hide_unhide('hide_editdir_".$_REQUEST['id']."');
</script>";
	echo "<a href=\"".$_REQUEST['url']."\">".$_REQUEST['olddirname']."</a>";
	}
// Datei in ein anderes Verzeichnis verschieben
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "file_changedir" &&
   isset($_REQUEST['dirid']) && !empty($_REQUEST['dirid']) &&
   isset($_REQUEST['fileid']) && !empty($_REQUEST['fileid']) && $userdata['dateimanager'] == 2){
	$dirid = substr($_REQUEST['dirid'],4);
	$fileid =substr($_REQUEST['fileid'],5);
	$mysqli->query("UPDATE ".$mysql_tables['files']." SET dir = '".$mysqli->escape_string($dirid)."' WHERE id = '".$mysqli->escape_string($fileid)."' LIMIT 1");
	
	echo "<script type=\"text/javascript\"> Success_delfade('id".$fileid."'); </script>";
	}
// Storage-Daten speichern
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "savestorage" && $flag_indivstorage){
	$errorflag = false;
	$storage = getStorageData($_REQUEST['storagemodul']);

	// Pflichtfelder überprüfen
	if(isset($_POST['pflicht']) && !empty($_POST['pflicht'])){
		$pflicht = explode(",",$_POST['pflicht']);
		foreach($pflicht as $pfeld){
			if(!isset($_POST[$pfeld]) || isset($_POST[$pfeld]) && empty($_POST[$pfeld])){
				$errorflag = true;
				break;
				}
			}
		}
	
	// Wenn alle Pflichtfelder ausgefüllt wurden:
	if(!$errorflag){
		for($x=1;$x<=STORAGE_MAX;$x++){
			if(isset($_POST['field_'.$x]))
				$storage['field_'.$x] = utf8_decode($_POST['field_'.$x]); 
			}
		$mysqli->query("UPDATE ".$mysql_tables['module']." SET serialized_data='".$mysqli->escape_string(serialize($storage))."' WHERE idname='".$mysqli->escape_string($_REQUEST['storagemodul'])."' LIMIT 1");
		
		echo "
<script type=\"text/javascript\">
Stop_Loading_standard();
Success_standard()
</script>";
		}
	else{ //Wenn nicht alle Felder ausgefüllt wurden -> Ajax-Fehlermeldung
		echo "
<script type=\"text/javascript\">
Stop_Loading_standard();
ShowAjaxError('<b>Fehler:</b><br />Sie haben nicht alle ben&ouml;tigen Felder ausgef&uuml;llt!');
</script>";
		}
	}
// Kommentare löschen
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "delcomment" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) &&
   $userdata['editcomments'] == 1){
	$mysqli->query("DELETE FROM ".$mysql_tables['comments']." WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' LIMIT 1");
	
	echo "<script type=\"text/javascript\"> Success_delfade('id".$_REQUEST['id']."'); </script>";
	}
// Kommentare freischalten
elseif(isset($_REQUEST['ajaxaction']) && $_REQUEST['ajaxaction'] == "freecomment" &&
   isset($_REQUEST['id']) && !empty($_REQUEST['id']) &&
   $userdata['editcomments'] == 1){
    $mysqli->query("UPDATE ".$mysql_tables['comments']." SET frei='1' WHERE id='".$mysqli->escape_string($_REQUEST['id'])."' LIMIT 1");
	
	echo "<script type=\"text/javascript\"> Success_CFree('cfree".$_REQUEST['id']."'); </script>";
	}
else
	echo "<script type=\"text/javascript\"> Failed_delfade(); </script>";
	
	
// 01-ACP Copyright 2008-2009 by Michael Lorer - 01-Scripts.de
?>