;
$(function (){
	$('form[type=ajax]').each(function (){
		$(this).submit(function (e){
			e.preventDefault();
			$(this).ajaxSubmit();
		});
	});
});

$.fn.ajaxSubmit = function (){
	if(this.get(0).nodeName != 'FORM'){
		return false;
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
	mute = function (){
		return false;
	};
	var controls = this.attr('disabled', 'disabled').on('click', 'a,input,button', mute);
	return $.ajax({
		url     : act,
		dataType: 'json',
		method  : this.attr('method'),
		data    : this.serialize()
	}).done(function (json){
				if(json.code){
					$.dialog.alert(json.info + '<br/>' + json.extra).title('错误：' + json.message);
				} else if(json.code === 0){
					window.location.jumpto(json.jumpurl, json.timeout, null, json.message);
				} else{
					$.dialog.alert('HTTP错误，不明觉厉。').title('出现错误');
				}
			}).fail(function (obj, stat, msg){
				$.dialog.alert('HTTP ' + obj.status + '<br/>' + msg + '<br/><br/>' +
				               '请检查网络，如果确定不是网络问题，请联系我们！').title('出现错误');
			}).always(function (){
				controls.removeAttr('disabled').off('click', 'a,input,button', mute);
			});
};
