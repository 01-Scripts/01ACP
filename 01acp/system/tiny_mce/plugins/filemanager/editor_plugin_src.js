/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Michael Lorer
 * @copyright Copyright 2008-2009 by Michael Lorer - 01-Scripts.de
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('filemanager');

	tinymce.create('tinymce.plugins.Filemanager', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceFilemanager_pic', function() {
                if(parent.document.getElementById('modulid')){
                    var modul = parent.document.getElementById('modulid').value;
                    }
                else{ var modul = ''; }
                ed.windowManager.open({
					//file : url + '/dialog.htm',
					file : url+'../../../../../popups.php?action=tiny_uploader&modul='+modul+'&var1=pic&var2=post&var3=newsfeld&returnvalue=tinymce',
					width : 620,
					height : 480,
					inline : 1,
					scrollbars: 'yes'
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
            });
			ed.addCommand('mceFilemanager_file', function() {
                if(parent.document.getElementById('modulid')){
                    var modul = parent.document.getElementById('modulid').value;
                    }
                else{ var modul = ''; }
                ed.windowManager.open({
					//file : url + '/dialog.htm',
					file : url+'../../../../../popups.php?action=tiny_uploader&modul='+modul+'&var1=file&var2=post&var3=newsfeld&returnvalue=tinymce',
					width : 620,
					height : 480,
					inline : 1,
					scrollbars: 'yes'
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});
			ed.addCommand('mceFilemanager_gal2art', function() {
                if(parent.document.getElementById('modulid')){
                    var modul = parent.document.getElementById('modulid').value;
                    }
                else{ var modul = ''; }
                ed.windowManager.open({
					//file : url + '/dialog.htm',
					file : url+'../../../../../popups.php?action=art2gal&modul='+modul+'&var1=&var2=post&var3=newsfeld&returnvalue=tinymce',
					width : 620,
					height : 480,
					inline : 1,
					scrollbars: 'yes'
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register example button
			ed.addButton('filemanager_pic', {
				title : 'filemanager_pic.desc',
				cmd : 'mceFilemanager_pic',
				image : url + '/img/_pics.gif'
			});
			ed.addButton('filemanager_file', {
				title : 'filemanager_file.desc',
				cmd : 'mceFilemanager_file',
				image : url + '/img/_files.gif'
			});
			ed.addButton('filemanager_gal2art', {
				title : 'filemanager_gal2art.desc',
				cmd : 'mceFilemanager_gal2art',
				image : url + '/img/_photo.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				//cm.setActive('filemanager.desc', n.nodeName == 'IMG');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'File- and Picturemanager for TinyMCE',
				author : 'Michael Lorer',
				authorurl : 'http://www.01-scripts.de',
				infourl : '',
				version : "1.2"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('filemanager', tinymce.plugins.Filemanager);
})();