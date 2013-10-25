(function (window, $){
	var current_data = window.doc;
	window.onlogin(initWeiboFramework);
	$(document).on('click', '#WeiboContainer .clickStart', initWeiboFramework);
	$(function (){
		var container = $('#WeiboContainer');
		var loader = container.find('.Loader');
		loader.find('.center').css('height', loader.height());

		$(document).on({
			'mirai.login' : function (){
				container.removeClass('logout');
			},
			'mirai.logout': function (){
				container.addClass('logout');
			}
		});

		// 评分
		var maininfo = $('#mainInfo');
		$('.vote_small').click(function(){
			maininfo.toggleClass('vote_show');
			
		})
		
	});

	function initWeiboFramework(){
		if(!current_data || !current_data._id){
			return;
		}
		var container = $('#WeiboContainer').addClass('loaded');
		if(container.data('weibo')){
			return;
		}
		var list = container.find('.WBList');
		var template = list.find('li').removeClass('hide').remove();
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

		// 生成列表
		function setWeibo(wb){
			var item = template.clone();
			// 元素
			item.user = item.find('.user>.nickname');
			item.at = item.find('.user>a');
			item.time = item.find('.time');
			item.content = item.find('.content');
			item.forward = item.find('.forward');
			item.tree = item.find('.tree');
			item.beforwardcount = item.find('.beforwardcount');

			// 赋值
			item.user.text(wb.user);
			item.at.text('@' + wb.user);
			item.time.text(date('m月d日 H:i:s', wb.time));
			item.content.text(wb.content);
			if(wb.forward.type != self_forward[0] && wb.forward.content != self_forward[1]){
				item.forward.text(JSON.stringify(wb.forward));
			}
			item.beforwardcount.text('(' + wb.beforwardcount + ')');
			item.tree.text(JSON.stringify(wb.tree));
			item.attr('id', wb._id.$id);

			item.data('weibo', wb);
			item.data('forward', ['mirai/denpa', wb._id.$id]);
			return item;
		}

		// 加载评论、翻页
		var request = denpa.Channel('info' + current_data._oid);
		request.listHandler(function (wbl){
			list.empty();
			var user_list = [];
			var fill_list = {};
			$(wbl).each(function (_, wb){
				var item = setWeibo(wb);
				if(user_list.indexOf(wb.user) == -1){
					user_list.push(wb.user);
					fill_list[wb.user] = [];
				}
				fill_list[wb.user].push(item);
				list.append(item);
			});
			weibo.flush_user_property(user_list, function (property){
				if(fill_list.hasOwnProperty(property._id)){
					$(fill_list[property._id]).each(function (_, $elem){
						$elem.find('.user>.nickname').text(property.nick? property.nick : '无名');
						var av = $elem.find('.avatar');
						var h = av.css('height');
						var avatar = avatar_url(property.avatar, h? h : 48);
						av.attr('src', avatar);
					});
				}
			});
			loader.loadingStatus(false);
		});
		request.pageHandler(function (page){
			mpager.removeClass('hide').pager(page);
		});

		mpager.on('page', function (e, page){
			loader.loadingStatus('加载中……');
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
		var self_forward = [forward_type.val(), forward_content.val()];
		request.autoForward(new weibo.Forward(self_forward[0], self_forward[1]));
		var input_content = sender.find('[name=content]');
		// 发表新评论
		postform.submit(function (){
			var fType = forward_type.val();
			var forward = null;
			if(fType){
				forward = new weibo.Forward(fType, forward_content.val());
			}

			var content = input_content.val();
			request.post(content, forward).done(function (ret){
				if(ret.code == window.Think.ERR_NO_ERROR){
					forward_type.val('');
					input_content.val('');
					SimpleNotify('weibo').success('', '发表成功！').autoDestroy().hideTimeout(2000);
				} else{
					SimpleNotify('weibo').error(ret.message, '发表失败').autoDestroy();
				}
			})
		});
		// 转发
		var fTipText = sender.find('.forward-tip .text');
		var fTipContent = sender.find('.content-tip');
		list.on('click', ':not(.logout) .btnForward', function (){
			var $this = $(this);
			var r = $this.parentsUntil(list).filter('.weibo');
			var fd = r.data('forward');
			var wb = r.data('weibo');
			forward_type.val(fd[0]);
			forward_content.val(fd[1]);
			
			fTipContent.empty();
			r.find('.avatar,.user,.content').clone().appendTo(fTipContent);

			fTipText.text('转发');

			sender.addClass('forward');
		});
		fTipText.parent(/*parent是.tip*/).click(function(){
			sender.removeClass('forward');
			forward_type.val('');
			forward_content.val('');
		});

		container.data('weibo', true);
	}
})(window, jQuery);
