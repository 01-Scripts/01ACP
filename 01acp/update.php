<?PHP
/* 
	01ACP - Copyright 2008-2010 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	01ACP-Updates durchführen
	#fv.1200#
*/

$menuecat = "01acp_start";
$sitetitle = "01ACP aktualisieren";
$filename = $_SERVER['SCRIPT_NAME'];
$flag_acp = TRUE;
$flag_nofunctions = TRUE;
$flag_loginerror = FALSE;
$flag_stopupdate = FALSE;
$mootools_use = array(); 

// Dummy-Functions
function create_ModulDropDown($para) { return ""; }
function create_menue($para,$para2) { return ""; }
function load_js_and_moo($mootools_use) { return ""; }

// Config-Dateien
include("system/headinclude.php");
include("system/head.php");

$xml = simplexml_load_file("_info.xml",NULL,LIBXML_NOCDATA);
?>
<h1>ACP aktualisieren</h1>
<?PHP
// Update durchführen
if(isset($_POST['action']) && $_POST['action'] == "update" &&
   isset($_POST['update']) && !empty($_POST['update']) &&
   $xml->version > $settings['acpversion']){
    $update_ok = TRUE;
	include_once("_up_data.php");
	
	echo "<p class=\"meldung_error\"><b>Bitte löschen Sie die Datei _up_data.php von Ihrem Server!<br />
	Leeren Sie den Cache ihres Browsers und starten Sie ihn ggf. neu!</b></p>";
	echo "<p class=\"meldung_erfolg\"><a href=\"index.php\">Weiter zum Administrationsbereich &raquo;</a></p>";
	}
else{
	if($xml->version > $settings['acpversion']){
		$options = "";
		foreach($xml->updates as $updates){
			foreach($updates as $update){
				if($update->startv == $settings['acpversion'])
					$options .= "<option value=\"".$update->action."\">Version ".$update->startv." nach ".$update->zielv."</option>\n";
				}
			}
		if(empty($options) || !isset($options)){
			$options = "<option value=\"0\">Es konnte keine passende Version gefunden werden</option>";
			$flag_stopupdate = TRUE;
			}
?>
<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra">Bitte w&auml;hlen Sie das passende Update aus.</td>
    </tr>

	<tr>
		<td class="trb"><select name="update" size="1" class="input_select2"><?PHP echo $options; ?></select></td>
	</tr>
<?PHP if(!$flag_stopupdate){ ?>
    <tr>
        <td class="tra">
			<input type="hidden" name="action" value="update" />
			<input type="submit" value="Update starten &raquo;" class="input" />
		</td>
    </tr>
<?PHP } ?>
</table>
</form><br />
<?PHP
		}
	else
		echo "<p class=\"meldung_hinweis\">Sie setzen bereits die aktuellste Version des 01ACP ein.</p>";
	}

include("system/foot.php");
?>