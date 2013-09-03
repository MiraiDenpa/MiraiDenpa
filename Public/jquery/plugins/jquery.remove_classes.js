jQuery.fn.extend({
	removeClasses: function(regex){
		var reg = new RegExp('(\\s|^)'+regex+'(\\s|$)','g');
		return $(this).each(function(i,e){
			this.className = this.className.replace(reg, '');
		});
	}
});
