tinyMCEPopup.requireLangPack();

	var FileDialog = {
		init : function() {

		},

		insertgalpics : function() {
			// Insert the contents from the input into the document

			var pics  = document.getElementsByName('pics_anzahl')[0].value;
			var egalid = document.getElementById('sel_galid');
			var galid = egalid.options[egalid.selectedIndex].value;
			
            var inhalt = '{Insert#'+pics+'GalleryPicsFrom#'+galid+'}';

			if(pics > 0 && galid > 0){
                tinyMCEPopup.editor.execCommand('mceInsertContent', false, inhalt);
                tinyMCEPopup.close();
                }
		},

		insertpic : function(path,file) {
			// Insert the contents from the input into the document
			var ed = tinyMCEPopup.editor, dom = ed.dom;
				
            // Original Dateigröße oder kleinere Kantenlänge?
            if(document.getElementsByName('usesize')[0].checked == true){
				var size = '';
			}
            else{
                var size = document.getElementsByName('maxsize')[0].value;
            }			
			var inhalt = '<img src="'+path+'showpics.php?img='+file+'&amp;size='+size+'" alt="Bild" />';
            
            if(document.getElementsByName('link')[0].checked == true){
				tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {href : path+file, target: '_blank', class: 'lightbox'}, inhalt ));
				}
			else{
				tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
				src : path+'showpics.php?img='+file+'&size='+size,
				alt : 'Bild',
				border : 0
				}));
			}
			
			tinyMCEPopup.close();
		},
		
		insertpic_flist : function(path,file,size) {
			// Insert the contents from the input into the document
			var ed = tinyMCEPopup.editor, dom = ed.dom;
			var inhalt = '<img src="'+path+'showpics.php?img='+file+'&amp;size='+size+'" alt="Bild" />';

			tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {href : path+file, target: '_blank', class: 'lightbox'}, inhalt ));
			tinyMCEPopup.close();
		},
		
		insertfile : function(path,file,orgname) {
			// Insert the contents from the input into the document
			var ed = tinyMCEPopup.editor, dom = ed.dom;
			
			tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {href : path+'download.php?fileid='+file, target: '_blank'}, orgname+' herunterladen' ));
			tinyMCEPopup.close();
		}
	};

tinyMCEPopup.onInit.add(FileDialog.init);