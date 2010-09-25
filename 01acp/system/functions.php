<?PHP
/* 
	01ACP - Copyright 2008-2010 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Globale PHP-Funktionen
				Auf Funktionen kann nach dem Include der headinclude.php zugrgriffen werden
	#fv.1200#
*/

// E-Mail-Adresse auf äußerliche Gültigkeit überprüfen
function check_mail($email){
	if(eregi("[A-Za-z0-9\.!#\\$%&'\*+-/=\?\^_`{\|}~]{3,}@[a-z0-9]+([-_\.]?[a-z0-9])*\.[a-z]{2,4}", $email)) return TRUE;
	else return FALSE;
}


	
// Seiten-Funktion
/*&$query			MySQL-Query, der "limitiert" werden soll
  &$sites			Gesamtzahl der vorhandenen Seiten
  $get_var			Name der GET-'site'-Variablen
  $perpage			Anzahl Elemente pro Seite
*/
function makepages(&$query,&$sites,$get_var,$perpage){
global $db,$_GET;

$slc = mysql_query($query);
$sc = mysql_num_rows($slc);
$sites = ceil($sc/$perpage);

if(isset($_GET[$get_var]) && $_GET[$get_var] != "" && $_GET[$get_var] <= $sites){
    $start = $_GET[$get_var]*$perpage-$perpage;
    $query .= " LIMIT ".mysql_real_escape_string($start).",".mysql_real_escape_string($perpage)."";
    }
else{
    $query .= " LIMIT ".mysql_real_escape_string($perpage)."";
    }
	
return $query;
}


// Ausgabe der Seitenzahlen, Vor- / Zrücklinks bei Tabellen
/*$sites				Gesamtzahl an Seiten
  $tablewidth			Tabellenbreite
  $get_var				Name der GET-'site'-Variablen
  $add2url				Zusätzliche Parameter, die an die URL angefügt werden sollen
  $tableclass			CSS-Klasse für die Tabelle (zur Bestimmung der Breite etc.)
global $filename		Name der Ziel-URL (global)

RETURN: Gedrittelte Tabelle mit Vor, Zurück-Links und Seitenzahl
*/
function echopages($sites,$tablewidth,$get_var,$add2url,$tableclass=""){
global $filename;

$return = "";

if(!empty($tablewidth)) $tablewidth = " width=\"".$tablewidth."\"";
if(!empty($tableclass)) $tableclass = " class=\"".$tableclass."\"";

if(strchr($filename,"?")) $trenner = "&amp;";
else $trenner = "?";

if($sites > 1){
	$return = "<table border=\"0\" align=\"center\"".$tablewidth.$tableclass.">
	<tr>
		<td width=\"33%\" align=\"left\">";
			if(isset($_GET[$get_var]) && $_GET[$get_var] > 1){
				$return .= "<a href=\"".$filename.$trenner.$get_var."=1&amp;".$add2url."\">&laquo; 1</a> ";
				$sz = $_GET[$get_var]-1;
				$return .= "<a href=\"".$filename.$trenner.$get_var."=".$sz."&amp;".$add2url."\">&laquo; Zur&uuml;ck</a>";
				}
	$return .= "        </td>
		<td width=\"33%\" align=\"center\">";
			if(!isset($_GET[$get_var]) || isset($_GET[$get_var]) && $_GET[$get_var] == ""){
				$current = 1;
				if($sites > 1){ $sv = 2; }
				}
			else{
				$current = $_GET[$get_var];
				$sv = $_GET[$get_var]+1;
				}
			$return .= "<b>".$current."/".$sites."</b>";

	$return .= "        </td>
		<td width=\"33%\" align=\"right\">";
			if(isset($_GET[$get_var]) && $_GET[$get_var] < $sites || !isset($_GET[$get_var]) && $sites > 1){
				$return .= "<a href=\"".$filename.$trenner.$get_var."=".$sv."&amp;".$add2url."\">Weiter &raquo;</a> ";
				$return .= "<a href=\"".$filename.$trenner.$get_var."=".$sites."&amp;".$add2url."\">".$sites." &raquo;</a>";
				}
	$return .= "        </td>
	</tr>
</table>";
	}

return $return;
}










// Funktion zum Verkleinern der Bilder
/*$maxwidth			Maximale Bildkantenlänge
  &$picwidth		Bildbreite
  &$picheight		Bildhöhe
*/
function picbig($maxwidth,&$picwidth,&$picheight){
//Überprüfen welches die längere Seite ist
if($picwidth >= $picheight){
    $bigside = $picwidth;
    }
else{
    $bigside = $picheight;
    }

if($bigside > $maxwidth){
    $k = $bigside/$maxwidth;

    $picwidth = $picwidth/$k;
    $picheight = $picheight/$k;
    }
}



// ACP-Submenü generieren
/*$modul			Modulname
  $sub				Sub-Kategorie-ID
  
RETURN: Menue in <ul></ul>-Tags
*/
function create_menue($menuecat,$sub){
global $db,$mysql_tables,$userdata,$modul;

$first = 1;

$return = "<ul>";

if($sub == 0 && $modul != "01acp"){
	$return .= "\n    <li class=\"first\"><a href=\"_loader.php?modul=".$modul."\"><b>Modul-Startseite</b></a></li>\n";
	$first++;
	}

$list = mysql_query("SELECT id,name,link,rightname,rightvalue FROM ".$mysql_tables['menue']." WHERE (modul='".mysql_real_escape_string($menuecat)."' OR modul='overall') AND
		(sicherheitslevel <= '".mysql_real_escape_string($userdata['level'])."' OR sicherheitslevel = '0') AND
		subof = '".mysql_real_escape_string($sub)."' AND
		hide = '0'
		ORDER BY sortorder");
if(mysql_num_rows($list) >= 1){
	while($row = mysql_fetch_array($list)){
		if((isset($userdata[stripslashes($row['rightname'])]) && $userdata[stripslashes($row['rightname'])] == stripslashes($row['rightvalue'])) || empty($row['rightname'])){
			if($first == 1) $class = " class=\"first\"";
			else $class = "";
			
			$return .= "\n    <li".$class."><a href=\"".stripslashes($row['link'])."\">".stripslashes($row['name'])."</a>".create_menue($menuecat,$row['id'])."</li>\n";
			
			$first++;
			}
		
		}
	$return .= "</ul>";
	}
else $return = "";
	
return $return;
}



// Neues Zufallspasswort generieren
/*$laenge			Anzahl Zeichen des Passworts*/
function create_NewPassword($laenge){
mt_srand((double)microtime()*1000000); 
$zahl = mt_rand(1000, 9999);

$passzahl = md5($zahl);
$newpass = substr($passzahl, 0,$laenge);

return $newpass;
}





// Passwort Hash-Funktion
/*$password				Zu hashendes Passwort*/
function pwhashing($password){
global $salt;

for($i=0;$i<=500;$i++){
	$password = md5($password.$salt);
	}
return sha1($password.$salt);
}




// Einstellungs-Kategorien / Rechte-Kategorien in Array einlesen
/*$modul			Modulname
  $mysqltab			MySQL-Tabelle
  
RETURN: $return[catid][catid]	= Catid
		$return[catid][modul] 	= Zugewiesenes Modul
		$return[catid][name] 	= Name der Einstellung / des Rechts
*/
function getSettingCats($modul,$mysqltab){
global $db;

$return = "";

if(isset($modul) && !empty($modul)) $add2query = " AND modul='".mysql_real_escape_string($modul)."'";

$list = mysql_query("SELECT modul,catid,name FROM ".$mysqltab." WHERE is_cat='1'".$add2query." ORDER BY modul,sortid,catid");
while($row = mysql_fetch_array($list)){
	//$return[$row['catid']][0] = $row['name'];
	$return[$row['catid'].$row['modul']]['catid'] = $row['catid'];
	$return[$row['catid'].$row['modul']]['modul'] = $row['modul'];
	$return[$row['catid'].$row['modul']]['name'] = $row['name'];
	//$return[$row['catid']]['modul'] = $row['modul'];		//Gibt an welchem Modul das entsprechende Recht / die entsprechende Einstellung zugeordnet ist
	}
return $return;
}





// Installierte Module in Array einlesen
/*&$inst_module			Array mit allen installierten Modulen [idnamen]

RETURN: Array(id => Modul-Idname);
*/
function getModuls(&$inst_module){
global $db,$mysql_tables;

$list = mysql_query("SELECT * FROM ".$mysql_tables['module']." ORDER BY instname");
$fieldmenge = mysql_num_fields($list);
while($row = mysql_fetch_array($list)){
	for($i=0;$i < $fieldmenge;$i++){ 
		$module[stripslashes($row['idname'])][mysql_field_name($list, $i)] = stripslashes($row[mysql_field_name($list, $i)]);
		}
	$inst_module[] = stripslashes($row['idname']);
	}
return $module;
}








// Dropdown-Box aus installierten Modulen generieren (ohne Select-Tag)
/*$restricted			TRUE/FALSE es werden nur Module angezeigt für die der Benutzer auch Zugriffsrechte hat

RETURN: Option-Elemente für Select-Formularelement
*/
function create_ModulDropDown($restricted=FALSE){
global $inst_module,$module,$modul,$userdata;
$return = "";

if($modul == "01acp") $sel1 = " selected=\"selected\"";
else $sel1 = "";

$return .= "<option value=\"01acp\"".$sel1.">01ACP</option>\n";
foreach($inst_module as $value){
	if($modul == $value) $sel2 = " selected=\"selected\"";
	else $sel2 = "";
	
	if(isset($userdata[$value]) && $module[$value]['aktiv'] == 1 && (!$restricted || $restricted && $userdata[$value] == 1))
		$return .= "<option value=\"".$value."\"".$sel2.">".$module[$value]['instname']."</option>\n";
	}
	
return $return;
}








// Dropdown-Box mit allen angelegten Benutzern generieren
/*$unbenannt			Benutzer "Unbekannt" (ID = 0) mit ausgeben (TRUE,FALSE)?
  $selectedid=NULL		ID des vorselektierten Benutzers, wenn NULL -> $userdata['id']

RETURN: Option-Elemente für Select-Formularelement
*/
function create_UserDropDown($unbekannt,$selectedid=NULL){
global $mysql_tables,$userdata;
$return = "";

if($selectedid == NULL) $selectedid = $userdata['id'];
if(!$unbekannt) $add2query = " WHERE id != '0'";
else $add2query = "";

$list = mysql_query("SELECT id,username FROM ".$mysql_tables['user'].$add2query." ORDER BY username");
while($row = mysql_fetch_array($list)){
	if($selectedid == $row['id']) $sel = " selected=\"selected\"";
	else $sel = "";
	
	$return .= "<option".$sel." value=\"".$row['id']."\">".stripslashes($row['username'])."</option>\n";
	}
	
return $return;
}








// Komplettes Modul-Change-Formular generieren und ausgeben
/*$ziel				Zieladresse für Formular
  $class			CSS-Klasse für Submit-Button
  
RETURN: Formular mit Modul-Dropdownbox
*/
function create_ModulForm($ziel,$class,$restricted=FALSE){

$return  = "<form action=\"".$ziel."\" method=\"get\">\n";
$return .= "<select name=\"modul\" size=\"1\" class=\"input_select\" onchange=\"location.href='".$ziel."modul='+this.options[this.selectedIndex].value+''\">\n";
$return .= create_ModulDropDown($restricted);
$return .= "</select>\n<input type=\"submit\" value=\"Go &raquo;\" class=\"".$class."\" />\n";
$return .= "</form>";

return $return;
}







// Formularfeldtypen (Settings & Rights-Verwaltung) parsen
/*$row[]			Array mit Datensatz aus MySQL-Tabelle
  $class			CSS-Classe für Tabellenzeile (tra, trab)
  &$count			CSS-Classen-"Counter" für Tabellenzeilen (tra, trab)
  $jscssclass		CSS-Klasse für <tr> um Ein/Ausblenden per JS zu ermöglichen
  
RETURN: geparste Dateifelder in entsprechenden Tabellenzellen
  */
function parse_dynFieldtypes($row,$class,&$count,$jscssclass=""){
global $userdata;

if($row['exp'] != ""){ $exp = "<br /><span class=\"small\">".nl2br(stripslashes($row['exp']))."</span>"; }else{ $exp = ""; }

// Normales Textfeld
if($row['formename'] == "text"){
	$inputfield = "<input type=\"text\" name=\"".$row['idname']."\" value=\"".stripslashes($row['wert'])."\" size=\"".$row['formwerte']."\" />";
	}
	
// Textarea
elseif($row['formename'] == "textarea"){
	list($nrrows, $nrcols) = explode("|", stripslashes($row['formwerte']),2);
	$inputfield = "<textarea name=\"".$row['idname']."\" rows=\"".$nrrows."\" cols=\"".$nrcols."\" style=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal;\">".stripslashes($row['wert'])."</textarea>";
	}
	
//Radiobutton oder Auswahlliste
elseif(strstr($row['formename'], "|")){
	$field_array = explode('|', $row['formename']);
	$werte_array = explode('|', $row['formwerte']);
	//array_pop($field_array); //Löscht letztes Element des Array
	//array_pop($werte_array); //Löscht letztes Element des Array
	
	// 2 -> Radiobuttons
	if(count($field_array) == 2){
		if($werte_array[0] == $row['wert']){
			$inputfield = "<input type=\"radio\" name=\"".$row['idname']."\" value=\"".$werte_array[0]."\" checked=\"checked\" /> ".stripslashes($field_array[0])."<br />
						   <input type=\"radio\" name=\"".$row['idname']."\" value=\"".$werte_array[1]."\" /> ".stripslashes($field_array[1])."";
			}
		elseif($werte_array[1] == $row['wert']){
			$inputfield = "<input type=\"radio\" name=\"".$row['idname']."\" value=\"".$werte_array[0]."\" /> ".stripslashes($field_array[0])."<br />
						   <input type=\"radio\" name=\"".$row['idname']."\" value=\"".$werte_array[1]."\" checked=\"checked\" /> ".stripslashes($field_array[1])."";
			}
		}
	// Auswahlliste mit count($field_array) Elementen
	elseif(count($field_array) > 2){
		$inputfield = "<select name=\"".$row['idname']."\" size=\"1\" class=\"input_select\">\n";

		for($y=0;$y<count($field_array);$y++){
			if($werte_array[$y] == $row['wert'])
				$inputfield .= "<option value=\"".$werte_array[$y]."\" selected=\"selected\">".stripslashes($field_array[$y])."</option>\n";
			else
				$inputfield .= "<option value=\"".$werte_array[$y]."\">".stripslashes($field_array[$y])."</option>\n";

			}
		$inputfield .= "</select>";
		}
	}

if($row['input_exp'] != "") $inputfield .= " ".stripslashes(nl2br($row['input_exp']));

if($userdata['devmode'] == 1) $returnfname = " <i>".$row['idname']."</i>";
else $returnfname = "";

if(strstr($jscssclass,"01acp")) $display = "";
else $display = " style=\"display:none;\"";

// Ausgabe -> return;
if(!isset($nrcols) OR $nrcols <= "50"){
	$return = "\n    <tr class=\"".$jscssclass."\"".$display.">
<td width=\"60%\" class=\"".$class."\"><b>".stripslashes($row['name'])."</b>".$returnfname.$exp."</td>
<td class=\"".$class."\">".$inputfield."</td>
</tr>";
	}
// Breitere Zelle (colspan)
elseif($nrcols > "50"){
	$return = "\n    <tr class=\"".$jscssclass."\"".$display.">
<td colspan=\"2\" class=\"".$class."\" valign=\"top\"><b>".stripslashes($row['name'])."</b>".$returnfname.$exp."</td>
</tr>";

	$return .= "\n    <tr class=\"".$jscssclass."\"".$display.">
<td colspan=\"2\" class=\"".$class."\" align=\"center\" valign=\"top\">".$inputfield."</td>
</tr>";
	}
	
unset($nrcols);
return $return;
}





// Neuen Dateien oder Bilder hochladen
/*$fname			"Echter" Dateiname der hochzuladenden Datei
  $fsize			Größe der Datei, die hochgeladen werden soll
  $tname			Temporärer Name der hochgeladenen Datei
  $allowedtype		Erlaubter Datentyp (pic, file)
 ($modul)			Welchem Modul soll die Datei zugeordnet werden? Standard: 01acp
 ($destname)		ggf. "Ziel"-Dateiname (bei Reupload von bereits bestehenden Dateien (Dateien ersetzen))
 ($dirid)			Verzeichnis-ID Standard: 0

RETURN: Array(success,name,orgname,size,endung,fileart,msg);
  */
function uploadfile($fname,$fsize,$tname,$allowedtype,$modul="01acp",$destname="",$dirid=0){
    global $db,$userdata,$picuploaddir,$catuploaddir,$attachmentuploaddir,$mysql_tables,$settings,$picendungen,$picsize,$attachmentendungen,$attachmentsize,$instnr;

    mt_srand((double)microtime()*1000000);
	$new_filename = substr(md5($instnr.time().microtime().mt_rand(10000,99999999)),0,15);    
    $endung = getEndung($fname);
	
	if(empty($dirid) || !is_numeric($dirid)) $dirid = 0;
	
	// Wenn eine Datei ersetzt werden soll, müssen die Dateiendungen übereinstimmen -> alte Dateiendung und neue werden zusätzlich verglichen
	if(!empty($destname)){
		$destendung = getEndung($destname);
		}
	else $destendung = $endung;	// Wenn keine Datei ersetzt wird -> $destendung = $endung --> immer TRUE
    
    // Endungen überprüfen
    if(in_array($endung,$picendungen) && $userdata['upload'] == 1 && ($allowedtype == "pic" || empty($allowedtype)) && $destendung == $endung){
        $is_pic = 1;
        $dir = $picuploaddir;
        $size = $picsize;
        $fileart = "pic";
        }
    elseif(in_array($endung,$attachmentendungen) && $userdata['upload'] == 1 && ($allowedtype == "file" || empty($allowedtype)) && $destendung == $endung){
        $is_file = 1;
        $dir = $attachmentuploaddir;
        $size = $attachmentsize;
        $fileart = "file";
        }
    else{
        $fupload = array("success" 	=> 	0,
                         "name" 	=> 	0,
                         "orgname" 	=> 	0,
                         "size" 	=> 	0,
                         "endung" 	=> 	0,
                         "fileart" 	=> 	0,
                         "msg" 		=> 	"Die Datei besitzt keine der erlaubten Dateiendungen oder
										Sie haben keine Berechtigung diese Datei hochzuladen!"
                         );
        }

    // Bestimmten Dateinamen verwenden? (== bestehende Datei überschreiben)
    if(!empty($destname)){
		$split = explode(".", $destname,2);
		$filename = $split[0];
		
		// Thumbnails löschen
		if(file_exists($dir.$split[0]."_tb_".ACP_TB_WIDTH.".".$split[1])){
			@clearstatcache();  
			@chmod($dir.$split[0]."_tb_".ACP_TB_WIDTH.".".$split[1], 0777);
			@unlink($dir.$split[0]."_tb_".ACP_TB_WIDTH.".".$split[1]);
			}
		
		if(file_exists($dir.$split[0]."_tb_".ACP_TB_WIDTH200.".".$split[1])){
			@clearstatcache();  
			@chmod($dir.$split[0]."_tb_".ACP_TB_WIDTH200.".".$split[1], 0777);
			@unlink($dir.$split[0]."_tb_".ACP_TB_WIDTH200.".".$split[1]);
			}
		@clearstatcache();  
		@chmod($dir.$filename.".".$endung, 0777);
        }
    else
        $filename = $new_filename;

	if(isset($is_file) && $is_file == 1 || isset($is_pic) && $is_pic == 1){
        if($fsize <= $size){
            if(move_uploaded_file($tname,$dir.$filename.".".$endung)){
                $fupload = array("success" 	=> 	1,
                                 "name" 	=> 	$filename.".".$endung,
                                 "orgname" 	=> 	$fname,
                                 "size" 	=> 	$fsize,
                                 "endung" 	=> 	$endung,
                                 "fileart" 	=> 	$fileart,
                                 "msg" 		=> 	$fname." wurde erfolgreich hochgeladen."
                                );

                //Chmod-Rechte für Dateien ggf setzen (644):
                @clearstatcache();
                @chmod($dir.$filename.".".$endung, 0777);
				
				// Datensatz einer ggf. überschriebenen Datei löschen
				if(!empty($destname)){
					$list = mysql_query("SELECT id FROM ".$mysql_tables['files']." WHERE name='".mysql_real_escape_string($destname)."' LIMIT 1");
					while($row = mysql_fetch_array($list)){
						mysql_query("DELETE FROM ".$mysql_tables['files']." WHERE id='".$row['id']."' LIMIT 1");
						}
					}

                //Eintragung in Datenbank vornehmen:
                $sql_insert = "INSERT INTO ".$mysql_tables['files']." (type,modul,timestamp,dir,orgname,name,size,ext,uid) VALUES (
							'".mysql_real_escape_string($fupload['fileart'])."',
							'".mysql_real_escape_string($modul)."',
							'".time()."',
							'".mysql_real_escape_string($dirid)."',
							'".mysql_real_escape_string($fupload['orgname'])."',
							'".mysql_real_escape_string($fupload['name'])."', 
							'".mysql_real_escape_string($fupload['size'])."', 
							'".mysql_real_escape_string($fupload['endung'])."', 
							'".$userdata['id']."')";
                mysql_query($sql_insert) OR die(mysql_error());
                }
            else{
                $fupload['success'] = 0;
				$fupload['msg'] = "Ein unbekannter Fehler ist aufgetreten oder es wurde keine Datei hochgeladen.";
                }
            }
        else{
            //Wenn Dateigröße zu groß ist
			$fupload['success'] = 0;
			$fupload['msg'] = "Die gew&auml;hlte Datei ist zu gro&szlig;.";
            }
        }
    else{
        //Wenn keine passende Endung gewählt wurde
		$fupload['success'] = 0;
		$fupload['msg'] = "Die gew&auml;hlte Datei besitzt keine der erlaubten Dateiendungen.";
        }
    
    return $fupload;
}







// Datei oder Bild löschen
/*$dir				Verzeichnis in dem sich die zu löschende Datei befindet
  $file				Datei (inkl. Endung), die gelöscht werden soll

RETURN: TRUE/FALSE
  */
function delfile($dir,$file){
global $mysql_tables;

$split = explode(".", strtolower($file),2);

if(file_exists($dir.$split[0]."_tb_".ACP_TB_WIDTH.".".$split[1])){
	@clearstatcache();  
	@chmod($dir.$split[0]."_tb_".ACP_TB_WIDTH.".".$split[1], 0777);
	@unlink($dir.$split[0]."_tb_".ACP_TB_WIDTH.".".$split[1]);
	}
	
if(file_exists($dir.$split[0]."_tb_".ACP_TB_WIDTH200.".".$split[1])){
	@clearstatcache();  
	@chmod($dir.$split[0]."_tb_".ACP_TB_WIDTH200.".".$split[1], 0777);
	@unlink($dir.$split[0]."_tb_".ACP_TB_WIDTH200.".".$split[1]);
	}

@clearstatcache();  
@chmod($dir.$file, 0777);
if(unlink($dir.$file)){
	mysql_query("DELETE FROM ".$mysql_tables['files']." WHERE name='".mysql_real_escape_string($file)."' LIMIT 1");
	return TRUE;
	}
else return FALSE;
}








// Dateiendung einer Datei holen
/*$filestring		Dateiname inkl. Endung der Datei Form: abc.end

RETURN: Endung (ohne Trennungspunkt)
  */
function getEndung($filestring){

if(!empty($filestring)){
	$endungsarray = explode(".",$filestring);
	$endung = $endungsarray[count($endungsarray)-1];
	
	return strtolower($endung);
	}
else{
	return "";
	}
}








// Dynamisches Bild-Resizing durchführen
/*$sourcefile		Quellfile, dass verkleinert werden soll
  $resize			Gewünschte Größe in Pixeln

  */
function showpic($sourcefile,$resize){
global $_GLOBALS;

$split = explode(".", strtolower($sourcefile),2);
$filename = $split[0];
$fileType = $split[1];

// Thumbnail ausgeben, wenn vorhanden
if(file_exists($filename."_tb_".ACP_TB_WIDTH.".".$fileType) && $resize == ACP_TB_WIDTH || file_exists($filename."_tb_".ACP_TB_WIDTH200.".".$fileType) && $resize == ACP_TB_WIDTH200){
	switch($fileType){
	  case('png'):
		$sourcefile_id = imagecreatefrompng($filename."_tb_".$resize.".".$fileType);
		imagealphablending($sourcefile_id,TRUE);
		imagesavealpha($sourcefile_id,TRUE);
		header("Content-type: image/png");
		imagepng($sourcefile_id);
	  break;
	  default:
		$sourcefile_id = imagecreatefromjpeg($filename."_tb_".$resize.".".$fileType);
		header("Content-type: image/jpg");
		imagejpeg($sourcefile_id);
	  }
	}
else{

	$info = getimagesize($sourcefile);

	// Resize images
	if($info[0] >= $info[1]) $bigside = $info[0];
	else $bigside = $info[1];

	if($bigside > $resize){
		$k = $bigside/$resize;
		$picwidth = $info[0]/$k;
		$picheight = $info[1]/$k;
		}
	else{
		$picwidth = $info[0];
		$picheight = $info[1];
		}

	$echofile_id = imagecreatetruecolor($picwidth, $picheight);

	switch($fileType){
	  case('png'):
		$sourcefile_id = imagecreatefrompng($sourcefile);
		
		imagealphablending($echofile_id, false);
		$colorTransparent = imagecolorallocatealpha($echofile_id, 0, 0, 0, 127);
		imagefill($echofile_id, 0, 0, $colorTransparent);
		imagesavealpha($echofile_id, true);
	  break;
	  default:
		$sourcefile_id = imagecreatefromjpeg($sourcefile);
	  }

	// Create a jpeg out of the modified picture
	switch($fileType){
	  case('png'):
		header("Content-type: image/png");
		imagecopyresampled($echofile_id, $sourcefile_id, 0, 0, 0, 0, $picwidth, $picheight, $info[0], $info[1]);
		if($resize == ACP_TB_WIDTH || $resize == ACP_TB_WIDTH200)
			imagepng($echofile_id,$filename."_tb_".$resize.".".$fileType);
			
		imagealphablending($echofile_id,TRUE);
		imagesavealpha($echofile_id,TRUE);
		imagepng($echofile_id);
	  break;
	  default:
		header("Content-type: image/jpg");
		imagecopyresampled($echofile_id, $sourcefile_id, 0, 0, 0, 0, $picwidth, $picheight, $info[0], $info[1]);
		if($resize == ACP_TB_WIDTH || $resize == ACP_TB_WIDTH200)
			imagejpeg($echofile_id,$filename."_tb_".$resize.".".$fileType,80);
		imagejpeg($echofile_id);
	  }

	imagedestroy($sourcefile_id);
	imagedestroy($echofile_id);
	}
}










// TinyMCE-JS generieren und Editor laden
/*$barlook			Vordefinierte Buttonbars/Schaltflächen (biu,small,advanced,none)
  $bar_own			Individuelle Buttons (Nur eine Reihe möglich, Zusätzlich zu $barlook)
  $load_plugins		Weitere TinyMCE-Plugins laden. Aufzählung mit einem , beginnen!
  $classname		Definierte Eigenschaften wirken sich nur auf Textareas, mit entsprechend definierter Klasse aus
  $bar_location		Plugin-Buttons top oder bottom der Textarea
  $add_config		Weitere Konfigurationsvariablen hinzufügen (abschließendes Komma -> ,)

  */
function loadTinyMCE($barlook,$bar_own,$load_plugins,$classname,$bar_location,$add_config=""){
global $_GLOBALS,$settings;

$return = "";

// .js-Datei nur einmalig einbinden
if(!isset($_GLOBALS['tiny_loaded']) OR isset($_GLOBALS['tiny_loaded']) && $_GLOBALS['tiny_loaded'] == FALSE){
	$return .= "<script language=\"javascript\" type=\"text/javascript\" src=\"system/tiny_mce/tiny_mce.js\"></script>";
	$_GLOBALS['tiny_loaded'] = TRUE;
	}

// CSS-Datei einbinden
if(isset($settings['extern_css']) && !empty($settings['extern_css']))
	$cssfile = "content_css : '".$settings['extern_css']."',";
elseif(isset($settings['csscode']) && !empty($settings['csscode']) && (empty($settings['extern_css']) || $settings['extern_css'] == "http://"))
	$cssfile = "content_css : '".CSS_CACHE_DATEI."',";
else
	$cssfile = "";
	
$return .= "
<script language=\"javascript\" type=\"text/javascript\">
tinyMCE.init({
cleanup_on_startup : true,
theme : 'advanced',
language : 'de',
convert_urls : false,
theme_advanced_toolbar_location : '".$bar_location."',
theme_advanced_toolbar_align : 'left',
theme_advanced_statusbar_location : 'bottom',
".$cssfile."
theme_advanced_resizing : true,
theme_advanced_font_sizes : '1,2,3,4,5,6',
dialog_type : 'modal',
".$add_config;

if(!empty($classname)) $return .= "editor_selector : \"".$classname."\",\n";

switch($barlook){
  case "biu":
	$return .= "theme_advanced_buttons1 : \"bold,italic,underline\",";
	$return .= "theme_advanced_buttons2 : \"".$bar_own."\",";
	$return .= "theme_advanced_buttons3 : \"\",";
	$plugins = "";
  break;
  case "small":
	$return .= "theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,link,unlink,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,code\",";
	$return .= "theme_advanced_buttons2 : \"".$bar_own."\",";
	$return .= "theme_advanced_buttons3 : \"\",";
	
	$plugins = "inlinepopups";
  break;
  case "advanced":
	$return .= "theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect',|,tablecontrols,\",";
	$return .= "theme_advanced_buttons2 : \"fontsizeselect,forecolor,backcolor,|,bullist,numlist,|,outdent,indent,|,cut,copy,paste,|,undo,redo,|,link,unlink,anchor,|,emotions,image,filemanager_pic,filemanager_file,media,|,code\",";
	$return .= "theme_advanced_buttons3 : \"".$bar_own."\",";
	$return .= "theme_advanced_blockformats : \"p,div,h1,h2,h3,h4,h5,h6,blockquote,code\",";
	$return .= "table_cell_styles : \"Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1\",
	table_row_styles : \"Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1\",
	table_cell_limit : 100,
	table_row_limit : 25,
	table_col_limit : 25,";
	
	$plugins = "advimage,filemanager,paste,table,emotions,media,imagealignhelper";
  break;
  case "none":
    $return .= "theme_advanced_buttons1 : \"".$bar_own."\",";
	$return .= "theme_advanced_buttons2 : \"\",";
	$return .= "theme_advanced_buttons3 : \"\",";
	$plugins = "";
  break;
  }

$return .= "\nplugins : \"".$plugins.$load_plugins."\",
mode : \"textareas\"
});
</script>\n\n";

return $return;
}







// Bilder und Dateien aus DB auflisten
/*$query			Zusammengestellter MySQL-Query für die DB-Abfrage
  $url				Seiten-URL für Delete-Abfrage
  $show_edit		Bearbeiten / Löschen-Buttons anzeigen? TRUE/FALSE
  $show_tb			Thumbnails bei Bildern anzeigen? TRUE/FALSE
  $show_date		Datums-Spalte zeigen? TRUE/FALSE
  $show_username	Upload-Username-Spalte zeigen? TRUE/FALSE
  $insert			Links zum Einfügen der Dateien ausgeben? (NULL, tinymce, js)

RETURN: Tabellenzeilen mit den entsprechenden Spalten
  */
function getFilelist($query,$url,$show_edit,$show_tb,$show_date,$show_username,$insert){
global $mysql_tables,$attachmentuploaddir,$picuploaddir,$_REQUEST,$userdata,$filename2;

$return = "";

// Usernamen in Array einlesen um MySQL-Anfragen zu minimieren
if($show_username){
	$list = mysql_query("SELECT id,username FROM ".$mysql_tables['user']);
	while($row = mysql_fetch_array($list)){
		$usernames[$row['id']] = stripslashes($row['username']);
		}
	$usernames[0] = "gel&ouml;scht";
	}

// Filedirs auflisten
if(isset($_REQUEST['dir']) && !empty($_REQUEST['dir']) && is_numeric($_REQUEST['dir'])) $dirid = mysql_real_escape_string($_REQUEST['dir']);
else $dirid = 0;

$count = 0;
$colspan = 2;
if($show_date) $colspan++;
if($show_username) $colspan++;
if($show_edit && $userdata['dateimanager'] < 2){ $colspan = $colspan+2; }

if($dirid > 0){
	// Aufwärts-ID holen
	$list = mysql_query("SELECT parentid,name FROM ".$mysql_tables['filedirs']." WHERE id = '".$dirid."' LIMIT 1");
	$dirup = mysql_fetch_assoc($list);
	
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	
	$return .= "<tr>\n    ";
	if($show_edit) $return .= "<td align=\"center\" class=\"".$class."\"></td>";
	if($show_tb) $return .= "<td align=\"center\" class=\"".$class."\"><a href=\"".$url."&amp;dir=".$dirup['parentid']."\"><img src=\"images/icons/dir_up.gif\" alt=\"PC-Verzeichnis mit Pfeil nach oben\" title=\"Verzeichnis aufw&auml;rts\" /></a></td>";
	$return .= "<td class=\"".$class."\" colspan=\"".($colspan+2)."\"><div id=\"dir_".$dirup['parentid']."\" class=\"droppable\"><a href=\"".$url."&amp;dir=".$dirup['parentid']."\">Verzeichnis Aufw&auml;rts</a> | Sie befinden sich hier: <i>".stripslashes($dirup['name'])."</i></div></td>";
	$return .= "</tr>";	
	}

// Unterverzeichnisse auflisten:
$list = mysql_query("SELECT id,parentid,name FROM ".$mysql_tables['filedirs']." WHERE parentid = '".$dirid."' ORDER BY name");
while($row = mysql_fetch_assoc($list)){
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	
	$return .= "<tr id=\"dirid".$row['id']."\">\n    ";
	if($show_edit) $return .= "<td align=\"center\" class=\"".$class."\"></td>";
	if($show_tb) $return .= "<td align=\"center\" class=\"".$class."\"><a href=\"".$url."&amp;dir=".$row['id']."\"><img src=\"images/icons/folder.gif\" alt=\"PC-Verzeichnis\" title=\"Verzeichnis ausw&auml;hlen\" /></a></td>";
	if($show_edit && $userdata['dateimanager'] == 2){
		$return .= "<td class=\"".$class."\" colspan=\"".$colspan."\">";

		$return .= "
			<form id=\"editdirform_".$row['id']."\" action=\"_ajaxloader.php?modul=01acp&ajaxaction=savefiledir&id=".$row['id']."\" method=\"post\">
            <div id=\"hide_showdir_".$row['id']."\" style=\"display:block;\">
            <div id=\"dir_".$row['id']."\" class=\"droppable\">
                <a href=\"".$url."&amp;dir=".$row['id']."\">".stripslashes($row['name'])."</a>
            </div>
            </div>
			<div id=\"hide_editdir_".$row['id']."\" style=\"display:none;\">
                <input type=\"text\" size=\"26\" name=\"dirname\" value=\"".stripslashes($row['name'])."\" class=\"input_text\" />
                <select name=\"dir\" size=\"1\" class=\"input_select\">
					<option value=\"0\">Kein Unterverzeichnis</option>
					".getFileVerz_Rek(0,0,-1,"echo_FileVerz_select",$row['parentid'])."
				</select>
                <input type=\"hidden\" name=\"url\" value=\"".$url."&amp;dir=".$row['id']."\" />
                <input type=\"hidden\" name=\"olddirname\" value=\"".stripslashes($row['name'])."\" />
                <input type=\"hidden\" name=\"olddirid\" value=\"".$row['parentid']."\" />
				<input type=\"submit\" value=\"Speichern\" class=\"input\" />
            </div>
            </form>

            <script type=\"text/javascript\">
            $('editdirform_".$row['id']."').addEvent('submit', function(e) {
        		e.stop();

                this.set('send', {onComplete: function(response) {
        			$('hide_showdir_".$row['id']."').set('html', response);
        		}, evalScripts: true});
        		Start_Loading_standard();
        		this.send();
        	});
            </script>";
		$return .= "</td>";
		
		$return .= "<td class=\"".$class."\" align=\"center\"><a href=\"javascript:hide_unhide('hide_showdir_".$row['id']."'); hide_unhide('hide_editdir_".$row['id']."');\"><img src=\"images/icons/icon_edit.gif\" alt=\"Icon: Datei bearbeiten\" title=\"Datei bearbeiten / ersetzen\" /></a></td>\n";
		$return .= "<td class=\"".$class."\" align=\"center\"><a href=\"javascript:popup('dir_del1','".$row['id']."','".$row['name']."','',400,250);\"><img src=\"images/icons/icon_delete.gif\" alt=\"Icon: Datei l&ouml;schen\" title=\"Datei l&ouml;schen\" /></a></td>\n    ";
		}
	else
		$return .= "<td class=\"".$class."\" colspan=\"".$colspan."\"><a href=\"".$url."&amp;dir=".$row['id']."\">".stripslashes($row['name'])."</a></td>";
	$return .= "</tr>";
	
	}

	if($show_edit)
	    $return .= "\n<form action=\"".$filename2."\" method=\"post\" id=\"multidelform0815\">\n";

$downloads = $drag_start = $drag_ende = "";
// "Normale Dateien" auflisten
$list = mysql_query($query);
while($row = mysql_fetch_array($list)){
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	
	switch($row['type']){
	  case "pic":
	    $popuph = 400;
	  break;
	  case "file":
	    $popuph = 220;
	  break;
	  }
	
	$return .= "<tr id=\"id".$row['id']."\">\n    ";

	if($show_edit)	
		$return .= "<td class=\"".$class."\" align=\"center\"><input type=\"checkbox\"  name=\"delfiles[]\" value=\"".$row['id']."\" /></td>";
	
	if($show_tb){
		$return .= "<td class=\"".$class."\" align=\"center\">";
		
		if($row['type'] == "pic"){
			$split = explode(".", strtolower($row['name']),2);
			$filename = $split[0];
			$fileType = $split[1];
			if(file_exists($picuploaddir.$filename."_tb_".ACP_TB_WIDTH.".".$fileType))
				$return .= "<a href=\"".$picuploaddir.$row['name']."\" target=\"_blank\" class=\"lightbox\"><img src=\"".$picuploaddir.$filename."_tb_".ACP_TB_WIDTH.".".$fileType."\" alt=\"Hochgeladenes Bild\" /></a>";
			else
				$return .= "<a href=\"".$picuploaddir.$row['name']."\" target=\"_blank\" class=\"lightbox\"><img src=\"".$picuploaddir."showpics.php?img=".$row['name']."&amp;size=".ACP_TB_WIDTH."&amp;hidegif=normal\" alt=\"Hochgeladenes Bild\" /></a>";
			}
		else
			$return .= "<a href=\"".$attachmentuploaddir."download.php?fileid=".$row['id']."&amp;nocount=1\">".filetypes($row['ext'])."</a>";
		
		$return .= "</td>\n    ";
		}
		
	if($insert == "tinymce" && $row['type'] == "pic"){
		$link1 = "<a href=\"javascript:FileDialog.insertpic_flist('".$picuploaddir."','".stripslashes($row['name'])."');\">";
		$link2 = "</a>";
		}
	elseif($insert == "tinymce" && $row['type'] == "file"){
		$link1 = "<a href=\"javascript:FileDialog.insertfile('".$attachmentuploaddir."','".$row['id']."','".stripslashes($row['orgname'])."');\">";
		$link2 = "</a>";
		}
	elseif($insert == "js" && !empty($_REQUEST['formname']) && !empty($_REQUEST['formfield'])){
		$link1 = "<a href=\"javascript:opener.document.".$_REQUEST['formname'].".".$_REQUEST['formfield'].".value='".stripslashes($row['name'])."'; window.close();\">";
		$link2 = "</a>";
		}
	else{
		$link1 = ""; $link2 = "";
		}
	
	if($show_edit && $userdata['dateimanager'] == 2){
		if($row['type'] == "file")
			$downloads = "<span style=\"float:right;\">".$row['downloads']."</span>";
		else
			$downloads = "<span style=\"float:right;\">-</span>";
		$drag_start = "<div id=\"file_".$row['id']."\" class=\"dragable\">";
		$drag_ende = "</div>";
		}
	
	$return .= "<td class=\"".$class."\">".$drag_start.$link1.substr(stripslashes($row['orgname']),0,40).$downloads.$link2.$drag_ende."</td>\n    ";
	$return .= "<td class=\"".$class."\" align=\"center\">".parse_size($row['size'],"KB")." KB</td>\n    ";
	if($show_date) $return .= "<td class=\"".$class."\" align=\"center\">".date("d.m.Y",$row['timestamp'])."</td>\n    ";
	if($show_username) $return .= "<td class=\"".$class."\"><a href=\"".$url."&amp;uid=".$row['uid']."\">".$usernames[$row['uid']]."</a></td>\n    ";
	
	if($show_edit){
		$return .= "<td class=\"".$class."\" align=\"center\"><a href=\"javascript:popup('reuploader','".$row['type']."','".$row['id']."','".stripslashes($row['orgname'])."',620,480);\"><img src=\"images/icons/icon_edit.gif\" alt=\"Icon: Datei bearbeiten\" title=\"Datei bearbeiten / ersetzen\" /></a></td>\n";
		$return .= "<td class=\"".$class."\" align=\"center\"><a href=\"javascript:popup('file_del1','".$row['id']."','".$row['type']."','".$row['name']."-3-3-".stripslashes($row['orgname'])."',400,".$popuph.");\"><img src=\"images/icons/icon_delete.gif\" alt=\"Icon: Datei l&ouml;schen\" title=\"Datei l&ouml;schen\" /></a></td>\n    ";
		}
	
	$return .= "</tr>\n\n";
	}

return $return;
}







// Bilder und Dateien aus DB auflisten
/*$sizeof			Dateigröße in Byte
  $einheit			Gewünschte Zieleinheit (B, KB, MB)

RETURN: Übergebenen Byte-Wert umgerechnet in die Zieleinheit
  */
function parse_size($size,$einheit){

switch($einheit){
  case "KB":
    return round(($size/1000),2);
  break;
  case "MB":
    return round(($size/1000000),2);
  break;
  default:
    return $size;
  break;
  }
}







// Bilder und Dateien aus DB auflisten
/*$endung			Dateiendung ohne .

RETURN: Zur Datei passendes Filetype-Icon inkl. IMG-Tag
  */
function filetypes($endung){

switch($endung){
  case "bmp":
    $icon = "bmp.gif";
  break;
  case "html":
  case "htm":
  case "php":
  case "xml":
    $icon = "html.gif";
  break;
  case "pdf":
    $icon = "pdf.gif";
  break;
  case "png":
    $icon = "png.gif";
  break;
  case "gif":
    $icon = "gif.gif";
  break;
  case "jpg":
  case "jpeg":
    $icon = "jpg.gif";
  break;
  case "psd":
    $icon = "psd.gif";
  break;
  case "rar":
  case "ace":
    $icon = "rar.gif";
  break;
  case "txt":
    $icon = "txt.gif";
  break;
  case "zip":
    $icon = "zip.gif";
  break;
  case "doc":
    $icon = "doc.gif";
  break;
  default:
    $icon = "unknown.gif";
  break;
  }
  
return "<img src=\"images/filetypes/".$icon."\" alt=\"Filetype / Icon: ".$endung."\" />";
}







// Kommentare auflisten (ACP)
/*$query			gültiger MySQL-Query für die Kommentarliste
  $option			Manipulator für zusätzliche / weniger Spalten

RETURN: Komplette Liste (HTML)
  */
function getCommentList($query,$option){
global $_GET,$module,$modul,$filename;

if(!isset($_GET['site'])) $_GET['site'] = "";

$return = "<form action=\"".$filename."&amp;postid=".$_GET['postid']."&amp;site=".$_GET['site']."\" method=\"post\">\n";
$return .= "<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">

    <tr>
		<td class=\"tra\" colspan=\"2\"><b>Kommentar</b></td>\n";

if($option == "free") $return .= "<td class=\"tra\" width=\"25\"><!-- Freischalten -->&nbsp;</td>\n";

$return .="		<td class=\"tra\" width=\"25\"><!-- Ansehen -->&nbsp;</td>
		<td class=\"tra\" width=\"25\"><!-- Bearbeiten -->&nbsp;</td>
		<td class=\"tra\" width=\"25\" align=\"center\"><img src=\"images/icons/icon_trash.gif\" alt=\"M&uuml;lleimer\" title=\"Kommentar l&ouml;schen\" /></td>
	</tr>\n\n";

$count = 0;
$list = mysql_query($query);
while($row = mysql_fetch_array($list)){
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	if($option == "free" && $row['frei'] == 1)
		$colspan = " colspan=\"2\"";
	else
		$colspan = "";
	
	$return .= "<tr id=\"id".$row['id']."\">
	<td class=\"".$class."\" width=\"25\" align=\"center\"><input type=\"checkbox\" name=\"cdelid[]\" value=\"".$row['id']."\" /></td>
	<td class=\"".$class."\"".$colspan." onclick=\"popup('show_comment','".$row['id']."','','',510,450);\" style=\"cursor: pointer;\">
		Am ".date("d.m.Y - H:i",$row['timestamp'])."Uhr von <b>".stripslashes($row['autor'])."</b> (".$row['ip'].") verfasst:<br />
		<b>".call_user_func("_".$module[$modul]['modulname']."_getCommentParentTitle",$row['postid'])."</b><br />
		".substr(strip_tags(bb_code_comment(stripslashes($row['comment']),1,$row['bbc'],$row['smilies'])),0,250)." [...]
	</td>\n";
	
	if($option == "free" && $row['frei'] == 0) $return .= "<td class=\"".$class."\" align=\"center\"><img src=\"images/icons/ok.gif\" alt=\"OK\" title=\"Kommentar freischalten\" id=\"cfree".$row['id']."\" onclick=\"AjaxRequest.send('modul=01acp&ajaxaction=freecomment&id=".$row['id']."');\" /></td>\n";
	
	$return .= "<td class=\"".$class."\" align=\"center\"><a href=\"javascript:popup('show_comment','".$row['id']."','','',510,450);\"><img src=\"images/icons/icon_show.gif\" alt=\"Auge\" title=\"Kommentar ansehen\" /></a></td>
	<td class=\"".$class."\" align=\"center\"><a href=\"javascript:popup('edit_comment','".$row['id']."','','',510,450);\"><img src=\"images/icons/icon_edit.gif\" alt=\"Stift+Papier\" title=\"Kommentar bearbeiten\" /></a></td>
	<td class=\"".$class."\" align=\"center\" nowrap=\"nowrap\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Kommentar l&ouml;schen\" class=\"fx_opener\" style=\"border:0; float:left;\" align=\"left\" /><div class=\"fx_content tr_red\" style=\"width:60px; display:none;\"><a href=\"#foo\" onclick=\"AjaxRequest.send('modul=01acp&ajaxaction=delcomment&id=".$row['id']."');\">Ja</a> - <a href=\"#foo\">Nein</a></div></td>
	</tr>\n\n";
	}

$return .= "</table>";	
$return .= "<p>
<input type=\"checkbox\" name=\"delselected\" value=\"1\" />
<input type=\"submit\" value=\"Ausgewählte Kommentare löschen\" class=\"input\" />
Es erfolgt <b>keine</b> weitere Abfrage!
</p>";
$return .= "</form>";
	
return $return;
}







// Kommentare auflisten (ACP)
/*$query			gültiger MySQL-Query für die Kommentarliste
  $parent_child		parent oder child-Posts?

RETURN: Liste in kompletter HTML-Tabelle
  */
function getCommentPostList($query,$parent_child="parent"){
global $filename,$modul,$module,$mysql_tables,$_GET;

if(!isset($_GET['commentsites'])) $_GET['commentsites'] = "";

if($parent_child == "child"){
	$postid = "subpostid";
	$functionname = "Child";
	}
else{
	$postid = "postid";
	$functionname = "Parent";
	}

$return = "<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">

	<tr>
		<td class=\"tra\"><b>Kommentar zu</b></td>
		<td class=\"tra\" align=\"center\"><b>Kommentare</b></td>
		<td class=\"tra\" width=\"25\">&nbsp;<!-- Kommentare ansehen --></td>
		<td class=\"tra\" width=\"25\">&nbsp;<!-- Alle Kommentare löschen --></td>
	</tr>";
	
$count		= 0;
$cmenge		= 0;
$subcmenge	= 0;
$list = mysql_query($query);
while($row = mysql_fetch_array($list)){
	if(!isset($row['postid']) || isset($row['postid']) && empty($row['postid'])) $row['postid'] = $_GET['postid'];
	if(!isset($row['subpostid'])) $row['subpostid'] = "";

	if($parent_child == "child")
		$listcountcomments = mysql_query("SELECT id FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul='".mysql_real_escape_string($modul)."' AND subpostid='".$row['subpostid']."'");
	else
		$listcountcomments = mysql_query("SELECT id FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul='".mysql_real_escape_string($modul)."' AND postid='".$row['postid']."'");
	$cmenge = mysql_num_rows($listcountcomments);
	
	// Kommentare mit Subpostid vorhanden?
	if($parent_child != "child"){
		$listcountsubcomments = mysql_query("SELECT id FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul='".mysql_real_escape_string($modul)."' AND postid='".$row['postid']."' AND subpostid != '' AND subpostid != '0'");
		$subcmenge = mysql_num_rows($listcountsubcomments);
		}
	
	if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
	$ptitel = call_user_func("_".$module[$modul]['modulname']."_getComment".$functionname."Title",$row[$postid]);
	$return .= "\n\n    <tr>
	<td class=\"".$class."\">".$ptitel."</td>
	<td class=\"".$class."\" align=\"center\">".$cmenge."</td>";
	
	if($parent_child == "child" && isset($row['subpostid']) && !empty($row['subpostid']) && $row['subpostid'] != 0)
		$return .= "        <td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;postid=".$row['postid']."&amp;subpostid=".$row['subpostid']."\"><img src=\"images/icons/icon_show.gif\" alt=\"Auge\" title=\"Kommentar ansehen\" /></a></td>";
	elseif($subcmenge > 0)
		$return .= "        <td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;showsub=1&amp;postid=".$row['postid']."\"><img src=\"images/icons/icon_show.gif\" alt=\"Auge\" title=\"Kommentar ansehen\" /></a></td>";
	else
		$return .= "        <td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;postid=".$row['postid']."\"><img src=\"images/icons/icon_show.gif\" alt=\"Auge\" title=\"Kommentar ansehen\" /></a></td>";
	
	$return .= "        <td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;postid=".$row['postid']."&amp;subpostid=".$row['subpostid']."&amp;do=deleteall&amp;ptitel=".$ptitel."&amp;commentsites=".$_GET['commentsites']."\"><img src=\"images/icons/icon_delete.gif\" alt=\"rotes Kreuz\" title=\"Kommentar l&ouml;schen\" /></a></td>
	</tr>\n\n";
	}
$return .= "\n</table>\n<br />";

return $return;
}







// BB-Code-Funktion für Kommentare
/*$text				Text, der geparst werden soll
  $urls				URLs per HTML verlinke? (1/0)
  $bbc				BB-Code aktiviert? (1/0)
  $smilies			Smilies aktivieren? (1/0)

RETURN: Komplette Liste (HTML)
  */
/*******************************************************************************************
* Original BB-Code-Funktion: Autor: C by Michael Müller, 30.07.2003, 17:55 - www.php4u.net *
* Edited by Michael Lorer, 07.08.2008 - www.01-scripts.de                                  *
*******************************************************************************************/
function bb_code_comment($text,$urls,$bbc,$smilies){
global $smiliedir;

$max_lang = 150;				// max. Wortlänge
$word_replace = "-<br />";		// Wörter trennen mit

// Zitate umschließen mit
$header_quote = "<br /><blockquote>&raquo; ";
$footer_quote = "</blockquote>";


// alles was die Codeblöcke nicht betrifft
// zu lange Wörter kürzen
$lines = explode("\n", $text);
$merk = $max_lang;
for($n=0;$n<count($lines);$n++){
	$words = explode(" ",$lines[$n]);
	$count_w = count($words)-1;
	if($count_w >= 0){
		for($i=0;$i<=$count_w;$i++){
			$max_lang = $merk;
			$tword = trim($words[$i]);
			$tword = preg_replace("/\[(.*?)\]/si", "", $tword);
			$all = substr_count($tword, "http://") + substr_count($tword, "https://") + substr_count($tword, "www.") + substr_count($tword, "ftp://");
			if($all > 0){
				$max_lang = 200;
				}
				
			if(strlen($tword)>$max_lang){
				$words[$i] = chunk_split($words[$i], $max_lang, $word_replace);
				$length = strlen($words[$i])-5;
				$words[$i] = substr($words[$i],0,$length);
				}
			}
			
		$lines[$n] = implode(" ", $words);
		}
	else
		$lines[$n] = chunk_split($lines[$n], $max_lang, $word_replace);
	}
	
$text = implode("\n", $lines);

// URLs umformen
if($urls == 1){
	$text = preg_replace('"(( |^)((ftp|http|https){1}://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)"i',
	'<a href="\1" target="_blank">\\1</a>', $text);
	$text = preg_replace('"( |^)(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)"i',
	'\\1<a href="http://\2" target="_blank">\\2</a>', $text);
	}

// BB-Code
if($bbc == 1){
	$text = preg_replace("/\[b\](.*?)\[\/b\]/si",
	"<b>\\1</b>", $text);
	$text = preg_replace("/\[i\](.*?)\[\/i\]/si",
	"<i>\\1</i>", $text);
	$text = preg_replace("/\[u\](.*?)\[\/u\]/si",
	"<u>\\1</u>", $text);
	$text = preg_replace("/\[center\](.*?)\[\/center\]/si",
	"<p align=\"center\">\\1</p>", $text);
	$text = preg_replace("/\[img\]http:\/\/(.*?)\[\/img\]/si",
	"<img src=\"http://\\1\" alt=\"Verlinkte Bilddatei\" />", $text);
	$text = preg_replace("/\[img\](.*?)\[\/img\]/si",
	"<img src=\"http://\\1\" alt=\"Verlinkte Bilddatei\" />", $text);
	$text = preg_replace("/\[quote\](.*?)\[\/quote\]/si",
	$header_quote.'\\1'.$footer_quote, $text);
	$text = preg_replace("/\[url=http:\/\/(.*?)\](.*?)\[\/url\]/si",
	"<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $text);
	$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si",
	"<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $text);
	$text = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/si",
	"<a href=\"mailto:\\1\">\\2</a>", $text);
	}
	
// Smilies
if($smilies == 1){
	$text = str_replace(":eek:", "<img src=\"".$smiliedir."1.gif\" alt=\"Smilie :eek:\" style=\"border:0;\" />", $text);
	$text = str_replace(":D", "<img src=\"".$smiliedir."2.gif\" alt=\"Smilie :D\" style=\"border:0;\" />", $text);
	$text = str_replace(":p", "<img src=\"".$smiliedir."3.gif\" alt=\"Smilie :p\" style=\"border:0;\" />", $text);
	$text = str_replace(":?:", "<img src=\"".$smiliedir."4.gif\" alt=\"Smilie :?:\" style=\"border:0;\" />", $text);
	$text = str_replace("8)", "<img src=\"".$smiliedir."5.gif\" alt=\"Smilie 8)\" style=\"border:0;\" />", $text);
	$text = str_replace(":(", "<img src=\"".$smiliedir."6.gif\" alt=\"Smilie :(\" style=\"border:0;\" />", $text);
	$text = str_replace(":x:", "<img src=\"".$smiliedir."7.gif\" alt=\"Smilie :x:\" style=\"border:0;\" />", $text);
	$text = str_replace(":O_o:", "<img src=\"".$smiliedir."8.gif\" alt=\"Smilie :0_o:\" style=\"border:0;\" />", $text);
	$text = str_replace(":o", "<img src=\"".$smiliedir."9.gif\" alt=\"Smilie :o\" style=\"border:0;\" />", $text);
	$text = str_replace(":lol:", "<img src=\"".$smiliedir."10.gif\" alt=\"Smilie :lol:\" style=\"border:0;\" />", $text);
	$text = str_replace(":x(", "<img src=\"".$smiliedir."11.gif\" alt=\"Smilie :x(\" style=\"border:0;\" />", $text);
	$text = str_replace(":no:", "<img src=\"".$smiliedir."12.gif\" alt=\"Smilie :no:\" style=\"border:0;\" />", $text);
	$text = str_replace(":-:", "<img src=\"".$smiliedir."13.gif\" alt=\"Smilie :-:\" style=\"border:0;\" />", $text);
	$text = str_replace(":rolleyes:", "<img src=\"".$smiliedir."14.gif\" alt=\"Smilie :rolleyes:\" style=\"border:0;\" />", $text);
	$text = str_replace(";(", "<img src=\"".$smiliedir."15.gif\" alt=\"Smilie ;(\" style=\"border:0;\" />", $text);
	$text = str_replace(":)", "<img src=\"".$smiliedir."16.gif\" alt=\"Smilie :)\" style=\"border:0;\" />", $text);
	$text = str_replace(";)D", "<img src=\"".$smiliedir."17.gif\" alt=\"Smilie\" style=\"border:0;\" />", $text);
	$text = str_replace("X-X", "<img src=\"".$smiliedir."18.gif\" alt=\"Smilie X-X\" style=\"border:0;\" />", $text);
	$text = str_replace(";)", "<img src=\"".$smiliedir."19.gif\" alt=\"Smilie ;)\" style=\"border:0;\" />", $text);
	$text = str_replace(":yes:", "<img src=\"".$smiliedir."20.gif\" alt=\"Smilie :yes:\" style=\"border:0;\" />", $text);
	}

$text = nl2br($text);
return $text;
}








// Userstatistiken holen
/*$userid			UserID, zu der die Infos geholt werden sollen

RETURN: Array(
			statcat[x] 		=> "Statistikbezeichnung für Frontend-Ausgabe"
			statvalue[x] 	=> "Auszugebender Wert"
			)
  */
function getUserstats($userid){
global $mysql_tables;

if(isset($userid) && is_integer(intval($userid))){
	$picmenge = $filemenge = 0;
	list($picmenge) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['files']." WHERE type='pic' AND uid='".mysql_real_escape_string($userid)."'"));
	list($filemenge) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM ".$mysql_tables['files']." WHERE type='file' AND uid='".mysql_real_escape_string($userid)."'"));
	
	$ustats[] = array("statcat"	=> "Hochgeladene Bilder:",
						"statvalue"	=> $picmenge);
	$ustats[] = array("statcat"	=> "Hochgeladene Dateien:",
						"statvalue"	=> $filemenge);
	return $ustats;
	}
else
	return false;
}







// Usernamen oder alle vorhandenen Userdaten holen
/*$uid				Userinformationen für User mit der ID $uid;
  $login			Userinfos für den eingeloggten Benutzer (main.php) TRUE / FALSE

RETURN: Array mit den Userdaten. Name entspricht MySQL-Spaltennamen (ohne ModulPräfix)
		Es werden nur globale Berechtigungenund die jeweiligen Modul-Berechtigungen geladen
  */
function getUserdata($uid,$login=FALSE){
global $modul,$mysql_tables;

$list = mysql_query("SELECT modul,idname FROM ".$mysql_tables['rights']." WHERE (modul = '01acp' OR modul='".mysql_real_escape_string($modul)."') AND is_cat='0'");
while($row = mysql_fetch_array($list)){
	$loadrights[] = $row['modul']."_".$row['idname'];
	$loadrightnames[$row['modul']."_".$row['idname']] = $row['idname'];
	}

if($login)
	$list = mysql_query("SELECT id,username,mail,password,level,lastlogin,startpage,sperre,".implode(",",$loadrights)." FROM ".$mysql_tables['user']." WHERE id='".mysql_real_escape_string($_SESSION['01_idsession'])."' AND password='".mysql_real_escape_string($_SESSION['01_passsession'])."' LIMIT 1");
elseif(!empty($uid) && $uid > 0 && is_numeric($uid))
	$list = mysql_query("SELECT id,username,mail,password,level,lastlogin,startpage,sperre,".implode(",",$loadrights)." FROM ".$mysql_tables['user']." WHERE id='".mysql_real_escape_string($uid)."' LIMIT 1");
$fieldmenge = mysql_num_fields($list);
while($row = mysql_fetch_array($list)){

	if($row['sperre'] == 0){
		for($i=0;$i < $fieldmenge;$i++){ 
			if(isset($loadrightnames[mysql_field_name($list, $i)]) && !empty($loadrightnames[mysql_field_name($list, $i)]))
				$userdata[$loadrightnames[mysql_field_name($list, $i)]] = stripslashes($row[mysql_field_name($list, $i)]);
			else
				$userdata[mysql_field_name($list, $i)] = stripslashes($row[mysql_field_name($list, $i)]);
			}
		}
	else{
		$userdata['id'] = 0;
		$userdata['sperre'] = 1;
		}
	}
return $userdata;
}









// Individuelle Userfields holen
/*$uid				Userinformationen für User mit der ID $uid;
  $fields			Kommasperierte Rechte-Liste mit den GENAUEN MySQL-Spaltennamen!

RETURN: Array mit den Daten aus den übergebenen Userfields
  */
function getUserdatafields($uid,$fields){
global $modul,$mysql_tables;

$list = mysql_query("SELECT modul,idname FROM ".$mysql_tables['rights']." WHERE (modul = '01acp' OR modul='".mysql_real_escape_string($modul)."') AND is_cat='0'");
while($row = mysql_fetch_array($list)){
	$loadrights[] = $row['modul']."_".$row['idname'];
	$loadrightnames[$row['modul']."_".$row['idname']] = $row['idname'];
	}

$list = mysql_query("SELECT ".$fields." FROM ".$mysql_tables['user']." WHERE id='".mysql_real_escape_string($uid)."' LIMIT 1");
$fieldmenge = mysql_num_fields($list);
while($row = mysql_fetch_array($list)){

	for($i=0;$i < $fieldmenge;$i++){ 
		if(isset($loadrightnames[mysql_field_name($list, $i)]) && !empty($loadrightnames[mysql_field_name($list, $i)]))
			$userdata[$loadrightnames[mysql_field_name($list, $i)]] = stripslashes($row[mysql_field_name($list, $i)]);
		else
			$userdata[mysql_field_name($list, $i)] = stripslashes($row[mysql_field_name($list, $i)]);
		}
	}
return $userdata;
}








// Individuelle Userfields holen (weniger Querys)
/*$fields			Kommasperierte Rechte-Liste mit den GENAUEN MySQL-Spaltennamen!

RETURN: Mehrdimensionaler Array mit den Daten aus den übergebenen Userfields
		Struktur:
		-Userid
			-field
			-field
			-...
		-Userid
			-field
			-field
			-....
  */
function getUserdatafields_Queryless($fields){
global $modul,$mysql_tables;

$list = mysql_query("SELECT modul,idname FROM ".$mysql_tables['rights']." WHERE (modul = '01acp' OR modul='".mysql_real_escape_string($modul)."') AND is_cat='0'");
while($row = mysql_fetch_array($list)){
	$loadrights[] = $row['modul']."_".$row['idname'];
	$loadrightnames[$row['modul']."_".$row['idname']] = $row['idname'];
	}

$list = mysql_query("SELECT id,".$fields." FROM ".$mysql_tables['user']."");
$fieldmenge = mysql_num_fields($list);
while($row = mysql_fetch_array($list)){

	for($i=0;$i < $fieldmenge;$i++){ 
		if(isset($loadrightnames[mysql_field_name($list, $i)]) && !empty($loadrightnames[mysql_field_name($list, $i)]))
			$userdata[$row['id']][$loadrightnames[mysql_field_name($list, $i)]] = stripslashes($row[mysql_field_name($list, $i)]);
		else
			$userdata[$row['id']][mysql_field_name($list, $i)] = stripslashes($row[mysql_field_name($list, $i)]);
		}
	}
return $userdata;
}









// Captcha ausgeben
/*
RETURN: HTML-Ausgabe des Spamschutzes und erstellen der Ergebnis-Session-Var $_SESSION['antispam01']
  */
function create_Captcha(){
global $picuploaddir;

return "<img src=\"".$picuploaddir."secimg.php\" alt=\"Sicherheitscode (Spamschutz)\" title=\"Sicherheitscode: Anti-Spam-System\" />";
}









// Kommentar in DB hinzufügen und übergebene Werte überprüfen
/*$autor			Formulardaten (Name des Kommentar-Autors)
  $email_form		Formulardaten (E-Mail-Adresse zum Kommentar)
  $url_form			Formulardaten (Übergebene URL)
  $comment			Formulardaten (eigentlicher Kommentartext)
  $antispam			Formulardaten (Spamschutz-Ergebnis)
  $deaktivieren		Formulardaten (BBC/Smilies deaktivieren?)
  $postid			Formulardaten (Was für einem Post ist der Kommentar zugeordnet?)
  $uid				Formulardaten (UID - unique)
  
RETURN: $message mit Erfolgs/Fehler-Nummer
  */
function insert_Comment($autor,$email_form,$url_form,$comment,$antispam,$deaktivieren,$postid,$uid,$subpostid=0){
global $mysql_tables,$settings,$_SESSION,$modul,$filename,$names,$flag_utf8;
$zcount = $zcount2 = $zcount1 = 0;

// Zensur-Funktion
if($settings['comments_zensur'] == 1 && !empty($settings['comments_badwords']) &&
   isset($comment) && !empty($comment)){
   	$badwords = array();
	$badwords = explode("\n",$settings['comments_badwords']);	
	foreach ($badwords as &$badword){
		$badword = trim($badword);
		}
	
	$comment = str_replace($badwords, '***', $comment,$zcount1);
	
   	$specialchars = array(
        'A' => '(A|4|@|\?|^)',
        'B' => '(B|8|\|3|ß|b|l³|\|>|13)',
        'C' => '(C|\(|\[|\<|©|¢)',
        'D' => '(D|\|\)|\|\]|Ð|1\))',
        'E' => '(E|3|€|&|£)',
        'F' => '(F|\|=|PH|\|\*\|\-\||\|\"|ƒ|l²)',
        'G' => '(G|6|&|9)',
        'H' => '(H|\|\-\||#|\}\{|\]\-\[|\/\-\/)',
        'I' => '(I|\!|1|\||\]\[)',
        'J' => '(J|_\||¿)',
        'K' => '(K|\|\<|\|\{|\|\(|X)',
        'L' => '(L|1|\|_|£|\||\]\[_)',
        'M' => '(M|\/\\\/\\|\/v\\|\|V\||\]V\[|\|\\\/\||AA|\[\]V\[\]|\|11|\/\|\\|\^\^|\(V\)|\|Y\|)',
        'N' => '(N|\|\\\||\/\\\/|\/V|\|V|\/\\\\\/|\|1|2|?|\(\\\))',
        'O' => '(O|0|9|\(\)|\[\]|\*|\°|\<\>|ø|\{\[\]\})',
        'P' => '(P|\|°|\|\>|\|\*|\[\]D|\]\[D|\|²|\|\?|\|D)',
        'Q' => '(Q|0_|0)',
        'R' => '(R|2|\|2|\1²|®|\?)',
        'S' => '(S|5|\$|§|\?)',
        'T' => '(T|7|\+|†|\'\]\[\'|\|)',
        'U' => '(U|\|_\||µ|\[_\]|v)',
        'V' => '(V|\\\/|\|\/|\\\||\\\')',
        'W' => '(W|\\\/\\\/|VV|\\A\/|\\\\\'|uu|\\\^\/|\\\|\/)',
        'X' => '(X|\>\<|\)\(|\}\{|\%|\?|\]\[)',
        'Y' => '(Y|\`\/|°\/|9|¥)',
        'Z' => '(Z|2|\"\/_)',
        'Ä' => '(Ä|43|°A°)',
        'Ö' => '(Ö|03|°O°)',
        'Ü' => '(Ü|\|_\|3|°U°)',
		);
   	
   	foreach ($badwords as &$badword){
		$parts = str_split($badword, 1);
		$parts = str_ireplace(array_keys($specialchars), $specialchars, $parts);
		$badword = '/(?<=\b)('.implode('\W*',$parts).')(?=\b)/im';
		}	

	$comment = trim(preg_replace($badwords, '***', $comment,-1,$zcount2));
	
	$zcount = $zcount1+$zcount2;
   	}

if(isset($autor) && !empty($autor) && 
   isset($comment) && !empty($comment) && 
   (isset($antispam) && md5($antispam) == $_SESSION['antispam01'] && $settings['spamschutz'] == 1 || $settings['spamschutz'] == 0) &&
   ($settings['comments_zensur'] == 0 || empty($settings['comments_badwords']) || ($settings['comments_zensur'] == 1 && !empty($settings['comments_badwords']) && ($settings['comments_zensurlimit'] == "-1" || $zcount < $settings['comments_zensurlimit'])))){

	if(check_mail($email_form)) $email = mysql_real_escape_string(strip_tags($email_form)); else $email = "";
	if($settings['commentfreischaltung'] == 1) $frei = 0; else $frei = 1;
	if(!empty($url_form) && $url_form != "http://") $url = mysql_real_escape_string(strip_tags($url_form)); else $url = "";
	if($deaktivieren == 1){ $c_bbc = 0; $c_smilies = 0; }else{ $c_bbc = 1; $c_smilies = 1; }

	$clist = mysql_query("SELECT id,postid,uid,comment FROM ".$mysql_tables['comments']." WHERE postid='".mysql_real_escape_string($postid)."' AND uid='".mysql_real_escape_string($uid)."' OR postid='".mysql_real_escape_string($postid)."' AND comment='".mysql_real_escape_string(htmlentities($comment))."'");

	if(mysql_num_rows($clist) == 0){
	
		if($flag_utf8) $comment = utf8_decode(htmlentities($comment));
		// Eintragung in Datenbank vornehmen:
		$sql_insert = "INSERT INTO ".$mysql_tables['comments']." (modul,postid,subpostid,uid,frei,timestamp,ip,autor,email,url,comment,smilies,bbc) VALUES (
						'".mysql_real_escape_string($modul)."',
						'".mysql_real_escape_string($postid)."',
						'".mysql_real_escape_string($subpostid)."',
						'".mysql_real_escape_string($uid)."',
						'".$frei."',
						'".time()."',
						'".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."',
						'".mysql_real_escape_string(strip_tags($autor))."',
						'".$email."',
						'".$url."',
						'".mysql_real_escape_string(htmlentities($comment))."',
						'".$c_smilies."',
						'".$c_bbc."'
						)";
		$result = mysql_query($sql_insert) OR die(mysql_error());
		$jumpto_id = mysql_insert_id();

		if($frei == 0){
			$autor 		 = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$autor);
			$email 		 = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$email);
			$url 	 	 = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$url);
			$comment 	 = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$comment);
			
			$header = "From:".$settings['email_absender']."<".$settings['email_absender'].">\n";
			$email_betreff = $settings['sitename']." - Neuer Kommentar - bitte freischalten";
			$email_content = "Es wurde ein neuer Kommentar verfasst.
Kommentar zu: ".$_SERVER['HTTP_HOST'].$filename."?".$names['artid']."=".$postid."#01id".$postid."

Autor: ".$autor."
E-Mail-Adresse: ".$email."
Homepage: ".$url."

Kommentar:
".$comment."

Loggen Sie sich in den Administrationsbereich ein um den Kommentar freizuschalten:
".$settings['absolut_url']."/01acp/
---
Webmailer";

			$list = mysql_query("SELECT mail,01acp_editcomments FROM ".$mysql_tables['user']." WHERE 01acp_editcomments='1' AND sperre = '0' ORDER BY rand() LIMIT 10");
			while($row = mysql_fetch_array($list)){
				@mail(stripslashes($row['mail']),$email_betreff,$email_content,$header);
				}
			
			$message = 1;
			}
		else $message = 2;
		}
	else $message = 3;
	}
else $message = 4;
	
return $message;
}









// RSS-"Framework" (Head/Footer) generieren
/*$titel			RSS-Feed-Titel
  $url				URL zur zu verlinkenden Datei
  $descr			RSS-Feed-Beschreibung
  
RETURN: Array mit $array['header'] für RSS-Kopfdaten und $array['footer'] für RSS-Abschluß
  */
function create_RSSFramework($titel,$url,$descr){
global $settings;

$return = FALSE;
if($settings['rss_aktiv'] == 1){
	$return['header'] = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<rss version=\"2.0\">
  <channel>
    <title>".stripslashes($titel)."</title>
    <link>".str_replace("&","&amp;",stripslashes($url))."</link>
    <description>".stripslashes($descr)."</description>
    <copyright>".$settings['rss_copyright']."</copyright>
    <language>".$settings['rss_sprache']."</language>
    <pubDate>".date("r")."</pubDate>
    <lastBuildDate>".date("r")."</lastBuildDate>
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <generator>".RSS_GENERATOR."</generator>
    <webMaster>".$settings['email_absender']."</webMaster>
";

	$return['footer'] = "
  </channel>
</rss>";
	}
	
return $return;
}









// Neue Versionsinfos der Module vom 01-Scripts.de-Server holen
/*$quelle				Quelldatei (URL)
  $ziel					Zieldatei (URL)
  
RETURN: TRUE
  */
function getXML2Cache($quelle,$ziel){
global $mysql_tables;

$errno = "";
$errstr = "";
$fp = @fsockopen(VINFO_WWW_HOST, 80, $errno, $errstr, 3);
if($fp){

	$zeilen = @file($quelle);
	if(is_array($zeilen)){
		$cachefile = fopen($ziel,"w");
		$write = implode('', $zeilen);
		$wrotez = fwrite($cachefile, $write);
		fclose($cachefile);
		
		mysql_query("UPDATE ".$mysql_tables['settings']." SET wert = '".time()."' WHERE idname = 'cachetime' LIMIT 1");
		}
	}

return TRUE;
}









// SQL-Daten parsen (bestimmte Ersetzungen vornehmen)
/*$dumpline				MySQL-Datenline
  $modulnr				Modulinstallationsnummer für MySQL-Tabelle
  $modulidname			Modul ID-Name
  
RETURN: TRUE
  */
function parse_SQLLines($dumpline,$modulnr,$modulidname){
global $instnr,$userdata;

$dumpline = ereg_replace("\r\n$", "\n", $dumpline);
$dumpline = ereg_replace("\r$", "\n", $dumpline);
$dumpline = ereg_replace("01prefix_", "01_".$instnr."_", $dumpline);
$dumpline = ereg_replace("01modulprefix_", "01_".$instnr."_".$modulnr."_", $dumpline);
$dumpline = ereg_replace("#modul_idname#", $modulidname, $dumpline);
$dumpline = ereg_replace("#UID_ADMIN_AKT#", $userdata['id'], $dumpline);
$dumpline = ereg_replace("#01ACP_VERSION_NR#", _01ACP_VERSION_NR, $dumpline);

return $dumpline;
}









// Von einem Post/Eintrag abhängige Kommentare löschen
/*$postid			ID des Eintrags, der gelöscht wurde
  
RETURN:	Kein Return
  */
function delComments($postid){
global $mysql_tables,$modul;

mysql_query("DELETE FROM ".$mysql_tables['comments']." WHERE modul='".mysql_real_escape_string($modul)."' AND postid='".mysql_real_escape_string($postid)."'");
}









// Von einem Sub-Post/Eintrag abhängige Kommentare löschen
/*$postid			ID des Eintrags, dessen Kommentare gelöscht werden sollen
  $subpostid		ID des Sub-Eintrags, dessen Kommentare gelöscht werden sollen
  
RETURN:	Kein Return
  */
function delSubPostComments($postid,$subpostid){
global $mysql_tables,$modul;

mysql_query("DELETE FROM ".$mysql_tables['comments']." WHERE modul='".mysql_real_escape_string($modul)."' AND postid='".mysql_real_escape_string($postid)."' AND subpostid='".mysql_real_escape_string($subpostid)."'");
}







// Umlaute ersetzen
/*$string			String in dem äöü durch &auml; &ouml; etc. ersetzt werden soll
  
RETURN:	String, in dem die Umlaute ersetzt wurden
  */
function parse_uml($string){
$array_search = array('ä', 'ü', 'ö', 'Ä', 'Ü', 'Ö');
$array_replace = array('&auml;', '&uuml;', '&ouml;', '&Auml;', '&Uuml;', '&Ouml;');

for($x=0;$x<count($array_search)-1;$x++){
	$string = str_replace($array_search[$x],$array_replace[$x],$string);
	}
	
return $string;
}







// Storage-Data aus Datenbank lesen und unserialize anwenden
/*$modul			Modul-IDname, dessen Daten geladen werden sollen

RETURN:	Array mit dem im serialized_data enthaltenen Daten
  */
function getStorageData($modul){
global $mysql_tables;

$list = mysql_query("SELECT serialized_data FROM ".$mysql_tables['module']." WHERE idname = '".mysql_real_escape_string($modul)."' LIMIT 1");
$row = mysql_fetch_assoc($list);

$return = unserialize($row['serialized_data']);

if(empty($return)){
	for($x=1;$x<=STORAGE_MAX;$x++){
		$return['field_'.$x] = "";
		}
	}

for($x=1;$x<=STORAGE_MAX;$x++){
	$return['field_'.$x] = stripslashes($return['field_'.$x]);
	}

return $return;
}






// Rekursiv alle Datei-Verzeichnisse auflisten
/* $parentid			Verzeichnis-ID des übergeordneten Verzeichnisses
   $deep				Aktuelle Tiefe
   $maxdeep				Maximale Tiefe (int)
   $callfunction		Name der Funktion, die zur sichtbaren Ausgabe von Daten aufgerufen werden soll
						An die Funktion wird $row als 1. Parameter übergeben
   $givedeeperparam		Weiterer Parameter, der als 3. Parameter an die in $callfunction angegebene Funktion weitergereicht wird

RETURN: true
  */
function getFileVerz_Rek($parentid,$deep=0,$maxdeep=-1,$callfunction="",$givedeeperparam=""){
global $mysql_tables;
$return = "";

// Abbruch, falls $deep = 0 erreicht wurde
if($maxdeep == 0) return true;

$list = mysql_query("SELECT * FROM ".$mysql_tables['filedirs']." WHERE parentid = '".mysql_real_escape_string($parentid)."' ORDER BY name");
while($row = mysql_fetch_assoc($list)){
	if(!empty($callfunction) && function_exists($callfunction)) $return .= call_user_func($callfunction,$row,$deep,$givedeeperparam);

	// Rekursion
	$return .= getFileVerz_Rek($row['id'],($deep+1),($maxdeep-1),$callfunction,$givedeeperparam);
	}

return $return;

}









// Ausgabe der Verzeichnisnamen (Aufruf über Rekursive Funktion) für SELECT-Felder
/*$row				Array mit allen MySQL-Feldern aus der File-Verzeichnis-Tabelle zur entsprechenden ID
  $deep				Aktuelle "Tiefe"
  $selected			Vorselektierter Wert

RETURN: <option>-Fields
  */
function echo_FileVerz_select($row,$deep,$selected=""){

$return = "";
$tab = "";
for($x=0;$x<($deep*2);$x++){
	$tab .= "-";
	}

if($row['id'] == $selected) $sel = " selected=\"selected\"";
else $sel ="";

$return .= "<option value=\"".$row['id']."\"".$sel.">".$tab.htmlentities(stripslashes($row['name']))."</option>\n";

return $return;

}










// Fügt weitere Parameter an einen übergeben Link an (es wird herausgefunden, ob zuerst ? oder & verwendet werden muss)
/*$links			Link, an den der Parameter angehängt werden soll
  $parameter		Ohne den ersten Parameter-Trenner (also ohne ? oder & am Anfang) Bei mehreren Parameter aber schon verwenden
  $js				Soll ein Link für eine Javascript-Funktion generiert werden? (& statt &amp;)

RETURN: Link inkl. angehängter Parameter
  */
function addParameter2Link($link,$parameter,$js=false){

if($js) $amp = "&";
else $amp = "&amp;";

if(strchr($link,"?"))
	return $link.$amp.$parameter;
else
	return $link."?".$parameter;

}










// Setzt einen Cookie via blind-image-Datei
/*$cookiename		Gewünschter Name für den Cookie
  $cookiewert		Gewünschter Wert für den Cookie
  $cookietime		Lebenszeit des Cookies

RETURN: <img>-Tag auf 01acp/system/set_a_cookie.php mit display:none (1x1px), in der der Cookie gesetzt wird.
  */
function set_a_cookie($cookiename,$cookiewert,$cookietime){
global $subfolder;

return "<img src=\"".$subfolder."01acp/system/set_a_cookie.php?cookiename=".$cookiename."&amp;cookiewert=".$cookiewert."&amp;cookietime=".$cookietime."\" height=\"1\" width=\"1\" alt=\" \" style=\"border:0; position:absolute; top:0; left:0;\" />";

}










// Benötigte Mootool und JS-Dateien / DOM ggf. laden
/*$mootools_use		Array, der die zu ladenden Bestandteile enthält
  $cookiewert		Gewünschter Wert für den Cookie

RETURN: true
  */
function load_js_and_moo($mootools_use){
global $mootools,$domready;

// Benötigte MooTools laden
if(isset($mootools_use) && is_array($mootools_use)){
	foreach($mootools_use as $use){
		if(isset($mootools[$use])){
			foreach($mootools[$use] as $include){
				echo $include."\n";
				}
			}
		}
	}

// DOMReady ausgeben
if(isset($mootools_use) && is_array($mootools_use)){
	echo "<script type=\"text/javascript\">
	window.addEvent('domready',function(){
	";
	foreach($mootools_use as $use){
		if(isset($domready[$use])){
			foreach($domready[$use] as $include){
				include_once($include);
				echo "\n\n";
				}
			}
		}
	include("system/js/domready-javas.js");
	echo "
    });
</script>\n";
	}

return true;
}










// Leere Link-Parameter aus Links entfernen
/*$link				Link, der bereinigt werden soll

RETURN: Bereinigter Link
  */
function parse_cleanerlinks($link,$js=false){

$url = parse_url($link);

$params = explode("&",$url['query']);

$url_parameter = array();
foreach($params as $param){
	$value = $key = "";
	list($key, $value) = explode("=", $param,2);
	
	if(!empty($value) && !empty($key)) $url_parameter[$key] = $value;
	}
	
$makelink = "";
if(isset($url['scheme']) && !empty($url['scheme'])) $makelink .= $url['scheme']."://";
if(isset($url['host']) && !empty($url['host'])) $makelink .= $url['host'];
if(isset($url['path']) && !empty($url['path'])) $makelink .= $url['path'];
if(!$js) $makelink .= "?".http_build_query($url_parameter, '', '&amp;');
  else	 $makelink .= "?".http_build_query($url_parameter, '', '&');
if(isset($url['fragment']) && !empty($url['fragment'])) $makelink .= "#".$url['fragment']; 

return $makelink;

}

?>