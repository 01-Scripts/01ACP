var r = new MooRainbow('myRainbow', {
		'startColor': [58, 142, 246],
		'onChange': function(color) {
			$('HEXColorCode').value = color.hex;
		}
		});