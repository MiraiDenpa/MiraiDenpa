$(function (){
	$(document).on('click', '.gt-switcher', function (e){
		if(e.which != 1){
			return;
		}
		var $parent = $(this);
		var $this = $parent.find('>:visible');
		var $nxt = $this.next();
		if(!$nxt.length){
			$nxt = $parent.children().first();
		}

		$this.hide();
		$nxt.show();

		var value = $nxt.attr('value');
		$parent.attr('value', value);
		$parent.trigger('change', [$parent, value, $nxt]);
	});
	$('.gt-switcher').each(function (){
		var val = $(this).attr('value');
		if(!val){
			return;
		}
		$(this).find('>:visible').hide();
		$(this).find('[value='+val+']').show();
	});
});
