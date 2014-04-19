/*!
  01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
  Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
  Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

  Modul:    01ACP
  Dateiinfo:  Globale JavaScript-Funktionen
  Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.2.2/01scripts/01acp/system/js/javas.js
  #fv.130#
*/



function redirect(url){
    window.location.href = url;
}



function popup(action,var1,var2,var3,w,h) {
window.open('popups.php?action='+action+'&var1='+var1+'&var2='+var2+'&var3='+var3+'','_blank','width='+w+',height='+h+',scrollbars=yes,resizable=yes,status=no,toolbar=no,left=400,top=150');
}

function modulpopup(modul,action,var1,var2,var3,w,h) {
window.open('popups.php?action='+action+'&modul='+modul+'&var1='+var1+'&var2='+var2+'&var3='+var3+'','_blank','width='+w+',height='+h+',scrollbars=yes,resizable=yes,status=no,toolbar=no,left=400,top=150');
}




/* Überprüfen ob ein in ein Formularfeld eingegebener Wert eine Zahl ist */
function IsZahl(feldname){
var field = document.getElementsByName(feldname)[0];
var Wert = field.value;

if(isNaN(Wert)){
	alert(Wert + " ist keine Zahl!");
	field.value = "";
	return false;
	}
else{
	return true;
	}
}


//JS-Funktion mit freundlicher Unterstützung von "Berni*" (http://www.bernis-board.de/)
function clearField(field){
if(field.value == field.defaultValue){
    field.value = "";
    }
}
//JS-Funktion mit freundlicher Unterstützung von "Berni*" (http://www.bernis-board.de/)
function checkField(field){
if(field.value == ""){
    field.value = field.defaultValue;
    }
}





function fade_element(elementId){
var temp = document.getElementById(elementId);
temp.fade('toggle');
}

function hide_unhide(elementId){
var temp = document.getElementById(elementId);
temp.style.display = (temp.style.display == 'block') ? 'none' : 'block';
}

function hide_always(elementId){
var temp = document.getElementById(elementId);
temp.style.display = 'none';
}

function show_always(elementId){
var temp = document.getElementById(elementId);
temp.style.display = 'block';
}

function hide_unhide_inline(elementId){
var temp = document.getElementById(elementId);
temp.style.display = (temp.style.display == 'inline') ? 'none' : 'inline';
}

function hide_unhide_tr(elementId){
var temp = document.getElementById(elementId);

if(temp.style.display == 'none'){
	try { temp.style.display = 'table-row'; } 	catch(e) {
	temp.style.display = 'inline'; };
	}
else{
	temp.style.display = 'none';
	}

}

function hide_unhide_tr_byclass(elementClass){
var allElems = document.getElementsByTagName('tr');

for(var i = 0; i < allElems.length; i++){
	var thisElem = allElems[i];
	if(thisElem.className && thisElem.className == elementClass){
		if(thisElem.style.display == 'none'){
			try { thisElem.style.display = 'table-row'; } 	catch(e) {
			thisElem.style.display = 'inline'; };
			}
		else{
			thisElem.style.display = 'none';
			}
		}
	}
}

function moo_hide_unhide_tr(elementClass){
var temp = $$('tr.'+elementClass);
//var test = temp.getStyle('display')[0];
//alert(test);
if(temp.getStyle('display')[0] == 'none'){
	try { temp.setStyle('display','table-row'); } 	catch(e) {
	temp.setStyle('display','inline'); };
	}
else{
 temp.setStyle('display','none');
	}

}


/*BB-Code-Funktion (c) by http://aktuell.de.selfhtml.org/artikel/javascript/bbcode/index.htm */
function bbcinsert(aTag, eTag) {
  var input = document.forms['post'].elements['newsfeld'];
  input.focus();
  /* für Internet Explorer */
  if(typeof document.selection != 'undefined') {
    /* Einfügen des Formatierungscodes */
    var range = document.selection.createRange();
    var insText = range.text;
    range.text = aTag + insText + eTag;
    /* Anpassen der Cursorposition */
    range = document.selection.createRange();
    if (insText.length == 0) {
      range.move('character', -eTag.length);
    } else {
      range.moveStart('character', aTag.length + insText.length + eTag.length);      
    }
    range.select();
  }
  /* für neuere auf Gecko basierende Browser */
  else if(typeof input.selectionStart != 'undefined')
  {
    /* Einfügen des Formatierungscodes */
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var insText = input.value.substring(start, end);
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
    /* Anpassen der Cursorposition */
    var pos;
    if (insText.length == 0) {
      pos = start + aTag.length;
    } else {
      pos = start + aTag.length + insText.length + eTag.length;
    }
    input.selectionStart = pos;
    input.selectionEnd = pos;
  }
  /* für die übrigen Browser */
  else
  {
    /* Abfrage der Einfügeposition */
    var pos;
    var re = new RegExp('^[0-9]{0,3}$');
    while(!re.test(pos)) {
      pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
    }
    if(pos > input.value.length) {
      pos = input.value.length;
    }
    /* Einfügen des Formatierungscodes */
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
  }
}