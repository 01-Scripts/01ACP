(function(){tinymce.PluginManager.requireLangPack("filemanager");tinymce.create("tinymce.plugins.Filemanager",{init:function(a,b){a.addCommand("mceFilemanager_pic",function(){if(parent.document.getElementById("modulid")){var c=parent.document.getElementById("modulid").value}else{var c=""}a.windowManager.open({file:b+"../../../../../popups.php?action=tiny_uploader&modul="+c+"&var1=pic&var2=post&var3=newsfeld&returnvalue=tinymce",width:620,height:480,inline:1,scrollbars:"yes"},{plugin_url:b,some_custom_arg:"custom arg"})});a.addCommand("mceFilemanager_file",function(){if(parent.document.getElementById("modulid")){var c=parent.document.getElementById("modulid").value}else{var c=""}a.windowManager.open({file:b+"../../../../../popups.php?action=tiny_uploader&modul="+c+"&var1=file&var2=post&var3=newsfeld&returnvalue=tinymce",width:620,height:480,inline:1,scrollbars:"yes"},{plugin_url:b,some_custom_arg:"custom arg"})});a.addButton("filemanager_pic",{title:"filemanager_pic.desc",cmd:"mceFilemanager_pic",image:b+"/img/_pics.gif"});a.addButton("filemanager_file",{title:"filemanager_file.desc",cmd:"mceFilemanager_file",image:b+"/img/_files.gif"});a.onNodeChange.add(function(d,c,e){})},createControl:function(b,a){return null},getInfo:function(){return{longname:"File- and Picturemanager",author:"Michael Lorer",authorurl:"http://www.01-scripts.de",infourl:"",version:"1.1"}}});tinymce.PluginManager.add("filemanager",tinymce.plugins.Filemanager)})();