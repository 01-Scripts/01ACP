-- 01-Artikelsystem V3 - Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
-- Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
-- Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

-- Modul:		01article
-- Dateiinfo:	SQL-Befehle für die Erstinstallation des Artikelsystems V3
--  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  *  *

-- --------------------------------------------------------

SET AUTOCOMMIT=0;
START TRANSACTION;

-- --------------------------------------------------------

-- 
-- Neue Einstellungs-Kategorie für Modul anlegen
-- Einstellungen importieren
-- 

INSERT INTO 01prefix_settings (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,wert,nodelete,hide) VALUES 
('#modul_idname#','1','1','1','articlesettings','Einstellungen','','','','','','','0','0'),
('#modul_idname#','0','1','1','articleperpage','Artikel pro Seite','','text','5','','5','5','0','0'),
('#modul_idname#','0','1','2','artikelsuche','Suchfunktion','','aktivieren|deaktivieren','1|0','','1','1','0','0'),
('#modul_idname#','0','1','3','artikelfreischaltung','Freischaltung n&ouml;tig?','Ein neuer Artikel muss vor der Ver&ouml;ffentlichung durch einen Benutzer mit den entsprechenden Rechten freigeschaltet werden.','Freischaltung n&ouml;tig|Keine Freischaltung n&ouml;tig','1|0','','0','0','0','0'),
('#modul_idname#','0','1','4','artikeleinleitung','Einleitungstext verwenden?','','Erzwingen|Verwenden, wenn vorhanden|Unterdr&uuml;cken (deaktivieren)','1|2|0','','2','2','0','0'),
('#modul_idname#','0','1','5','artikeleinleitungslaenge','L&auml;nge des Einleitungstextes','','text','5','Zeichen','1000','1000','0','0'),
('#modul_idname#','0','1','6','archiv_time','Alte Beitr&auml;ge ins Archiv verschieben?','Geben Sie an nach wievielen Tagen Beitr&auml;ge ins Archiv verschoben werden sollen.','text','5','Tage','','','0','0'),

('#modul_idname#','1','3','2','csssettings','CSS-Einstellungen','','','','','','','0','0'),
('#modul_idname#','0','3','1','extern_css','Externe CSS-Datei','Geben Sie einen absoluten Pfad inkl. <b>http://</b> zu einer externen CSS-Datei an.\nLassen Sie dieses Feld leer um die nachfolgend definierten CSS-Eigenschaften zu verwenden.','text','50','','','','0','0'),
('#modul_idname#','0','3','2','csscode', 'CSS-Eigenschaften', 'Nachfolgende CSS-Definitionen werden nur ber&uuml;cksichtigt, wenn <b>keine</b> URL zu einer externen CSS-Datei eingegeben wurde!', 'textarea', '18|100', '', '', '/* Äußere Box für den gesamten Artikel-Bereich - DIV selber (id = 01article) */\r\n#_01article{\r\n	text-align:left;\r\n	}\r\n\r\n.box_out{\r\n	width: 800px;\r\n	margin: 0 auto;\r\n	text-align:left;\r\n	font-family: Verdana, Arial, Helvetica, sans-serif;\r\n	color:#000;\r\n	}\r\n\r\n/* Link-Definitionen (box_out) */\r\n.box_out a:link,.box_out a:visited  {\r\n	text-decoration: underline;\r\n	color: #000;\r\n}\r\n.box_out a:hover  {\r\n	text-decoration: none;\r\n	color: #000;\r\n}\r\n\r\n\r\n	\r\n	\r\n	\r\n\r\n\r\n\r\n	\r\n	\r\n.artikel_headline {\r\n	width:100%;\r\n	}\r\n	\r\n/* Artikel-Titel (innerhalb von artikel_headline) */\r\nh2.titel{\r\n	font-weight:bold;\r\n	font-size:1.4em;\r\n	color:#000;\r\n	margin-bottom:0;\r\n	padding-bottom:10px;\r\n	text-decoration:none;\r\n	}\r\n\r\n/* Link-Definitionen (titel) */\r\n.titel a:link, .titel a:visited {\r\n	text-decoration:none;\r\n	border:0;\r\n	color:#000;\r\n	}\r\n\r\n/* Informationen zum Artikel unterhalb des Titels */\r\n.headline_small, .footline_small {\r\n	width:100%;\r\n	font-size:10px;\r\n	text-decoration:none;\r\n	text-transform: uppercase;\r\n	font-family: Arial, Helvetica, sans-serif;\r\n	}\r\n	\r\n/* Link-Definitionen (artikel_headline) */\r\n.artikel_headline a:link,.artikel_headline a:visited  {\r\n	text-decoration: underline;\r\n	color: #000;\r\n}\r\n.artikel_headline a:hover  {\r\n	text-decoration: none;\r\n	color: #000;\r\n}\r\n\r\n\r\n\r\n\r\n	\r\n	\r\n\r\n	\r\n	\r\n	\r\n	\r\n	\r\n/* Aussehen der Artikel-DIV-Box */\r\n.artikel_textbox {\r\n	border-bottom: 1px dotted #999;\r\n	}\r\n	\r\n/* Aussehen der Artikel-Texte / der Artikel-DIV-Box */\r\n.artikel_text, body.mceContentBody {\r\n	font-size:10pt;\r\n	font-family: Verdana, Arial, Helvetica, sans-serif;\r\n	color:#000;\r\n	}\r\n\r\n/* Link-Definitionen (artikel_text) */\r\n.artikel_text a:link, .artikel_text a:visited, .mceContentBody a  {\r\n	text-decoration: underline;\r\n	color: #000;\r\n}\r\n.artikel_text a:hover  {\r\n	text-decoration: none;\r\n	color: #000;\r\n}\r\n	\r\n/* CSS-Eigenschaten für das Kategorie-Bild */\r\n\r\n.artikel_catimg img {\r\n	text-align:left;\r\n	float:left;\r\n	padding:5px;\r\n	padding-top:15px;\r\n	}\r\n\r\n	\r\n	\r\n	\r\n\r\n\r\n\r\n\r\n\r\n\r\n	\r\n\r\n/* Definition für TABELLE mit der Seiten-Navigation */\r\n.table_page {\r\n	padding-top:15px;\r\n	width:100%;\r\n	border:0;\r\n	}\r\n	\r\n/* Textdefinition für Seiten-Navigation (Vor, Zurück etc.) */\r\n.page_text {\r\n	font-size:12px;\r\n	text-decoration:none;\r\n	}\r\n	\r\n/* Definition für Tabellenabschnitt mit weiteren Feldern (Suchbox, RSS, Archiv, Seiten etc.) */\r\n.table_page_fields {\r\n	padding-top:15px;\r\n	width:auto;\r\n	border:0;\r\n	}\r\n	\r\n.table_page_fields td {\r\n	padding:5px;\r\n	}\r\n\r\n\r\n\r\n\r\n	\r\n	\r\n/* Tabelle für Archiv-Ansicht */\r\n.table_archiv {\r\n	width:100%;\r\n	border:0;\r\n	}\r\n\r\n.table_archiv td {\r\n	padding:5px;\r\n	}\r\n	\r\n.table_archiv_headline {\r\n	font-weight:bold;\r\n	}\r\n	\r\n	\r\n	\r\n\r\n	\r\n/* Definition für Kommentar-Box (Anzeige von Kommentaren) */\r\n.commentbitbox {\r\n	width:100%;\r\n	text-align:left;\r\n	border: 1px dotted #999;\r\n	padding:8px;\r\n	}\r\n	\r\n.comment_text {\r\n	font-size:12px;\r\n	text-decoration:none;\r\n	}\r\n\r\n/* Definition für \\"Kommentar-Hinzufügen\\"-Tabelle */\r\n.commentaddbox {\r\n	width:102%;\r\n	text-align:left;\r\n	border: 1px dotted #999;\r\n	padding:8px;\r\n	}\r\n	\r\n\r\n	\r\n\r\n	\r\n/* Aussehen von kleinem Text */\r\n.small, .small a:link,.small a:visited {\r\n	font-size:10px;\r\n	text-decoration:none;\r\n	text-transform: uppercase;\r\n	font-family: Arial, Helvetica, sans-serif;\r\n	}\r\n.small a:link,.small a:visited {\r\n	text-decoration:underline;\r\n	}\r\n.box_out a:hover  {\r\n	text-decoration: none;\r\n}\r\n	\r\n/* Hervorgehobener, wichtiger Text */\r\n.highlight {\r\n	font-weight:bold;\r\n	color:red;\r\n	}\r\n	\r\n\r\n\r\n	\r\n	\r\n\r\n\r\n\r\n/* Formular-Elemente */\r\n/* Normales Textfeld */\r\n.input_field {\r\n\r\n	}\r\n	\r\n/* Formular-Buttons */\r\n.input_button {\r\n\r\n	}\r\n	\r\n/* Dropdown-Boxen */\r\n.input_selectfield {\r\n	\r\n	}\r\n	\r\n	\r\n	\r\n	\r\n	\r\n	\r\n	\r\n/* Rahmen bei Bildern mit Link entfernen */\r\nimg {\r\n	border: 0;\r\n	}\r\n	\r\n.float_left {\r\n	text-align:left;\r\n	float:left;\r\n	}\r\n.float_right {\r\n	text-align:right;\r\n	float:right;\r\n	}\r\n	\r\n	\r\n/* Copyright-Hinweis */\r\n/* Sichtbare Hinweis darf ohne eine entsprechende Lizenz NICHT entfernt werden! */\r\n.copyright {\r\n	padding-top:15px;\r\n	font-size:11px;\r\n	text-decoration:none;\r\n	}', 0, 0),

('#modul_idname#','1','2','3','artikelrssfeed','RSS-Feed','','','','','','','0','0'),
('#modul_idname#','0','2','1','artikelrssfeedaktiv','RSS-Feed für Artikel aktivieren?','','RSS-Feed aktivieren|RSS-Feed deaktivieren','1|0','','1','1','0','0'),
('#modul_idname#','0','2','2','artikelkommentarfeed','RSS-Kommentar-Feed aktivieren','','aktivieren|deaktivieren','1|0','','0','0','0','0'),
('#modul_idname#','0','2','3','artikelrsstargeturl','Include/ Ziel-URL','Komplette URL der Datei (inkl. http:// und eventueller Parameter) <b>in die Sie das Modul includiert / eingebunden</b> haben.\r\nWenn Sie die eingetragene URL in Ihrem Browser aufrufen, muss das in Ihre Seite integrierte Artikelmodul angezeigt werden.','text','50','','','','0','0'),
('#modul_idname#','0','2','4','artikelrsstitel','Titel des RSS-Feeds','','text','25','','','','0','0'),
('#modul_idname#','0','2','5','artikelrssanzahl','Anzahl an Eintr&auml;gen','','text','5','','25','25','0','0'),
('#modul_idname#','0','2','6','artikelrsslaenge','RSS-Feed k&uuml;rzen?','M&ouml;chten Sie den kompletten Eintrag im Feed bereitstellen oder nur eine Kurzzusammenfassung?','Kompletten Eintrag zeigen|Kurzversion zeigen','all|short','','all','all','0','0'),
('#modul_idname#','0','2','7','artikelrssbeschreibung','Kurzbeschreibung','','textarea','5|50','','','','0','0');



-- --------------------------------------------------------

-- 
-- Menüeinträge anlegen
-- 

INSERT INTO 01prefix_menue (name,link,modul,sicherheitslevel,rightname,rightvalue,sortorder,subof,hide) VALUES 
('<b>Neuen Artikel schreiben</b>', '_loader.php?modul=#modul_idname#&amp;action=newarticle&amp;loadpage=article', '#modul_idname#', '1', 'newarticle', '1', '1', '', '0'),
('Artikel bearbeiten', '_loader.php?modul=#modul_idname#&amp;action=articles&amp;loadpage=article', '#modul_idname#', '1', 'editarticle', '1', '2', '', '0'),
('Artikel bearbeiten', '_loader.php?modul=#modul_idname#&amp;action=articles&amp;loadpage=article', '#modul_idname#', '1', 'editarticle', '2', '2', '', '0'),
('Neue statische Seite', '_loader.php?modul=#modul_idname#&amp;action=newstatic&amp;loadpage=article', '#modul_idname#', '1', 'staticarticle', '1', '3', '', '0'),
('Statische Seiten', '_loader.php?modul=#modul_idname#&amp;action=statics&amp;loadpage=article', '#modul_idname#', '1', 'staticarticle', '1', '4', '', '0'),
('Kategorien verwalten', '_loader.php?modul=#modul_idname#&amp;action=&amp;loadpage=category', '#modul_idname#', '1', 'settings', '1', '5', '0', '0'),
('Kommentare verwalten', 'comments.php?modul=#modul_idname#', '#modul_idname#', '1', 'editcomments', '1', '6', '0', '0');



-- --------------------------------------------------------

-- 
-- Benutzerrechte und Rechte-Kategorien anlegen
-- 

INSERT INTO 01prefix_rights (modul,is_cat,catid,sortid,idname,name,exp,formename,formwerte,input_exp,standardwert,nodelete,hide,in_profile) VALUES
('#modul_idname#', '1', '1', '1', '01article_userrights', 'Benutzerrechte', NULL , '', '', NULL , NULL , '0', '0', '0'),
('#modul_idname#', '0', '1', '1', 'newarticle', 'Neue Artikel verfassen', '', 'Ja|Nein', '1|0', '', '1', '0', '0', '0'),
('#modul_idname#', '0', '1', '2', 'editarticle', 'Artikel bearbeiten', '', 'Nur eigene Artikel bearbeiten|Alle Artikel bearbeiten &amp; freischalten|kein Zugriff', '1|2|0', '', '1', '0', '0', '0'),
('#modul_idname#', '0', '1', '3', 'staticarticle', 'Statische Seiten erstellen & bearbeiten', '', 'Ja|Nein', '1|0', '', '0', '0', '0', '0'),
('#modul_idname#', '0', '1', '4', 'freischaltung', 'Freischaltung von Artikeln', '', 'Artikel des Benutzers m&uuml;ssen vor der Ver&ouml;ffentlichung freigeschaltet werden|Keine Freischaltung n&ouml;tig', '1|0', '', '1', '0', '0', '0');


ALTER TABLE `01prefix_user` ADD `#modul_idname#_newarticle` tinyint( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_editarticle` tinyint( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_staticarticle` tinyint( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `01prefix_user` ADD `#modul_idname#_freischaltung` tinyint( 1 ) NOT NULL DEFAULT '1';

-- 
-- Dem Benutzer, der das Modul installiert hat die entsprechenden Rechte zuweisen
-- 

UPDATE `01prefix_user` SET `#modul_idname#_newarticle` = '1' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_editarticle` = '2' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_staticarticle` = '1' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;
UPDATE `01prefix_user` SET `#modul_idname#_freischaltung` = '0' WHERE `01prefix_user`.`id` = #UID_ADMIN_AKT# LIMIT 1;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `01prefix_article`
-- 

CREATE TABLE `01modulprefix_article` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `timestamp` int(15) default '0',
  `endtime` int(15) default '0',
  `frei` tinyint(1),
  `hide` tinyint(1),
  `icon` varchar(25),
  `titel` varchar(255),
  `newscatid` varchar(255) default '0',
  `text` text,
  `autozusammen` tinyint(1),
  `zusammenfassung` text,
  `comments` tinyint(1),
  `hide_headline` tinyint(1),
  `uid` int(10),
  `static` tinyint(1),
  `hits` int(10) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `01prefix_article`
-- Dummy-Eintrag
-- 

INSERT INTO `01modulprefix_article` (`id`, `timestamp`, `endtime`, `frei`, `hide`, `icon`, `titel`, `newscatid`, `text`, `autozusammen`, `zusammenfassung`, `comments`, `hide_headline`, `uid`, `static`, `hits`) VALUES
(1, 1199142001, 0, 1, 0, '13.gif', '01-Artikelsystem V3 erfolgreich installiert', '0', '<p><strong><span style="color: #008000;">Vielen Dank, dass Sie sich f&uuml;r das 01-Artikelsystem entschieden haben!</span></strong><br />Diesen ersten Eintrag k&ouml;nnen Sie l&ouml;schen, nachdem Sie sich in den Administrationsbereich eingeloggt haben.</p>\r\n<p>Bei Fragen oder Problemen rund um das <strong>01-Artikelsystem</strong> oder das <strong>01acp</strong> stehe ich Ihnen gerne im <a href="http://board.01-scripts.de/" target="_blank">01-Supportforum</a> oder <a href="http://www.01-scripts.de/contact.php" target="_blank">per E-Mail</a> zu Verf&uuml;gung.</p>\r\n<p>Bitte beachten Sie die <a href="http://www.01-scripts.de/lizenz.php" target="_blank">g&uuml;ltigen Lizenzbestimmungen</a>! Das <strong>01-Artikelsystem</strong> und das <strong>01acp</strong> werden unter der Creative-Commons-Lizenz "<em><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/" target="_blank">Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland</a></em>" ver&ouml;ffentlicht.</p>\r\n<p>Informationen zum Erwerb einer <em>Lizenz zur kommerziellen Nutzung</em> (Gestattet den Einsatz auf kommerziellen Seiten und/oder Firmenseiten) oder eine <em>Non-Copyright-Lizenz</em> (die zum Entfernen des sichtbaren Urheberrechts-Hinweises berechtigt) entnehmen Sie bitte <a href="http://www.01-scripts.de/preise.php" target="_blank">dieser Seite</a>.</p>\r\n<p>MfG,<br />Michael Lorer<br />Web: <a href="http://www.01-scripts.de" target="_blank">http://www.01-scripts.de</a><br />Mail: <a href="mailto:info@01-scripts.de">info@01-scripts.de</a></p>\r\n<p><img style="float: left; margin: 10px; border: 0px none #000000;" title="01-Scripts.de" src="http://www.01-scripts.de/pics/system/01logo.jpg" alt="01-Scripts.de-Logo" width="300" height="40" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.</p>', 0, '', 1, 0, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `01prefix_category`
-- 

CREATE TABLE `01modulprefix_articlecategory` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50),
  `catpic` varchar(15),
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

COMMIT;

-- 01-Artikelsystem V3 Copyright 2006-2008 by Michael Lorer - 01-Scripts.de