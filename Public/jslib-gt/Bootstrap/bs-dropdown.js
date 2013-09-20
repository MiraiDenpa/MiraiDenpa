(function ($){
	$.fn.bs_dropdown = function (){
		return this.each(function (){
			var $this = $(this);
			var $parent = getParent($this);
			var $title = $this.find('.title');
			var value = '';

			if(!$title.length){
				$title = $this;
			}
			$parent.on('click', '[role=menu] [value]', function (){
				var $a = $(this);
				if(value == $a.attr('value')){
					return;
				}
				$this.attr('value', value);
				value = $a.attr('value');
				$title.html($a.attr('title'));
				$this.trigger('change', [$this, value]);
			});
			if($this.attr('value').length){
				$parent.find('[role=menu] [value=' + $this.attr('value') + ']').click();
			}
		})
	}

	// 从bootstrap复制过来
	function getParent($this){
		var selector = $this.attr('data-target');

		if(!selector){
			selector = $this.attr('href');
			selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
		}

		var $parent = selector && $(selector);

		return $parent && $parent.length? $parent : $this.parent()
	}
})(jQuery);
$(function (){
	$('.bs-dropdown').bs_dropdown();
});
