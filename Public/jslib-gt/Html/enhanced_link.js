(function (){
	"use strict";

	$.fn.hlink = function (opt){
		opt = $.extend({
			href   : '',
			preview: '',
			append : [],
			app    : '',
			action : '',
			method : '',
			path   : {},
			ask    : ''
		}, opt);
		this.addClass('hlink').each(function (){
			var option = $.extend({}, opt);
			var self = $(this);
			var i, arr;

			// 替换连接
			if(self.data('href')){
				option.href = self.data('href');
			} else{
				option.href = self.attr('href');
			}

			if(self.data('app')){
				option.app = self.data('app');
			}
			if(self.data('action')){
				option.action = self.data('action');
			}
			if(self.data('method')){
				option.method = self.data('method');
			}

			/**
			 * 添加get参数
			 *          data-append="#field1->val,#field2->text"
			 *          ====>
			 *          {
			 *              $('#field1').attr('name') : $('#field1').val(),
			 *              $('#field2').attr('name') : $('#field2').text(),
			 *          }
			 *          添加到get中
			 *  也可以从 $().hlink({append:[]})传入，
			 *  [
			 *         变量名: function(){返回需要的值;}
			 *  ]
			 */
			if(self.data('append')){
				arr = self.data('append').split(',');
				for(i = 0; i < arr.length; i++){
					var kp = arr[i].split('->');
					kp = (function (kp){
						return function (param){
							var obj = $(kp[0]);
							param[obj.attr('name')] = obj[kp[1]? kp[1] : 'val']();
						};
					})(kp);
					option.append.push(kp);
				}
			}

			/**
			 * 修改path
			 *          data-path="1:#field1->val,3:#field2->text,4:#asd"
			 *          ====>
			 *          path[1] = $('#field1').val(),
			 *          path[3] = $('#field2').text(),
			 *          path[4] = '#asd', // 需要变量则必须带着 "->"
			 *          }
			 *        得到URL:
			 *          http://xxx.com/act/mtd/(1)/原来2处变量/(3)/(4)
			 *  也可以从 $().hlink({path:[]})传入，
			 *  [
			 *         顺序号: function(){返回需要的值;}
			 *  ]
			 */
			if(self.data('path')){
				var items = self.data('path').split(',');
				for(i = 0; i < items.length; i++){
					var names = items[i].split(':');
					var info = names[1].split('->');
					if(info.length == 1){
						option.path[names[0]] = (function (info){
							return function (){
								return $(info[0])[info[1]]();
							}
						})(info);
					} else{
						option.path[names[0]] = info[0];
					}
				}
			}

			// 跳转确认
			if(self.data('ask')){
				option.ask = self.data('ask');
			}

			// 鼠标指向，自动预览
			if(self.data('preview')){
				option.preview = self.data('preview');
			} else if(self.hasClass('hlink-preview')){
				option.preview = option.href;
			}

			self.data('hlink', option);
		});
	};

	$(document).on('click', '.hlink', function (e){
		var option = $(this).data('hlink');
		if(!option){
			$(this).hlink();
			option = $(this).data('hlink');
		}
		var cb = function (){
			var _href = $.modifyUrl(option.href, option);
			if(e.which == 2){
				window.open(_href);
			} else{
				window.location.href = _href;
			}
		};
		if(option.ask){
			$.dialog.confirm(option.ask, cb, '取消').title('电波娘如此询问道：');
		} else{
			cb();
		}
		return false;
	});
})();
$(function (){
	$('a.hlink,button.hlink').hlink();
});
