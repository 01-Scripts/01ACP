/* 
	01-Artikelsystem V3 - Copyright 2006-2008 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01article
	Dateiinfo:	JavaScript-Funktionen
*/



function change_readonly(elementId){

var temp = document.getElementById(elementId);
temp.readOnly = (temp.readOnly == false) ? true : false;

}

function checkLen(elementId){

var txt = document.getElementById(elementId).value;
document.getElementById('zcounter').value= txt.length;

}


/* 01-ACP Copyright 2008 by Michael Lorer - 01-Scripts.de */