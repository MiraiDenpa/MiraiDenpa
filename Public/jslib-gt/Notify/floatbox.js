(function (){
	/**
	 *  全局变量
	 *  $box - 顶部通知根容器
	 *  $tray - 右上图标根容器
	 *  contents - Notify对象缓存(id->Notify)
	 *  visable_contents - Notify可见性(id->true)
	 *  is_div_show - 顶部$box当前是否可见
	 *  message_tray - 顶部通知的图标（TrayIcon）
	 */
	var $box, $tray;
	var contents = {};
	var visable_contents = [];
	var is_div_show = false;
	var message_tray = false;

	/**
	 * 显示/隐藏顶部通知
	 */
	function show_div(show, cb){
		if(is_div_show == show){
			if(cb){
				cb();
			}
			return;
		}
		is_div_show = show;
		if(show){
			$box.show().stop(true, false).transit({'top': '0', 'opacity': 1, queue: false}, cb);
		} else{
			$box.stop(true, false).transit({'top': '-2em', 'opacity': 0, queue: false}, function (){
				if(cb){
					cb();
				}
				if(!is_div_show){
					$box.hide();
				}
			});
		}
	}

	/**
	 * 初始化右上角的图标区域、顶部的根容器
	 */
	$(function (){
		// 初始化
		$box = $('<div/>').css({'position': 'fixed', 'top': '-2em', 'left': 0, 'width': '100%', 'opacity': 0, 'zIndex': 1000}).addClass('text-center').hide().appendTo($('body'));
		var tray_box = $('<div/>').css({'position': 'fixed', 'top': 0, 'right': 0, 'zIndex': 2000}).addClass('text-right').show().appendTo($('body'));
		$tray = $('<ul/>').css({'listStyle': 'none'}).appendTo(tray_box).on('mouseenter', 'li',function (){
			$(this).css({'opacity': 1});
		}).on('mouseleave', 'li', function (){
					$(this).css({'opacity': 0.7});
				});
		return $box;
	});

	var cache_tray_icon = {};
	/**
	 * @param id 图标的唯一ID
	 * @param icon 图标（glyphicon-*）
	 * @param title 生成li的标题，指上去显示
	 * @param fn 点击回调
	 * @return TrayIcon
	 * @constructor
	 */
	var TrayIcon = function (id, icon, title, fn){
		if(cache_tray_icon[id]){
			return cache_tray_icon[id];
		}
		//创建通知图标
		var $icon = $('<i/>').addClass('glyphicon glyphicon-' + icon).css({'margin': '2px'});
		var $li = $('<li/>').css({'display': 'inline', 'cursor': 'pointer', 'opacity': 0.7, 'transition': '0.4s, color 0.5s'}).append($icon).appendTo($tray);
		if(title){
			$li.attr('title', title);
		}
		if(fn){
			$li.click(fn);
		}
		return cache_tray_icon[id] = $.extend(this, {
			id      : id,
			icon    : function (newone){ // 替换图标icon
				$icon.removeClass().addClass('glyphicon glyphicon-' + newone);
				return this;
			},
			remove  : function (){ // 删除图标
				$li.remove();
				$icon = null;
				$li = null;
			},
			show    : function (){ // 显示图标
				$li.show();
				return this;
			},
			hide    : function (){ // 隐藏图标
				$li.hide();
				return this;
			},
			title   : function (newone){ // 修改title属性
				$li.attr('title', newone);
				return this;
			}, 
			click: function (fn){ // 添加一个新的回调方法
				$li.click(fn);
				return this;
			}, 
			css  : function (arg1, arg2){ // 修改css
				$li.css(arg1, arg2);
				return this;
			},
			alert   : function (type){ // 高亮图标
				$li.removeClasses('text\\-.*');
				if(type === true){
					$li.addClass('text-primary');
				} else if(type){
					$li.addClass('text-' + type);
				}
				return this;
			}
		});
	};

	/**
	 * 初始化messsage（顶部通知）的小图标，发生在第一个Notify类初始化的时候
	 */
	function init_message_tray(){
		message_tray = new TrayIcon('tray', 'info-sign', '通知', function (){
			show_div(!is_div_show);
			if(!is_div_show && visable_contents.length > 0){
				message_tray.alert(true);
			}
		});
	}

	var cache_notify = {};
	/**
	 * 用来显示一行通知
	 * x=new Notify
	 * x.content 可以得到其中内容（jquery对象）
	 *
	 * @param id 唯一ID
	 * @param $content 通知的内容，可以是jquery对象。
	 * @returns {Notify}
	 * @constructor
	 */
	var Notify = function (id, $content){
		if(cache_notify[id]){
			return cache_notify[id];
		}
		var first = false;
		if(!message_tray){
			init_message_tray();
			first = true;
		}
		if(contents[id] === undefined){
			contents[id] = $('<div/>').attr('id', id).css({'position': 'relative'});
			$($content).appendTo(contents[id]);
			if(!first){
				contents[id].css({'padding': '7px'});
			}
			$box.append(contents[id].hide());
		}

		this.__defineGetter__('content', function (){
			return contents[id];
		});

		$.extend(this, contents[id]);
		this.hide = function (hide, time){
			if(undefined === hide){
				hide = 'fadeOut';
			}
			if(undefined === time){
				time = 800;
			}
			visable_contents.remove(id);
			if(visable_contents.length == 0){
				message_tray.alert(false);
				show_div(false, function (){
					contents[id].hide();
				});
			} else if(contents[id] !== undefined){
				contents[id][hide](time);
			}
		};
		this.show = function (show, time){
			if(undefined === show){
				show = 'fadeIn';
			}
			if(undefined === time){
				time = 1000;
			}
			visable_contents.push(id);
			if(is_div_show){
				contents[id][show](time);
			} else{
				contents[id].show();
				show_div(true);
			}
		};
		this.remove = function (){
			if(contents[id] !== undefined){
				contents[id].slideUp(function (){
					contents[id].remove();
					delete contents[id];
				});
			}
			visable_contents.remove(id);
			if(visable_contents.length == 0){
				show_div(false);
			}
		};

		return cache_notify[id] = this;
	};

	$.extend(window, {
		TrayIcon: TrayIcon,
		Notify  : Notify
	});
})(window);
