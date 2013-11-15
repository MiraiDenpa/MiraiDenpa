$(function (){
	// 当前文档的信息
	var current_data = window.doc;
	// dao 
	var weibo = window.denpa;
	var request = weibo.Channel('info' + current_data['_oid']);
	request.autoForward(new weibo.Forward('mirai/info-entry', current_data['_oid']));

	// 初始化 data-weibo-channel="info{$doc->_oid}" 
	var container = $('#WeiboContainer');
	var loader = container.find('.Loader');
	loader.txt = loader.find('.text');
	loader.center = loader.find('.center').css('height', loader.height());

	
	var op = new StandartInfoSiteWeiboList({
		container   : container,
		request     : request,
		FetchOn     : 'login'
	});
	container.on('click', '.clickStart', function (){
		"use strict";
		op.initWeiboComment();
		container.find('.clickStart').removeClass('clickStart');
	});
	op.on({
		'mirai.denpa.statechange': function (e, state){
			"use strict";
			if(state == 'ready'){
				changeLoader(false);
			} else{
				changeLoader('正在载入……');
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

	//SimpleNotify('weibo').success('', '发表成功！').autoDestroy().hideTimeout(2000);

	function changeLoader(state){
		if(!state){
			return loader.hide();
		}
		loader.show();
		loader.center.css('height', loader.height());
		loader.txt.text(state);
	}
});
