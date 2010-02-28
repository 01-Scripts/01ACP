<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Modulspezifische Funktionen
	
*/

/* SYNTAKTISCHER AUFBAU VON FUNKTIONSNAMEN BEACHTEN!!!
	_ModulName_beliebigerFunktionsname()
	Beispiel: 
	if(!function_exists("_example_TolleFunktion")){
		_example_TolleFunktion($parameter){ ... }
		}
*/

// Globale Funktionen - nötig!

// Funktion wird zentral aufgerufen, wenn ein Benutzer gelöscht wird.
/*$userid			UserID des gelöschten Benutzers
  $username			Username des gelöschten Benutzers
  $mail				E-Mail-Adresse des gelöschten Benutzers

RETURN: TRUE/FALSE
*/
if(!function_exists("_01article_DeleteUser")){
function _01article_DeleteUser($userid,$username,$mail){
global $mysql_tables;

mysql_query("UPDATE ".$mysql_tables['artikel']." SET uid='0' WHERE uid='".mysql_real_escape_string($userid)."'");

return TRUE;
}
}








// String des Artikels, Beitrags, Bildes etc. dem der übergebene IdentifizierungsID zugeordnet ist
/*$postid			Beitrags-ID

RETURN: String mit dem entsprechenden Text
*/
if(!function_exists("_01article_getCommentParentTitle")){
function _01article_getCommentParentTitle($postid){
global $mysql_tables;

//return "SELECT titel FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($postid)."' LIMIT 1";
$list = mysql_query("SELECT titel FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($postid)."' LIMIT 1");
while($row = mysql_fetch_array($list)){
	return stripslashes($row['titel']);
	}
}
}







// String des Artikels, Beitrags, Bildes etc. dem der übergebene IdentifizierungsID zugeordnet ist
/*
RETURN: Array $input_fields mit $_POST-Werten
*/
if(!function_exists("_01article_getForm_DataArray")){
function _01article_getForm_DataArray(){
global $_POST;

$form_data = array("id"				=> $_POST['id'],
				   "starttime_date"	=> $_POST['starttime_date'],
				   "starttime_uhr"	=> $_POST['starttime_uhr'],
				   "endtime_date" 	=> $_POST['endtime_date'],
				   "endtime_uhr" 	=> $_POST['endtime_uhr'],
				   "icon" 			=> $_POST['icon'],
				   "titel" 			=> stripslashes($_POST['titel']),
				   "textfeld"		=> stripslashes($_POST['textfeld']),
				   "autozusammen" 	=> $_POST['autozusammen'],
				   "zusammenfassung"=> stripslashes($_POST['zusammenfassung']),
				   "comments" 		=> $_POST['comments'],
				   "hide_headline"	=> $_POST['hide_headline'],
				   "uid"			=> $_POST['uid']
				  );
if(is_array($_POST['newscat']))
	$form_data['newscat'] = implode(",",$_POST['newscat']);
else
	$form_data['newscat'] = "";

return $form_data;
}
}








// Ausgabe für RSS-Feed. RSS-Header-Daten werden global zur Verfügung gestellt. Siehe 01example.rss
/*

RETURN: RSS-XML-Daten
*/
if(!function_exists("_01article_RSS")){
function _01article_RSS($show,$entrynrs,$cats){
global $mysql_tables,$settings,$modul,$names;

$rssdata = create_RSSFramework($settings['artikelrsstitel'],$settings['artikelrsstargeturl'],$settings['artikelrssbeschreibung']);
$write_text = "";

// LIMIT
if(isset($entrynrs) && is_numeric($entrynrs) && $entrynrs > 0)
	$limit = mysql_real_escape_string($entrynrs);
else
	$limit = $settings['artikelrssanzahl'];

	
	
// RSS-Feed für KOMMENTARE
if(isset($show) && $show == "show_commentrssfeed" && $settings['artikelkommentarfeed'] == 1){
	// Newstitel in Array einlesen (um MySQL-Abfragen zu verringern)
	$list = mysql_query("SELECT id,titel FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0')");
	while($row = mysql_fetch_array($list)){
		$arttitel[$row['id']] = stripslashes(str_replace("&","&amp;",$row['titel']));
		}
		
	$list = mysql_query("SELECT postid,timestamp,autor,comment FROM ".$mysql_tables['comments']." WHERE modul='".$modul."' AND frei='1' ORDER BY timestamp DESC LIMIT ".mysql_real_escape_string($settings['artikelrssanzahl'])."");
	while($row = mysql_fetch_array($list)){

		if(substr_count($settings['artikelrsstargeturl'], "?") < 1)
			$echolink = $settings['artikelrsstargeturl']."?".$names['artid']."=".$row['postid']."#01id".$row['postid'];
		else
			$echolink = str_replace("&","&amp;",$settings['artikelrsstargeturl'])."&amp;".$names['artid']."=".$row['postid']."#01id".$row['postid'];	

		$echotext = stripslashes(str_replace("&","&amp;",$row['comment']));
		$echotext = bb_code_comment($echotext,1,1,0);
		
		$write_text .= "<item>
  <title>Neuer Kommentar zu ".$arttitel[$row['postid']]."</title>
  <link>".$echolink."</link>
  <description><![CDATA[".$echotext."]]></description>
  <author>".stripslashes(str_replace("&","&amp;",$row['autor']))."</author>
  <pubDate>".date("r",$row['timestamp'])."</pubDate>
  <guid>".$echolink."</guid>
</item>
";
		}
	$return = $rssdata['header'].$write_text.$rssdata['footer'];
	}
// RSS-Feed für ARTIKEL
elseif($settings['artikelrssfeedaktiv'] == 1){
	if(isset($cats) && !empty($cats) && substr_count($cats, ",") >= 1){
		$cats_array = explode(",",$cats);

		$add2query_cat = " 1=2 ";
		foreach($cats_array as $value){
			$add2query_cat .= " OR newscatid LIKE '%,".mysql_real_escape_string($value).",%' ";
			}
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND (".$add2query_cat.") ORDER BY timestamp DESC LIMIT ".$limit."";
		}
	elseif(isset($cats) && !empty($cats))
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') AND newscatid LIKE '%,".mysql_real_escape_string($cats).",%' ORDER BY timestamp DESC LIMIT ".$limit."";
	else
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE frei='1' AND hide='0' AND static='0' AND timestamp <= '".time()."' AND (endtime >= '".time()."' OR endtime = '0') ORDER BY timestamp DESC LIMIT ".$limit."";
	
	$list = mysql_query($query);
	while($row = mysql_fetch_array($list)){

		if(substr_count($settings['artikelrsstargeturl'], "?") < 1)
			$echolink = $settings['artikelrsstargeturl']."?".$names['artid']."=".$row['id']."#01id".$row['id'];
		else
			$echolink = str_replace("&","&amp;",$settings['artikelrsstargeturl'])."&amp;".$names['artid']."=".$row['id']."#01id".$row['id'];
	
		// Inhalt parsen
		if($settings['artikelrsslaenge'] == "short"){
			// Zusammenfassung only:
			if($row['autozusammen'] == 0 && !empty($row['zusammenfassung']))
				$echotext = stripslashes($row['zusammenfassung']);
			else
				$echotext = stripslashes(substr($row['text'],0,$settings['artikeleinleitungslaenge']));
				
			$echotext = str_replace("&","&amp;",$echotext);
			$echotext .= " ...";
			}
		else{
			// Komplett
			$echotext = stripslashes($row['text']);
			}

		// Pfade anpassen
		$echotext = str_replace("../01pics/",$settings['absolut_url']."01pics/",$echotext);
		$echotext = str_replace("../01files/",$settings['absolut_url']."01files/",$echotext);
		
		$username_array 	= getUserdatafields($row['uid'],"username,01acp_signatur");
		$username 			= stripslashes($username_array['username']);
		$signatur 			= "<p>".nl2br(stripslashes(str_replace("&","&amp;",$username_array['signatur'])))."</p>";

		$write_text .= "<item>
  <title>".stripslashes(str_replace("&","&amp;",$row['titel']))."</title>
  <link>".$echolink."</link>
  <description><![CDATA[".$echotext.$signatur."]]></description>
  <author>".$username."</author>
  <pubDate>".date("r",$row['timestamp'])."</pubDate>
  <guid>".$echolink."</guid>
</item>
";
		}
		
	$return = $rssdata['header'].$write_text.$rssdata['footer'];
	}
else{
	$return = $rssdata['header']."<item>Fehler: der RSS-Feed wurde deaktiviert</item>".$rssdata['footer'];
	}

return $return;
}
}








// Dropdown-Box aus angelegten Kategorien generieren (ohne Select-Tag)
/*
RETURN: Option-Elemente für Select-Formularelement
*/
if(!function_exists("_01article_CatDropDown")){
function _01article_CatDropDown(){
global $mysql_tables;

$list = mysql_query("SELECT id,name FROM ".$mysql_tables['cats']." ORDER BY name");
while($row = mysql_fetch_array($list)){
	$return .= "<option value=\"".$row['id']."\">".stripslashes($row['name'])."</option>\n";
	}
	
return $return;
}
}







// Aus CSS-Eigenschaften aus der DB eine CSS-Datei schreiben / cachen
/*
RETURN: TRUE
*/
if(!function_exists("_01article_CreateCSSCache")){
function _01article_CreateCSSCache($zieldatei){
global $settings;

$cachefile = fopen($zieldatei,"w");
$wrotez = fwrite($cachefile, $settings['csscode']);
fclose($cachefile);

return TRUE;
}
}

// 01-Artikelsystem Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
?>