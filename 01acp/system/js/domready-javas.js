/*!
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo:	JavaScript-Befehle die im ACP zum domready-Zeitpunkt ausgeführt werden
	Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.3.0/01scripts/01acp/system/js/domready-javas.js
	#fv.130#
*/
// Ajax-Meldungsboxen ausblenden
var ajaxboxes = $$('div.ajax_box');
ajaxboxes.fade('hide');
ajaxboxes.setStyle('display','block');

// Elemente der Klasse moo_hide ausblenden
var moo_hiddenelements = $$('div.moo_hide');
moo_hiddenelements.fade('hide');
moo_hiddenelements.setStyle('display','block');

// Elemente der Klasse moo_inlinehide ausblenden
var moo_hiddeninlineelements = $$('div.moo_inlinehide');
moo_hiddeninlineelements.fade('hide');
moo_hiddeninlineelements.setStyle('display','inline');