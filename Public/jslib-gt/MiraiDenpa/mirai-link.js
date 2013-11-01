$(function (){
	"use strict";
	var dispatcher = {};
	$('.mirai-link').on('click',  function (e){
		console.log(e);
		return;
		if($(this).data('mirai-link')){
			return $(this).data('mirai-link')();
		}
		var self = $(this);
		var data = self.data();
		var fn = dispatcher[data.type];
		if(!fn){
			throw new Error('miraiLink 回调类型不存在[' + data.type + ']');
		}
		delete(data.type);
		var arg = [];
		for(var i in fn.args){
			arg.push(data[fn.args[i]]);
		}
		self.data('mirai-link', function (){
			return fn.apply(self, arg);
		});
		return fn.apply(self, arg);
	});

	dispatcher.WeiboDetail = function (id){
		location.href = 'http://'+window.Think.URL_MAP['weibo']+'/'+id;
	};
	auto(dispatcher.WeiboDetail);

	function auto(fn){
		var str = fn.toString();
		var args = str.match(/function.*\((.*?)\)/);
		return fn.args = args[1].split(',');
	}
});
