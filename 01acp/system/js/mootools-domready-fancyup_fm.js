	up = new FancyUpload2($('fancy-status'), $('fancy-list'), { // options object
		// we console.log infos, remove that in production!!
		verbose: true,

		// url is read from the form, so you just have to change one place
		url: $('fancy-form').action,

		// path to the SWF file
		path: 'system/js/Swiff.Uploader.swf',

		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'fancy-browse',

		data: {
			'ajaxaction': 'fancyupload'
		},

		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('fancy-status').removeClass('hide'); // we show the actual UI
			$('fancy-fallback').destroy(); // ... and hide the plain form

			// We relay the interactions with the overlayed flash to the link
			this.target.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});

			// Interactions for the 2 other buttons

			$('fancy-clear').addEvent('click', function() {
				up.remove(); // remove all files
				return false;
			});

			$('fancy-upload').addEvent('click', function() {
				up.start(); // start upload
				return false;
			});
		},

		// Edit the following lines, it is your custom event handling
		
		/**
		 * Is called when files were not added, "files" is an array of invalid File classes.
		 *
		 * This example creates a list of error elements directly in the file list, which
		 * hide on click.
		 */
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('FancyUpload', 'removeTitle'),
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).inject(this.list, 'top');
			}, this);
		},

		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
		 */
		onFileSuccess: function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});

			if (json.get('status') == '1') {
				file.element.addClass('file-success');
				file.info.set('html', 'Datei wurde erfolgreich hochgeladen');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', 'Es trat ein Fehler auf ' + (json.get('error') ? (json.get('error')) : response));
			}
		},

		/**
		 * onFail is called when the Flash movie got bashed by some browser plugin
		 * like Adblock or Flashblock.
		 */
		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					ShowAjaxError('Bitte setzen Sie den Multiuploader auf eine Whitlist in Ihrem Browser, um ihn zu nutzen (Adblock)');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					ShowAjaxError('Bitte heben Sie die Blockade des Flash-Plugins auf, um den Multiuploader zu nutzen (Flashblock)');
					break;
				case 'empty': // Oh oh, wrong path
					ShowAjaxError('Systemfehler: Eine Datei konnte nicht gefunden werden');
					break;
				case 'flash': // no flash 9+ :(
					$('fancy-status').destroy();
					ShowAjaxError('Um den Multiuploader nutzen zu k�nnen muss ein Adobe Flash-Plugin V9 oder h�her installiert werden')
			}
		}

	});