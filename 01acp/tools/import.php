<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Importiert Einträge aus dem 01-Newsscript V 2.1.0.x
	#fv.122#
-->
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="de" />

<title>01acp - IMPORTER</title>

<link rel="stylesheet" type="text/css" href="system/default.css" />
<script src="system/js/javas.js" type="text/javascript"></script>

</head>

<body>
<?PHP
if(!file_exists("../01_config.php")){
	echo "<p class=\"meldung_error\"><b>Fehler:</b> Bitte kopieren Sie diese Datei in das Verzeichnis
			<i>01acp/</i> und rufen Sie dann die Datei erneut auf.</p>";
	exit;
	}
?>
<div id="content" style="width:800px;">

<h1>01acp - Import von Eintr&auml;gen aus dem 01-Newsscript V 2.1.0.x</h1>

<?PHP
$flag_acp = TRUE;
include_once("system/headinclude.php");
include_once("system/functions.php");

if(isset($_REQUEST['newsscript_instnr']) && !empty($_REQUEST['newsscript_instnr'])){
	$instnr				= $mysqli->escape_string($_REQUEST['newsscript_instnr']);
	$table_users		= "01news_".$instnr."_users";
	$table_news			= "01news_".$instnr."_news";
	$table_newscat		= "01news_".$instnr."_newscat";
	$table_comments		= "01news_".$instnr."_comments";
	$table_settings		= "01news_".$instnr."_settings";
	$table_attachments	= "01news_".$instnr."_attachments";
	$table_pics			= "01news_".$instnr."_pics";
	}

//BB-Code-Funktion (nur für Import!)
function bb_code(&$text,$urls,$bbc){
/**********************************************************************************
* BB-Code-Funktion: Autor: C by Michael Müller, 30.07.2003, 17:55 - www.php4u.net *
* Edited by Michael Lorer, 01.04.2007, 20:37 - www.01-scripts.de                  *
**********************************************************************************/
      # Config #
      $max_lang = 150;//max. Wortlänge
      $word_replace = "-<br />";//Wörter werden getrennt

      // Header und Footer beschreiben, wie...
      // der farbige PHP-Code umschlossen wird
      $header_php = '<br /><b>PHP-CODE:</b><br /><code>';
      $footer_php = '</code>';

      // Zitate umschlossen werden
      $header_quote = '<br /><i><b>Zitat:</b><br />';
      $footer_quote = '</i>';

      // normaler code umschlossen wird
      $header_code = '<br /><b>CODE:</b><br /><pre>';
      $footer_code = '</pre>';

      #####################################################################

      # PHP-Code-Blöcke zwischenspeichern #
      $c = md5(time());
      $pattern = "/\[php\](.*?)\[\/php\]/si";
      preg_match_all ($pattern, $text, $results);
      for($i=0;$i<count($results[1]);$i++) {
          $text = str_replace($results[1][$i], $c.$i.$c, $text);
      }
      # PHP-Code-Blöcke zwischenspeichern #

      # alles, was die Codeblöcke nicht betrifft #
      // zu lange Wörter kürzen
      $lines = explode("\n", $text);
      $merk = $max_lang;
      for($n=0;$n<count($lines);$n++) {
          $words = explode(" ",$lines[$n]);
          $count_w = count($words)-1;
          if($count_w >= 0) {
              for($i=0;$i<=$count_w;$i++) {
                  $max_lang = $merk;
                  $tword = trim($words[$i]);
                  $tword = preg_replace("/\[(.*?)\]/si", "", $tword);
                  $all = substr_count($tword, "http://") + substr_count($tword, "https://") + substr_count($tword, "www.") + substr_count($tword, "ftp://");
                  if($all > 0) {
                      $max_lang = 200;
                  }
                  if(strlen($tword)>$max_lang) {
                      $words[$i] = chunk_split($words[$i], $max_lang, $word_replace);
                      $length = strlen($words[$i])-5;
                      $words[$i] = substr($words[$i],0,$length);
                  }
                }
                $lines[$n] = implode(" ", $words);
          } else {
              $lines[$n] = chunk_split($lines[$n], $max_lang, $word_replace);
          }
      }
      $text = implode("\n", $lines);
      //$text = nl2br($text);

      // URLs umformen
      if($urls == 1){
          $text = preg_replace('"(( |^)((ftp|http|https){1}://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)"i',
                                  '<a href="\1" target="_blank">\\1</a>', $text);
          $text = preg_replace('"( |^)(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)"i',
                                  '\\1<a href="http://\2" target="_blank">\\2</a>', $text);
      }

      // BB-Code
      if($bbc == 1) {
          $text = preg_replace("/\[b\](.*?)\[\/b\]/si",
                                  "<b>\\1</b>", $text);
          $text = preg_replace("/\[i\](.*?)\[\/i\]/si",
                                  "<i>\\1</i>", $text);
          $text = preg_replace("/\[u\](.*?)\[\/u\]/si",
                                  "<u>\\1</u>", $text);
          $text = preg_replace("/\[center\](.*?)\[\/center\]/si",
                                  "<p align=\"center\">\\1</p>", $text);
          $text = preg_replace("/\[list\](.*?)\[\/list\]/si",
                                  "<ul>\\1</ul>", $text);
          $text = preg_replace("/\[list=(.*?)\](.*?)\[\/list\]/si",
                                  "<ol type=\"\\1\">\\2</ol>", $text);
          $text = preg_replace("/\[\*\](.*?)\\n/si",
                                  "<li>\\1</li>", $text);
          $text = preg_replace("/\[color=(.*?)\](.*?)\[\/color\]/si",
                                  "<span style=\"color: \\1;\">\\2</span>", $text);
          $text = preg_replace("/\[size=(.*?)\](.*?)\[\/size\]/si",
                                  "<font size=\"\\1\">\\2</font>", $text);
          $text = preg_replace("/\[img\](.*?)\[\/img\]/si",
                                  "<img src=\"\\1\" alt=\"verlinktes Bild\" title=\"verlinktes Bild\" border=\"0\" />", $text);
          $text = preg_replace("/\[quote\](.*?)\[\/quote\]/si",
                                  $header_quote.'\\1'.$footer_quote, $text);
          $text = preg_replace("/\[code\](.*?)\[\/code\]/si",
                                  $header_code.'\\1'.$footer_code, $text);
          $text = preg_replace("/\[url=http:\/\/(.*?)\](.*?)\[\/url\]/si",
                                  "<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $text);
          $text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si",
                                  "<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $text);
          $text = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/si",
                                  "<a href=\"mailto:\\1\">\\2</a>", $text);
      }
    # alles, was die Codeblöcke nicht betrifft #

    # PHP-Code-Blöcke umwandeln #
      for($i=0;$i<count($results[1]);$i++) {
          ob_start();
          $results[1][$i] = str_replace("&lt;","<",$results[1][$i]);
          $results[1][$i] = str_replace("&gt;",">",$results[1][$i]);
          highlight_string(trim($results[1][$i]));
          $ht = ob_get_contents();
          ob_end_clean();
          $all = $header_php.$ht.$footer_php;
          if(function_exists("str_ireplace")) {
              $text = str_replace("[php]".$c.$i.$c."[/php]",$all,$text);
          } else {
              $text = str_replace("[php]".$c.$i.$c."[/php]",$all,$text);
              $text = str_replace("[PHP]".$c.$i.$c."[/PHP]",$all,$text);
          }
      }
    # PHP-Code-Blöcke umwandeln #

    return $text;
}

//Funktion zum entfernen der Smilie-Codes
function smilies(&$text){
$text = str_replace(":eek:", "", $text);
$text = str_replace(":D", "", $text);
$text = str_replace(":p", "", $text);
$text = str_replace(":?:", "", $text);
$text = str_replace("8)", "", $text);
$text = str_replace(":(", "", $text);
$text = str_replace(":x:", "", $text);
$text = str_replace(":O_o:", "", $text);
$text = str_replace(":o", "", $text);
$text = str_replace(":lol:", "", $text);
$text = str_replace(":x(", "", $text);
$text = str_replace(":no:", "", $text);
$text = str_replace(":-:", "", $text);
$text = str_replace(":rolleyes:", "", $text);
$text = str_replace(";(", "", $text);
$text = str_replace(":)", "", $text);
$text = str_replace(";)D", "", $text);
$text = str_replace("X-X", "", $text);
$text = str_replace(";)", "", $text);
$text = str_replace(":yes:", "", $text);

return $text;
}

//Bilderpfad extrahieren und Info-Funktion aufrufen
function parse_uploads($text){
global $settings;

    $text = preg_replace("/\[imgnormal\](.*?)\[\/imgnormal\]/ise",
                         "getimageinfo_old('\\1','none','".$settings['thumbwidth']."')", $text);
	$text = preg_replace("/\[imgleft\](.*?)\[\/imgleft\]/ise",
                         "getimageinfo_old('\\1','left','".$settings['thumbwidth']."')", $text);
    $text = preg_replace("/\[imgcenter\](.*?)\[\/imgcenter\]/ise",
                         "getimageinfo_old('\\1','center','".$settings['thumbwidth']."')", $text);
    $text = preg_replace("/\[imgright\](.*?)\[\/imgright\]/ise",
                         "getimageinfo_old('\\1','right','".$settings['thumbwidth']."')", $text);

    $text = preg_replace("/\[file\](.*?)\[\/file\]/sie",
                         "getattachment('\\1')", $text);

    return $text;
}

function getattachment($file){
global $attachmentuploaddir;

if(file_exists($attachmentuploaddir.$file)){
	return "<p align=\"left\"><a href=\"".$attachmentuploaddir.$file."\" target=\"_blank\">Anhang herunterladen</a></p>";
	}
else return "";
}

function getimageinfo_old($picture,$align,$resize){
global $picuploaddir;

//Bild vorhanden?
if(file_exists($picuploaddir.$picture)){

if(function_exists("gd_info")){ $gd = @gd_info(); }
else{ $gd = ""; }

//case 2 -> resize-Funktion
//case 1 -> normale img-Ausgabe ohne Funktion

//Infos über Bildgröße (check1)
$info = getimagesize($picuploaddir.$picture);
if($info[0] >= $info[1]){ $bigside = $info[0]; }else{ $bigside = $info[1]; }
if($bigside > $resize){ $check1 = 2; $bigger = 1; }
else{ $check1 = 1; }

//Infos über Filetype (check2)
$filetype = strtolower(substr($picture, strlen($picture)-3));
if($filetype == "gif"){ $check2 = 1; }
elseif($gd['JPG Support'] && $filetype == "jpg" OR $gd['PNG Support'] && $filetype == "png"){ $check2 = 2; }
else{ $check2 = 1; }

//GD-Support (check3)
if(!empty($gd['GD Version'])) $check3 = 2;
else $check3 = 1;

//Weitere Funktion aufrufen (2) oder normalen IMG-Tag ausgeben (1)
if($check1 == 2 && $check2 == 2 && $check3 == 2)
	{
	//case 2
	if($align == "left")
		{
		$return = "<a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$imgfunctionpath.$picture."&amp;size=".$resize."\" alt=\"hochgeladenes Bild\" title=\"Bild\" border=\"0\" align=\"left\" style=\"margin-right:15px; margin-bottom:15px; margin-top:15px;\" /></a>";
		}
	elseif($align == "center")
		{
		$return = "<p align=\"center\"><a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$imgfunctionpath.$picture."&amp;size=".$resize."\" alt=\"hochgeladenes Bild\" title=\"Bild\" border=\"0\" align=\"middle\" style=\"margin-right:15px; margin-bottom:15px; margin-top:15px; margin-left:15px;\" /></a></p>";
		}
	elseif($align == "right")
		{
		$return = "<a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$imgfunctionpath.$picture."&amp;size=".$resize."\" alt=\"hochgeladenes Bild\" title=\"Bild\" border=\"0\" align=\"right\" style=\"margin-left:15px; margin-bottom:15px; margin-top:15px;\" /></a>";
		}
	elseif($align == "none")
		{
		$return = "<a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir."showpics.php?img=".$imgfunctionpath.$picture."&amp;size=".$resize."\" alt=\"hochgeladenes Bild\" title=\"Bild\" border=\"0\" /></a>";
		}
	}
else
	{
	//case 1
	//Resize?
	if($bigger == 1)
		{
		//Resize-Infos holen
		//K-Faxtor:
		$k = $bigside/$resize;
		//Seiten berechnen:
		$picwidth = $info[0]/$k;
		$picheight = $info[1]/$k;
		}
	else
		{
		$picwidth = $info[0];
		$picheight = $info[1];
		}

	if($align == "left")
		{
		$return = "<a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir.$picture."\" alt=\"hochgeladenes Bild\" title=\"Bild\" width=\"".$picwidth."\" height=\"".$picheight."\" border=\"0\" align=\"left\" style=\"margin-right:15px; margin-bottom:15px; margin-top:15px;\" /></a>";
		}
	elseif($align == "center")
		{
		$return = "<p align=\"center\"><a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir.$picture."\" alt=\"hochgeladenes Bild\" title=\"Bild\" width=\"".$picwidth."\" height=\"".$picheight."\" border=\"0\" align=\"middle\" style=\"margin-right:15px; margin-bottom:15px; margin-top:15px; margin-left:15px;\" /></a></p>";
		}
	elseif($align == "right")
		{
		$return = "<a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir.$picture."\" alt=\"hochgeladenes Bild\" title=\"Bild\" width=\"".$picwidth."\" height=\"".$picheight."\" border=\"0\" align=\"right\" style=\"margin-left:15px; margin-bottom:15px; margin-top:15px;\" /></a>";
		}
	elseif($align == "none")
		{
		$return = "<a href=\"".$picuploaddir.$picture."\" target=\"_blank\"><img src=\"".$picuploaddir.$picture."\" alt=\"hochgeladenes Bild\" title=\"Bild\" width=\"".$picwidth."\" height=\"".$picheight."\" border=\"0\" /></a>";
		}
	}
	return $return;
	}//Ende: file_exists()
else return ""; 
}

// Schritt 5 - MySQL-Import von Newseinträgen, Benutzern und Kommentaren
if(isset($_REQUEST['step']) && $_REQUEST['step'] == 5 &&
   isset($_REQUEST['newsscript_instnr']) && !empty($_REQUEST['newsscript_instnr']) &&
   isset($_REQUEST['modul']) && !empty($_REQUEST['modul'])){

    $insertquery = "";
	$uc = 0;
	// Benutzer
	// Vorhandene Benutzer in Array einlesen
	$iusers_id[] = 0;
	$iusers_username[] = "";
	$iusers_mail[] = "";
	$list = $mysqli->query("SELECT id,username,mail FROM ".$mysql_tables['user']." WHERE id!='1' AND id != '0'");
	while($row = $list->fetch_assoc()){
		$iusers_id[] = $row['id'];
		$iusers_username[] = $row['username'];
		$iusers_mail[] = $row['mail'];
		}
	$list = $mysqli->query("SELECT * FROM ".$table_users." WHERE id!='1' AND id != '0'");
	while($row = $list->fetch_assoc()){
		if(!in_array($row['id'],$iusers_id) && !in_array($row['username'],$iusers_username) && !in_array($row['mail'],$iusers_mail)){
			if($uc > 0) $insertquery .= ",";
			$insertquery .= "(
							'".$row['id']."',
							'".$mysqli->escape_string($row['username'])."',
							'".$mysqli->escape_string($row['mail'])."',
							'".pwhashing(create_NewPassword(6))."',
							'".$row['level']."',
							'',
							'0'
							)";
			$uc++;
			}
		}
		
	if($uc > 0){
		$sql_insert = "INSERT INTO ".$mysql_tables['user']." (id,username,mail,password,level,lastlogin,sperre) VALUES ".$insertquery.";";
		//echo $sql_insert;
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		}
		
    $insertquery = "";
	$nc = 0;
	// Newseinträge
	// Vorhandene Einträge in Array einlesen
	$entrys[] = 0;
	$list = $mysqli->query("SELECT id FROM ".$mysql_tables['artikel']."");
	while($row = $list->fetch_assoc()){
		$entrys[] = $row['id'];
		}
	$list = $mysqli->query("SELECT * FROM ".$table_news."");
	while($row = $list->fetch_assoc()){
		if(!in_array($row['id'],$entrys)){
			if($nc > 0) $insertquery .= ",";
			
			// Haupttext für Import parsen:
		    if($row['auto_url'] == 1)	$urls = 1; 	else $urls = 0; 
		    if($row['bbc'] == 1)		$bbc = 1; 	else $bbc = 0;

			$newstext = stripslashes($row['text']);

		    if($row['html'] != 1) $newstext = nl2br($newstext);
			$newstext = bb_code($newstext,$urls,$bbc);
		    if($row['smilies'] == 1) $newstext = smilies($newstext);
		    $newstext = parse_uploads($newstext);
			
			$insertquery .= "(
							'".$row['id']."',
							'".$row['timestamp']."',
							'".$row['endtime']."',
							'".$row['frei']."',
							'".$row['hide']."',
							'".$mysqli->escape_string(stripslashes($row['icon']))."',
							'".$mysqli->escape_string(htmlentities(stripslashes($row['titel']),$htmlent_flags,$htmlent_encoding_acp))."',
							'".$row['newscatid']."',
							'".$mysqli->escape_string($newstext)."',
							'0',
							'',
							'".$row['comments']."',
							'0',
							'".$row['uid']."',
							'0',
							'0'
							)";
			$nc++;
			}
		}
		
	if($nc > 0){
		$sql_insert = "INSERT INTO ".$mysql_tables['artikel']." (id,timestamp,endtime,frei,hide,icon,titel,newscatid,text,autozusammen,zusammenfassung,comments,hide_headline,uid,static,hits) VALUES ".$insertquery.";";
		//echo $sql_insert;
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		}
		
    $insertquery = "";
	$cc = 0;
	// Kommentare
	$list = $mysqli->query("SELECT * FROM ".$table_comments."");
	while($row = $list->fetch_assoc()){
		if($cc > 0) $insertquery .= ",";
		$insertquery .= "(
						'".$mysqli->escape_string($modul)."',
						'".$row['newsid']."',
						'".$row['uid']."',
						'".$row['frei']."',
						'".$row['timestamp']."',
						'".$row['ip']."',
						'".$mysqli->escape_string($row['name'])."',
						'".$mysqli->escape_string($row['email'])."',
						'".$mysqli->escape_string($row['url'])."',
						'".$mysqli->escape_string($row['comment'])."',
						'".$row['smilies']."',
						'".$row['bbc']."'
						)";
		$cc++;
		}
		
	if($cc > 0){
		$sql_insert = "INSERT INTO ".$mysql_tables['comments']." (modul,postid,uid,frei,timestamp,ip,autor,email,url,comment,smilies,bbc) VALUES ".$insertquery.";";
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		}
?>
<h1>Schritt 5 - Benutzer, Newseintr&auml;ge &amp; Kommentare importieren</h1>

<p class="meldung_ok">
	Die Datenbankeintr&auml;ge f&uuml;r Benutzer, Newseintr&auml;ge &amp; Kommentare wurden importiert.<br />
	Datensätze (Benutzer): <?PHP echo $uc; ?><br />
	Datensätze (Eintr&auml;ge): <?PHP echo $nc; ?><br />
	Datensätze (Kommentare): <?PHP echo $cc; ?><br />
</p>

<p class="meldung_ok">
	<b>Der Import wurde erfolgreich abgeschlossen!</b><br />
	<a href="index.php">In den Administrationsbereich einloggen &raquo;</a><br />
	<b class="red">Importierte Benutzer m&uuml;ssen sich ein neues Passwort zuschicken lassen!</b>
</p>

<p class="meldung_hinweis">
	Bitte beachten Sie, dass es trotz des Imports zu Anzeigeproblemen bei &auml;lteren, importierten
	Eintr&auml;gen kommen kann.<br />
	Nehmen Sie ggf. manuelle Korrekturen an den Eintr&auml;gen vor.
</p>

<p class="meldung_error">
	<b class="red">Bitte l&ouml;schen Sie diese Datei aus Sicherheitsgr&uuml;nden von Ihrem Server!</b>
</p>
<?PHP
    }
// Schritt 4 - MySQL-Import von pics,files,newscats
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 4 &&
   isset($_REQUEST['newsscript_instnr']) && !empty($_REQUEST['newsscript_instnr']) &&
   isset($_REQUEST['modul']) && !empty($_REQUEST['modul'])){
   
    $insertquery = "";
	$c = 0;
	// Dateien
	$list = $mysqli->query("SELECT * FROM ".$table_attachments."");
	while($row = $list->fetch_assoc()){
		if($c > 0) $insertquery .= ",";
		if(empty($row['uid'])) $uid = 0;
		else $uid = $row['uid'];
		$insertquery .= "(
						'file',
						'".$mysqli->escape_string($modul)."',
						'".$row['orgname']."',
						'".$row['name']."',
						'".$row['size']."',
						'".$row['ext']."',
						'".$uid."'
						)";
		$c++;
		}
	$filecount = $c;
	// Bilder
	$piccount = 0;
	$list = $mysqli->query("SELECT * FROM ".$table_pics."");
	while($row = $list->fetch_assoc()){
		if($c > 0) $insertquery .= ",";
		if(empty($row['uid'])) $uid = 0;
		else $uid = $row['uid'];
		$insertquery .= "(
						'pic',
						'".$mysqli->escape_string($modul)."',
						'".$row['orgname']."',
						'".$row['name']."',
						'".$row['size']."',
						'".$row['ext']."',
						'".$uid."'
						)";
		$c++;
		$piccount++;
		}
	
	if($c > 0){
		$sql_insert = "INSERT INTO ".$mysql_tables['files']." (type,modul,orgname,name,size,ext,uid) VALUES ".$insertquery.";";
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		}
	
	// Newskategorien
	// Vorhandene Kategorien in Array einlesen
	$installed_cats[] = 0;
	$list = $mysqli->query("SELECT id FROM ".$mysql_tables['cats']."");
	while($row = $list->fetch_assoc()){
		$installed_cats[] = $row['id'];
		}
	$insertquery = "";
	$c = 0;
    $list = $mysqli->query("SELECT * FROM ".$table_newscat."");
	while($row = $list->fetch_assoc()){
		if(!in_array($row['id'],$installed_cats)){
			if($c > 0) $insertquery .= ",";
			$insertquery .= "(
							'".$row['id']."',
							'".$row['name']."',
							'".$row['catpic']."'
							)";
			$c++;
			}
		}
	if($c > 0){
		$sql_insert = "INSERT INTO ".$mysql_tables['cats']." (id,name,catpic) VALUES ".$insertquery.";";
		$result = $mysqli->query($sql_insert) OR die($mysqli->error);
		}
?>
<h1>Schritt 4 - Dateien, Bilder &amp; Newskategorien in DB importieren</h1>

<p class="meldung_ok">
	Die Datenbankeintr&auml;ge f&uuml;r Dateien, Bilder und Newskategorien wurden importiert.<br />
	Datensätze (Dateien): <?PHP echo $filecount; ?><br />
	Datensätze (Bilder): <?PHP echo $piccount; ?><br />
	Datensätze (Kategorien): <?PHP echo $c; ?><br />
</p>

<form action="import.php" method="post">
<div class="rundrahmen">

<p>
	Im n&auml;chsten Schritt werden die Benutzerkonten, Newseintr&auml;ge und Kommentare importiert.
</p>

<p>
	<input type="hidden" name="step" value="5" />
	<input type="hidden" name="newsscript_instnr" value="<?PHP echo $_REQUEST['newsscript_instnr']; ?>" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="submit" value="Weiter &raquo;" class="input" />
</p>
</div>
</form>
<?PHP
    }
// Schritt 3 - Aufforderungen + Anleitung die Dateien und Bilder zu kopieren
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 3 &&
   isset($_REQUEST['newsscript_instnr']) && !empty($_REQUEST['newsscript_instnr']) &&
   isset($_REQUEST['modul']) && !empty($_REQUEST['modul'])){
?>
<h1>Schritt 3 - Bilder &amp; Dateien &uuml;bernehmen</h1>

<p class="meldung_hinweis">
	Beim Kopieren der Dateien kann es n&ouml;tig sein, dass Sie die entsprechenden Dateien zuerst per FTP 
	aus dem Quellverzeichnis auf Ihren heimischen PC herunterladen und anschlie&szlig;end wieder per FTP in das 
	neue Zielverzeichnis hochladen.
</p>

<form action="import.php" method="post">
<div class="rundrahmen">
<ul>
	<li>
		Kopieren Sie alle Dateien aus dem Quell-Verzeichnis des 01-Newsscripts: <b>01news/01newsattachments/</b><br />
		in das Zielverzeichnis des 01acp: <b>01scripts/01files/</b>
	</li>
	<li>
		Kopieren Sie alle Bild-Dateien aus dem Quell-Verzeichnis des 01-Newsscripts: <b>01news/01newspicupload/</b><br />
		in das Zielverzeichnis des 01acp: <b>01scripts/01pics/</b>
	</li>
	<li>
		Kopieren Sie alle Kategoriebild-Dateien aus dem Quell-Verzeichnis des 01-Newsscripts: <b>01news/01newspicupload/catpics/</b><br />
		in das Zielverzeichnis des 01acp: <b>01scripts/01pics/catpics/</b>
	</li>
</ul>

<p>
	<input type="hidden" name="step" value="4" />
	<input type="hidden" name="newsscript_instnr" value="<?PHP echo $_REQUEST['newsscript_instnr']; ?>" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="submit" value="Weiter &raquo;" class="input" />
</p>
</div>
</form>

<p class="meldung_hinweis">
	Sollte es bei der sp&auml;teren Verwendung der Bilder zu Problemen kommen, setzen Sie bitte mit Ihrem
	FTP-Programm die <a href="http://de.wikipedia.org/wiki/Chmod" target="_blank">chmod</a>-Rechte 
	f&uuml;r die soeben kopierten Bilder und Dateien manuell auf <b>0777</b>.
</p>

<p class="meldung_error">
	<b class="red">Wenn Sie es noch nicht getan habe: Bitte legen Sie sp&auml;testens jetzt ein 
	Backup Ihrer Datenbank an, bevor Sie mit dem Import beginnen!</b><br />
	Informationen zum Anlegen eines Backups finden Sie <a href="http://www.01-scripts.de/mysql_backup.php" target="_blank">hier</a>.
</p>
<?PHP
	}
// Schritt 3: Fehlermeldung
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 3){
	echo "<p class=\"meldung_error\"><b>Fehler:</b> Bitte w&auml;hlen Sie ein Modul aus!<br />
			<a href=\"javascript:history.back();\">Zur&uuml;ck</a></p>";
	}
// Schritt 2 - Modul auswählen und Datenbank & Version überprüfen
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 2 &&
   isset($_GET['newsscript_instnr']) && !empty($_GET['newsscript_instnr'])){
   
    echo "<h1>Schritt 2 - Modul w&auml;hlen</h1>\n";

    // Verbindung zu einer Newsscript-Tabelle möglich?
	if($mysqli->query("SELECT COUNT(*) FROM ".$table_users."")){
		$list = $mysqli->query("SELECT idname,wert FROM ".$table_settings." WHERE idname = 'version' LIMIT 1");
		while($row = $list->fetch_assoc()){
			$version = $row['wert'];
			}
		// Version überprüfen
		if($version >= "2.1.0.0"){
			echo "<p class=\"meldung_erfolg\">Es konnte eine Verbindung zur Datenbank hergestellt 
					werden und Sie setzen die richtige Version des 01-Newsscripts ein.</p>\n\n";
   
			echo "<form action=\"import.php\" method=\"post\">\n<div class=\"rundrahmen\">";
			echo "<p>Bitte w&auml;hlen Sie das Modul aus, in das die Eintr&auml;ge importiert werden sollen:</p>";
			echo "<select name=\"modul\" size=\"1\" class=\"input_select\">\n";
			$c = FALSE;
			foreach($module as $mod){
				if($mod['modulname'] == "01article"){
					echo "<option value=\"".$mod['idname']."\">".$mod['instname']."</option>\n";
					$c = TRUE;
					}
				}
			echo "</select>";
			if($c)
				echo "\n<p><input type=\"hidden\" name=\"step\" value=\"3\" />
						<input type=\"hidden\" name=\"newsscript_instnr\" value=\"".$_GET['newsscript_instnr']."\" />
						<input type=\"submit\" value=\"Weiter &raquo;\" class=\"input\" /></p>";
			echo "\n</div>\n</form>";
			
			if(!$c)
				echo "<p class=\"meldung_error\">Bitte installieren Sie zuerst das Modul <i>01-Artikelsystem</i>!</p>";
			}
		}
	else
		echo "<p class=\"meldung_error\"><b>Fehler:</b> Es konnte keine Verbindung zu den Datenbanktabellen des 01-Newsscripts hergestellt werden.<br />
			<a href=\"javascript:history.back();\">Zur&uuml;ck</a></p>";
		
	}
// Schritt 2: Fehlermeldung
elseif(isset($_REQUEST['step']) && $_REQUEST['step'] == 2){
	echo "<p class=\"meldung_error\"><b>Fehler:</b> Bitte geben Sie die Installationsnummern des 01-Newsscripts ein!<br />
			<a href=\"javascript:history.back();\">Zur&uuml;ck</a></p>";
	}
// Schritt 1
else{
?>

<h1>Schritt 1</h1>

<p class="meldung_hinweis">
	<b>Bitte beachten Sie:</b><br />
	Ein erfolgreicher Import kann nur vorgenommen werden, wenn Sie das 01acp in die <b>gleiche MySQL-Datenbank</b>
	installiert haben, in der sich auch die MySQL-Tabellen des 01-Newsscripts befinden.<br />
	Sollte dies nicht der Fall sein beachten Sie bitte 
	<a href="http://www.01-scripts.de/board/thread.php?threadid=938" target="_blank">folgende Hinweise</a>.
</p>

<form action="import.php" method="get">
<div class="rundrahmen">
	<p>W&auml;hrend des Imports werden folgende Daten &uuml;bernommen:</p>
	<ul>
		<li>Newseintr&auml;ge, au&szlig;er ein Artikel mit der gleichen ID existiert bereits (<b class="red">OHNE Zusammenfassungstexte</b>; verwendete Smilies werden entfernt)</li>
		<li>News-Kategorien (inkl. Kategoriebilder, au&szlig;er eine Kategorie mit gleicher ID besteht bereits)</li>
		<li>Benutzeraccounts (<b class="red">AU&szlig;ER dem Administrator mit der Userid 1 und Benutzer deren Userid ebenfalls bereits exisitiert.<br />
			Berechtigungen und Passw&ouml;rter werden NICHT &uuml;bernommen und m&uuml;ssen vom Administrator nach dem Import erneut eingestellt werden!</b>)</li>
		<li>Kommentare</li>
		<li>Bilder &amp; Dateien</li>
	</ul>
	<p>Bitte geben Sie die Installationsnummer des <b>Newsscripts V 2.1.0.x</b> ein: <input type="text" name="newsscript_instnr" value="1" size="10" /></p>
	<p><input type="hidden" name="step" value="2" /><input type="submit" value="Weiter &raquo;" class="input" /></p>
</div>
</form>

<p class="meldung_error">
	<b class="red">Bitte legen Sie dringend ein Backup Ihrer Datenbank an, bevor Sie mit dem Import beginnen!</b><br />
	Informationen zum Anlegen eines Backups finden Sie <a href="http://www.01-scripts.de/mysql_backup.php" target="_blank">hier</a>.
</p>
<?PHP
	}
?>

</div>

<div id="footer" style="width:800px;">
	<p>&copy; 2006-<?PHP echo date("Y"); ?> by <a href="http://www.01-scripts.de" target="_blank">01-Scripts.de</a></p>
</div>

</body>
</html>