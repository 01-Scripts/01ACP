/*!
	01ACP - Copyright 2008-2014 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

	Modul:		01ACP
	Dateiinfo:	JavaScript-Funktionen für AJAX-Requests and Answers
	Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.3.0/01scripts/01acp/system/js/mootools-request.js
	#fv.130#
*/

// Überprüfen ob eine Funktion existiert
function function_exists(fName, pObj) {
  if(!pObj) pObj = window;
  return (typeof pObj[fName] == 'function') ? true : false;
}




// Standard Ladeanimation (Animation einblenden) (für Ajax-Requests)
function Start_Loading_standard() {
	$$('div.ajax_loading').fade(1);
}

// Standard Ladeanimation (Animation ausblenden) (für Ajax-Requests)
function Stop_Loading_standard() {
	$$('div.ajax_loading').fade(0);
}


// Standard Erfolgsmeldung (für Ajax-Requests)
function Success_standard() {
	$$('div.ajax_erfolg').fade(1);
	setTimeout(function(){ $$('div.ajax_erfolg').fade(0); },2000);
}

// Löschen erfolgreich + TR ausblenden
function Success_delfade(rowid) {
	temp = document.getElementById(rowid);
	$$('div.ajax_erfolg').fade(1);
	//temp.fade(0);
	//setTimeout(function(){ temp.setStyle('display','none'); },600);
	setTimeout(function(){ temp.style.display = 'none'; },600);
	setTimeout(function(){ $$('div.ajax_erfolg').fade(0); },2000);
	}
	
// Löschen nicht erfolgreich / andere Standardfehlermeldung
function Failed_delfade() {
	$$('div.ajax_error').fade(1);
	setTimeout(function(){ $$('div.ajax_error').fade(0); },5000);
	}
	
// Ajax Fehlertext anzeigen
function ShowAjaxError(message) {
	$$('div.ajax_meldung').fade(1);
	$$('div.ajax_meldung').set('html', message);
	setTimeout(function(){ $$('div.ajax_meldung').fade(0); },5000);
	}
	
// Kommentar erfolgreich freigeschaltet
function Success_CFree(id) {
	//document.getElementById(id).fade(0);
	document.getElementById(id).style.display = 'none';
	$$('div.ajax_erfolg').fade(1);
	setTimeout(function(){ $$('div.ajax_erfolg').fade(0); },2000);
}




// Einfacher Ajax-Request
var AjaxRequest = new Request({
	method: 'post', 
	url: '_ajaxloader.php',
	evalScripts: true,
	onSuccess: function(response) { 

		}
	});