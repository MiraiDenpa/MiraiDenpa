(function (window, $){
	"use strict";
	// 当前文档的信息
	var current_data = window.doc;
	var self_forward = ['mirai/info-entry', current_data['_oid']];
	
	// dao
	var weibo = window.denpa;
	var request = weibo.Channel('info' + current_data['_oid']);

	// dom对象
	var template;
	var container, list, loader;

	$(function (){
		// 初始化
		container = $('#WeiboContainer');
		list = container.find('.WBList');
		loader = container.find('.Loader');
		loader.find('.center').css('height', loader.height());
		template = $('.WBList').find('li').removeClass('hide').remove();

		$(document).on('click', '#WeiboContainer .clickStart', initWeiboComment);
		window.onlogin(initWeiboComment);
		window.onlogin(initSenderBox);
	});

	function changeLoader(state){
		if(!state){
			return loader.hide();
		}
		loader.show();
		loader.center.css('height', loader.height());
		loader.txt.text(state);
	}

	// 生成列表
	var remaining_fill_list = {};
	$(document).on('mirai.user.property', function (e, property){
		if(remaining_fill_list.hasOwnProperty(property._id)){
			$(remaining_fill_list[property._id]).each(function (_, $elem){
				$elem.user.text(property.nick? property.nick : '无名');
				//var h = $elem.avatar.css('height');
				var avatarurl = avatar_url(property.avatar, /*h? h : */48);
				$elem.avatar.attr('src', avatarurl);
			});
			delete(remaining_fill_list[property._id]);
		}
	});

	function createWeiboItem(wb){
		var item = template.clone();

		// 用户
		item.user = item.find('.user>.nickname');
		item.avatar = item.find('.avatar');
		if(!remaining_fill_list[wb.user]){
			remaining_fill_list[wb.user] = [];
			weibo.request_user_property(wb.user);
		}
		remaining_fill_list[wb.user].push(item);

		// 元素
		item.at = item.find('.user>a');
		item.time = item.find('.time');
		item.content = item.find('.content');
		item.forward = item.find('.forward');
		item.beforwardcount = item.find('.beforwardcount');

		// 赋值
		item.user.text(wb.user);
		item.at.text('@' + wb.user).attr('href', 'http://' + window.Think.URL_MAP['home'] + '/@' + wb.user);

		item.time.text(date('m月d日 H:i:s', wb.time));
		item.content.html(wb.content);
		item.beforwardcount.text('(' + wb.beforwardcount + ')');

		item.addClass('level' + wb['level']);

		item.data('weibo', wb);
		item.attr('id', wb._id['$id']);
		item.data('forward', ['mirai/denpa', wb._id['$id']]);

		return item;
	}

	function createFwList(wb){
		var ul = $('<ul class="fw-list"/>').attr('id', 'fowrard' + wb._id['$id']);
		$('<li class="fw"/>').append(ul).insertAfter($('#' + wb._id['$id']));
		return ul;
	}

	function onPostWeibo(wb, contain_ul){
		var item = createWeiboItem(wb);

		if(!contain_ul){
			contain_ul = list;
		}
		contain_ul.append(item);

		// 转发列表
		if(wb.list){
			var ul = createFwList(wb);
			$(wb.list).each(function (_, wb){
				onPostWeibo(wb, ul);
			});
			if(wb.list.length == 5){
				$('<span class="display_full btn btn-default btn-sm" style="padding:5px;" title="查看更多"/>')
						.append($('<span class="glyphicon glyphicon-arrow-down"/>'))
						.data({id: wb._id['$id']})
						.insertAfter(ul);
			}
		}
	}

	$(document).on('mirai.denpa.post', function (e, content, ret, forward){
		var item = createWeiboItem({
			_id           : {$id: ret['id']},
			content       : content,
			forward       : forward,
			time          : time(),
			beforwardcount: 0,
			user          : window.user.token_data.user
		});

		var target;
		if(forward){
			target = $('#fowrard' + forward.content);
			if(!target.length){
				target = $('#' + forward.content);
				if(target.length){
					var tmp = target.parent();
					if(tmp.hasClass('WBList')){
						target = createFwList(target.data('weibo'));
					} else{
						target = tmp;
					}
				} else{
					target = list;
				}
			}
		} else{
			target = list
		}
		item.addClass('new');
		target.prepend(item);
	});

	function initWeiboComment(){
		if(!current_data || !current_data._id || initWeiboComment.loaded){
			return;
		}
		container.addClass('loaded');

		var mpager = $('#mainpager');

		// “点击加载评论”
		var loader = container.find('.Loader');
		loader.txt = loader.find('.text');
		loader.center = loader.find('.center');

		// 加载评论、翻页
		request.listHandler(function (wbl){
			list.empty();
			$(wbl).each(function (_, wb){
				onPostWeibo(wb, list);
			});
			changeLoader(false);
		});
		request.pageHandler(function (page){
			mpager.removeClass('hide').pager(page);
		});

		mpager.on('page', function (e, page){
			changeLoader('加载中……');
			request.page(page);
		});

		// 启动
		container.find('.clickStart').removeClass('clickStart');
		$(document).off('click', initWeiboComment);
		request.page();

		initWeiboComment.loaded = true;
	}

	function initSenderBox(){
		if(!current_data || !current_data._id || initSenderBox.loaded){
			return;
		}
		// 表单变量
		var sender = container.find('.Sender');
		var postform = sender.find('form');
		var forward_type = sender.find('[name=forward_type]').val('');
		var forward_content = sender.find('[name=forward_content]').val('');
		request.autoForward(new weibo.Forward(self_forward[0], self_forward[1]));
		var input_content = sender.find('[name=content]');
		// 发表新评论
		postform.submit(function (){
			if(!window.user.isLogin){
				return;
			}
			var fType = forward_type.val();
			var forward = null;
			if(fType/*  */){
				forward = new weibo.Forward(fType, forward_content.val());
			}

			var content = input_content.val();
			request.post(content, forward).done(function (ret){
				if(ret.code == window.Think.ERR_NO_ERROR){
					forward_type.val('');
					input_content.val('');
					SimpleNotify('weibo').success('', '发表成功！').autoDestroy().hideTimeout(2000);
					$(document).trigger('mirai.denpa.post', [content, ret, forward])
				} else{
					SimpleNotify('weibo').error(ret.message, '发表失败').autoDestroy();
				}
			})
		});
		// 转发
		var fTipText = sender.find('.forward-tip .text');
		var fTipContent = sender.find('.content-tip');
		list.on('click', '.btnForward', function (){
			if(!window.user.isLogin){
				return;
			}
			var $this = $(this);
			var r = $this.parentsUntil('.weibo').parent();
			var fd = r.data('forward');
			//var wb = r.data('weibo');
			forward_type.val(fd[0]);
			forward_content.val(fd[1]);

			fTipContent.empty();
			r.find('>.avatar,>div>.head>.user,>div>.body>.content').clone().appendTo(fTipContent);

			fTipText.text('转发');

			sender.addClass('forward');

			//noinspection JSValidateTypes
			$(document).scrollTop(sender.offset().top - window.outerHeight/3);
			input_content.focus();
		});
		fTipText.parent(/*parent是.tip*/).click(function (){
			sender.removeClass('forward');
			forward_type.val('');
			forward_content.val('');
		});
		initSenderBox.loaded = true;
	}
})(window, window.$);
