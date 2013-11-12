(function (window, $){
	$(function (){
		"use strict";
		var container = $('#chapter');
		var toggleTarget = $('#mainInfo');
		var list = container.find('.list');
		var $btnList = list.find('ul');

		// 处理左右键点击章节按钮
		var item_coord = $btnList.find('li:first').outerHeight(true);
		var coord_center = 0.3;
		var last_clicked = undefined;
		$btnList.on({
			'mousedown'  : function (e){
				var $this = $(this);
				var offset = $this.offset();
				var size = $this.height();
				var center_up = size*coord_center, center_down = size - center_up;
				var x = e.pageX - offset.left;
				var y = e.pageY - offset.top;
				$this.removeClass('ot or ob ol oc');
				if(x > center_up && x < center_down && y > center_up && y < center_down){
					$this.addClass('c oc');
				} else if(y < x && y < size - x){
					$this.addClass('t ot');
				} else if(y > x && y < size - x){
					$this.addClass('l ol');
				} else if(y > x && y > size - x){
					$this.addClass('b ob');
				} else if(y < x && y > size - x){
					$this.addClass('r or');
				}
				$(document).on('mouseup', handleUp);
				function handleUp(e){
					$(document).off('mouseup', handleUp);
					$this.removeClass('t r b l c');
					var sec = $this.find('section');
					if(e.which == 3 && (sec[0] == e.target || $.contains(sec[0], e.target))){
						$this.toggleClass('selected')
					}
				}
			},
			'contextmenu': function (e){
				e.preventDefault();
			},
			'click'      : function (e){
				if(e.which == 1){
					if(this == last_clicked){
						toggleTarget.removeClass('chapter_show');
						last_clicked = undefined;
					} else{
						toggleTarget.addClass('chapter_show');
						last_clicked = this;
					}
					handleClick.call($(this));
				}
			}
		}, 'li');

		// 章节列表滚动
		var scrollTop = 0;
		var line = '';
		list.on({
			'mousewheel': function (e, delta){
				if(expanded){
					return;
				}
				if(delta > 0){
					line--;
					if(line < 0){
						line = 0;
					} else{
						e.preventDefault();
					}
				} else{
					if(line*item_coord < $btnList.height() - list.height()){
						line++;
						e.preventDefault();
					}
				}
				scrollTop = line*item_coord;
				list.stop(true).animate({'scrollTop': line*item_coord}, 200);
			},
			'mouseup'   : function (){
				if(expanded){
					return;
				}
				var top = list.scrollTop();
				if(top == scrollTop){
					return;
				}
				line = Math.round(top/item_coord);
				list.stop(true).animate({'scrollTop': line*item_coord});
				scrollTop = line*item_coord;
			}
		});

		// 展开章节列表
		var expanded = false;
		var expandbtn = container.find('.expand').on('click',function (){
			if(expanded){
				list.css('max-height', '');
				list.scrollTop(line*item_coord);
			} else{
				list.css('max-height', '100%');
			}
			expanded = !expanded;
			expandbtn.toggleClass('glyphicon-arrow-down glyphicon-arrow-up');
		}).find('.glyphicon');

		$btnList.find('li').each(function (){
			var $this = $(this);
			var id = $this.data('id');
			var chap = ChapterList[id];
			var state_text = '';
			switch(chap.state){
			case 'comming':
				state_text = '未播出';
				break;
			case 'pass':
				if(chap.isChinese){
					state_text = '熟肉可看';
				}else{
					state_text = '已播出';
				}
				break;
			case 'onair':
				state_text = '正在播放';
				break;
			case 'near':
				state_text = '即将播放';
				break;
			default :
				state_text = '无情报';
			}
			$this.find('.state').text(state_text);
		})
	});

	var ChapterList = window.doc['_chapter'];
	$(document).on({
		'mirai.login' : function (){
			"use strict";
		},
		'mirai.logout': function (){
			"use strict";
		}
	});

	function handleClick(){
		if(!window.user.isLogin){
			return false;
		}
		var id = this.data('id');
		console.log(id, ChapterList[id]);
	}

	// 初始化播放状态
	$(ChapterList).each(function (_, chap){
		"use strict";
		var now = new Date();
		var actualFirst = new Date(chap.first_date);
		var onair = new Date(actualFirst);
		if(isNaN(actualFirst.getTime())){
			chap.firstInvalid = true;
			chap.state = 'unknown';
		} else{
			actualFirst.setHours(chap.first_hour);
			actualFirst.setMinutes(chap.first_minute);
			chap.actualFirst = actualFirst;
			if(actualFirst > now){ // 首播时机未到
				onair.setDate(actualFirst.getDate() - 1);
				console.log(onair ,now);
				if(onair > now){
					chap.state = 'comming';
				} else{
					chap.state = 'near';
				}
			} else{
				onair.setMinutes(onair.getMinutes() + intval(chap.time));
				if(onair < now){
					chap.state = 'pass';
				} else{
					chap.state = 'onair';
				}
			}
		}
	});
})(window, $);
