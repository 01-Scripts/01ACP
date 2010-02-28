var mySortables = new Sortables('sortliste', {
    revert: { duration: 500, transition: 'elastic:out' },
	onComplete: function(){
		$('sortdatafield' ).value = this.serialize(); }
	});