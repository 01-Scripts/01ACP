tinyMCEPopup.requireLangPack();

	var FileDialog = {
		init : function() {

		},

		insertpic : function(path,file) {
			// Insert the contents from the input into the document

			var size = document.getElementsByName('maxsize')[0].value;
				
			if(document.getElementsByName('link')[0].checked == true){
				var link1 = '<a href="'+path+file+'" target="_blank" class="lightbox">';
				var link2 = '</a>';
				}
			else{ var link1 = ''; var link2 = ''; }
			
			var inhalt = link1+'<img src="'+path+'showpics.php?img='+file+'&size='+size+'" alt="Hochgeladene Bilddatei" />'+link2+' <br />';

			tinyMCEPopup.editor.execCommand('mceInsertContent', false, inhalt);
			tinyMCEPopup.close();
		},
		
		insertpic_flist : function(path,file) {
			// Insert the contents from the input into the document

			var link1 = '<a href="'+path+file+'" target="_blank" class="lightbox">';
			var link2 = '</a>';
			
			var inhalt = link1+'<img src="'+path+'showpics.php?img='+file+'&size=300" alt="Hochgeladene Bilddatei" />'+link2+' <br />';

			//var editorid = tinyMCE.selectedInstance.editorId;
			//tinyMCE.execInstanceCommand(editorid,'mceInsertContent', false, inhalt);
			tinyMCEPopup.editor.execCommand('mceInsertContent', false, inhalt);
			tinyMCEPopup.close();
		},
		
		insertfile : function(path,file,orgname) {
			// Insert the contents from the input into the document

			var inhalt = '<a href="'+path+'download.php?fileid='+file+'">'+orgname+' herunterladen</a> <br />';

			tinyMCEPopup.editor.execCommand('mceInsertContent', false, inhalt);
			tinyMCEPopup.close();
		}
	};

	tinyMCEPopup.onInit.add(FileDialog.init);