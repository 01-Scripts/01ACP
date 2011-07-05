tinyMCEPopup.requireLangPack();

	var FileDialog = {
		init : function() {

		},

		insertpic : function(path,file) {
			// Insert the contents from the input into the document
			var ed = tinyMCEPopup.editor, dom = ed.dom;
			var size = document.getElementsByName('maxsize')[0].value;			
			var inhalt = '<img src="'+path+'showpics.php?img='+file+'&amp;size='+size+'" alt="Hochgeladene Bilddatei" />';
				
			if(document.getElementsByName('link')[0].checked == true){
				tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {href : path+file, target: '_blank', class: 'lightbox'}, inhalt ));
				}
			else{
				tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
				src : path+'showpics.php?img='+file+'&size='+size,
				alt : 'Hochgeladene Bilddatei',
				border : 0
				}));
				}
			
			tinyMCEPopup.close();
		},
		
		insertpic_flist : function(path,file) {
			// Insert the contents from the input into the document
			var ed = tinyMCEPopup.editor, dom = ed.dom;
			var inhalt = '<img src="'+path+'showpics.php?img='+file+'&amp;size=300" alt="Hochgeladene Bilddatei" />';

			tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {href : path+file, target: '_blank', class: 'lightbox'}, inhalt ));
			tinyMCEPopup.close();
		},
		
		insertfile : function(path,file,orgname) {
			// Insert the contents from the input into the document
			var ed = tinyMCEPopup.editor, dom = ed.dom;
			
			tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {href : path+'download.php?fileid='+file, target: '_blank', class: 'lightbox'}, orgname+' herunterladen' ));
			tinyMCEPopup.close();
		}
	};

	tinyMCEPopup.onInit.add(FileDialog.init);