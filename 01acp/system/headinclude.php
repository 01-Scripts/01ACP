<?PHP
/*
	01ACP - Copyright 2008-2017 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo: 	Globale Variablendefinitionen, Verbindung zur MySQL-Tabelle aufbauen, Settings aus MySQL-DB
				auslesen, installierte Module auslesen, Modulspezifische Dateien includieren, Grundeinstellungen,
				Pfadangaben, Funktionen includen
				Datei wird sowohl im Frontpanel als auch im Adminbereich als erstes includiert
	#fv.132#
*/

//Session starten:
$flag_sessionbugfix = FALSE;					// Workaround fuer Session-Bug im FnacyUp-Fileuploader
if(session_id() == "" && $flag_sessionbugfix || !$flag_sessionbugfix) @session_start();

@error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);
@ini_set('session.bug_compat_warn', "0");
//@ini_set("register_globals", "0");

//@error_reporting(E_ALL ^ E_NOTICE);
@date_default_timezone_set("Europe/Berlin");

/* Globale Einstellungen und Definitionen */
if($flag_acp) $subfolder = "../";
include_once($subfolder."01_config.php");

// Zentrale MySQL-Tabellen
$mysql_tables['files'] 		= "01_".$instnr."_files";
$mysql_tables['filedirs']	= "01_".$instnr."_filedirs";
$mysql_tables['comments'] 	= "01_".$instnr."_comments";
$mysql_tables['menue'] 		= "01_".$instnr."_menue";
$mysql_tables['module'] 	= "01_".$instnr."_module";
$mysql_tables['rights'] 	= "01_".$instnr."_rights";
$mysql_tables['settings'] 	= "01_".$instnr."_settings";
$mysql_tables['user'] 		= "01_".$instnr."_user";

// Pfade
$admindir 					= $subfolder."01acp/";
$moduldir 					= $subfolder."01module/";
$basicdir					= $moduldir."_basics/";
if(isset($_REQUEST['modul']) && !empty($_REQUEST['modul'])) $modulpath = $moduldir.$_REQUEST['modul']."/";
else $modulpath = "";

$picuploaddir 				= $subfolder."01pics/";
$smiliedir 					= $picuploaddir."smilies/";
$attachmentuploaddir 		= $subfolder."01files/";
$catuploaddir 				= $picuploaddir."catpics/";


// Sonstige Variablen
$flag_modcheck 		= FALSE;
$flag_showacpRSS 	= TRUE;						// RSS-Feed auf der ACP-Startseite anzeigen?
$flag_credits       = TRUE;						// Credits-Link  im Footer des 01ACP anzeigen?
$flag_indivstorage	= FALSE;
$stid				= 0;						// Formular-ID für Storage-Formlare (automatisches ++)
$flag_oldfileupload = FALSE;					// Alten Datei-Upload für Filemanager nutzen?
$inst_module 		= array();
if(!defined('ENT_HTML401')) define('ENT_HTML401', 0);
$htmlent_flags		= ENT_COMPAT | ENT_HTML401;	// Standard-Flags für die htmlentities()-Funktion
$htmlent_encoding_acp = "ISO-8859-1";			// Standard-Encoding für alle htmlentities()-Funktionen innerhalb des ACP
// Standard-Encoding für alle htmlentities()-Funktionen im Frontend abhängig vo $flag_utf8:
if(isset($flag_utf8) && $flag_utf8 == TRUE && !isset($flag_acp))
	$htmlent_encoding_pub = "UTF-8";
else
	$htmlent_encoding_pub = "ISO-8859-1";
if(!isset($flag_nofunctions)) $flag_nofunctions = FALSE;
$forbidden_chars = array("ä","Ä","ö","Ö","ü","Ü","ß","-",".",";",",","_","/","\$","(",")","=","?","´","`","#","+","*","'","\\"," ");

define('ACPSTART_RSSFEED_URL', 'http://www.01-scripts.de/01scripts/01module/01article/01article.php?rss=show_rssfeed&modul=01article&catid=15');				// URL zum RSS-Feed, der auf der Startseite des ACP angezeigt werden soll
define('RSS_CACHEFILE', $admindir.'cache/01rss.xml');
define('THUMBWIDTH_CACHEFILE', $admindir.'cache/thumbwidth.php');
define('ACP_PER_PAGE', 15); 					// Einträge pro Seite im ACP
define('ACP_TB_WIDTH', 40); 					// Max. Kantenlänge von Bildern im ACP (1) (in showpics.php zusätzlich definieren)
define('ACP_TB_WIDTH200', 200); 				// Max. Kantenlänge von Bildern im ACP (größer) (z.B. Popup-Vorschau nach Hochladen und vor dem Löschen)
define('MIN_COMMENT_TIME', 5);					// Minimale Zeit, die nach dem Laden eines Kontaktformulars vergehen muss, bevor ein abgesendeter Kommentar angenommen wird (in Sekunden)
define('reCAPTCHA_THEME', 'light');				// Theme für reCAPTCHA (light, dark)
define('FILE_404_THUMB', '404thumb.gif');		// gif im Verzeichnis 01pics, dass angezeigt wird, wenn die eigentliche Datei nicht gefunden werden kann (gelöscht)
define('FILE_GIF_THUMB', 'gifthumb.gif');		// gif im Verzeichnis 01pics, dass statt des gif-Bildes bei Aufruf der showpics.php?hidegif=normal angezeigt wird
define('FILE_NO_THUMBS', 'no_thumbs.gif');		// gif im Verzeichnis 01pics, dass angezeigt wird, wenn kein Thumbnail vorhanden ist
define('PW_LAENGE', 6);							// Minimale Passwortlänge
define('RSS_GENERATOR', '01-ACP RSS-Generator');
define('CACHE_TIME', 600);						// Alle wieviel Sekunden soll die versionsinfo.xml vom Server abgerufen und gecached werden?
define('CACHE_TIME_01RSS', 43200);				// Alle wieviel Sekunden soll der 01-Scripts.de RSS-Feed vom Server abgerufen und gecached werden?
define('VINFO_WWW_HOST', 'www.01-scripts.de');
define('HTTP_VERSIONSINFO_DATEILINK', 'http://www.01-scripts.de/versionsinfo.xml');
define('LOKAL_VERSIONSINFO_DATEILINK', $admindir.'cache/vinfo.cache');
define('STORAGE_MAX', 100);


// Verfügbare MooTools
/*	MooTools, die verwendet werden sollen im Array $mootools_use[] übergeben.
	Die entsprechenden .js und .css-Files für die Module werden dann in der head.php automatisch integriert. */
$mootools['moo_core'][] 		= "<script src=\"system/js/mootools-core.js\" type=\"text/javascript\"></script>";		// V 1.2.3
$mootools['moo_more'][] 		= "<script src=\"system/js/mootools-more.js\" type=\"text/javascript\"></script>";		// V 1.2.3.1
$mootools['moo_request'][]		= "<script src=\"system/js/mootools-request.js\" type=\"text/javascript\"></script>";
$mootools['moo_calendar'][] 	= "<script src=\"system/js/mootools-calendar.js\" type=\"text/javascript\"></script>";
$mootools['moo_calendar'][] 	= "<link rel=\"stylesheet\" type=\"text/css\" href=\"system/js/mootools-calendar.css\" />";
$mootools['moo_remooz'][] 		= "<script src=\"system/js/mootools-remooz.js\" type=\"text/javascript\"></script>";
$mootools['moo_remooz'][] 		= "<link rel=\"stylesheet\" type=\"text/css\" href=\"system/js/mootools-remooz.css\" />";
$mootools['moo_fancyup'][]	 	= "<script src=\"system/js/Swiff.Uploader.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup'][]	 	= "<script src=\"system/js/Fx.ProgressBar.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup'][] 		= "<script src=\"system/js/mootools-more-lang.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup'][] 		= "<script src=\"system/js/FancyUpload2.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup_fm'][] 	= "<script src=\"system/js/Swiff.Uploader.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup_fm'][] 	= "<script src=\"system/js/Fx.ProgressBar.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup_fm'][] 	= "<script src=\"system/js/mootools-more-lang.js\" type=\"text/javascript\"></script>";
$mootools['moo_fancyup_fm'][] 	= "<script src=\"system/js/FancyUpload2.js\" type=\"text/javascript\"></script>";
$mootools['moo_rainbow'][]	 	= "<script src=\"system/js/mooRainbow.1.2b2.js\" type=\"text/javascript\"></script>";
$mootools['moo_rainbow'][] 		= "<link rel=\"stylesheet\" type=\"text/css\" href=\"system/js/mootools-rainbow.css\" />";

// DOMReady-Code
/*	Dateinamen der Dateien, die in den DOMReady-Bereich includiert werden sollen */
$domready['moo_rainbow'][]  	= "system/js/mootools-domready-rainbow.js";			// Color Picker
$domready['moo_calendar'][] 	= "system/js/mootools-domready-calendar.js";		// Date Picker
$domready['moo_slidev'][]		= "system/js/mootools-domready-slidev.js";			// Verikaler Slide-Effekt
$domready['moo_slideh'][]		= "system/js/mootools-domready-slideh.js";			// Horizontaler Slide-Effekt
$domready['moo_remooz'][]		= "system/js/mootools-domready-remooz.js";			// Remooz (Lightbox)
$domready['moo_sortable'][]		= "system/js/mootools-domready-sortables.js";		// Sortable (Listen sortieren))
$domready['moo_dragdrop'][]		= "system/js/mootools-domready-dragdrop.js";		// Drag & Drop
$domready['moo_fancyup'][]		= "system/js/mootools-domready-fancyup.js";			// Fancy Upload
$domready['moo_fancyup_fm'][]	= "system/js/mootools-domready-fancyup_fm.js";		// Fancy Upload (für Filemanager)


/* Verbindung zur MySQL-Datenbank aufbauen */
$mysqli = new mysqli($host, $user, $passw, $database);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// Globale functions.php einbinden
if(!$flag_nofunctions)
	include_once($admindir."system/functions.php");

// DB: Installierte Module in Array $module['idname'][] einlesen
if(!$flag_nofunctions)
	$module = getModuls($inst_module);
/*
Struktur von $module
	array()
		['modul->idname']
			['db-feld'] -> 'wert'
			['db-feld'] -> 'wert'
			...
		['modul->idname']
			['db-feld'] -> 'wert'
			['db-feld'] -> 'wert'
			...

Struktur von $inst_module
	array()
		['idname']
		['idname']
		...
*/

// ggf. Slashes aus $modul entfernen
if(isset($modul) && strchr($modul,"/") == "/")
    $modul = substr($modul,0,-1);

// Modul-Spezifische headinclude- & _functions-Datei einbinden
if(isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) && in_array($_REQUEST['modul'],$inst_module) OR
   isset($modul) && !empty($modul) && in_array($modul,$inst_module)){

	if(isset($_REQUEST['modul']) && !empty($_REQUEST['modul']) || empty($modul)) $modul = $_REQUEST['modul'];
	$flag_modcheck = TRUE;
	if(file_exists($modulpath."_headinclude.php")) include_once($modulpath."_headinclude.php");
	if(file_exists($modulpath."_functions.php")) include_once($modulpath."_functions.php");
	}else $modul = "01acp";

// DB: Einstellungen in Array $settings[] einlesen
$list = $mysqli->query("SELECT idname,wert FROM ".$mysql_tables['settings']." WHERE is_cat = '0' AND (modul = '01acp' OR modul = '".$mysqli->escape_string($modul)."')");
while($row = $list->fetch_assoc()){
	$settings[stripslashes($row['idname'])] = stripslashes($row['wert']);
	}



/* Auf MySQL-Daten basierende weitere Einstellungen & Standardwerte */

// Erlaubte Dateiendungen
// Bilder
$picendungen 			= explode(",",$settings['pic_end']);
$picsize 				= $settings['pic_size']*1000;

// Files
$attachmentendungen 	= explode(",",$settings['attachment_end']);
$attachmentsize 		= $settings['attachment_size']*1000;

// Storage-Daten aus DB holen
if($flag_indivstorage && !$flag_nofunctions) $storage = getStorageData($modul);

?>