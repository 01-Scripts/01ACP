/*!
    01ACP - Copyright 2008-2014 by Michael Lorer - 01-Scripts.de
    Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
    Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

    Modul:      01ACP
    Dateiinfo:  JavaScript-Befehl: Sortables-Call
    #fv.130#
*/
var mySortables = new Sortables('sortliste', {
    revert: { duration: 500, transition: 'elastic:out' },
	onComplete: function(){
		$('sortdatafield' ).value = this.serialize(); }
	});