<?PHP
/* 
	01-Example - Copyright 2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01example
	Dateiinfo: 	Modulspezifische Grundeinstellungen, Variablendefinitionen etc.
				Wird automatisch am Anfang jeden Modulaufrufs automatisch includiert.
*/

// Modul-Spezifische MySQL-Tabellen
$mysql_tables['example'] = "01_".$instnr."_".$module[$modul]['nr']."_example";

$addJSFile 	= "";				// Zusätzliche modulspezifische JS-Datei (im Modulverzeichnis!)
$addCSSFile = "";				// Zusätzliche modulspezifische CSS-Datei (im Modulverzeichnis!)

// Welche PHP-Seiten sollen abhängig von $_REQUEST['loadpage'] includiert werden?
$loadfile['index'] 		= "index.php";			// Standardseite, falls loadpage invalid ist
$loadfile['test1'] 		= "index.php";
$loadfile['test2']		= "index.php";

// Weitere Pfadangaben
$tempdir	= "templates/";			// Template-Verzeichnis


?>