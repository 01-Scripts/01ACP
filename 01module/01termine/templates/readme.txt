//  01-Newsscript V2 - Copyright 2006 by Michael Lorer - 01-Scripts.de
//  Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
//  Support unter: http://www.01-scripts.de/support.php
//**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  *  *

1. Einf�hrung
2. Aufbau einer Template-Datei
3. Wichtige Template-Dateien
4. Text �ndern. Wie?
5. Weitere Informationen

1. Einf�hrung:
--------------

Templates sind im Prinzip eine Art "Kontainer", der durch die Gestaltungssprache HTML
in seinem Aussehen beliebig ver�ndert werden kann. Diese Template-Kontainer werden
anschlie�end durch das 01-Newsscript mit Daten aus der Datenbank gef�llt (Newseintr�ge,
Kommentare, etc.).
Das bedeutet f�r Sie, dass an den Templates einfache �nderungen am Design vorgenommen werden
k�nnen ohne das man zu viel am eigentlichen Kernprogramm ver�ndern muss.

Der HTML-Quelltext f�r die Ausgabe-Datei ist auf die Template-Dateien (*.html*)
in diesem Ordner und einige wenige Sprachvariablen in der Datei variablen.txt verteilt.

F�r �nderungen an den Templates und Sprachvariablen des 01-Newsscripts sind zumindest 
grundlegende HTML-Kenntnisse n�tig.

Weiterf�hrende Informationen zu HTML finden Sie hier:
http://de.selfhtml.org/


2. Aufbau einer Template-Datei:
-------------------------------

Template-Dateien bestehen im Prinzip aus einfachem HTML-Quellcode, der f�r die Darstellung
des Designs verantwortlich ist. Der jeweils aktuelle Inhalt wird dann immer per PHP aus
der Datenbank in die Templates "geschrieben".
Um Beispielsweise den zu einem Newseintrag geh�renden Usernamen auszulesen ist folgender
PHP-Code n�tig:

<?PHP echo $username; ?>

<?PHP -> Dieser Teil zeigt dem Browser an, das ab jetzt kein HTML, sondern PHP-Code auszuf�hren
         ist.
echo $username; -> Diese Zeichenfolge wird sp�ter durch den Usernamen ersetzt.
?>   -> Mit diesem Befehl wird der PHP-Code wieder geschlossen. Danach folgt wieder normales
        HTML.

Zus�tzlich zu dieser einfachen Ausgabe von Informationen gibt es auch einfache if-Abfragen
(Entscheidungsfragen) in den Templates.
Eine solche Abfrage sieht folgenderma�en aus:

<?php if($xyz == 1): ?>
Hier kann HTML-Code stehen, der ausgegeben wird, wenn $xyz = 1 ist.
<?php elseif($xzy == 2): ?>
Hier kann HTML-Code stehen, der ausgegeben wird, wenn $xyz nicht = 1, sondern $xyz = 2 ist.
<?php else: ?>
Hier kann HTML-Code stehen, der ausgegeben wird, wenn $xyz weder 1 noch 2 ist.
<?php endif; ?> //Dieser Befehl beendet die if-Abfrage

Der Rest der Templates besteht aus reinem HTML-Code und kann mit etwas Hintergrundwissen
problemlos bearbeitet und ge�ndert werden.
Bei Fragen oder Problemen stehe ich Ihnen gerne �ber das 01-Supportforum zur Verf�gung:
http://board.01-scripts.de

3. Wichtige Template-Dateien
----------------------------

Folgende Template-Dateien, die sich alle im Verzeichnis 01news/01newstemplates/ befinden,
werden vom 01-Newsscript verwendet:

-commentbit.html      Dieses Template ist f�r die Ausgabe von Kommentaren zust�ndig.
                      Es wird f�r jeden einzelnen Kommentar jeweils neu aufgerufen.
-comments_add.html    In diesem Template befindet sich das HTML-Formular um neue Kommentare
                      zu einem Beitrag hinzuf�gen zu k�nnen.
-comments_end.html    Ausgabe von Seitenzahl u.�. unterhalb der Kommentare.
-comments_head.html   Dieses Template wird oberhalb von Kommentaren eingebunden.
                      Es kann hier beispielsweise noch eigener Text eingef�gt werden.
-foot.html            Diese Datei wird ganz am Ende unterhalb von Newseintr�gen angezeigt
                      und enth�lt HTML-Code um eine HTML-Seite syntaktisch korrekt abzuschlie�en.
                      Dieses Template wird nur angezeigt, wenn Sie als Einbindungs-Methode
                      "Eigene Seite" oder "Iframe" verwenden.
-head.html            Das head-Template enth�lt HTML-Code um eine HTML-Seite syntaktisch
                      korrekt zu beginnen.
                      Dieses Template wird nur angezeigt, wenn Sie als Einbindungs-Methode
                      "Eigene Seite" oder "Iframe" verwenden.
!MAIN_BOTTOM.html     Der Inhalt dieses Templates wird immer unterhalb der News/Kommentare
                      angezeigt. Dies ist das richtige Template um eigenen Text unterhalb
                      der News anzuzeigen!
!MAIN_top.html        Der Inhalt dieses Templates wird immer oberhalb der News/Kommentare
                      angezeigt. Dies ist das richtige Template um eigenen Text oberhalb
                      der News anzuzeigen!
!news.html            Durch dieses Template werden die einzelnen News-Beitr�ge ausgegeben
                      und angezeigt.
-seiten.html          Diese Datei ist f�r die Ausgabe der Seitenzahlen & Links verantwortlich.
                      Au�erdem befinden sich hier die Links zu RSS-Dateien, die Suchfunktion etc.

4. Text �ndern. Wie?
--------------------

Um Text aus den Templates zu �ndern oder zu �bersetzen kopieren Sie die entsprechende
Stelle aus Ihrem Browser in die Zwischenablage (Text markieren und STRG+C dr�cken).
�ffnen Sie anschlie�end das Template, in dem sich der Text befindet k�nnte (siehe 3.), mit
einem Texteditor.
Durchsuchen Sie das Template durch dr�cken von STRG+F nach dem Text (Sie k�nnen den Text durch
STRG+V aus der Zwischenablage in das Suchfeld kopieren).
Ersetzen Sie die gefundene Textstelle durch Ihre eigene.

5. Weitere Informationen:
-------------------------

Ausf�hrliche Informationen zu individuellen �nderungen werden gerne im 01-Supportforum
beantwortet:
http://board.01-scripts.de

Weiterf�hrende Informationen zu HTML finden Sie hier:
http://de.selfhtml.org/

Informationen zu PHP finden Sie auf folgender Seite:
http://www.selfphp.info/