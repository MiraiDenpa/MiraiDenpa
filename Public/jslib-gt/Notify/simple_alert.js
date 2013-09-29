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
	var timmer = $('<div/>').css({'position': 'absolute', 'left': '50px', 'top': '5px', 'backgroundColor': 'blue', 'border-radius': '10px', 'height': '10px', 'width': '10px'});
	$div.push(timmer[0]);

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

	var _time;// 倒计时id
	return SimpleNotify.cache_notify[id] = $.extend(notify, {
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
		autoDestroy: function (set){
			if(set === undefined){
				auto_destroy = true;
			} else{
				auto_destroy = set;
			}
			return this;
		},
		hideTimeout: function (timeout){
			if(timeout){
				function cb(){
					notify.hide();
					_time = false;
					notify.content.off('mouseenter').off('mouseleave');
					if(auto_destroy){
						notify.remove();
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
function CheckStandardReturn(ret, title){
	if(ret.code){
		var msg = ret.message + '，' + ret.info;
		if(ret.redirect){
			msg += '<div style="padding-top:24px;"></div><div style="position:absolute;right:10px;bottom:0px;">';
			for(var i in ret.redirect){
				if(ret.redirect.hasOwnProperty(i)){
					msg += '<a class="btn btn-link" href="' + ret.redirect[i] + '">' + i + '</a>';
				}
			}
			msg += '</div>';
		}

		return SimpleNotify(time()).error(msg, title + '失败').autoDestroy();
	}
}
