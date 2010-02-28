//  01-Newsscript V2 - Copyright 2006 by Michael Lorer - 01-Scripts.de
//  Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
//  Support unter: http://www.01-scripts.de/support.php
//**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  *  *

1. Einführung
2. Aufbau einer Template-Datei
3. Wichtige Template-Dateien
4. Text ändern. Wie?
5. Weitere Informationen

1. Einführung:
--------------

Templates sind im Prinzip eine Art "Kontainer", der durch die Gestaltungssprache HTML
in seinem Aussehen beliebig verändert werden kann. Diese Template-Kontainer werden
anschließend durch das 01-Newsscript mit Daten aus der Datenbank gefüllt (Newseinträge,
Kommentare, etc.).
Das bedeutet für Sie, dass an den Templates einfache Änderungen am Design vorgenommen werden
können ohne das man zu viel am eigentlichen Kernprogramm verändern muss.

Der HTML-Quelltext für die Ausgabe-Datei ist auf die Template-Dateien (*.html*)
in diesem Ordner und einige wenige Sprachvariablen in der Datei variablen.txt verteilt.

Für Änderungen an den Templates und Sprachvariablen des 01-Newsscripts sind zumindest 
grundlegende HTML-Kenntnisse nötig.

Weiterführende Informationen zu HTML finden Sie hier:
http://de.selfhtml.org/


2. Aufbau einer Template-Datei:
-------------------------------

Template-Dateien bestehen im Prinzip aus einfachem HTML-Quellcode, der für die Darstellung
des Designs verantwortlich ist. Der jeweils aktuelle Inhalt wird dann immer per PHP aus
der Datenbank in die Templates "geschrieben".
Um Beispielsweise den zu einem Newseintrag gehörenden Usernamen auszulesen ist folgender
PHP-Code nötig:

<?PHP echo $username; ?>

<?PHP -> Dieser Teil zeigt dem Browser an, das ab jetzt kein HTML, sondern PHP-Code auszuführen
         ist.
echo $username; -> Diese Zeichenfolge wird später durch den Usernamen ersetzt.
?>   -> Mit diesem Befehl wird der PHP-Code wieder geschlossen. Danach folgt wieder normales
        HTML.

Zusätzlich zu dieser einfachen Ausgabe von Informationen gibt es auch einfache if-Abfragen
(Entscheidungsfragen) in den Templates.
Eine solche Abfrage sieht folgendermaßen aus:

<?php if($xyz == 1): ?>
Hier kann HTML-Code stehen, der ausgegeben wird, wenn $xyz = 1 ist.
<?php elseif($xzy == 2): ?>
Hier kann HTML-Code stehen, der ausgegeben wird, wenn $xyz nicht = 1, sondern $xyz = 2 ist.
<?php else: ?>
Hier kann HTML-Code stehen, der ausgegeben wird, wenn $xyz weder 1 noch 2 ist.
<?php endif; ?> //Dieser Befehl beendet die if-Abfrage

Der Rest der Templates besteht aus reinem HTML-Code und kann mit etwas Hintergrundwissen
problemlos bearbeitet und geändert werden.
Bei Fragen oder Problemen stehe ich Ihnen gerne über das 01-Supportforum zur Verfügung:
http://board.01-scripts.de

3. Wichtige Template-Dateien
----------------------------

Folgende Template-Dateien, die sich alle im Verzeichnis 01news/01newstemplates/ befinden,
werden vom 01-Newsscript verwendet:

-commentbit.html      Dieses Template ist für die Ausgabe von Kommentaren zuständig.
                      Es wird für jeden einzelnen Kommentar jeweils neu aufgerufen.
-comments_add.html    In diesem Template befindet sich das HTML-Formular um neue Kommentare
                      zu einem Beitrag hinzufügen zu können.
-comments_end.html    Ausgabe von Seitenzahl u.ä. unterhalb der Kommentare.
-comments_head.html   Dieses Template wird oberhalb von Kommentaren eingebunden.
                      Es kann hier beispielsweise noch eigener Text eingefügt werden.
-foot.html            Diese Datei wird ganz am Ende unterhalb von Newseinträgen angezeigt
                      und enthält HTML-Code um eine HTML-Seite syntaktisch korrekt abzuschließen.
                      Dieses Template wird nur angezeigt, wenn Sie als Einbindungs-Methode
                      "Eigene Seite" oder "Iframe" verwenden.
-head.html            Das head-Template enthält HTML-Code um eine HTML-Seite syntaktisch
                      korrekt zu beginnen.
                      Dieses Template wird nur angezeigt, wenn Sie als Einbindungs-Methode
                      "Eigene Seite" oder "Iframe" verwenden.
!MAIN_BOTTOM.html     Der Inhalt dieses Templates wird immer unterhalb der News/Kommentare
                      angezeigt. Dies ist das richtige Template um eigenen Text unterhalb
                      der News anzuzeigen!
!MAIN_top.html        Der Inhalt dieses Templates wird immer oberhalb der News/Kommentare
                      angezeigt. Dies ist das richtige Template um eigenen Text oberhalb
                      der News anzuzeigen!
!news.html            Durch dieses Template werden die einzelnen News-Beiträge ausgegeben
                      und angezeigt.
-seiten.html          Diese Datei ist für die Ausgabe der Seitenzahlen & Links verantwortlich.
                      Außerdem befinden sich hier die Links zu RSS-Dateien, die Suchfunktion etc.

4. Text ändern. Wie?
--------------------

Um Text aus den Templates zu ändern oder zu übersetzen kopieren Sie die entsprechende
Stelle aus Ihrem Browser in die Zwischenablage (Text markieren und STRG+C drücken).
Öffnen Sie anschließend das Template, in dem sich der Text befindet könnte (siehe 3.), mit
einem Texteditor.
Durchsuchen Sie das Template durch drücken von STRG+F nach dem Text (Sie können den Text durch
STRG+V aus der Zwischenablage in das Suchfeld kopieren).
Ersetzen Sie die gefundene Textstelle durch Ihre eigene.

5. Weitere Informationen:
-------------------------

Ausführliche Informationen zu individuellen Änderungen werden gerne im 01-Supportforum
beantwortet:
http://board.01-scripts.de

Weiterführende Informationen zu HTML finden Sie hier:
http://de.selfhtml.org/

Informationen zu PHP finden Sie auf folgender Seite:
http://www.selfphp.info/