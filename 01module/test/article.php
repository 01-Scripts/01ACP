<?PHP
/* 
	01-Artikelsystem V3 - Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo: 	Artikel: Übersicht, Bearbeiten, Erstellen
	
*/

// Berechtigungsabfragen
if((isset($_REQUEST['action']) && $_REQUEST['action'] == "newarticle" && $userdata['newarticle'] == 1) ||
   (isset($_REQUEST['action']) && $_REQUEST['action'] == "articles" && $userdata['editarticle'] >= 1) ||
   (isset($_REQUEST['action']) && $_REQUEST['action'] == "edit" && ($userdata['editarticle'] >= 1 || $userdata['staticarticle'] == 1)) ||
   (isset($_REQUEST['action']) && ($_REQUEST['action'] == "newstatic" || $_REQUEST['action'] == "statics") && $userdata['staticarticle'] == 1))
{
_01article_CreateCSSCache(CSS_CACHE_DATEI);

// Variablen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit"){
	$input_field['publish'] 	= "Übernehmen";
	$input_field['save'] 		= "Zwischenspeichern";
	}
else{
	$input_field['publish'] 	= "Veröffentlichen";
	$input_field['save'] 		= "Zwischenspeichern";
	}
if(!isset($_REQUEST['who'])) $_REQUEST['who'] = "";
	
$add_filename 	= "&amp;search=".$_GET['search']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."&amp;site=".$_GET['site']."";
$flag_overview 	= FALSE;
	
	
	
	
	
	
	
	
	
	
	
// Formular wurde abgeschickt - Daten überprüfen
$flag_parsed = FALSE;
if(isset($_POST['do']) && ($_POST['do'] == "save" || $_POST['do'] == "update") && 
	isset($_POST['textfeld']) && !empty($_POST['textfeld']) &&
	isset($_POST['titel']) && !empty($_POST['titel'])){
	//Auswertung der Fomurlardaten zur Eintragung in die Datenbank

	// Datum / Uhrzeit:
	if(isset($_POST['starttime_date']) && !empty($_POST['starttime_date']))
		$start_date 	= explode(".",$_POST['starttime_date']);
	else
		$start_date	= explode(".",date("d.m.Y"));
	if(isset($_POST['starttime_uhr']) && !empty($_POST['starttime_uhr']))
		$start_uhr 		= explode(".",$_POST['starttime_uhr']);
	else
		$start_uhr		= explode(".",date("G.i"));
		
	$start_mysqldate 	= mktime($start_uhr[0], $start_uhr[1], "0", $start_date[1], $start_date[0], $start_date[2]);
	
	if(isset($_POST['endtime_date']) && !empty($_POST['endtime_date']) && isset($_POST['endtime_uhr']) && !empty($_POST['endtime_uhr'])){
		$ende_date 		= explode(".",$_POST['endtime_date']);
		$ende_uhr 		= explode(".",$_POST['endtime_uhr']);
		$ende_mysqldate = mktime($ende_uhr[0], $ende_uhr[1], "0", $ende_date[1], $ende_date[0], $ende_date[2]);
		}
	else
		$ende_mysqldate = "0";
	
	// Kategorien parsen:
	if(isset($_POST['newscat']) && $_POST['newscat'] != "" && is_array($_POST['newscat']) && !in_array(0,$_POST['newscat'])){
		$newscats_string = ",";
		$newscats_string .= implode(",",$_POST['newscat']);
		$newscats_string .= ",";
		}
	else
		$newscats_string = 0;
		
	// Zusammenfassung
	if(!isset($_POST['zusammenfassung'])) $_POST['zusammenfassung'] = "";
	if(isset($_POST['autozusammen']) && $_POST['autozusammen'] == 1 && $settings['artikeleinleitung'] == 2 || 
	   isset($_POST['autozusammen']) && $_POST['autozusammen'] != 1 && (isset($_POST['zusammenfassung']) && empty($_POST['zusammenfassung']) || !isset($_POST['zusammenfassung'])) && $settings['artikeleinleitung'] == 2){
		$autozusammen = 1;
		$zusammen = "";
		}
	elseif($settings['artikeleinleitung'] >= 1){
		$autozusammen = 0;
		$zusammen = substr(stripslashes($_POST['zusammenfassung']),0,$settings['artikeleinleitungslaenge']);
		}
	else{
		$autozusammen = 0;
		$zusammen = "";
		}
		
	// Text parsen
	$text = stripslashes($_POST['textfeld']);
		
	// Freischaltung
	if($userdata['freischaltung'] == 1 && $settings['artikelfreischaltung'] == 1)
		$frei = 0;
	else
		$frei = 1;
		
	// Verstecken / Zwischenspeichern?
	if(isset($_POST['submit']) && $_POST['submit'] == $input_field['save'])
		$hide = 1;
	else
		$hide = 0;
		
	// Kommentare de/aktivieren
	if(isset($_POST['comments']) && $_POST['comments'] == 1)
		$comments = 0;
	else
		$comments = 1;
		
	// Hide Headline
	if(!isset($_POST['hide_headline'])) $_POST['hide_headline'] = 0;
	}

	
	
	
	
	
	
	
	
	

// NEUEN ARTIKEL / neue statische Seite anlegen (SPEICHERN)
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "newarticle" || $_REQUEST['action'] == "newstatic")){
	$flag_formular = TRUE;
	
	$input_do						= "save";
	switch($_REQUEST['action']){
	  case "newarticle":
		$input_field['site_titel'] 	= "Neuer Artikel";
		$input_field['bezeichnung'] = "Artikel";
		$input_field['next']		= "Neuen Artikel";
		$input_section				= "article";
		$input_section2				= "articles";
		$input_action 				= "newarticle";
		$flag_static				= 0;
		$_POST['hide_headline']		= 0;
	  break;
	  case "newstatic":
		$input_field['site_titel'] 	= "Neue Seite";
		$input_field['bezeichnung'] = "Seiten";
		$input_field['next']		= "Neue statische Seite";
		$input_section				= "static";
		$input_section2				= "statics";
		$input_action 				= "newstatic";
		$flag_static				= 1;
	  break;
	  }
	
	// Formular wurde abgeschickt - Daten überprüfen und in DB eintragen
	if(isset($_POST['do']) && $_POST['do'] == "save" && 
		isset($_POST['textfeld']) && !empty($_POST['textfeld']) &&
		isset($_POST['titel']) && !empty($_POST['titel'])){
			
		//Eintragung in Datenbank vornehmen:
		$sql_insert = "INSERT INTO ".$mysql_tables['artikel']." (timestamp,endtime,frei,hide,icon,titel,newscatid,text,autozusammen,zusammenfassung,comments,hide_headline,uid,static) VALUES (
						'".$start_mysqldate."',
						'".$ende_mysqldate."',
						'".$frei."',
						'".$hide."',
						'".mysql_real_escape_string($_POST['icon'])."',
						'".mysql_real_escape_string(htmlentities($_POST['titel']))."',
						'".mysql_real_escape_string($newscats_string)."',
						'".mysql_real_escape_string($text)."',
						'".$autozusammen."',
						'".mysql_real_escape_string($zusammen)."',
						'".$comments."',
						'".mysql_real_escape_string($_POST['hide_headline'])."',
						'".$userdata['id']."',
						'".$flag_static."'
						)";
		$result = mysql_query($sql_insert, $db) OR die(mysql_error());
		$saved_id = mysql_insert_id();
		
		if($saved_id > 0 && $hide == 1){
			// Artikel / Seite wurde NUR zwischengespeichert
			echo "<p class=\"meldung_erfolg\">".$input_field['site_titel']." wurde <b>zwischengespeichert.</b><br />
					Ihre Eingaben wurden noch <b>nicht</b> ver&ouml;ffentlicht!<br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$saved_id."&amp;static=".$flag_static."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<b><a href=\"".$filename."&amp;action=".$input_section2."&amp;do=publish&amp;id=".$saved_id."\">Jetzt ver&ouml;ffentlichen &raquo;</a></b></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		elseif($saved_id > 0 && $hide == 0 && $frei == 1){
			// Artikel / Seite wurde gespeichert UND veröffentlicht (sichtbar)
			echo "<p class=\"meldung_erfolg\"><b>".$input_field['site_titel']." wurde hinzugef&uuml;gt</b><br /><br />
					<a href=\"".$filename."&amp;action=".$input_action."\">".$input_field['next']." erstellen &raquo;</a><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$saved_id."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		elseif($saved_id > 0 && $hide == 0 && $frei == 0){
			// Artikel / Seite wurde gespeichert UND wartet auf die Freischaltung
			echo "<p class=\"meldung_erfolg\"><b>".$input_field['site_titel']." wurde gespeichert und muss nun vor seiner Ver&ouml;ffentlichung freigeschaltet werden.</b><br />
					Benutzer mit entsprechenden Rechten wurden bereits informiert!<br /><br />
					<a href=\"".$filename."&amp;action=".$input_action."\">".$input_field['next']." erstellen &raquo;</a><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$saved_id."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			
			// E-Mails an "Moderatoren" verschicken
			$header = "From:".$settings['email_absender']."<".$settings['email_absender'].">\n";
			$email_betreff = $settings['sitename']." - ".$input_field['site_titel']." - bitte freischalten";
			$emailbody = "Es wurde soeben ein neuer Artikel / eine neue Seite erstellt, die von Ihnen überprüft und freigeschaltet werden kann.
Bitte loggen Sie sich in den Administrationsbereich ein
".$settings['absolut_url']."/01acp/
und überprüfen Sie ihn.\n\n---\nWebmailer";
				
			// Es werden 10 beliebige Benutzer mit den entsprechenden Rechten per E-Mail informiert.
			$list = mysql_query("SELECT id,username,mail FROM ".$mysql_tables['user']." WHERE ".mysql_real_escape_string($modul)."_editarticle = '2' AND sperre = '0' AND 01acp_".mysql_real_escape_string($modul)." = '1' ORDER BY rand() LIMIT 10");
			while($row = mysql_fetch_array($list)){
		        mail(stripslashes($row['mail']),$email_betreff,$emailbody,$header);
				}
			}
		else
			echo "<p class=\"meldung_error\"><b>Es trat ein unvorhergesehener Fehler auf.<br />
					Bitte beachten Sie die MySQL-Fehlermeldung!</b></p>";
		
		if($saved_id > 0 && $flag_static == 1 && $hide == 0)
			echo "<p class=\"meldung_hinweis\">Die statische Seite wurde mit der ID <b>".$saved_id."</b> gespeichert.
					Sie k&ouml;nnen die Seite &uuml;ber diese ID einbinden.<br />
					Der PHP-Befehl dazu lautet:<br />
					<code>
					&lt;?PHP<br />
					\$subfolder = \"01scripts/\";<br />
					\$modul = \"".$modul."\";<br />
					<br />
					\$_GET['".$names['artid']."'] = ".$saved_id.";<br />
					include(\$subfolder.\"01module/\".\$modul.\"/01article.php\");<br />
					?&gt;</code>
				  </p>";
		}
	elseif(isset($_POST['do']) && $_POST['do'] == "save"){
		echo "<p class=\"meldung_error\"><b>Fehler: Sie haben nicht alle ben&ouml;tigten Felder
				(Titel und Textfeld) ausgef&uuml;llt!</b></p>";

		$form_data = _01article_getForm_DataArray();
		}

	// Formular ausgeben
	if($flag_formular){
		if(!isset($form_data)){
			$form_data = array("starttime_date"	=> date("d.m.Y"),
							   "starttime_uhr"	=> date("G.i"),
							   "endtime_date" 	=> "",
							   "endtime_uhr"	=> "00.00",
							   "icon" 			=> "",
							   "titel" 			=> "",
							   "textfeld"		=> "",
							   "autozusammen" 	=> 0,
							   "zusammenfassung"=> "",
							   "comments" 		=> 0,
							   "hide_headline"	=> 1,
							   "uid"			=> 0
							  );
			}
		include_once($modulpath."write_form.php");
		}
	}
	
	
	
	
	
	
	
	
	
// Artikel / Seite bearbeiten
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit" && ($userdata['editarticle'] >= 1 || $userdata['staticarticle'] == 1) &&
	   isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) &&
	   isset($_REQUEST['static']) && is_numeric($_REQUEST['static'])){
	
	$input_do						= "update";
	switch($_REQUEST['static']){
	  case "0":
		$input_field['site_titel'] 	= "Artikel bearbeiten";
		$input_field['bezeichnung'] = "Artikel";
		$input_field['next']		= "Neuen Artikel";
		$input_section				= "article";
		$input_section2				= "articles";
		$input_action 				= "edit";
		$input_action2 				= "newarticle";
		$flag_static				= 0;
		$_POST['hide_headline']		= 0;
	  break;
	  case "1":
		$input_field['site_titel'] 	= "Statische Seite bearbeiten";
		$input_field['bezeichnung'] = "Seite";
		$input_field['next']		= "Neue statische Seite";
		$input_section				= "static";
		$input_section2				= "statics";
		$input_action 				= "edit";
		$input_action2 				= "newstatic";
		$flag_static				= 1;
	  break;
	  }
	
	// Formular wurde abgeschickt - Daten überprüfen und in DB eintragen
	if(isset($_POST['do']) && $_POST['do'] == "update" && 
		isset($_POST['textfeld']) && !empty($_POST['textfeld']) &&
		isset($_POST['titel']) && !empty($_POST['titel'])){
			
		// Eintragung in Datenbank aktualisieren:
		if(mysql_query("UPDATE ".$mysql_tables['artikel']." SET 
						timestamp		= '".$start_mysqldate."',
						endtime			= '".$ende_mysqldate."',
						hide			= '".$hide."',
						icon			= '".mysql_real_escape_string($_POST['icon'])."',
						titel			= '".mysql_real_escape_string(htmlentities($_POST['titel']))."',
						newscatid		= '".mysql_real_escape_string($newscats_string)."',
						text			= '".mysql_real_escape_string($text)."',
						autozusammen	= '".$autozusammen."',
						zusammenfassung	= '".mysql_real_escape_string($zusammen)."',
						comments		= '".$comments."',
						hide_headline	= '".mysql_real_escape_string($_POST['hide_headline'])."'
						WHERE id = '".mysql_real_escape_string($_POST['id'])."'"))
			$saved = TRUE;
		else $saved = FALSE;
		
		if($saved && $hide == 1){
			// Artikel / Seite wurde NUR zwischengespeichert
			echo "<p class=\"meldung_erfolg\">".$input_field['bezeichnung']." wurde <b>zwischengespeichert.</b><br />
					Ihre Eingaben wurden noch <b>nicht</b> ver&ouml;ffentlicht!<br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$_POST['id']."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<b><a href=\"".$filename."&amp;action=".$input_section2."&amp;do=publish&amp;id=".$_POST['id']."\">Jetzt ver&ouml;ffentlichen &raquo;</a></b></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		elseif($saved && $hide == 0){
			// Artikel / Seite wurde gespeichert UND veröffentlicht (sichtbar)
			echo "<p class=\"meldung_erfolg\"><b>".$input_field['bezeichnung']." wurde gespeichert &amp; ver&ouml;ffentlicht (wenn eine Freischaltung nicht mehr n&ouml;tig ist).</b><br /><br />
					<a href=\"".$filename."&amp;action=edit&amp;id=".$_POST['id']."&amp;static=".$_POST['static']."\">".$input_field['bezeichnung']." erneut bearbeiten &raquo;</a><br />
					<a href=\"".$filename."&amp;action=".$input_action2."\">".$input_field['next']." erstellen &raquo;</a></p>";
			$flag_formular = FALSE;
			$flag_overview = TRUE;
			}
		else
			echo "<p class=\"meldung_error\"><b>Es trat ein unvorhergesehener Fehler auf.<br />
					Bitte beachten Sie die MySQL-Fehlermeldung!</b></p>";
		}
	elseif(isset($_POST['do']) && $_POST['do'] == "update"){
		echo "<p class=\"meldung_error\"><b>Fehler: Sie haben nicht alle ben&ouml;tigten Felder
				(Titel und Textfeld) ausgef&uuml;llt!</b></p>";

		$form_data = _01article_getForm_DataArray();
		
		include_once($modulpath."write_form.php");
		}
	else{
		$query = "SELECT * FROM ".$mysql_tables['artikel']." WHERE id = '".mysql_real_escape_string($_REQUEST['id'])."'";
		switch($_REQUEST['static']){
		  case "0":
			if($userdata['editarticle'] == 2)
				$query .= " AND static = '0'";
			elseif($userdata['editarticle'] == 1)
				$query .= " AND uid = '".$userdata['id']."' AND static = '0'";
		  break;
		  case "1":
			if($userdata['staticarticle'] == 1)
				$query .= " AND static = '1'";
		  break;
		  }
		$query .= " LIMIT 1";
		
		$list = mysql_query($query);
		while($row = mysql_fetch_array($list)){			
			$form_data = array("id"				=> $row['id'],
							   "starttime_date"	=> date("d.m.Y",$row['timestamp']),
							   "starttime_uhr"	=> date("G.i",$row['timestamp']),
							   "newscat"		=> $row['newscatid'],
							   "icon" 			=> $row['icon'],
							   "titel" 			=> stripslashes($row['titel']),
							   "textfeld"		=> stripslashes($row['text']),
							   "autozusammen" 	=> $row['autozusammen'],
							   "zusammenfassung"=> stripslashes($row['zusammenfassung']),
							   "hide_headline"	=> $row['hide_headline'],
							   "uid"			=> $row['uid']
							  );
			
			if($row['comments'] == 1) $form_data['comments'] = 0;
			else $form_data['comments'] = 1;
			
			if($row['endtime'] > 0){
				$form_data['endtime_date'] = date("d.m.Y",$row['endtime']);
				$form_data['endtime_uhr'] = date("G.i",$row['endtime']);
				}
			else{
				$form_data['endtime_date'] = "";
				$form_data['endtime_uhr'] = "00.00";
				}
			}
		
		if($form_data['id'] > 0)
			include_once($modulpath."write_form.php");
		else
			$flag_loginerror = true;
		}

	}
	
	
	
	
	
	
	
	
	
// Artikel-Übersicht
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "articles" || $_REQUEST['action'] == "statics") || $flag_overview){

if($flag_overview) $_REQUEST['action'] = $_REQUEST['who'];

	switch($_REQUEST['action']){
	  case "articles":
		$input_field['site_titel'] 	= "Artikel-&Uuml;bersicht / Artikel bearbeiten";
		$input_field['bezeichnung'] = "Artikel";
		$input_section				= "article";
		$input_action 				= "articles";
		$flag_static				= 0;
	  break;
	  case "statics":
		$input_field['site_titel'] 	= "Statische Seite (&Uuml;bersicht) / Bearbeiten";
		$input_field['bezeichnung']	= "Seite";
		$input_section				= "static";
		$input_action 				= "statics";
		$flag_static				= 1;
	  break;
	  }

	if(!isset($_GET['search'])) 	$_GET['search'] = "";
	if(!isset($_GET['sort'])) 		$_GET['sort'] = "";
	if(!isset($_GET['orderby'])) 	$_GET['orderby'] = "";
	
	$filename2 = $filename."&amp;action=".$input_action."&amp;serach=".$_GET['search']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."";
	
	// Artikel / Seiten freischalten
	if(isset($_GET['do']) && $_GET['do'] == "free" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) && $userdata['editarticle'] == 2){
		mysql_query("UPDATE ".$mysql_tables['artikel']." SET frei='1' WHERE id='".mysql_real_escape_string($_GET['id'])."' LIMIT 1");
		echo "<p class=\"meldung_erfolg\"><b>".$input_field['bezeichnung']." wurde freigeschaltet</b></p>";
		}
		
	// Artikel veröffentlichen
	if(isset($_GET['do']) && $_GET['do'] == "publish" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		mysql_query("UPDATE ".$mysql_tables['artikel']." SET hide='0' WHERE id='".mysql_real_escape_string($_GET['id'])."' AND uid = '".$userdata['id']."' LIMIT 1");
		echo "<p class=\"meldung_erfolg\"><b>".$input_field['bezeichnung']." wurde ver&ouml;ffentlicht</b></p>";
		}
		
	// Sicherheitsabfrage: Löschen
	if(isset($_GET['do']) && $_GET['do'] == "ask_del" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		echo "<p class=\"meldung_frage\">
				M&ouml;chten Sie <i>\"".stripslashes($_GET['titel'])."\"</i> wirklich l&ouml;schen?<br /><br />
				<b><a href=\"".$filename."&amp;action=".$input_action."&amp;do=do_del&amp;id=".$_GET['id'].$add_filename."\">JA</a> | <a href=\"javascript:history.back();\">NEIN</a></b>
			</p>";
		}
	
	// Löschen
	if(isset($_GET['do']) && $_GET['do'] == "do_del" && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		if($flag_static == 0 && $userdata['editarticle'] == 2){
			mysql_query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($_GET['id'])."' AND static = '0' LIMIT 1");
			delComments($_GET['id']);
			}
		elseif($flag_static == 0 && $userdata['editarticle'] == 1){
			mysql_query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($_GET['id'])."' AND uid = '".$userdata['id']."' AND static = '0' LIMIT 1");
			delComments($_GET['id']);
			}
		elseif($flag_static == 1 && $userdata['staticarticle'] == 1){
			mysql_query("DELETE FROM ".$mysql_tables['artikel']." WHERE id='".mysql_real_escape_string($_GET['id'])."' AND static = '1' LIMIT 1");
			delComments($_GET['id']);
			}
		}

	// Auflistung
?>
<h1><?PHP echo $input_field['site_titel']; ?></h1>
<?PHP if($input_action == "statics"){ ?>
<p><a href="javascript:hide_unhide('includestatic');"><img src="images/icons/kreis_frage.gif" alt="Fragezeichen" title="Statische Seiten einbinden - so gehts!" /> Statische Seiten einbinden (PHP-Code anzeigen)</a></p>
<p class="meldung_hinweis" id="includestatic" style="display:none;">Statische Seiten können Sie mit folgendem PHP-Code einbinden:<br />
	<code>&lt;?PHP<br />
$subfolder		= "01scripts/"; // Unterverzeichnis<br />
$modul			= "<?PHP echo $modul; ?>";<br />
<br />
$_GET['<?PHP echo $names['artid']; ?>']	= <b class="red">ID</b> // ID der Seite, die angezeigt werden soll<br />
include($subfolder."01module/".$modul."/01article.php");<br />
?&gt;
	</code>
</p>
<?PHP } ?>
<form action="<?PHP echo $filename; ?>" method="get" style="float:left; margin-right:20px;">
	<input type="text" name="search" value="<?PHP echo $input_field['bezeichnung']; ?> suchen" size="30" onfocus="clearField(this);" onblur="checkField(this);" class="input_search" /> <input type="submit" value="Suchen &raquo;" class="input" />
	<input type="hidden" name="action" value="<?PHP echo $input_action; ?>" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="hidden" name="loadpage" value="article" />
</form>
<?PHP
if($input_action == "articles"){
?>
<form action="<?PHP echo $filename; ?>" method="get">
	<select name="catid" size="1" class="input_select">
		<?PHP echo _01article_CatDropDown(); ?>
	</select>
	<input type="hidden" name="action" value="articles" />
	<input type="hidden" name="modul" value="<?PHP echo $modul; ?>" />
	<input type="hidden" name="loadpage" value="article" />
	<input type="submit" value="Go &raquo;" class="input" />
</form>
<?PHP
	}
?>

<?PHP
	if((!isset($_GET['orderby']) || isset($_GET['orderby']) && empty($_GET['orderby'])) && (!isset($_GET['sort']) || isset($_GET['sort']) && empty($_GET['sort'])))
		$sortorder = "DESC";
	elseif(isset($_GET['sort']) && $_GET['sort'] == "desc") $sortorder = "DESC";
	else $sortorder = "ASC";
	
	if(isset($_GET['search']) && !empty($_GET['search'])) $where = " WHERE (titel LIKE '%".mysql_real_escape_string($_GET['search'])."%' OR text LIKE '%".mysql_real_escape_string($_GET['search'])."%' OR zusammenfassung LIKE '%".mysql_real_escape_string($_GET['search'])."%') AND static = '".$flag_static."' ";
	elseif(isset($_GET['catid']) && !empty($_GET['catid']) && is_numeric($_GET['catid'])) $where = " WHERE newscatid LIKE '%,".mysql_real_escape_string($_GET['catid']).",%' ";
	else $where = " WHERE static = '".$flag_static."' ";
	
	if($userdata['editarticle'] == 1 && $flag_static == 0)
		$where .= " AND uid = '".$userdata['id']."' ";
		
	$where .= " AND (hide = '0' OR hide = '1' AND uid = '".$userdata['id']."') ";

	switch($_GET['orderby']){
	  case "id":
	    $orderby = "id";
	  break;
	  case "status":
	    $orderby = "frei ASC, hide DESC, id";
	  break;
	  case "titel":
	    $orderby = "titel";
	  break;
	  default:
	    $orderby = "timestamp";
	  break;
	  }

	$sites = 0;
	$query = "SELECT * FROM ".$mysql_tables['artikel']."".$where." ORDER BY ".$orderby." ".$sortorder;
	$query = makepages(&$query,&$sites,"site",ACP_PER_PAGE);

	// Fehlermeldung bei erfolgloser Suche
	if($sites == 0 && isset($_GET['search']) && !empty($_GET['search']))
		echo "<p class=\"meldung_error\">Es konnten leider kein passenden Eintr&auml;ge zu Ihrer Sucheingabe \"".stripslashes($_GET['search'])."\" gefunden werden!<br />
			Bitte probieren Sie es erneut.</p>";

?>
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
		<td class="tra" width="50" align="center"><b>ID</b>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;orderby=id&amp;sort=asc"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren (ASC)" /></a>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;orderby=id&amp;sort=desc"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
        <td class="tra" width="110"><b>Datum / Zeit</b>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=asc&amp;orderby=timestamp"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=desc&amp;orderby=timestamp"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra" width="200"><b>Status</b>			
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=asc&amp;orderby=status"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<!--<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=desc&amp;orderby=status"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>-->
		</td>
		<td class="tra"><b>Titel</b>			
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=asc&amp;orderby=titel"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo $filename; ?>&amp;action=<?PHP echo $input_action; ?>&amp;search=<?PHP echo $_GET['search']; ?>&amp;sort=desc&amp;orderby=titel"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra"><b>Benutzer</b></td>
		<?PHP if($userdata['freischaltung'] == 1){ ?><td class="tra" width="25">&nbsp;<!--Freischalten--></td><?PHP } ?>
		<td class="tra" width="25">&nbsp;<!--Bearbeiten--></td>
		<td class="tra" width="25" align="center"><!--Löschen--><img src="images/icons/icon_trash.gif" alt="M&uuml;lleimer" title="Datei l&ouml;schen" /></td>
    </tr>
<?PHP
	if($userdata['editarticle'] == 1 && $flag_static == 0)
		$artuserdata[$userdata['id']] = $userdata;
	else
		$artuserdata = getUserdatafields_Queryless("username");
	
	// Ausgabe der Datensätze (Liste) aus DB
	$count = 0;
	$list = mysql_query($query);
	while($row = mysql_fetch_array($list)){
		if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
		
		// Status-Bestimmung
		if($row['hide'] == 1)
			$status = "<b class=\"zwischengesp\">Zwischengespeichert</b>
						<a href=\"".$filename2."&amp;do=publish&amp;id=".$row['id']."\"><img src=\"images/icons/ok.gif\" alt=\"gr&uuml;ner Hacken\" title=\"Beitrag jetzt ver&ouml;ffentlichen\" /></a>";
		elseif($row['frei'] == 0){
			$status = "<b class=\"free_wait\">&Uuml;berpr&uuml;fung n&ouml;tig</b>";
			if($userdata['editarticle'] == 2) $status .="<a href=\"".$filename2."&amp;do=free&amp;id=".$row['id']."\"><img src=\"images/icons/ok.gif\" alt=\"gr&uuml;ner Hacken\" title=\"Beitrag jetzt freischalten\" /></a>";
			}
		elseif($row['endtime'] > 0 && $row['endtime'] > time())
			$status = "<b class=\"public\">Ver&ouml;ffentlicht bis ".date("d.m.Y, G:i",$row['endtime'])."</b>";
		elseif($row['endtime'] > 0 && $row['endtime'] < time())
			$status = "<b class=\"abgelaufen\">Abgelaufen seit ".date("d.m.Y, G:i",$row['endtime'])."</b>";
		elseif($row['endtime'] == 0 && $row['timestamp'] < time())
			$status = "<b class=\"public\">Ver&ouml;ffentlicht</b>";
		elseif($row['entdime'] == 0 && $row['timestamp'] > time())
			$status = "<b class=\"public\">Wird ver&ouml;ffentlicht</b>";
		
		echo "    <tr>
		<td class=\"".$class."\" align=\"center\">".$row['id']."</td>
		<td class=\"".$class."\">".date("d.m.Y - G:i",$row['timestamp'])."</td>
		<td class=\"".$class."\" align=\"center\">".$status."</td>
		<td class=\"".$class."\">".stripslashes($row['titel'])."</td>
		<td class=\"".$class."\">".$artuserdata[$row['uid']]['username']."</td>
		<td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;action=edit&amp;id=".$row['id']."&amp;static=".$row['static'].$add_filename."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Bearbeiten - Stift\" title=\"Eintrag bearbeiten\" style=\"border:0;\" /></a></td>
		<td class=\"".$class."\" align=\"center\"><a href=\"".$filename."&amp;action=".$input_action."&amp;do=ask_del&amp;id=".$row['id']."&amp;titel=".htmlentities($row['titel']).$add_filename."\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Eintrag l&ouml;schen\" style=\"border:0;\" /></a></td>
	</tr>";		
		}
	
	echo "</table>\n<br />";
	
	echo echopages($sites,"80%","site","action=".$input_action."&amp;search=".$_GET['search']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."");	
	}

}else $flag_loginerror = true;

// 01-Artikelsystem Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
?>