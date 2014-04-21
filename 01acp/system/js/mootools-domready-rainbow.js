/*! Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.3.0/01scripts/01acp/system/js/mootools-domready-rainbow.js */
var r = new MooRainbow('myRainbow', {
		'startColor': [58, 142, 246],
		'onChange': function(color) {
			$('HEXColorCode').value = color.hex;
		}
		});