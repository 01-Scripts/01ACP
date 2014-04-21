/*!
    01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
    Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
    Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php

    Modul:      01ACP
    Dateiinfo:  JavaScript-Befehl: Vertikales Slide In/Out
    Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.3.0/01scripts/01acp/system/js/mootools-domready-slidev.js
    #fv.130#
*/
    var links = $$('tr.fx_opener');
    var contents = $$('tr.fx_content');
 
	contents.set('slide',{
	        duration:600
	    });

	contents.slide('hide');
	//contents.setStyle('display','none');
	
	links.addEvent('click',function(e){
        e.stop();
        var index = links.indexOf(this);
        if(!contents.some(function(content,i){
            var slide = content.get('slide');
            if(slide.open && i != index){
                slide.slideOut().chain(function(){contents[index].slide('in')});
                return true;
            }
        })){
            contents[index].slide('toggle');
        }
		contents[index].setStyle('display','table-row');
    });