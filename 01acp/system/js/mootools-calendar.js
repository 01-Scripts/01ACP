/*!
 * DatePicker
 * @author Rick Hopkins (http://www.styledisplay.com/mootoolsdatepicker/)
 * @modified by Micah Nolte and Martin Vasina
 * @modified again by Arian Stolwijk (version 0.4) (http://www.aryweb.nl/voorbeelden/datepicker/, http://www.mooforum.net/scripts12/mootools-date-picker-t666.html)
 * @version 0.4
 * @classDescription A date picker object. Created with the help of MooTools v1.2.1
 * MIT-style License - http://www.opensource.org/licenses/mit-license.php
 *
 * Unkomprimierte Version der Datei: https://github.com/01-Scripts/01ACP/blob/V1.3.0/01scripts/01acp/system/js/mootools-calendar.js

-- start it up by doing this in your domready:

$$('input.DatePicker').each( function(el){
	new DatePicker(el);
});

 */

var DatePicker = new Class({

	Implements: [Options],
	
	options: {
		dayChars: 2,
		dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
		daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
		format: 'dd.mm.yyyy',
		monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
		startDay: 7,
		yearOrder: 'asc',
		yearRange: 2,
		yearStart: (new Date().getFullYear())
	},

	/* set and create the date picker text box */
	initialize: function(dp,options){
		this.setOptions(options);

		// Finds the entered date, or uses the current date
		if(dp.get('value') != '') {
			this.then = new Date(dp.value);
			this.today = new Date();
		} else {
			this.then = this.today = new Date();
		}
		// Set beginning time and today, remember the original
		this.oldYear = this.year = this.then.getFullYear();
		this.oldMonth = this.month = this.then.getMonth();
		this.oldDay = this.then.getDate();
		this.nowYear = this.today.getFullYear();
		this.nowMonth = this.today.getMonth();
		this.nowDay = this.today.getDate();

		this.options.monthNames = (this.options.monthNames && this.options.monthNames.length == 12 ? this.options.monthNames : this.options.monthNames) || this.options.monthNames; 
		this.options.daysInMonth = (this.options.daysInMonth && this.options.daysInMonth.length == 12 ? this.options.daysInMonth : this.options.daysInMonth) || this.options.daysInMonth; 
		this.options.dayNames = (this.options.dayNames && this.options.dayNames.length == 7 ? this.options.dayNames : this.options.dayNames) || this.options.dayNames;

		this.dp = dp;
		this.dp.set('id',dp.get('name'));

		this.container = this.calendar = this.active = false;
		this.dp.addEvents({
			'click': function(){this.create()}.bind(this),
			'focus': function(){this.create()}.bind(this)
		});
	},

	/* create the calendar */
	create: function(fade){
		if (this.calendar) return false;

		fade = ($type(fade) == 'boolean') ? fade : true;
		// IE does not want to fade :S
		if(Browser.Engine.trident && Browser.Engine.version <= 5){
			fade = false;
		}

		// Hide select boxes while calendar is up
		if(Browser.Engine.trident && Browser.Engine.version <= 4){
			$$('select').addClass('dp_hide');
		}
		
		/* create the outer container */
		this.container = new Element('div', {'class':'dp_container'}).injectBefore(this.dp);

		if (fade) {
			this.container.fade('hide');
		}
				
		/* create the calendar */
		this.calendar = new Element('div', {'class':'dp_cal'}).injectInside(this.container);
		
		/* create the date object */
		var date = new Date();
		
		/* create the date object */
		if (this.month && this.year) {
			date.setFullYear(this.year, this.month, 1);
		} else {
			this.month = date.getMonth();
			this.year = date.getFullYear();
			date.setDate(1);
		}
		this.year % 4 == 0 ? this.options.daysInMonth[1] = 29 : this.options.daysInMonth[1] = 28;
		
		/* set the day to first of the month */
		var firstDay = (1-(7+date.getDay()-this.options.startDay)%7);
		
		/* create the month select box */
		monthSel = new Element('select', {'id':this.dp.get('id') + '_monthSelect'});
		this.options.monthNames.each(function(item,index){
			monthSel.options[index] = new Element('option', {value: index});
			monthSel.options[index].set('text',item);
			if (this.month == index) monthSel.options[index].set('selected','selected');
		}.bind(this));
		
		/* create the year select box */
		yearSel = new Element('select', {'id':this.dp.get('id') + '_yearSelect'});
		var years = new Array();
		if (this.options.yearOrder == 'desc'){
			for (var y = this.options.yearStart; y > (this.options.yearStart - this.options.yearRange - 1); y--){
				years.include(y);
			}
		} else {
			for (var y = this.options.yearStart; y < (this.options.yearStart + this.options.yearRange + 1); y++){
				years.include(y);
			}
		}

		years.each(function(y,i){
			yearSel.options[i] = new Element('option',{value: y});
			yearSel.options[i].set('text',y);
			if (this.year == y) yearSel.options[i].set('selected','selected');			
		}.bind(this));
		
		/* start creating calendar */
		calTable = new Element('table');
		calTableThead = new Element('thead');
		calSelRow = new Element('tr');
		calSelCell = new Element('th', {'colspan':'7'});
		monthSel.injectInside(calSelCell);
		yearSel.injectInside(calSelCell);
		calSelCell.injectInside(calSelRow);
		calSelRow.injectInside(calTableThead);
		calTableTbody = new Element('tbody');
		
		/* create day names */
		calDayNameRow = new Element('tr');
		for (var i = 0; i < this.options.dayNames.length; i++) {
			calDayNameCell = new Element('th');
			calDayNameCell.appendText(this.options.dayNames[(this.options.startDay+i)%7].substr(0, this.options.dayChars)); 
			calDayNameCell.injectInside(calDayNameRow);
		}
		calDayNameRow.injectInside(calTableTbody);
		
		/* create the day cells */
		while (firstDay <= this.options.daysInMonth[this.month]){
			calDayRow = new Element('tr');
			for (i = 0; i < 7; i++){
				if ((firstDay <= this.options.daysInMonth[this.month]) && (firstDay > 0)){
					calDayCell = new Element('td', {'class':this.dp.get('id') + '_calDay', 'axis':this.year + '|' + (parseInt(this.month) + 1) + '|' + firstDay}).appendText(firstDay).injectInside(calDayRow);
				} else {
					calDayCell = new Element('td', {'class':'dp_empty'}).appendText(' ').injectInside(calDayRow);
				}
				// Show the previous day
				if ( (firstDay == this.oldDay) && (this.month == this.oldMonth ) && (this.year == this.oldYear) ) {
					calDayCell.addClass('dp_selected');
				}
				// Show today
				if ( (firstDay == this.nowDay) && (this.month == this.nowMonth ) && (this.year == this.nowYear) ) {
					calDayCell.addClass('dp_today');
				}
				firstDay++;
			}
			calDayRow.injectInside(calTableTbody);
		}
		
		/* table into the calendar div */
		calTableThead.injectInside(calTable);
		calTableTbody.injectInside(calTable);
		calTable.injectInside(this.calendar);

		if (fade) {
			this.container.fade('in');
		}
		
		/* set the onmouseover events for all calendar days */
		$$('td.' + this.dp.get('id') + '_calDay').each(function(el){
			el.addEvents({
				'mouseover': function(){
					el.addClass('dp_roll');
				},
				'mouseout': function(){
					el.removeClass('dp_roll');
				},
				'click': function(){
					ds = el.axis.split('|');
					this.dp.value = this.formatValue(ds[0], ds[1], ds[2]);
					this.remove();				
				}.bind(this)			
			});
		}.bind(this));
		
		/* set the onchange event for the month & year select boxes */
		[monthSel,yearSel].each(function(elmt){
			elmt.addEvents({
				'focus': function(){ 
					this.active = true; 
				}.bind(this),
				'change': function(){
					this.month = monthSel.value;
					this.year = yearSel.value;
					this.remove(false);
					this.create(false);
					this.active = false;
					this.dp.focus();
				}.bind(this)
			});
		}.bind(this));

		this.dp.addEvent('blur',function(){
			(function(){
				if (!this.active) {
					this.remove();
				}				
			}.bind(this)).delay(500);
		}.bind(this))
	},
	
	/* Format the returning date value according to the selected formation */
	formatValue: function(year, month, day){
		/* setup the date string variable */
		var dateStr = '';
		
		/* check the length of day */
		if (day < 10) day = '0' + day;
		if (month < 10) month = '0' + month;
		
		/* check the format & replace parts // thanks O'Rey */
		dateStr = this.options.format.replace( /dd/i, day ).replace( /mm/i, month ).replace( /yyyy/i, year );
		this.month = this.oldMonth = '' + (month - 1) + '';
		this.year = this.oldYear = year;
		this.oldDay = day;
		
		/* return the date string value */
		return dateStr;
	},
	
	/* Remove the calendar from the page */
	remove: function(fade){
		fade = ($type(fade) == 'boolean') ? fade : true;
		if(Browser.Engine.trident && Browser.Engine.version <= 5){
			fade = false;
		}
		if ($type(this.container) == 'element') {
			if (fade != false) {
				this.container.fade('out');
			}else{
				this.container.dispose();
			}
		}
		this.active = this.container = this.calendar = false;
		$$('select.dp_hide').removeClass('dp_hide');
	}
});

Element.implement({
	DatePicker: function (options){
		new DatePicker(this,options);
		return this;
	}
});