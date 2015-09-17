-- 01ACP - Copyright 2008-2015 by Michael Lorer - 01-Scripts.de
-- Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
-- Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

-- Modul:		01acp
-- Dateiinfo:	SQL-Befehle für die Erstinstallation des 01ACP
-- #fv.131#
--  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  *  *

-- --------------------------------------------------------

SET AUTOCOMMIT=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' ;
START TRANSACTION;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_comments`
--

CREATE TABLE IF NOT EXISTS `01prefix_comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `modul` varchar(25) NULL DEFAULT NULL,
  `postid` varchar(255) NOT NULL DEFAULT '0',
  `subpostid` varchar(255) NOT NULL DEFAULT '0',
  `uid` varchar(32) NOT NULL DEFAULT '0',
  `frei` tinyint(1) NOT NULL DEFAULT '0',
  `utimestamp` int(15) NOT NULL DEFAULT '0',
  `ip` varchar(15) NULL DEFAULT NULL,
  `autor` varchar(50) NULL DEFAULT NULL,
  `email` varchar(75) NULL DEFAULT NULL,
  `url` varchar(100) NULL DEFAULT NULL,
  `message` text NULL DEFAULT NULL,
  `smilies` tinyint(1) NOT NULL DEFAULT '0',
  `bbc` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_files`
--

CREATE TABLE IF NOT EXISTS `01prefix_files` (
  `id` int(10) NOT NULL auto_increment,
  `filetype` varchar(4) NULL COMMENT 'Dateityp pic oder file',
  `modul` varchar(25) NULL DEFAULT NULL,
  `utimestamp` int(15) NOT NULL DEFAULT '0',
  `dir` int(10) NOT NULL DEFAULT '0',
  `orgname` varchar(255) NULL DEFAULT NULL,
  `name` varchar(25) NOT NULL DEFAULT '',
  `size` varchar(20) NULL DEFAULT NULL,
  `ext` varchar(5) NULL DEFAULT NULL,
  `uid` int(10) NULL DEFAULT NULL COMMENT 'uid=0 für gelöschte Benutzer',
  `downloads` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_files`
--

CREATE TABLE IF NOT EXISTS `01prefix_filedirs` (
  `id` INT( 10 ) NOT NULL AUTO_INCREMENT,
  `parentid` INT( 10 ) NOT NULL DEFAULT '0',
  `utimestamp` INT( 15 ) NOT NULL DEFAULT '0',
  `name` VARCHAR( 50 ) NULL,
  `uid` INT( 10 ) NOT NULL DEFAULT '0',
  `hide` TINYINT( 1 ) NOT NULL DEFAULT '0',
  `sperre` TINYINT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_menue`
--

CREATE TABLE IF NOT EXISTS `01prefix_menue` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) NULL,
  `link` varchar(100) NULL,
  `modul` varchar(25) NULL,
  `sicherheitslevel` int(3) NOT NULL DEFAULT '0',
  `rightname` varchar(40) NULL,
  `rightvalue` varchar(255) NULL,
  `sortorder` int(3) NOT NULL DEFAULT '0',
  `subof` int(10) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 ;

--
-- Daten für Tabelle `01_1_menue`
--

INSERT INTO `01prefix_menue` (`id`, `name`, `link`, `modul`, `sicherheitslevel`, `rightname`, `rightvalue`, `sortorder`, `subof`, `hide`) VALUES
(1, 'Login', 'index.php', 'login', 0, '', '', 1, 0, 0),
(2, 'Passwort vergessen?', 'javascript: hide_unhide(''loginform''); hide_unhide(''passwordbox'');', 'login', 0, '', '', 2, 0, 0),
(3, 'Module verwalten', 'module.php', '01acp_start', 1, 'module', '1', 2, 0, 0),
(4, 'Einstellung hinzuf&uuml;gen', 'settings.php?action=add_setting', '01acp_settings', 10, 'settings', '1', 3, 0, 0),
(5, 'Einstellungen sortieren', 'settings.php?action=sort_settings', '01acp_settings', 10, 'settings', '1', 4, 0, 0),
(6, 'ACP-Startseite', 'acp.php', '01acp_start', 1, '', '', 0, 0, 0),
(7, '<b>Einstellungen</b>', 'settings.php?action=settings', '01acp_settings', 1, 'settings', '1', 1, 0, 0),
(8, 'Benutzerrecht hinzuf&uuml;gen', 'rights.php?action=add_right', '01acp_users', 10, 'rights', '1', 3, 0, 0),
(9, 'Benutzerrechte sortieren', 'rights.php?action=sort_rights', '01acp_users', 10, 'rights', '1', 4, 0, 0),
(10, 'Benutzer erstellen', 'users.php?action=add_user', '01acp_users', 0, 'userverwaltung', '1', 2, 0, 0),
(11, '<b>Benutzer bearbeiten</b>', 'users.php?action=edit_users', '01acp_users', 0, 'userverwaltung', '2', 1, 0, 0),
(12, 'Benutzer erstellen', 'users.php?action=add_user', '01acp_users', 0, 'userverwaltung', '2', 2, 0, 0),
(13, 'Bilder verwalten', 'filemanager.php?type=pic', '01acp_filemanager', 0, 'dateimanager', '1', 1, 0, 0),
(14, 'Dateien verwalten', 'filemanager.php?type=file', '01acp_filemanager', 0, 'dateimanager', '1', 2, 0, 0),
(15, 'Bilder verwalten', 'filemanager.php?type=pic', '01acp_filemanager', 0, 'dateimanager', '2', 1, 0, 0),
(16, 'Dateien verwalten', 'filemanager.php?type=file', '01acp_filemanager', 0, 'dateimanager', '2', 2, 0, 0),
(17, 'Module verwalten', 'module.php', '01acp_module', 1, 'module', '1', 1, 0, 0),
(18, 'Module verwalten', 'module.php', '01acp_settings', 1, 'module', '1', 2, 0, 0),
(19, 'Kommentare verwalten', 'comments.php', '01acp_start', 1, 'editcomments', '1', 1, 0, 0),
(20, 'Kommentare verwalten', 'comments.php', '01acp_comments', 1, 'editcomments', '1', 1, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_module`
--

CREATE TABLE IF NOT EXISTS `01prefix_module` (
  `id` int(10) NOT NULL auto_increment,
  `nr` int(3) NOT NULL DEFAULT '0',
  `aktiv` tinyint(1) NOT NULL DEFAULT '1',
  `modulname` varchar(25) NOT NULL COMMENT 'Gibt die Modulart z.B. "01news" "01gallery" an.',
  `instname` varchar(50) NULL COMMENT 'Individueller, vom Benutzer eingegebene Name',
  `idname` varchar(25) NULL COMMENT 'entspricht dem Verzeichnisnamen',
  `version` varchar(10) NULL,
  `serialized_data` MEDIUMBLOB NULL DEFAULT NULL COMMENT 'use unserialize() to get data back',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idname` (`idname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_rights`
--

CREATE TABLE IF NOT EXISTS `01prefix_rights` (
  `id` int(5) NOT NULL auto_increment,
  `modul` varchar(25) NULL,
  `is_cat` tinyint(1) NOT NULL DEFAULT '0',
  `catid` tinyint(2) NOT NULL DEFAULT '0',
  `sortid` int(5) DEFAULT NULL,
  `idname` varchar(40) NULL COMMENT 'rightname',
  `name` varchar(50) DEFAULT NULL,
  `exp` text,
  `formename` text NULL,
  `formwerte` varchar(255) NULL,
  `input_exp` varchar(255) DEFAULT NULL,
  `standardwert` text,
  `nodelete` tinyint(1) NOT NULL DEFAULT '0',
  `hide` tinyint(1) DEFAULT '0',
  `in_profile` tinyint(1) DEFAULT '0' COMMENT 'Right kann im Profil selber bearbeitet werden',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `modul` (`modul`,`idname`)
) ENGINE=MyISAM AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `01prefix_rights`
--

INSERT INTO `01prefix_rights` (`id`, `modul`, `is_cat`, `catid`, `sortid`, `idname`, `name`, `exp`, `formename`, `formwerte`, `input_exp`, `standardwert`, `nodelete`, `hide`, `in_profile`) VALUES
(1, '01acp', 1, 1, 1, 'cat_global', 'Globale Berechtigungen', NULL, '', '', NULL, NULL, 1, 0, 0),
(2, '01acp', 1, 2, 2, 'cat_allgsettings', 'Allgemeine Einstellungen', NULL, '', '', NULL, NULL, 0, 0, 0),
(3, '01acp', 0, 2, 1, 'profil', 'Eigenes Profil bearbeiten', '', 'Ja|Nein', '1|0', '', '1', 0, 0, 0),
(4, '01acp', 0, 1, 5, 'upload', 'Dateien &amp; Bilder hochladen', '', 'Darf Dateien &amp; Bilder hochladen|Darf nichts hochladen', '1|0', '', '1', 0, 0, 0),
(5, '01acp', 0, 3, 3, 'devmode', 'Entwicklungsansicht', 'Ist die Entwicklungsansicht aktiviert werden im ACP f&uuml;r den entsprechenden Benutzer weitergehende Systeminformationen angezeigt.\r\nNUR F&Uuml;R MODULENTWICKLER / ZUR FEHLERBEHEBUNG EMPFOHLEN!', 'Aktivieren|Deaktivieren', '1|0', '', '0', 1, 0, 0),
(6, '01acp', 0, 1, 6, 'dateimanager', 'Datei- &amp; Bildmanager', 'Benutzer, die Zugriff auf alle Dateien &amp; Bilder haben, k&ouml;nnen auch Verzeichnisse anlegen und bearbeiten.', 'Kein Zugriff|Nur eigene Dateien/Bilder verwalten|Zugriff auf alle Dateien &amp; Bilder', '0|1|2', '', '1', 0, 0, 0),
(7, '01acp', 0, 1, 1, 'settings', 'Einstellungen bearbeiten', 'Benutzer kann globale Einstellungen und Einstellungen für Module vornehmen.', 'Ja|Nein', '1|0', '', '0', 1, 0, 0),
(8, '01acp', 0, 1, 4, 'userverwaltung', 'Benutzer anlegen / bearbeiten', '', 'Keine Berechtigung|Neuen Benutzer anlegen|Benutzer anlegen &amp; bearbeiten', '0|1|2', '', '0', 1, 0, 0),
(9, '01acp', 0, 3, 2, 'rights', 'Weitere Benutzerrechte anlegen', 'Benutzer mit Sicherheitslevel 10 k&ouml;nnen eigene, weitere Benutzerrechte f&uuml;r den Administrationsbereich oder einzelne Module anlegen.', 'Ja|Nein', '1|0', '', '0', 1, 0, 0),
(10, '01acp', 1, 3, 4, 'cat_higherrights', 'Erweiterte Berechtigungen', NULL, '', '', NULL, NULL, 1, 0, 0),
(11, '01acp', 0, 2, 2, 'signatur', 'Signatur', NULL, 'textarea', '5|50', NULL, NULL, 0, 0, 1),
(12, '01acp', 0, 3, 1, 'addsettings', 'Weitere Einstellungsm&ouml;glichkeiten', 'Benutzer mit Sicherheitslevel 10 k&ouml;nnen eigene, weitere Einstellungsm&ouml;glichkeiten f&uuml;r den Administrationsbereich oder einzelne Module anlegen.', 'Ja|Nein', '1|0', '', '0', 1, 0, 0),
(13, '01acp', 1, 4, 3, 'cat_modulaccess', 'Modulzugriff', NULL, '', '', NULL, NULL, 1, 0, 0),
(14, '01acp', 0, 1, 3, 'module', 'Module verwalten', 'Module installieren und aktualisieren', 'Ja|Nein', '1|0', '', '0', 1, 0, 0),
(15, '01acp', 0, 1, 2, 'editcomments', 'Kommentare bearbeiten &amp; freischalten', '', 'Ja|Nein', '1|0', '', '0', 0, 0, 0),
(16, '01acp', 0, 2, 3, 'notepad', 'Notizblock', '', 'textarea', '5|50', '', '', 0, 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_settings`
--

CREATE TABLE IF NOT EXISTS `01prefix_settings` (
  `id` int(5) NOT NULL auto_increment,
  `modul` varchar(25) NULL,
  `is_cat` tinyint(1) NOT NULL DEFAULT '0',
  `catid` tinyint(2) NOT NULL DEFAULT '0',
  `sortid` int(5) DEFAULT NULL,
  `idname` varchar(25) NULL,
  `name` varchar(50) DEFAULT NULL,
  `exp` text,
  `formename` text NULL,
  `formwerte` varchar(255) NULL,
  `input_exp` varchar(255) DEFAULT NULL,
  `standardwert` text,
  `wert` text,
  `nodelete` tinyint(1) NOT NULL DEFAULT '0',
  `hide` tinyint(1) DEFAULT '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `modul` (`modul`,`idname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `01prefix_settings`
--

INSERT INTO `01prefix_settings` (`modul`, `is_cat`, `catid`, `sortid`, `idname`, `name`, `exp`, `formename`, `formwerte`, `input_exp`, `standardwert`, `wert`, `nodelete`, `hide`) VALUES
('01acp', 1, 1, 1, 'cat_global', 'Globale Einstellungen', NULL, 'text', '', NULL, NULL, NULL, 0, 0),
('01acp', 0, 1, 1, 'email_absender', 'Kontakt-E-Mail-Adresse', 'Diese Adresse wird als Kontakt- und Absende-Adresse verwendet.', 'text', '50', '', '', '', 0, 0),
('01acp', 0, 1, 2, 'sitename', 'Seitentitel', '', 'text', '50', '', 'Seitentitel', 'Seitentitel', 0, 0),
('01acp', 0, 1, 3, 'absolut_url', 'Absolute URL', 'Bitte geben Sie die absolute URL (inkl. http://) zum Verzeichnis <i>01scripts/</i> ein.\r\nBeispiel: http://www.domainname.de/pfad/01scripts/', 'text', '50', '', '', '', 1, 0),
('01acp', 0, 1, 4, 'spamschutz', 'Spamschutz', 'Zur Nutzung von reCAPTCHA m&uuml;ssen Daten im Abschnitt <i>Webservices</i> hinterlegt werden.', '01ACP Captcha-Bild|reCAPTCHA|kein Spamschutz', '1|2|0', '<a href=\"javascript:popup(\'captcha_test\',\'\',\'\',\'\',500,550);\">Testen</a>', '1', '1', 0, 0),
('01acp', 0, 1, 5, 'acp_captcha4login', 'Captcha bei ACP-Login verwenden?', '', 'Ja|Nein', '1|0', '', '0', '0', 1, 0),
('01acp', 0, 1, 0, 'acpversion', 'ACP-Version', '', 'text', '10', '', '1.3.1', '1.3.1', 0, 1),
('01acp', 0, 1, 0, 'cachetime', 'Cachetime (XML)', '', 'text', '10', '', '', '0', 0, 1),
('01acp', 0, 1, 0, 'installed', 'installiert', '', 'text', '10', '', '1', '0', 0, 1),
('01acp', 1, 2, 2, 'cat_manager', 'Datei- und Bildmanager', NULL, 'text', '', NULL, NULL, NULL, 0, 0),
('01acp', 0, 2, 1, 'pic_end', 'Bilder: Erlaubte Dateiendungen', 'Dateiendungen mit Kommas trennen', 'text', '50', '', 'jpg,jpeg,gif,bmp,png', 'jpg,jpeg,gif,bmp,png', 0, 0),
('01acp', 0, 2, 2, 'pic_size', 'Bilder: Maximale Dateigr&ouml;&szlig;e', '', 'text', '5', 'KB', '500', '500', 0, 0),
('01acp', 0, 2, 3, 'thumbwidth', 'Maximale Thumbnail-Seitenl&auml;nge', '', 'function', '5', 'px', '200', '200', 1, 0),
('01acp', 0, 2, 4, 'attachment_end', 'Dateien: Erlaubte Dateiendungen', 'Dateiendungen mit Kommas trennen', 'text', '50', '', 'zip,rar,pdf,txt', 'zip,rar,pdf,txt', 0, 0),
('01acp', 0, 2, 5, 'attachment_size', 'Dateien: Maximale Dateigr&ouml;&szlig;e', '', 'text', '5', 'KB', '500', '500', 0, 0),
('01acp', 0, 2, 0, 'filesecid','File Security ID','From this ID on only Hash values are allowed to access an uploaded file.','text','5','','0','0',0,1),
('01acp', 1, 3, 3, 'commentsettings', 'Kommentarfunktion', NULL, '', '', NULL, NULL, NULL, 0, 0),
('01acp', 0, 3, 1, 'comments', 'Kommentarfunktion', '', 'aktivieren|deaktivieren', '1|0', '', '1', '1', 0, 0),
('01acp', 0, 3, 2, 'comments_perpage', 'Kommentare pro Seite', '', 'text', '5', '', '25', '25', 0, 0),
('01acp', 0, 3, 3, 'commentfreischaltung', 'Kommentare freischalten?', '', 'keine Freischaltung|manuelle Freischaltung', '0|1', '', '0', '0', 0, 0),
('01acp', 0, 3, 5, 'comments_smilies', 'Smilies aktivieren?', '', 'Ja|Nein', '1|0', '', '1', '1', 0, 0),
('01acp', 0, 3, 6, 'comments_bbc', 'BB-Code aktivieren?', '', 'Ja|Nein', '1|0', '', '1', '1', 0, 0),
('01acp', 0, 3, 7, 'comments_zensur','Zensur aktivieren?','','Ja|Nein','1|0','','0','1', 0, 0),
('01acp', 0, 3, 8, 'comments_badwords','Zu zensierende W&ouml;rter','Pro Zeile ein Wort eingeben, welches zensiert werden soll.','textarea','5|50','','','', 0, 0),
('01acp', 0, 3, 9, 'comments_zensurlimit','Kommentar abweisen ab','-1 weist keine Kommentare ab','text','4','erkannten W&ouml;rtern.','5','5', 0, 0),
('01acp', 1, 4, 4, 'cat_rss', 'RSS-Feed', NULL, 'text', '', NULL, NULL, NULL, 0, 0),
('01acp', 0, 4, 1, 'rss_aktiv', 'RSS-Feed aktivieren?', 'Hat Auswirkungen auf die RSS-Feeds <b>aller</b> installierter Module!', 'Ja|Nein', '1|0', '', '1', '1', 0, 0),
('01acp', 0, 4, 2, 'rss_sprache', 'Sprache', 'In welcher Sprache stellen Sie Ihre Informationen bereit? Eine &Uuml;bersicht der Sprachk&uuml;rzel finden Sie <a href=\\"http://de.selfhtml.org/diverses/sprachenkuerzel.htm\\" target=\\"_blank\\">hier</a>.', 'text', '10', '', 'de-de', 'de-de', 0, 0),
('01acp', 0, 4, 3, 'rss_copyright', 'Copyright-Informationen', '', 'textarea', '5|50', '', 'Die Inhalte werden unter einer Creative-Commons-Lizenz veröffentlicht, die <a href=\\"http://creativecommons.org/licenses/by-nc-sa/3.0/de/\\" target=\\"_blank\\">hier</a> einsehbar ist.', 'Die Inhalte werden unter einer Creative-Commons-Lizenz veröffentlicht, die unter folgender URL einsehbar ist:\r\nhttp://creativecommons.org/licenses/by-nc-sa/3.0/de/', 0, 0),
('01acp', 1, 5, 5, 'email_settings', 'E-Mail-Versand', NULL , NULL , NULL , NULL , NULL , NULL ,0,0),
('01acp', 0, 5, 1, 'smtp_enable', 'E-Mail-Versand per SMTP <i>(Beta)</i>','Ausgehende E-Mails werden wenn m&ouml;glich &uuml;ber das nachfolgend konfigurierte SMTP-Konto versendet.','Ja|Nein','1|0','','0','0',0,0),
('01acp', 0, 5, 2, 'smtp_host', 'SMTP-Server','','text','50','','','',0,0),
('01acp', 0, 5, 3, 'smtp_port', 'SMTP-Server TCP Port','','text','50','','587','587',0,0),
('01acp', 0, 5, 4, 'smtp_username', 'SMTP Username','','text','50','','','',0,0),
('01acp', 0, 5, 5, 'smtp_password', 'SMTP Password','Das SMTP Passwort wird aus technischen Gr&uuml;nden unverschl&uuml;sselt gespeichert.','text','50','','','',0,0),
('01acp', 1, 6, 6, 'webservices','Webservices', NULL , NULL , NULL , NULL , NULL , NULL ,0,0),
('01acp', 0, 6, 1, 'ReCaptcha_PubKey','reCAPTCHA Websiteschl&uuml;ssel','reCAPTCHA API-Key von <a href=\"https://www.google.com/recaptcha/admin\" target=\"_blank\">https://www.google.com/recaptcha/admin</a>','text','50','','','',0,0),
('01acp', 0, 6, 2, 'ReCaptcha_PrivKey','reCAPTCHA Geheimer Schl&uuml;ssel','','text','50','','','',0,0),
('01acp', 0, 6, 3, 'Disqus_Username','Disqus Shortname','Registrierung bei <a href=\"https://disqus.com/admin/signup/\" target=\"_blank\">Disqus.com</a> n&ouml;tig.','text','50','','','',0,0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `01prefix_user`
--

CREATE TABLE IF NOT EXISTS `01prefix_user` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(50) NULL,
  `mail` varchar(50) NULL,
  `userpassword` varchar(40) NULL,
  `level` int(3) NOT NULL DEFAULT '0',
  `lastlogin` int(10) NOT NULL DEFAULT '0',
  `startpage` varchar(25) NOT NULL DEFAULT '01acp',
  `cookiehash` varchar(40) NULL DEFAULT NULL,
  `sessionhash` varchar(40) NULL DEFAULT NULL,
  `sperre` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_rights` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_profil` tinyint(1) NOT NULL DEFAULT '1',
  `01acp_upload` tinyint(1) NOT NULL DEFAULT '1',
  `01acp_dateimanager` tinyint(1) NOT NULL DEFAULT '1',
  `01acp_settings` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_userverwaltung` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_signatur` text NOT NULL DEFAULT '' COMMENT 'Manuell von varchar auf text geändert',
  `01acp_addsettings` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_devmode` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_module` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_editcomments` tinyint(1) NOT NULL DEFAULT '0',
  `01acp_notepad` text NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `01prefix_user`
--

INSERT INTO `01prefix_user` (`id`, `username`, `mail`, `userpassword`, `level`, `lastlogin`, `sperre`, `01acp_rights`, `01acp_profil`, `01acp_upload`, `01acp_dateimanager`, `01acp_settings`, `01acp_userverwaltung`, `01acp_signatur`, `01acp_addsettings`, `01acp_devmode`, `01acp_module`, `01acp_editcomments`) VALUES
(0, 'Unbekannt', '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0);

-- --------------------------------------------------------

COMMIT;