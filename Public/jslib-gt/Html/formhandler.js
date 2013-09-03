$(function (){
	$('form[type=ajax]').each(function (){
		$(this).removeAttr('type').submit(function (e){
			e.preventDefault();
			$(this).ajaxSubmit();
		});
	});
});

$.fn.ajaxSubmit = function (){
	if(this.get(0).nodeName != 'FORM'){
		return false;
	}
	if(!this.data('notify')){
		this.data('notify', new SimpleNotify(this[0].id));
	}
	var notify = this.data('notify');

	var target = this.attr('target');
	if(target){
		target = target == '_blank';
	} else{
		target = false;
	}
	var act = this.data('action');
	if(!act){
		act = this.attr('action');
		if(act.search('.html')){
			act = act.replace('.html', '.form');
		} else if(act.search('?')){
			act = act.replace('?', '.form?');
		} else{
			act += '.form';
		}
		this.data('action', act);
	}
	function mute(){
		return false;
	}

	notify.info('正在请求');
	var data = this.serialize();

	var controls = this.on('click', 'a,input,button', mute).find('input,button').attr('disabled', 'disabled');
	return $.ajax({
		url     : act,
		dataType: 'json',
		method  : this.attr('method'),
		data    : data,
		context : this
	}).done(function (json){
				if(json.code){
					if(json.jumpurl){
						notify.hide();
						window.location.jumpto(json.jumpurl, json.timeout, null, json.message, 'error', target);
						return;
					} else{
						notify.error(json.extra? json.extra : json.info, json.name + ': ' + json.message);
					}
				} else if(json.code === 0){
					if(json.jumpurl){
						notify.hide('slideUp',2000);
						window.location.jumpto(json.jumpurl, json.timeout, null, json.message, 'success', target);
						return;
					} else{
						notify.success(json.extra? json.extra : json.info, json.message);
					}
				} else{
					notify.warning('', '服务器错误，不明觉厉。');
				}
				notify.hideTimeout(2000);
			}).fail(function (obj, stat, msg){
				notify.error(msg + '<br/>' + '请检查网络，如果确定不是网络问题，请联系我们！', 'HTTP ' + obj.status);
			}).always(function (){
				controls.removeAttr('disabled');
				this.off('click', 'a,input,button', mute);
			});
};
