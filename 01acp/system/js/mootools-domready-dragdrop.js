/* Drag & Drop-Funktion based on http://davidwalsh.name/mootools-drag-drop-lock */
/* Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.3.0/01scripts/01acp/system/js/mootools-domready-dragdrop.js */

document.ondragstart = function () { return false; }; //IE drag hack
			
//for every dragable image...
$$('.dragable').each(function(drag) {
	
	//this is where we'll save the initial spot of the image
	var position = drag.getPosition();
	
	//make it dragable, and set the destination divs
	new Drag.Move(drag, { 
		droppables: '.droppable',
		onDrop: function(el,droppable) {
			if(droppable){
				AjaxRequest.send('modul=01acp&ajaxaction=file_changedir&fileid='+el.id+'&dirid='+droppable.id+'');
				droppable.removeClass('dropzone_entered');
				this.detach();
				}
			else{
				el.setStyles({'left':0,'top':0,'position':'relative','margin':0}); //hack -- wtf?
				}
		},
		onEnter: function(el,droppable) {
			droppable.addClass('dropzone_entered');
		},
		onLeave: function(el,droppable) {
			droppable.removeClass('dropzone_entered');
		}
	});
});