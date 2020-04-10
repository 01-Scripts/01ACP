# 01ACP

Das **01ACP** stellt einen zentralen Administrationsbereich dar, der entwickelt wurde um die Entwicklung von kundenspezifischen Modulen & Erweiterungen zu erleichtern. Das 01ACP �bernimmt dabei die Verwaltung aller installierter Module zusammen mit allen Einstellungen, dem Benutzersystem, dem Kommentarsystem sowie der Datei- & Bildverwaltung (inkl. Multiupload-Funktionalit�t und WYSIWYG-Editor-Integration).
F�r den Administrationsbereich existieren mittlerweile verschiedene Module, die genutzt werden k�nnen. Alle administrativen Aufgaben k�nnen dann �ber eine identische Oberfl�che zentral erledigt werden. Verschiedene Logins und Administrationsbereich entfallen dadurch.

## Features

* WYSIWYG-Editor (TinyMCE) integriert 
* Module online installieren verwalten, aktualisieren, deaktivieren   l�schen
* Einstellungen online f�r alle Module vornehmen
* Benutzerverwaltung (Benutzer anlegen, Berechtigungen & Einstellungen f�r alle Module verwalten, Benutzer sperren, etc.)
* Datei- & Bildverwaltung (Multipload-Funktion, Dateien bearbeiten, einbinden und l�schen, Verzeichnisse erstellen und verwalten)
* Dynamische Resize- und Caching-Funktion f�r hochgeladene Bilder
* Zentrales Kommentarsystem (inkl. Antispam- & Zensurfunktion) zur Nutzung in allen Modulen integriert
* Erstellung von eigenen Modulen auf Basis des 01ACP m�glich
* kostenlos (CC BY-NC-SA-Lizenz)

## Download

Die jeweils aktuellste Version des 01ACP kann unter
[https://www.01-scripts.de/down_zip.php?godownload=1&01acp=1](https://www.01-scripts.de/down_zip.php?godownload=1&01acp=1)
heruntergeladen werden.

Einzelne Update-Pakete f�r alle ver�ffentlichten Updates sind hier zu finden:
[https://www.01-scripts.de/downloads.php](https://www.01-scripts.de/downloads.php)

## Installation

### Den Administrationsbereich (01ACP) installieren

Um einzelne Module wie das [01-Artikelsystem](https://github.com/01-Scripts/01-Artikelsystem) oder die [01-Gallery](https://github.com/01-Scripts/01-Gallery) zu installieren, muss zuerst einmalig der f�r alle Module ben�tigte Administrationsbereich (das [01ACP](https://github.com/01-Scripts/01ACP)) installiert werden:

#### Dateien hochladen

Entpacken sie das heruntergeladene .zip-Archiv in einen beliebigen Ordner auf ihrer lokalen Festplatte.  
Im Verzeichnis  `Administrationsbereich/`  finden sie das Verzeichnis  `01scripts/`  das alle Dateien zur Installation des 01ACP enth�lt.  
Laden sie das gesamte  `01scripts/`-Verzeichnis mit ihrem  FTP-Programm  in das Hauptverzeichnis ihrer Internetseite hoch. Achten sie dabei unbedingt darauf, dass die Datei- und Verzeichnisstruktur erhalten bleibt!  
  
Im Idealfalls sollte sich nach dem Hochladen der Daten das Verzeichnis  `01scripts/`  auf der gleichen Verzeichnisebene befinden wie ihre Index- oder Startdatei (meist index.php).

#### Installation des 01ACP starten

Nachdem sie alle Dateien auf ihren Webspace hochgeladen haben rufen sie in ihrem Browser bitte folgende Internetadresse auf:  
`http://www.ihre-domain.de/01scripts/01acp/install.php`  
  
Falls sich das Verzeichnis  `01scripts/`  **nicht**  im Hauptverzeichnis ihrer Internetseite befindet, m�ssen sie den Pfad der URL entsprechend anpssen:  
`http://www.ihre-domain.de/pfad-zum-script/01scripts/01acp/install.php`  
  
Sie werden anschlie�end mit der Installationsroutine durch die weitere Installation des 01ACP gef�hrt. Folgen sie dazu einfach den Anweisungen auf dem Bildschirm.

#### Module installieren

Nachdem sie das 01ACP erfolgreich installiert haben und den ersten Benutzer-Account angelegt haben l�schen sie bitte aus Sicherheitsgr�nden die beiden Dateien install.php und install_sql.php!  
Anschlie�end loggen sie sich bitte mit ihrem Admin-Account den sie bei der Installation angelegt haben, in den Administrationsbereich ein:  
`http://www.ihre-domain.de/01scripts/01acp/index.php`

### Module in das 01ACP installieren

F�r die weiteren Schritte muss das 01ACP bereits installiert sein!

#### Modul-Verzeichnis hochladen

Wenn sie es noch nicht getan haben, dann entpacken sie das von  01-Scripts.de  heruntergeladene .zip-Archiv in einen beliebigen Ordner auf ihrer lokalen Festplatte.  
Im Verzeichnis  `Module/`  finden sie das von ihnen heruntergeladene Modul in einem Verzeichnis.  
Laden sie das Verzeichnis (z.B.  `01article/`) mit ihrem  FTP-Programm  in folgendes Verzeichnis:  
`/pfad-zum-script/01scripts/01module/`

#### Hochgeladenes Modul installieren

Loggen sie sich nun mit ihrem Admin-Account in das 01ACP ein.  
Nach dem Login finden sie im linken Men� der 01ACP-Startseite den Men�punkt _Module verwalten_  klicken sie auf ihn.
![Men�punkt Module verwalten zur Modul-Installation](https://www.01-scripts.de/assets/img/doku/01acp_module.gif)
In der erscheinenden Tabelle werden alle vorhandenen Module aufgelistet. Mit einem Klick auf den gr�nen Plus-Button starten sie die Installation des gew�nschten Moduls.  
Geben sie einen Installationsnamen f�r das Modul an.  
Nach der Installation steht das Modul sofort zur Verf�gung. In den Einstellungen k�nnen nun die Einstellungen f�r das installierte Modul vorgenommen werden.
![F�r Modul-Einstellungen das entsprechende Modul im Dropdown ausw�hlen](https://www.01-scripts.de/assets/img/doku/01acp_moduleinstellungen.gif)
Die Startseite des Moduls rufen sie �ber die Drop-Down-Box oben rechts im 01ACP auf:
![Die Modul-Startseite ist per Dropdown oben rechts zu erreichen](https://www.01-scripts.de/assets/img/doku/01acp_dropdown.gif)

## Lizenz

Das 01ACP ist unter [CC BY-NC-SA](https://creativecommons.org/licenses/by-nc-sa/3.0/de/) -Lizenz ver�ffentlicht

*Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland*

* Alle PHP-Scripte von [01-Scripts.de](https://www.01-scripts.de) k�nnen f�r den privaten, nicht-kommerziellen Einsatz kostenlos genutzt werden. 
* F�r die kommerzielle Nutzung (darunter f�llt auch der Einsatz auf Firmenseiten!) k�nnen Sie eine Lizenz zur kommerziellen Nutzung [erwerben](https://www.01-scripts.de/shop.php).
* Wenn Sie den sichtbaren Urheberrechtshinweis entfernen m�chten, erwerben Sie bitte eine Non-Copyright-Lizenz.

Lizenzen k�nnen hier bestellt werden:
[https://www.01-scripts.de/shop.php](https://www.01-scripts.de/shop.php)