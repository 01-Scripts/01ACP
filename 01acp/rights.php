<?PHP
/* 
	01ACP - Copyright 2008-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Benutzerrechte verwalten (hinzuf�gen & sortieren)
	#fv.130#
*/

$menuecat = "01acp_users";
$sitetitle = "Benutzerverwaltung : Rechteverwaltung";
$filename = $_SERVER['SCRIPT_NAME'];

// Config-Dateien
include("system/main.php");
include("system/head.php");

// Sicherheitsabfrage: Login
if(isset($userdata['id']) && $userdata['id'] > 0){

// Sicherheitsabfrage: Userberechtigungen (Einstellungen erstellen / sortieren)
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "add_right" && $userdata['rights'] == 1 && $userdata['level'] == 10){
	
	// Eintragung in Datenbank vornehmen
	if(isset($_POST['send']) && $_POST['send'] == 1){
		$sql_insert = "INSERT INTO ".$mysql_tables['rights']." (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,nodelete,hide,in_profile) VALUES (
						'".$mysqli->escape_string($_POST['modulname'])."',
						'0',
						'".$mysqli->escape_string($_POST['catid'])."',
						'".$mysqli->escape_string($_POST['sortid'])."',
						'".$mysqli->escape_string($_POST['idname'])."',
						'".$mysqli->escape_string($_POST['name'])."',
						'".$mysqli->escape_string($_POST['exp'])."',
						'".$mysqli->escape_string($_POST['formename'])."',
						'".$mysqli->escape_string($_POST['formwerte'])."',
						'".$mysqli->escape_string($_POST['input_exp'])."',
						'".$mysqli->escape_string($_POST['standardwert'])."',
						'".$mysqli->escape_string($_POST['nodelete'])."',
						'".$mysqli->escape_string($_POST['hide'])."',
						'".$mysqli->escape_string($_POST['in_profile'])."')";
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		
		//Datenbankspalte erzeugen
		if($_POST['formwerte'] == "text")
			$query = "ALTER TABLE `".$mysql_tables['user']."` ADD `".$mysqli->escape_string($_POST['modulname'])."_".$mysqli->escape_string($_POST['idname'])."` TEXT NOT NULL DEFAULT '".$mysqli->escape_string($_POST['standardwert'])."'";
		else
			$query = "ALTER TABLE `".$mysql_tables['user']."` ADD `".$mysqli->escape_string($_POST['modulname'])."_".$mysqli->escape_string($_POST['idname'])."` VARCHAR( 255 ) NOT NULL DEFAULT '".$mysqli->escape_string($_POST['standardwert'])."'";
			
		$mysqli->query($query);
		

		if($userdata['devmode'] == 1){
			echo htmlentities($sql_insert)."<br />";
			echo htmlentities($query)."<br /><br />";
			}
		echo "<p class=\"meldung_ok\"><b>Datensatz wurde hinzugef�gt</b></p>";
		}
?>

<h1>Benutzerrecht hinzuf&uuml;gen</h1>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td colspan="2">Auf dieser Seite k&ouml;nnen neue Benutzerrechte hinzugef&uuml;gt werden, die dann zur Userverwaltung im Administrationsbereich verwendet werden k&ouml;nnen.<br />
        Dieser Bereich ist normalerweise nur f�r Entwickler von Modifikationen f�r das 01ACP interessant. Ein Funktion zur Bearbeitung von Datens&auml;tzen
        ist nicht vorhanden.<br />
		F�r jedes neue Benutzerrecht wird sowohl ein neuer Eintrag in der Tabelle <i><?PHP echo $mysql_tables['rights']; ?></i>
		angelegt, als auch in der Tabelle <i><?PHP echo $mysql_tables['user']; ?></i> f�r jedes "Recht" eine Spalte angelegt, die mit dem 
		eingetragenen Standardwert gef�llt wird.<br />
        Fragen werden gerne im <a href="http://forum.01-scripts.de/" target="_blank">01-Supportforum</a> gerne beantwortet.</td>
    </tr>

    <tr>
        <td class="trb" width="50%"><b>Kategorie*:</b><br /><span class="small">catid(tinyint,2)</span></td>
        <td class="trb" width="50%">
			<select name="catid" size="1">
			<?PHP
			$cats_rights = getSettingCats("",$mysql_tables['rights']);
			
			$x = 0;
			foreach($cats_rights as $catvalue){
				echo "<option value=\"".$catvalue['catid']."\">".$catvalue['modul']." &raquo; ".$catvalue['name']." (ID: ".$catvalue['catid'].")</option>\n";
				$x++;
				}
			?>
			</select>
		</td>
    </tr>
	
    <tr>
        <td class="tra"><b>Modul*:</b><br /><span class="small">modul(varchar,25)</span></td>
        <td class="tra"><input type="text" name="modulname" value="<?PHP if(isset($_POST['modulname'])) echo $_POST['modulname']; ?>" size="25" maxlength="25" /></td>
    </tr>

    <tr>
        <td class="trb"><b>SortID:</b><br /><span class="small">sortid(int,5): Sortier-Reihenfolge innerhalb der Kategorien (catid)</span></td>
        <td class="trb"><input type="text" name="sortid" value="<?PHP if(isset($_POST['sortid'])) echo $_POST['sortid']+1; ?>" size="2" maxlength="5" /></td>
    </tr>

    <tr>
        <td class="tra"><b>Idname (unique)*:</b><br /><span class="small">idname(varchar,25): Eindeutiger Name um den Datensatz anzusprechen</span></td>
        <td class="tra"><input type="text" name="idname" value="" size="25" maxlength="25" /></td>
    </tr>

    <tr>
        <td class="trb"><b>Name:</b><br /><span class="small">name(varchar,50): Name des neuen "Rechts"</span></td>
        <td class="trb"><input type="text" name="name" value="" size="50" maxlength="50" /></td>
    </tr>

    <tr>
        <td class="tra"><b>Beschreibung:</b><br /><span class="small">exp(text): Beschreibung zum Datensatz f�r die Benutzerverwaltung</span></td>
        <td class="tra"><textarea name="exp" rows="5" cols="60" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;"></textarea></td>
    </tr>

    <tr>
        <td class="trb"><b>Formular-Type*:</b><br /><span class="small">formename(text):<br /><b>1:</b> text = einfaches Textfeld<br /><b>2:</b> textarea = Textarea<br /><b>3:</b> wahl1|wahl2 = Radiobutton Wahl1 X Wahl2 X<br /><b>4:</b> wahl1|wahl2|wahl3|... = Auswahlliste<br /><b>5:</b> function = Funktionsaufruf von <i>modul_right_Idname()</i></span></td>
        <td class="trb"><textarea name="formename" rows="5" cols="60" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;"></textarea></td>
    </tr>

    <tr>
        <td class="tra"><b>Formwerte*:</b><br /><span class="small">formwerte(varchar,255):<br /><b>1:</b> 123 = size<br /><b>2:</b> 5|50 = rows|cols<br /><b>3:</b> Wert1|Wert2 = Zu speichernder Wert<br /><b>4:</b> Wert1|Wert2|Wert3|... = zu speichernde Werte<br /><b>5:</b> text|varchar = Feldtyp f&uuml;r die Rights-Tabelle</span></td>
        <td class="tra"><input type="text" name="formwerte" value="" size="50" maxlength="255" /></td>
    </tr>

    <tr>
        <td class="trb"><b>Feld-Beschreibung:</b><br /><span class="small">input_exp(varchar,50): Beschreibung nach dem Input-Feld</span></td>
        <td class="trb"><input type="text" name="input_exp" value="" size="50" maxlength="50" /></td>
    </tr>

    <tr>
        <td class="tra"><b>Standardwert:</b><br /><span class="small">standardwert(text): Standardwert<br />(bei 3 und 4: -> =selected)</span></td>
        <td class="tra"><textarea name="standardwert" rows="5" cols="60" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;"></textarea></td>
    </tr>

    <tr>
        <td class="trb"><b>Ausblenden (Benutzer):</b><br /><span class="small">hide(tinyint,1): Einstellung beim Benutzer bearbeiten ausblenden? (1/0)</span></td>
        <td class="trb"><input type="text" name="hide" value="0" size="2" maxlength="1" /></td>
    </tr>

    <tr>
        <td class="tra"><b>In Profil:</b><br /><span class="small">in_profile(tinyint,1): Kann der Inhalt dieses Feldes vom Benutzer selber in seinem Profil ge&auml;ndert werden?<br /><b>NICHT f�r sicherheitsrelevante Berechtigungen verwenden!</b></span></td>
        <td class="tra"><input type="text" name="in_profile" value="0" size="2" maxlength="1" /></td>
    </tr>
	
    <tr>
        <td class="trb"><b>NoDelete:</b><br /><span class="small">nodelete(tinyint,1): Benutzerrecht kann nicht mehr gel&ouml;scht werden.</span></td>
        <td class="trb"><input type="text" name="nodelete" value="0" size="2" maxlength="1" /></td>
    </tr>
	
    <tr>
        <td class="tra" colspan="2">Mit <b>*</b> markierte Felder m&uuml;ssen ausgef&uuml;llt werden. Es erfolgt jedoch keine &Uuml;berpr&uuml;fung der Formulardaten.</td>
    </tr>

    <tr>
        <td class="tra" align="left"><input type="reset" value="Zur�cksetzen" class="input" /></td>
        <td class="tra" align="right"><input type="hidden" name="action" value="add_right" /><input type="hidden" name="send" value="1" /><input type="submit" value="Datensatz anlegen" class="input" /></td>
    </tr>
</table>	
</form>

<?PHP
	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "add_right")
		$flag_loginerror = true;

// Datens�tze neu sortieren
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "sort_rights" && $userdata['rights'] == 1 && $userdata['level'] == 10){
	
	echo "<h1>Benutzerrechte sortieren</h1>";
	
	echo "<div class=\"meldung_hinweis\"><p><b>Benutzerrechte eines anderen Moduls sortieren:</b></p>".
	create_ModulForm($filename."?action=sort_settings&amp;","input2",TRUE)."</div>";
	
	$cats_settings = getSettingCats($modul,$mysql_tables['rights']);
	
	// Eintr�ge speichern
	if(isset($_POST['send']) && $_POST['send'] == 1){
	
		$list_save = $mysqli->query("SELECT id,sortid FROM ".$mysql_tables['rights']." WHERE is_cat='0' AND modul='".$mysqli->escape_string($modul)."'");
		while($row_save = $list_save->fetch_assoc()){
			$mysqli->query("UPDATE ".$mysql_tables['rights']." SET sortid='".$mysqli->escape_string($_POST[$row_save['id']])."' WHERE id='".$row_save['id']."'");
			if($userdata['devmode'] == 1) echo "UPDATE ".$mysql_tables['rights']." SET sortid='".$mysqli->escape_string($_POST[$row_save['id']])."' WHERE id='".$row_save['id']."'<br />\n";
			}

		echo "<p class=\"meldung_ok\"><b>Gespeichert</b></p>";
		}
		

        echo "<form action=\"".$filename."?action=sort_rights&amp;modul=".$modul."\" method=\"post\">";
        echo "<ul>\n";

		$save1 = "";
        $list = $mysqli->query("SELECT id,catid,sortid,name FROM ".$mysql_tables['rights']." WHERE is_cat='0' AND modul='".$mysqli->escape_string($modul)."' ORDER BY catid,sortid");
        while($row = $list->fetch_assoc()){
            if($save1 != $row['catid']) echo "<li><h2>".$cats_settings[$row['catid'].$modul]['name']."</h2></li>";
			echo "<li><input type=\"text\" name=\"".$row['id']."\" size=\"3\" value=\"".$row['sortid']."\" maxlength=\"5\" /> ".$row['name']." <i>ID: ".$row['id']."</i></li>\n";

			$save1 = $row['catid'];
            }
        echo "</ul>\n";

        echo "<p><input type=\"reset\" value=\"Reset &raquo;\" class=\"input\" /> <input type=\"submit\" value=\"Sortieren &raquo;\" class=\"input\" /><input type=\"hidden\" name=\"send\" value=\"1\" /></p>";

        echo "</form>";
		
    }elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "sort_rights") 
		$flag_loginerror = true;


}else $flag_loginerror = true;
include("system/foot.php");

?>