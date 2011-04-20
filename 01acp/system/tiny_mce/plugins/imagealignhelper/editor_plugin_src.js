/*

	ImageAlignHelper Plugin - by Thomas Kjoernes <thomas@ipv.no>
	http://sourceforge.net/tracker/?func=detail&aid=2904558&group_id=103281&atid=738747

	Adds classes 'justifyleft' or 'justifyright' to floated images
	for improved automatic styling.

*/

(function() {

	tinymce.create('tinymce.plugins.ImageAlignHelper', {

		init : function(editor, url) {

			editor.onNodeChange.add(function(editor, cmd, node) {

				if (node && node.nodeName.toUpperCase() === "IMG") {

					var dom = editor.dom;

					var float = tinymce.DOM.getStyle(node, "float");

					dom.removeClass(node, "justifyleft");
					dom.removeClass(node, "justifyright");

					switch (float) {
						case "left" :
							dom.addClass(node, "justifyleft");
							break;
						case "right" :
							dom.addClass(node, "justifyright");
							break;
					}

				}

			});
		}
	});

	tinymce.PluginManager.add("imagealignhelper", tinymce.plugins.ImageAlignHelper);

})();
