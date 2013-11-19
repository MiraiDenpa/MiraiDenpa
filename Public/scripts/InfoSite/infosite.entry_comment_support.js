function StandartInfoSiteWeiboList(options){
	"use strict";
	// dao
	var weibo = window.denpa;

	var request = options.request,
			container = options.container,
			pager = options.pager || container.find('.page_container .pagination'),
			ReadOnly = options['ReadOnly'],
			FetchOn = options['FetchOn'];

	// dom对象
	var template;
	var list = container.find('.WBList');

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
		if(wb.list && wb.list.length){
			var ul = createFwList(wb);
			$(wb.list).each(function (_, wb){
				onPostWeibo(wb, ul);
			});
			if(wb.list.length == 5){
				$('<span class="display_full btn btn-default btn-sm" style="padding:5px;" title="查看更多"/>')
						.append($('<span class="glyphicon glyphicon-arrow-down"/>'))
						.data({id: wb._id['$id']})
						.insertAfter(ul).click(function (e){
							var id = $(this).data('id');
							if(!id){
								return;
							}
							if(e.which === 2){
								window.open('http://' + window.Think.URL_MAP['weibo'] + '/' + id);
							} else{

							}
						});
			}
		}
	}

	function newItemCreated(e, content, forward){
		var item = createWeiboItem({
			_id           : {$id: e['id']},
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
	}

	var channels = {};

	function handleChannel(channel){
		request = channel;
		if(channels[channel.id]){
			return;
		}
		channels[channel.id] = true;
		// 加载评论、翻页
		channel.listHandler(function (wbl){
			list.empty();
			$(wbl).each(function (_, wb){
				onPostWeibo(wb, list);
			});
			container.trigger('mirai.denpa.statechange', ['ready']);
		});
		channel.pageHandler(function (page){
			pager.removeClass('hide').pager(page);
		});
	}

	var comment_loaded = false, sender_loaded = false;

	function initWeiboComment(){
		if(!comment_loaded){
			container.addClass('loaded');
			pager.on('page', function (e, page){
				container.trigger('mirai.denpa.statechange', ['ongoing']);
				request.page(page);
			});

			// 启动
			$(document).off('click', initWeiboComment);

			handleChannel(request);
			comment_loaded = true;
		}
		request.page();
		container.trigger('mirai.denpa.statechange', ['ongoing']);
	}

	var sender;
	var forward_type;
	var forward_content;

	function initSenderBox(){
		if(sender_loaded){
			return;
		}
		// 表单变量
		sender = container.find('.Sender');
		forward_type = sender.find('[name=forward_type]').val('');
		forward_content = sender.find('[name=forward_content]').val('');
		var postform = sender.find('form');
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
					ret.type = 'mirai.denpa.postsuccess';
				} else{
					ret.type = 'mirai.denpa.postfail';
				}
				ret.module_id = request.id;
				newItemCreated(ret, content, forward);
				container.trigger(ret, [content, forward]);
			})
		});
		// 转发
		var fTipText = sender.find('.forward-tip').click(container.cancelForward)
				.find('.text');
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
			container.offsetParent().scrollTop(sender.offset().top - window.outerHeight/3);
			input_content.focus();
		});
		sender_loaded = true;
	}

	// 初始化
	template = container.find('.WBList li').removeClass('hide').remove();
	if(FetchOn === 'auto'){
		setTimeout(initWeiboComment, 0);
	} else if(FetchOn === 'login'){
		window.onlogin(initWeiboComment);
	} else if(FetchOn === 'manual'){
		// run throw
	} else{
		throw new Error('FetchOn 参数必须取 auto|login|manual 之一 (current=' + FetchOn + ')');
	}
	if(ReadOnly){
		container.find('.Sender').remove();
	} else{
		window.onlogin(initSenderBox);
	}

	container.clear = function (){
		list.empty();
	};

	container.initWeiboComment = initWeiboComment;
	container.handleChannel = handleChannel;
	container.toForward = function (){

	};
	container.cancelForward = function (){
		sender.removeClass('forward');
		forward_type.val('');
		forward_content.val('');
	};
	return container;
}
