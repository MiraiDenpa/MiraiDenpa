$(function (){
	"use strict";
	var chapterContainer = $('#chapter');
	var toggleTarget = $('#mainInfo');
	var listContainer = chapterContainer.find('.list');
	var list = listContainer.find('ul');
	var $btnList = list.children();

	// 处理左右键点击章节按钮
	var item_coord = list.find('li:first').outerHeight(true);
	var coord_center = 0.3;
	var last_clicked = undefined;

	// 章节列表滚动
	var line = '';
	listContainer.on({
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
				if(line*item_coord < list.height() - listContainer.height()){
					line++;
					e.preventDefault();
				}
			}
			listContainer.stop(true).animate({'scrollTop': line*item_coord}, 200);
		},
		'mouseup'   : function (){
			if(expanded){
				return;
			}
			var top = listContainer.scrollTop();
			var nline = Math.round(top/item_coord);
			if(top == nline*item_coord){
				return;
			}
			listContainer.stop(true).animate({'scrollTop': nline*item_coord});
			line = nline;
		},
		'scrollTo'  : function (e){
			if(expanded){
				return;
			}
			var top = e.top;
			var nline = Math.round(top/item_coord);
			if(nline == line){
				return;
			}
			listContainer.stop(true).animate({'scrollTop': nline*item_coord});
			line = nline;
		}
	});

	// 全选反选、标记 etc
	var buttons = chapterContainer.find('.buttons .btn').click(function (){
		var action = $(this).data('action');
		switch(action){
		case 'all':
			$btnList.each(function (_, li){
				handleChapterSelect($(li).data('id'));
			});
			refreshButtons();
			break;
		case 'revert':
			$btnList.each(function (_, li){
				var $this = $(li);
				if($this.hasClass('selected')){
					handleChapterDeselect($this.data('id'));
				} else{
					handleChapterSelect($this.data('id'));
				}
				refreshButtons();
			});
			break;
		case 'none':
			$btnList.each(function (_, li){
				handleChapterDeselect($(li).data('id'));
			});
			refreshButtons();
			break;
		case 'mark-pass':

			break;
		case 'mark-no':

			break;
		default :
		}
	});

	// 展开章节列表
	var expanded = false;
	var expandbtn = chapterContainer.find('.expand').on('click',function (){
		if(expanded){
			listContainer.css('max-height', '');
			listContainer.scrollTop(line*item_coord);
		} else{
			listContainer.css('max-height', '100%');
		}
		expanded = !expanded;
		expandbtn.toggleClass('glyphicon-arrow-down glyphicon-arrow-up');
	}).find('.glyphicon');

	// 处理每一话的信息(显示出来)
	$btnList.each(function (){
		var $this = $(this);
		var id = $this.data('id');
		var chap = ChapterDefine[id];
		ChapterDefine[id].dom = $this;

		// 填充播放状态等
		var state_text = '', state_class = '';
		switch(chap.state){
		case 'comming':
			state_text = '未播出';
			state_class = 'disabled';
			$this.data('protected', 1);
			break;
		case 'pass':
			if(chap['isChinese']){
				state_text = '熟肉可看';
			} else{
				state_text = '已播出';
			}
			break;
		case 'onair':
			state_text = '正在播放';
			state_class = 'current';
			$this.data('protected', 1);
			break;
		case 'near':
			state_text = '即将播放';
			break;
		default :
			state_text = '无情报';
		}
		$this.addClass(state_class);
		$this.find('.state').text(state_text);

		// 注册点击回调
		$this.on({
			'mousedown'  : ItemMousedownHandler,
			'contextmenu': ItemContextHandler,
			'click'      : ItemClickHandler
		});
	});

	list.on('contextmenu', ItemContextHandler);

	//支持函数
	function ItemMousedownHandler(e){
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
			// 右键点击切换选择
			if(e.which == 3 || e.which == 2 && (e.target == $this[0] || $.contains($this[0], e.target))){
				if($this.hasClass('selected')){
					handleChapterDeselect($this.data('id'));
				} else{
					handleChapterSelect($this.data('id'));
				}
				refreshButtons();
			}
		}
	}

	function ItemContextHandler(e){
		e.preventDefault();
	}

	function ItemClickHandler(e){
		if(e.which == 1){
			if(this == last_clicked){
				last_clicked = undefined;
			} else{
				last_clicked = this;
			}
			$btnList.each(function (_, li){
				handleChapterDeselect($(li).data('id'));
			});
			refreshButtons();
			handleChapterClick.call($(this));
		}
	}

	var $nsel = buttons.filter('.nsel');

	function refreshButtons(){
		var show = !!ChapterSelected.length;
		if(show == refreshButtons.last){
			return;
		}
		if(show){
			$nsel.removeClass('nsel');
		} else{
			$nsel.addClass('nsel');
		}
		refreshButtons.last = show;
	}
});
