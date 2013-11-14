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
$(function (){
	var containUl = $('<ul id="dummyItemHolder"/>').appendTo('body');
	$('#chapter_detail').find('.goback').click(function (){
		$('#chapter_detail').removeClass('showme');
		$dummyItem.removeClass('clicked');
		setTimeout(function (){
			"use strict";
			containUl.css('left', '-100%');
			$chapter_item_last.css('visibility', 'visible');
			$dummyItem.remove();
		}, 400);
	});
});

function handleChapterClick(){
	"use strict";
	var $this = $chapter_item_last = $(this);
	var id = $this.data('id');
	var last = handleChapterClick.last;

	var chap = ChapterDefine[id];

	// 动画效果 
	createitem(id);
	var offset = $this.offset();
	$('#dummyItemHolder').css(offset);
	offset.top -= $(document).scrollTop();
	$('#chapter_detail').css('transform-origin', offset.left + 'px ' + offset.top + 'px')
			.addClass('showme');
	$this.css('visibility', 'hidden');
	// 动画效果  END

	//basicBox.setTitle(chap.title, chap.info.replace(/\n/g, '<br/>'));
	if(chap['staff']){
		//basicBox.tab('staff', '制作信息', chap['staff']);
	}
	if(!$this.data('protected') && $this.hasClass('active')){
		//basicBox.tab('unofficial', '内容简介', chap['unofficial']);
	}
	handleChapterClick.last = id;
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
			console.log(onair, now);
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
