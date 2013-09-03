(function (){
	$.fn.hlink = function (option){
		option = $.extend({append: {}, href: '', preview: '', ask: ''}, option);
		this.each(function (){
			var itemData = {};
			var self = $(this);
			var href, append, preview, ask;

			// 替换连接
			if(self.data('href')){
				href = self.data('href');
			} else if(option.href){
				href = option.href;
			} else{
				href = self.attr('href');
			}

			// 自动预览
			if(self.data('preview')){
				preview = self.data('preview');
			} else if(option.preview){
				preview = option.preview;
			} else if(self.hasClass('hlink-preview')){
				preview = href;
			}

			// 添加get参数
			if(self.data('append')){
				append = self.data('append');
				var arr = append.split(',');
				append = {};
				for(var i = 0; i < arr.length; i++){
					var kp = arr[i].split('->');
					append[kp[0]] = kp[1]? kp[1] : 'val';
				}
				append = $.extend({}, option.append, append);
				i = arr = kp = null;
			} else{
				append = option.append;
			}

			// 跳转确认
			if(self.data('ask')){
				ask = self.data('ask');
			} else if(option.ask){
				ask = option.ask;
			}

			self.click(function (e){
				var cb = function (){
					var _href;
					if(append){
						var param = [];
						for(var selector in append){
							var obj = $(selector);
							param.push(obj.attr('name') + '=' + obj[append[selector]]());
						}
						param = param.join('&');
						if(href.search('\\?') > 0){
							_href = href + '&' + param;
						} else{
							_href = href + '?' + param;
						}
					} else{
						_href = href;
					}
					if(e.which==2){
						window.open(_href);
					}else{
						window.location.href = _href;
					}
				};
				if(ask){
					$.dialog.confirm(ask, cb, '取消').title('电波娘如此询问道：');
				} else{
					cb();
				}
				return false;
			});

			if(preview){
				self.mouseenter(function (){

				}).mouseleave(function (){

						});
			}

			//window.location.href=$(this).attr('href')+'?email='+$('#email').val();
			//return false;
		});
	};
})();
$(function (){
	$('a.hlink,button.hlink').hlink();
});
