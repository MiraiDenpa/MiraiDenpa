// 评分
$(function (){
	"use strict";
	var current_data = window.doc;

	var DRAGGER_SIZE = 34;

	var maininfo = $('#mainInfo');
	var vote_instanced = false;
	var user_vote = null;
	var container = $('.vote_large .contain');
	var bars = [];

	// 评分面板的 显示与隐藏
	$('.vote_small').click(function (){
		if(!vote_instanced){
			instance_vote();
		}
		maininfo.toggleClass('vote_show');
		bootUserVote();
	});

	$('#WeiboContainer').register_middle_hack('.display_full', function (e){
		var id = $(this).data('id');
		if(!id){
			return;
		}
		if(e.which === 2){
			window.open('http://' + window.Think.URL_MAP['weibo'] + '/' + id);
		} else{

		}
	});

	// 显示评分部分
	function instance_vote(){
		"use strict";
		vote_instanced = true;
		if(!current_data || !current_data._id){
			return;
		}

		$(vote_catelog).each(function (_, catelog){
			if(catelog.when){
				var ret = new MongoLike(catelog.when).test(current_data);
				// console.log(catelog.when, ret);
				if(!ret){
					delete(vote_catelog[_]);
					return;
				}
			}

			var $item = $('<div class="voteitem col-md-6"/>').appendTo(container);
			var $bar, value;
			switch(catelog.type){
			case 0:
				$bar = new window.components.ValueBar('success');
				// 中央文字 - 评分名字
				covertext(catelog.name, 'center').addClass('revert').appendTo($bar);
				// 好评差评
				covertext(catelog.values[0], 'left').appendTo($bar);
				covertext(catelog.values[1], 'right').appendTo($bar);
				break;
			case 1:
				$bar = new window.components.CenterBar('warning', 'success');
				// 中央文字 - 评分名字
				covertext(catelog.name, 'center').addClass('revert').appendTo($bar);
				// 好评
				covertext(catelog.values[0], 'left').appendTo($bar);
				// 中评
				covertext(catelog.values[1], 'center').appendTo($bar);
				// 差评
				covertext(catelog.values[2], 'right').appendTo($bar);
				break;
			case 2:
				$bar = new window.components.TwoSideBar('danger', 'success');
				// 中央文字 - 评分名字
				covertext(catelog.name, 'center').addClass('revert').appendTo($bar);
				// 好评
				covertext(catelog.values[0], 'left').appendTo($bar);
				// 中评
				covertext(catelog.values[1], 'center').appendTo($bar);
				// 差评
				covertext(catelog.values[2], 'right').appendTo($bar);
				break;
			default :
				throw new Error('未知类型：' + catelog.type);
			}
			$bar.center = catelog.offset;
			$bar.data('catelog', catelog);
			$bar.addClass('hovershow trans-opacity').appendTo($item);

			// 处理、保存用户评价
			$bar.input = $('<input type="hidden" name="vote[' + catelog.id + ']"/>').appendTo($item);
			$bar.text = $('<span/>').appendTo($('<span class="full_cover score-item text-center"/>').text('评价：').appendTo($bar));
			Object.defineProperty($bar, 'user_value', {
				get: function (){
					return value;
				},
				set: function (v){
					value = v;
					this.input.val(v);
					if(v === undefined || v === null){
						this.text.text('未评价');
					} else{
						this.text.text(v);
					}
				}
			});

			bars.push($bar);
		});
	}

	// 用户评分部分
	$(document).on('mirai.login', bootUserVote);
	function bootUserVote(){
		if(user_vote){ // 已经加载了
			return;
		}
		if(window.user.isLogin && (maininfo.hasClass('vote_show') || $('.vote_large').css('opacity') != 0)){
			user_vote = false;
			SimpleNotify('user_vote_load').info('正在载入您的评分……');
			var u = $.modifyUrl('http://' + window.Think.URL_MAP['info'], {
				action: 'Vote',
				method: 'index',
				suffix: 'json'
			});
			var dfd = $.ajax({
				url : u,
				data: {
					id: current_data['_oid']
				}
			});
			LogStandardReturn(dfd, '载入用户评分');
			SimpleNotifyAjaxDfd('user_vote_load', dfd, false);
			dfd.done(function (ret){
				if(ret.code === window.Think.ERR_NO_ERROR){
					user_vote = ret['vote'];
					initUserVote();
				}
			});
		}
	}

	function initUserVote(){
		if(initUserVote.inited){
			return;
		}
		var handle = dragHandle();
		var last = '', tmpv;
		var dragging = false;
		handle.appendTo(container);

		// 鼠标移出评分区，如果不是拖动状态则隐藏拖柄
		container.mouseleave(function (){
			if(dragging){
				tmpv = last;
				last = false;
				return;
			}
			handle.stop(false, false).transit({opacity: 0}, 300, function (){
				if(!last){
					handle.hide();
				}
			});
			last = false;
		}).mouseenter(function (){
					if(dragging){
						last = tmpv;
					}
				});
		// 保存按钮指向
		$('#submitbtn').hover(function (){
			container.addClass('score');
		}, function (){
			container.removeClass('score');
		});

		// 鼠标指向滚动条，显示拖柄
		$(bars).each(function (_, $bar){
			var catelog = $bar.data('catelog');
			$bar.user_value = user_vote[catelog.id];
			$bar.hover(function (){
				if(!window.user.isLogin){
					return;
				}
				if(!user_vote || dragging){
					return;
				}
				if(catelog.id === last){
					return;
				}
				last = catelog.id;
				handle.bar = $bar;
				handle.y = $bar._top;
				handle.current_value = $bar.user_value;
				if($bar.center > 0){
					handle.addClass('positive').removeClass('negative');
				} else if($bar.center < 0){
					handle.removeClass('positive').addClass('negative');
				} else{
					handle.removeClass('positive negative');
				}
				handle.show().stop(false).transit({opacity: 1}, 300);
			});
		});
		// 重计算各个滚动条的大小（拖柄绝对定位用）
		function ccSize(){
			var p = container.offset();
			$(bars).each(function (_, $bar){
				var t = $bar.offset();
				$bar._left = t.left - p.left;
				$bar._ttop = t.top - p.top;
				$bar._top = $bar._ttop + $bar.height();
				$bar._width = $bar.width() + intval($bar.css('paddingLeft'));
			});
		}

		// 拖动柄
		var startX = 0, ZeroX = 0, FullX = 0, FlipY = 0;
		handle.on('mousedown', function (e){
			startX = e.pageX;
			dragging = handle.bar;
			ZeroX = dragging.offset().left - DRAGGER_SIZE/2;
			FullX = ZeroX + dragging._width + DRAGGER_SIZE;
			FlipY = dragging.offset().top;
			$(document).on({'mouseup': handleUp, 'mousemove': handleMove});
			$('body').addClass('noselect move');
		});
		function handleUp(){
			handle.bar.user_value = (handle.current_value);
			handle.flip = false;
			handle.removeClass('positive negative ll rr');
			//handle.content.css('backgroundColor', '');
			$(document).off({'mouseup': handleUp, 'mousemove': handleMove});
			$('body').removeClass('noselect move');
			dragging = false;
			if(!last){
				handle.hide();
			}
		}

		function handleMove(e){
			if(e.pageX < ZeroX){
				handle.current_value = 0;
			} else if(e.pageX > FullX){
				handle.current_value = 100;
			} else{
				handle.current_value = Math.round(1000*(e.pageX - ZeroX)/(FullX - ZeroX))/10;
			}
			handle.flip = e.pageY < FlipY
		}

		$(window).resize(ccSize);
		ccSize();
		initUserVote.inited = true;
	}

	function covertext(text, textdir){
		return $('<span class="full_cover"/>').addClass('text-' + textdir + ' hovershow-item').text(text);
	}

	function dragHandle(){
		if(dragHandle.cache){
			return dragHandle.cache;
		}
		var dg = dragHandle.cache = new window.components.DragPointer(DRAGGER_SIZE);
		dg.rotate = 90;
		dg.content.css({'border': '2px solid #A9A9A9', 'padding': '5px 1px', 'textAlign': 'center', 'fontWeight': 'bold'});
		dg.css('opacity', 0);
		Object.defineProperty(dg, 'text', {
			"set": function (v){
				dg.content.text(v);
			}
		});
		var flip, current_value = 0;
		Object.defineProperty(dg, 'current_value', {
			"set": function (v){
				if(v === undefined || v === null || isNaN(v) || v === ''){
					v = 0;
					dg.text = 'NaN';
					dg.removeClass('ll rr');
					dg.content.css('backgroundColor', 'hsl(110,0%,87%)');
				} else{
					dg.text = v;
					if(dg.bar.center > 0){
						dg.content.css('backgroundColor', 'hsl(' + (40 + 0.7*v) + ',' + Math.abs(50 - v)*2 + '%,87%)');
					} else if(dg.bar.center < 0){
						dg.content.css('backgroundColor', 'hsl(' + (1.1*v) + ',' + Math.abs(50 - v)*2 + '%,87%)');
					} else{
						dg.content.css('backgroundColor', 'hsl(110,' + v + '%,87%)');
					}
					if(v > 50){
						dg.addClass('rr').removeClass('ll');
					} else if(v < 50){
						dg.removeClass('rr').addClass('ll');
					} else{
						dg.removeClass('ll rr');
					}
				}
				current_value = v;
				if(flip){
					dg.rotate = 180 + intval(v*10)*0.09;
				} else{
					dg.rotate = 90 - intval(v*10)*0.09;
				}
				dg.x = dg.bar._width*v/100 + dg.bar._left;
			},
			get  : function (){
				return current_value;
			}
		});
		Object.defineProperty(dg, 'flip', {
			"set": function (v){
				flip = v;
				if(v){
					dg.y = dg.bar._ttop;
				} else{
					dg.y = dg.bar._top;
				}
			},
			get  : function (){
				return flip;
			}
		});
		return dg;
	}

	setTimeout(function (){
		$('.vote_small').click();
	}, 0);
});
