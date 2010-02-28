    var links = $$('img.fx_opener,div.fx_opener,td.fx_opener,tr.fx_opener');
    var contents = $$('div.fx_content');
 
	contents.set('slide',{
	        duration:600,
			mode: 'horizontal'
	    });

	contents.slide('hide');
	contents.setStyle('display','block');
	
	links.addEvent('click',function(e){
		e.stop();
        var index = links.indexOf(this);
        if(!contents.some(function(content,i){
            var slide = content.get('slide');
            if(slide.open && i != index){
                slide.slideOut().chain(function(){links[index].fade(0); setTimeout(function(){ links[index].setStyle('display','none') },300); contents[index].slide('in'); setTimeout(function(){ links[i].setStyle('display','block') },600); setTimeout(function(){ links[i].fade(1) },400); });
				return true;
            }
        })){
		links[index].fade(0);
		setTimeout(function(){ links[index].setStyle('display','none') },300);
		setTimeout(function(){ contents[index].slide('in') },400);
        }

    });
	
	contents.addEvent('click',function(e){

		var index = contents.indexOf(this);
		contents[index].slide('toggle');
		setTimeout(function(){ links[index].setStyle('display','block') },600);
		setTimeout(function(){ links[index].fade(1) },400);
	});