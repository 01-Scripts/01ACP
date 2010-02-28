<?PHP
/* 
	01-Example - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01example
	Dateiinfo: 	Modulspezifische Funktionen
	
*/

/* SYNTAKTISCHER AUFBAU VON FUNKTIONSNAMEN BEACHTEN!!!
	ModulIDName_beliebigerFunktionsname()
	Beispiel: example_TolleFunktion($parameter){ ... }
*/

// Globale Funktionen - nötig!

// Funktion wird zentral aufgerufen, wenn ein Benutzer gelöscht wird.
/*$userid			UserID des gelöschten Benutzers
  $username			Username des gelöschten Benutzers
  $mail				E-Mail-Adresse des gelöschten Benutzers

RETURN: TRUE/FALSE
*/
function _01example_DeleteUser($userid,$username,$mail){
global $mysql_tables;

//...

return TRUE;
}







// String des Artikels, Beitrags, Bildes etc. dem der übergebene IdentifizierungsID zugeordnet ist
/*$postid			Beitrags-ID

RETURN: String mit dem entsprechenden Text
*/
function _01example_getCommentParentTitle($postid){
global $mysql_tables;

//...

return TRUE;
}











// Ausgabe für RSS-Feed. RSS-Header-Daten werden global zur Verfügung gestellt. Siehe 01example.rss
/*

RETURN: RSS-XML-Daten
*/
function _01example_RSS($show,$entrynrs,$cats){
global $mysql_tables,$settings,$modul,$names;

$rssdata = create_RSSFramework($settings['artikelrsstitel'],$settings['artikelrsstargeturl'],$settings['artikelrssbeschreibung']);
$write_text = "";

// LIMIT
if(isset($entrynrs) && is_numeric($entrynrs) && $entrynrs > 0)
	$limit = "";
else
	$limit = "";

//...	

$return = $rssdata['header'].$write_text.$rssdata['footer'];

return $return;
}

?>