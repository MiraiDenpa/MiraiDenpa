var ChapterDefine = window.doc['_chapter'];
var ChapterSelected = [];

$(document).on({
	'mirai.login' : function (){
		"use strict";
	},
	'mirai.logout': function (){
		"use strict";
	}
});

var $dummyItem = null;
var $chapter_item_last = null;
var $chapter_item_cache = {};
function createitem(id){
	"use strict";
	if(!$chapter_item_cache[id]){
		$chapter_item_cache[id] = ChapterDefine[id].dom.clone().appendTo('#dummyItemHolder');
	}
	$dummyItem = $chapter_item_cache[id].appendTo('#dummyItemHolder');
	setTimeout(function (){
		$dummyItem.addClass('clicked');
	}, 0);
}

var detail_pannel = null;
function showDetailPannel(){
	if(detail_pannel){
		return detail_pannel;
	}
	detail_pannel = $('#chapter_detail');
	var lasttab = 0, current_id = null;
	var tablist = detail_pannel.find('.menu > .item'), bodylist = [], tabmap = {};

	// 基本信息/制作信息...
	tablist.click(function (){
		var $this = $(this);
		var index = $this.data('index');
		if($this.hasClass('disabled')){
			return;
		}
		if($this.data('tab') == 'weibo'){
			createWeiboFramework(bodylist[index], detail_pannel.find('.loader'));
			prepare_weibo_channel.call(detail_pannel, current_id);
		}
		if(lasttab == index){
			return;
		}
		bodylist[lasttab].hide();
		$(tablist[lasttab]).removeClass('active');
		lasttab = index;
		bodylist[index].show();
		$(tablist[index]).addClass('active');
	}).each(function (i, e){
				var $e = $(e), tab = $e.data('tab');
				$e.data('index', i);
				tabmap[tab] = $e;
				if($e.data('tab') == 'weibo'){
					bodylist[i] = detail_pannel.find('section.WB');
				} else{
					bodylist[i] = $('<section/>').appendTo(detail_pannel.find('>.main'));
					tabmap[tab].body = $('<pre class="well"/>').appendTo(bodylist[i]);
				}
				if(i > 0){
					bodylist[i].hide();
				} else{
					$e.addClass('active');
				}
			});

	// “返回”/“标记为”...
	var containUl = $('<ul id="dummyItemHolder"/>').appendTo('body');
	detail_pannel.find('.options').on('click', '.item', function (){
		if($(this).hasClass('disabled')){
			return;
		}
		switch($(this).data('action')){
		case 'goback':
			$('#chapter_detail').removeClass('showme');
			$dummyItem.removeClass('clicked');
			setTimeout(function (){
				containUl.css('left', '-100%');
				$chapter_item_last.css('visibility', 'visible');
				$dummyItem.remove();
			}, 400);
			setTimeout(function (){
				$('body').removeClass('model-visable');
			}, 200);
			$(tablist[0]).click();
			break;
		case 'next-ep':
			if(ChapterDefine[current_id + 1]){
				switchContent(current_id + 1);
			}
			$(tablist[0]).click();
			break;
		case 'prev-ep':
			if(ChapterDefine[current_id - 1]){
				switchContent(current_id - 1);
			}
			$(tablist[0]).click();
			break;
		case 'mark':
			$(this).toggleClass('active');
			break;
		default :
			console.log($(this).data('action'));
		}
	});
	// “标记为” 的项目
	$('#chapmarkmenu').on('click', '>li', function (){
		switch($(this).data('type')){
		case 'pass':
			__PrograssMarkPass([current_id]);
			break;
		case 'notpass':
			__PrograssMarkUnPass([current_id]);
			break;
		case 'current':
		default:
			console.log($(this).data('type'));
		}
	});

	var static_header = detail_pannel.find('.main > header');
	var static_repv_btn = detail_pannel.find('.options > .prev');
	var static_next_btn = detail_pannel.find('.options > .next');

	function switchContent(id){
		id = parseInt(id);
		if(isNaN(id)){
			throw new Error('id 必须是数字');
		}
		var chap = ChapterDefine[id];
		static_repv_btn[ChapterDefine[id - 1]? 'removeClass' : 'addClass']('disabled');
		static_next_btn[ChapterDefine[id + 1]? 'removeClass' : 'addClass']('disabled');

		tabmap['info'].body.text(chap.info);
		static_header.text('第' + chap.key + '话 —— ' + chap.title);

		$(['staff', 'unofficial']).each(function (_, name){
			if(chap[name]){
				tabmap[name].removeClass('disabled');
				tabmap[name].body.text(chap[name]);
			} else{
				tabmap[name].addClass('disabled')
			}
		});
		//basicBox.tab('unofficial', '内容简介', chap['unofficial']);
		current_id = id;
	}

	detail_pannel.switchContent = switchContent;

	return detail_pannel;
}

function handleChapterClick(){
	"use strict";
	var $this = $chapter_item_last = $(this);
	var id = $this.data('id');

	// 准备数据并显示
	var chapter_detail = showDetailPannel();
	chapter_detail.switchContent(id);

	// 动画效果 
	createitem(id);
	var offset = $this.offset();
	$('#dummyItemHolder').css(offset);
	offset.top -= $(document).scrollTop();
	chapter_detail.css('transform-origin', offset.left + 'px ' + offset.top + 'px').addClass('showme');
	$this.css('visibility', 'hidden');
	$('body').addClass('model-visable');
}

function handleChapterSelect(id){
	"use strict";
	ChapterDefine[id].dom.addClass('selected');
	if(ChapterSelected.indexOf(id) == -1){
		ChapterSelected.push(id);
	}
}

function handleChapterDeselect(id){
	"use strict";
	ChapterDefine[id].dom.removeClass('selected');
	var i = ChapterSelected.indexOf(id);
	if(i > -1){
		ChapterSelected.splice(i, 1);
	}
}

function InfoChapPrograssMark(passed){
	"use strict";
	if(passed){
		__PrograssMarkPass(ChapterSelected);
	}else{
		__PrograssMarkUnPass(ChapterSelected);
	}
}

function __PrograssMarkPass(chap_arr){
	//window.doc._id.$id
	console.log('__PrograssMarkPass: ',chap_arr);
}

function __PrograssMarkUnPass(chap_arr){
	//window.doc._id.$id
	console.log('__PrograssMarkUnPass: ',chap_arr);
}

function __PrograssMarkSpecial(chap_arr){

}

// 初始化播放状态
$(ChapterDefine).each(function (_, chap){
	"use strict";
	var now = new Date();
	var actualFirst = new Date(chap['first_date']);
	var onair = new Date(actualFirst);
	if(isNaN(actualFirst.getTime())){
		chap.firstInvalid = true;
		chap.state = 'unknown';
	} else{
		actualFirst.setHours(chap['first_hour']);
		actualFirst.setMinutes(chap['first_minute']);
		chap.actualFirst = actualFirst;
		if(actualFirst > now){ // 首播时机未到
			onair.setDate(actualFirst.getDate() - 1);
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

function scrollChapListTo(id){
	$.event.trigger({type: 'scrollTo', top: ChapterDefine[id].dom.position().top}, undefined, $('#chapter').find('>.list')[0], true);
}
