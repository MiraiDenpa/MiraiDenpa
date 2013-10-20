(function (window, $){
	var current_data = window.doc;
	if(current_data._id){
		$(document).on('mirai.login', initWeiboFramework);
		$('#WeiboContainer').find('.clickStart').click(initWeiboFramework);
	}

	function initWeiboFramework(){
		var container = $('#WeiboContainer');
		if(container.data('weibo')){
			return;
		}

		var list = container.find('.WBList');
		var sender = container.find('.Sender');

		var channel = container.data('weiboChannel');
		var weibo = window.weibo;

		// “点击加载评论”
		var loader = container.find('.Loader');
		loader.txt=loader.find('.text');
		loader.center=loader.find('.center');
		loader.setState = function (state){
			if(!state){
				return this.hide();
			}
			this.show();
			loader.center.css('height', this.height());
			this.txt.text(state);
		};

		// 加载评论、翻页
		var initPage = function (ret){
			var data = ret.list;
			var page = ret.page;
			list.empty();
			$(data).each(function (i, e){
				var li = new Weibo(e);
				list.append(li);
			});
			$('#mainpager').removeClass('hide').pager(page);
		};
		var loadPage = function (page){
			if(last){
				last.abort();
			}
			loader.setState('正在加载……');
			last = weibo.channel(channel, page).done(function (ret){
				if(ret.code == window.Think.ERR_NO_ERROR){
					loader.setState();
					initPage(ret);
				} else{
					loader.setState('抱歉，载入失败，请重试。');
					SimpleNotify('weibo').error(ret.message, '评论加载失败。').autoDestroy();
				}
			}).fail(function (){
						loader.setState('载入失败，服务器可能在维护或出错。');
						SimpleNotify('weibo').error('HTTP错误', '评论加载失败。').autoDestroy();
					});
			return last;
		};
		History.Adapter.bind(window, 'statechange', function (){ // Note: We are using statechange instead of popstate
			var State = History.getState(); // Note: We are using History.getState() instead of event.state
			initPage(State.data);
		});
		var statepageurl = $.modifyUrl(location.href, {}, true);
		var page_title = $('title').text();
		$('#mainpager').on('page', function (e, page, url){
			loadPage(page).done(function (ret){
				if(ret.code == window.Think.ERR_NO_ERROR){
					History.pushState(ret, page_title, statepageurl.modify({param: {p: page}}));
				}
			});
		});
		var last;

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

		// 启动
		loadPage(statepageurl.param.p);

		container.data('weibo', true);
	}

	function Weibo(wb){
		var obj = $('<li class="weibo"/>');
		obj.text(wb.content);

		return obj;
	}
})(window, jQuery);
