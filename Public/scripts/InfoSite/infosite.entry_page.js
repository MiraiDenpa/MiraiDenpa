(function (window, $){
	var current_data = window.doc;
	$(document).on('mirai.login', initWeiboFramework);
	$(document).on('click', '#WeiboContainer .clickStart', initWeiboFramework);
	$(function (){
		var loader = $('#WeiboContainer').find('.Loader');
		loader.find('.center').css('height', loader.height());
	});

	function initWeiboFramework(){
		if(!current_data || !current_data._id){
			return;
		}
		var container = $('#WeiboContainer');
		if(container.data('weibo')){
			return;
		}
		var list = container.find('.WBList');
		var mpager = $('#mainpager');
		var sender = container.find('.Sender');

		var channel = container.data('weiboChannel');
		var weibo = window.denpa;

		// “点击加载评论”
		var loader = container.find('.Loader');
		loader.txt = loader.find('.text');
		loader.center = loader.find('.center');
		loader.loadingStatus = function (state){
			if(!state){
				return this.hide();
			}
			this.show();
			loader.center.css('height', this.height());
			this.txt.text(state);
		};

		// 加载评论、翻页
		var request = denpa.Channel(current_data._oid);
		request.listHandler(function (wbl){
			list.empty();
			$(wbl).each(function (_, wb){
				list.append($('<li/>').text(_+' -> '+wb.content))
			});
		});
		request.pageHandler(function (page){
			mpager.removeClass('hide').pager(page);
		});

		mpager.on('page', function (e, page){
			request.page(page);
		});

		// 启动
		container.find('.clickStart').removeClass('clickStart');
		$(document).off('click', initWeiboFramework);
		request.page();

		// 表单变量
		var postform = sender.find('form');
		var forward_type = sender.find('[name=forward_type]');
		var forward_content = sender.find('[name=forward_content]');
		var default_type = forward_type.val();
		var defaults_content = forward_content.val();
		var input_content = sender.find('[name=content]');
		// 发表新评论
		postform.submit(function (){
			var wb = {
				channel: channel
			};

			if(forward_type.val() && forward_content.val()){
				wb.forward = new weibo.Forward(forward_type.val(), forward_content.val());
			}
			wb.content = input_content.val();
			weibo.post(wb).done(function (ret){
				if(ret.code == window.Think.ERR_NO_ERROR){
					forward_type.val(default_type);
					forward_content.val(defaults_content);
					SimpleNotify('weibo').success('', '发表成功！').autoDestroy().hideTimeout(2000);
				} else{
					SimpleNotify('weibo').error(ret.message, '发表失败').autoDestroy();
				}
			})
		});

		container.data('weibo', true);
	}
})(window, jQuery);
