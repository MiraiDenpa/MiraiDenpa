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
		}
	});
}
function LogStandardReturn(dfd, title){
	/*function dispatch_standard_object(obj){
		var msg = obj.message + '，' + obj.info;
		if(obj.redirect){
			msg += '<div style="padding-top:24px;"></div><div style="position:absolute;right:10px;bottom:0px;">';
			for(var i in obj.redirect){
				if(obj.redirect.hasOwnProperty(i)){
					msg += '<a class="btn btn-link" href="' + obj.redirect[i] + '">' + i + '</a>';
				}
			}
			msg += '</div>';
		}
		return SimpleNotify(time()).error(msg, title + '失败').hideTimeout(1000).autoDestroy();
	}*/

	dfd.done(function (ret){
		if(typeof ret === 'string'){
			try{
				ret = JSON.parse(ret);
			} catch(e){
				console.groupCollapsed('△失败：' + title + '，返回内容不是json。');
				console.dir(e);
				console.groupEnd();
			}
		}
		if(ret.code){
			console.groupCollapsed('△失败： ' + title + '，返回消息： ' + ret.message + '，' + ret.info);
			console.dir(ret);
			console.groupEnd();
			//dispatch_standard_object(ret);
		} else if(JS_DEBUG){
			console.groupCollapsed('●成功： ' + title);
			console.dir(ret);
			console.groupEnd();
		}
	});
	if(JS_DEBUG){
		dfd.fail(function (xhr, state, error){
			error = typeof error == 'string'? error : error.message;
			console.groupCollapsed('△失败:' + title + '，HTTP错误 [' + state + ']: ' + error);
			console.dir({response: xhr.responseText});
			console.groupEnd();
		});
	}
	return dfd;
}
