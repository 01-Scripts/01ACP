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