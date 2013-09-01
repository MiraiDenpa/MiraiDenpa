(function (){
	var $box;
	var contents = {};
	var show = [];
	var is_show = false;

	function show_div(show){
		is_show = show;
		if(show){
			$box.show().transit({'top': '0', 'opacity': 1});
		} else{
			$box.transit({'top': '-2em', 'opacity': 0}, function (){
				$box.hide();
			});
		}
	}

	function top_box_init(){
		if($box){
			return $box;
		}
		$box = $('<div/>').css({'position': 'fixed', 'top': '-2em', 'left': 0, 'width': '100%', 'opacity': 0}).addClass('text-center').hide().appendTo($('body'));
		return $box;
	}

	function top_box_add_content(id, $content){
		if(contents[id] === undefined){
			contents[id] = $($content).append('<br/>').attr('id', id);
			$box.append(contents[id].slideUp(0));
		}
		show.push(id);
		if(is_show){
			contents[id].slideDown('fast');
		} else{
			show_div(true);
		}
	}

	function top_box_hide_content(id){
		if(contents[id] !== undefined){
			contents[id].slideUp('fast');
		}
		show.remove(id);
		if(show.length == 0){
			show_div(false);
		}
	}

	function top_box_remove_content(id){
		if(contents[id] !== undefined){
			contents[id].slideUp(function (){
				contents[id].remove();
				delete contents[id];
			});
		}
		show.remove(id);
		if(show.length == 0){
			show_div(false);
		}
	}

	$.extend(window, {
		top_box_init          : top_box_init,
		top_box_add_content   : top_box_add_content,
		top_box_remove_content: top_box_remove_content,
		top_box_hide_content  : top_box_hide_content
	});
})(window);
