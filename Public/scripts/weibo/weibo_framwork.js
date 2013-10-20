(function (window){
	var weibo = window.weibo;
	try{
		if(!weibo.token){
			weibo.token = window.token || window.user.token || $.cookie('token');
		}
	} catch(e){
	}

	function url(url){
		var u = '' + url;
		if(/token=/.test(u)){
			return u;
		} else if(/\?/.test(u)){
			return u + '&token=' + weibo.token;
		} else{
			return u + '?token=' + weibo.token;
		}
	}

	var posturl;
	weibo.post = function (wb){
		if(!posturl){
			posturl = $.modifyUrl(weibo.baseurl,
					{action: 'My', method: 'next', extension: 'json'}
					, true);
		}
		if(!wb.content){
			alert('空内容');
		}
		var dfd = $.ajax({
			url : url(posturl),
			data: wb,
			type: 'post'
		});
		LogStandardReturn(dfd, '发表微博');
		return dfd;
	};

	var channelurl;
	weibo.channel = function (channel, page){
		if(!channelurl){
			channelurl = $.modifyUrl(weibo.baseurl,
					{action: 'List', method: 'channel', path: [channel], extension: 'json'}
					, true);
		}
		channelurl.path[0] = channel;
		var dfd = $.ajax({
			url : url(channelurl),
			data: {p: page},
			type: 'get'
		});
		LogStandardReturn(dfd, '微博评论加载');
		return dfd;
	};

	weibo.list = function (listname){

	};

	weibo.home = function (uid){

	};

	/**
	 * @constructor
	 */
	weibo.Forward = function (type, content, list, original, arg1, arg2){
		var obj = {
			type    : type,
			content : content,
			list    : list,
			original: original,
			arg1    : arg1,
			arg2    : arg2
		};
		if(!content){
			obj = null;
		}
		return obj;
	}
})(window);
