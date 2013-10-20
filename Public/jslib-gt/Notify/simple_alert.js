function SimpleNotify(id){
	if(!SimpleNotify.cache_notify){
		SimpleNotify.cache_notify = {}
	}
	if(SimpleNotify.cache_notify[id]){
		return SimpleNotify.cache_notify[id];
	}

	var $div = $('<div/>').css({'display': 'inline-block', 'margin': 'auto', 'paddingLeft': '40px', 'paddingRight': '40px', 'position': 'relative'}).addClass('alert');
	var $title_container = $('<div class="clearfix"/>').appendTo($div);
	var $title = $('<h4/>').css({'position': 'inline-block', 'max-width': '440px', 'overflow': 'hidden', 'white-space': 'nowrap', 'text-align': 'center', 'margin': 'auto'}).appendTo($title_container);
	var $closer = $('<a class="text-muted" style="vertical-align:middle;line-height:26px;position:absolute;right:10px;top:5px;"/>').html('<i class="glyphicon glyphicon-remove"></i>').appendTo($title_container);
	var $message = $('<div class="text-left"/>').insertAfter($title_container);
	var timmer = $('<div/>').css({'position': 'absolute', 'left': '5px', 'top': '5px', 'backgroundColor': 'blue', 'border-radius': '10px', 'height': '10px', 'width': '10px'});
	timmer.prependTo($div);
	var notify = new Notify('simple_alert' + id, $div);
	var auto_destroy = false;

	var move_in_call;

	function change_content(type, content, title){
		$message.empty().append(content);
		if(!title){
			title = '';
		}
		$title.html(title);
		if(!$div.hasClass('alert-' + type)){
			$div.removeClasses('alert\\-.*').addClass('alert-' + type);
		}
		if(move_in_call){
			move_in_call();
		}
		notify.show();
		return this;
	}

	$closer.click(function (){
		notify.hide();
	});

	SimpleNotify.cache_notify[id] = $.extend(this, notify);
	var _time;// 倒计时id
	this.info = function (text, title){
		change_content('info', text, title);
		return this;
	};
	this.warning = function (text, title){
		change_content('warning', text, title);
		return this;
	};
	this.success = function (text, title){
		change_content('success', text, title);
		return this;
	};
	this.error = function (text, title){
		change_content('danger', text, title);
		return this;
	};
	this.remove = function (){
		delete SimpleNotify.cache_notify[id];
		$div.remove();
		timmer.remove();
		$message.remove();
		$closer.remove().off();
		$title.remove();
		$title_container.remove();
		$title_container.remove();
		notify.remove();
		timmer = notify = move_in_call = $message = $closer = $title = $title_container = $div = null;
	};
	this.autoDestroy = function (set){
		if(set === undefined){
			auto_destroy = true;
		} else{
			auto_destroy = set;
		}
		return this;
	};
	this.hideTimeout = function (timeout){
		if(timeout){
			function cb(){
				notify.hide();
				_time = false;
				notify.content.off('mouseenter').off('mouseleave');
				if(auto_destroy){
					this.remove();
				}
			}

			function i(){
				timmer.stop(true, false).css({
					opacity: 1
				});
				clearTimeout(_time);
				_time = 0;
				notify.content.on('mouseleave', o).off('mouseenter');
				move_in_call = false;
			}

			move_in_call = i;

			function o(){
				timmer.stop(true, false).css({
					opacity: 1
				}).animate({
							opacity: 0
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
	};
	return this;
}
