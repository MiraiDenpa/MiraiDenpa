"use strict";
$.fn.ajaxSubmit = function (){
	var dfd = new $.Deferred();
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
	$.ajax({
		url     : act,
		dataType: 'json',
		method  : this.attr('method'),
		data    : data,
		context : this
	}).done(function (json){
				if(json.code){
					if(json.jumpurl){
						notify.hide();
						window.location.jumpto(json.jumpurl, json.timeout, json.jumpname, json.message, 'error', target);
						return;
					} else{
						notify.error(json.extra? json.extra : json.info, json.name + ': ' + json.message);
					}
					dfd.reject(json);
				} else if(json.code === 0){
					if(json.jumpurl){
						notify.hide('slideUp', 2000);
						window.location.jumpto(json.jumpurl, json.timeout, json.jumpname, json.message, 'success', target);
						return;
					} else{
						notify.success(json.extra? json.extra : json.info, json.message);
					}
					dfd.resolve(json);
				} else{
					notify.warning('', '服务器错误，不明觉厉。');
					dfd.reject(json);
				}
				notify.hideTimeout(2000);
			}).fail(function (obj, stat, msg){
				notify.error(msg + '<br/>' + '请检查网络，如果确定不是网络问题，请联系我们！', 'HTTP ' + obj.status);
				dfd.reject();
			}).always(function (){
				controls.removeAttr('disabled');
				this.off('click', 'a,input,button', mute);
			});
	return dfd;
};

$(function (){
	var no_ajax_submit = false;
	var frmCnt = $('form[type=ajax]').each(function (){
		$(this).removeAttr('type').submit(function (e){
			if(no_ajax_submit){
				return true;
			}
			e.preventDefault();
			$(this).ajaxSubmit();
		});
	}).length;
	if(frmCnt){
		window.disableAutoAjax = function (arg){
			no_ajax_submit = arg !== false;
		}
	}
});
