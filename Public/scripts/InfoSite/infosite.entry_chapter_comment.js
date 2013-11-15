var SISWLOP = null;
function prepare_weibo_channel(chapterid){
	if(!SISWLOP){
		throw new Error('call createWeiboFramework first.');
	}
	var request = createChapChannel(chapterid);
	SISWLOP.initWeiboComment();
	SISWLOP.cancelForward();
	request.page();
}

function createWeiboFramework(section, loader){
	if(SISWLOP){
		return;
	}
	// 初始化
	section.removeClass();
	var container = $('#ChapComment');
	container.find('.Loader').remove();

	SISWLOP = new StandartInfoSiteWeiboList({
		container: container,
		FetchOn  : 'manual'
	});
	SISWLOP.on({
		'mirai.denpa.statechange': function (e, state){
			if(state == 'ready'){
				loader.data('loader').hide();
			} else{
				loader.data('loader').show();
			}
		},
		'mirai.denpa.postsuccess': function (e){
			"use strict";
			console.log(e);
		},
		'mirai.denpa.postfail'   : function (e){
			"use strict";
			console.log(e);
		}
	});
}

var wbChannelCache = [];
function createChapChannel(chapterid){
	if(wbChannelCache[chapterid]){
		request = wbChannelCache[chapterid];
	} else{
		// 当前文档的信息
		var current_data = window.doc;
		var weibo = window.denpa;
		var request = weibo.Channel('chap' + current_data['_oid'] + '_' + chapterid);
		request.autoForward(new weibo.Forward('mirai/info-chapter', chapterid, undefined, undefined, current_data['_oid']));
	}
	SISWLOP.handleChannel(request);
	return request;
}
