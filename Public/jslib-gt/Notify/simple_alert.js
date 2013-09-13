function SimpleNotify(id){
	var $div = $('<div/>').css({'display': 'inline-block', 'margin': 'auto', 'paddingLeft': '40px', 'paddingRight': '40px'}).addClass('alert');
	var $title = $('<h3/>').css({'position': 'inline-block', 'max-width': '440px', 'overflow': 'hidden', 'white-space': 'nowrap', 'text-align': 'center', 'margin': 'auto'}).appendTo($div);
	var $message = $('<div/>').insertAfter($title);
	var timmer = $('<div/>').css({'position': 'absolute', 'left': '50px', 'top': '5px', 'backgroundColor': 'blue', 'border-radius': '10px', 'height': '10px', 'width': '10px'});
	$div.push(timmer[0]);

	var notify = new Notify('simple_alert' + id, $div);

	function change_content(type, content, title){
		$message.empty().append(content);
		if(!title){
			title = '';
		}
		$title.html(title);
		if(!$div.hasClass('alert-' + type)){
			$div.removeClasses('alert\\-.*').addClass('alert-' + type);
		}
		notify.show();
		return this;
	}

	var _time;// 倒计时id
	return $.extend(notify, {
		info       : function (text, title){
			change_content('info', text, title);
			return this;
		},
		warning    : function (text, title){
			change_content('warning', text, title);
			return this;
		},
		success    : function (text, title){
			change_content('success', text, title);
			return this;
		},
		error      : function (text, title){
			change_content('danger', text, title);
			return this;
		},
		remove     : function (){
			this.remove();
			$div.remove();
			notify = $div = $title = $message = null;
		},
		hideTimeout: function (timeout){
			if(timeout){
				function cb(){
					notify.hide();
					_time = false;
					notify.content.off('mouseenter').off('mouseleave');
				}

				function i(){
					timmer.stop(true, false).css({
						opacity: 1
					});
					clearTimeout(_time);
					_time = 0;
					notify.content.on('mouseleave', o).off('mouseenter');
				}

				function o(){
					timmer.stop(true, false).css({
						opacity: 1
					}).animate({
								opacity: 0.1
							}, timeout);
					if(_time){
						clearTimeout(_time);
						notify.content.off('mouseenter');
					}
					_time = setTimeout(cb, timeout);
					notify.content.off('mouseleave').on('mouseenter', i);
				}
				o();
			} else{
				this.hide();
			}
		}
	});
}
