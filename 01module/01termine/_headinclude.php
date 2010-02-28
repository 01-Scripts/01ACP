<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Modulspezifische Grundeinstellungen, Variablendefinitionen etc.
				Wird automatisch am Anfang jeden Modulaufrufs automatisch includiert.
*/

// Modul-Spezifische MySQL-Tabellen
$mysql_tables['artikel'] 	= "01_".$instnr."_".$module[$modul]['nr']."_article";
$mysql_tables['cats'] 		= "01_".$instnr."_".$module[$modul]['nr']."_articlecategory";

$addJSFile 	= "java.js";			// Zusätzliche modulspezifische JS-Datei (im Modulverzeichnis!)
$addCSSFile = "modul.css";			// Zusätzliche modulspezifische CSS-Datei (im Modulverzeichnis!)

// Welche PHP-Seiten sollen abhängig von $_REQUEST['loadpage'] includiert werden?
$loadfile['index'] 		= "index.php";			// Standardseite, falls loadpage invalid ist
$loadfile['article'] 	= "article.php";
$loadfile['category']	= "category.php";

// Weitere Pfadangaben
$iconpf 	= "images/icons/";		// Verzeichnis mit Icon-Dateien
$tempdir	= "templates/";			// Template-Verzeichnis

// Weitere Variablen
$comment_desc = "DESC";				// Sortierreihenfolge der Kommentare
define('CSS_CACHE_DATEI', $admindir.'cache/cache_css.css');

// Variablennamen-Deklaration
$names['artid']		= "artid";
$names['search']	= "search";
$names['catid']		= "catid";
$names['page']		= "page";
$names['cpage']		= "cpage";
$names['rss']		= "rss";		//Bei Änderung ist zusätzlich eine manuelle Änderung in 01article.php Zeile 31 nötig!



// 01-Artikelsystem Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
?>