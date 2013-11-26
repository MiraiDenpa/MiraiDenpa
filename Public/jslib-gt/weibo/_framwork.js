(function (weibo){
	window.onlogin(changeToken);
	$(document).on({
		'mirai.login' : changeToken,
		'mirai.logout': changeToken
	});

	function changeToken(){
		if(window.token){
			weibo.token = window.token;
		} else if(window.user && window.user.token){
			weibo.token = window.user.token;
		} else if($.cookie && $.cookie('token')){
			weibo.token = $.cookie('token');
		} else if(location.current && location.current.param['id']){
			weibo.token = $.cookie('token');
		} else if(window.app_pub){
			weibo.app = app_pub;
		}
	}

	weibo.attachToken = function (data){
		if(weibo.token){
			data.token = weibo.token;
			return data;
		}
		if(weibo.app){
			data.app = weibo.app;
			return data;
		}
		throw new Error('需要apppub或者token来确定应用');
	};

	weibo.appendToken = function (url){
		if(/(app|token)=/.test(url)){
			return url;
		}
		if(weibo.token){
			if(/\?/.test(url)){
				return url + '&token=' + weibo.token;
			} else{
				return url + '?token=' + weibo.token;
			}
		}
		if(weibo.app){
			if(/\?/.test(url)){
				return url + '&token=' + weibo.app;
			} else{
				return url + '?token=' + weibo.app;
			}
		}
		throw new Error('需要apppub或者token来确定应用');
	};

	weibo.post = function (wb){
		var url = $.modifyUrl(weibo.baseurl, {action: 'My', method: 'next', extension: 'json'});
		if(!wb.content){
			throw new Error('微博内容为空');
		}
		if(!wb.forward && autoForward){
			wb.forward = autoForward;
		}var dfd=$.ajax({
			url  : weibo.appendToken(url),
			data : wb,
			type : 'post',
			title: '发表微博'
		});
		LogStandardReturn(dfd, '发表微博');
		return dfd;
	};

	/**
	 * @constructor
	 * @return {null}
	 */
	weibo.Forward = function (type, content, list, original, arg1, arg2){
		if(typeof(content) == 'number' || content){
			this.type = type;
			this.content = content;
			this.list = list;
			this.original = original;
			this.arg1 = arg1;
			this.arg2 = arg2;
		} else{
			throw new Error('Forward must be have content.');
		}
	};

	var autoForward;
	this.autoForward = function (fw){
		autoForward = fw;
	};

	var userStore = window.x = $.indexedDB("denpa_cache", {
		"version": 3,  // Integer version that the DB should be opened with
		"upgrade": function (transaction){
			var objectStore = transaction.createObjectStore("user", {
				"keyPath"      : "_id",
				"autoIncrement": false
			});
		}
	}).objectStore("user");
	var user_property_callback = {};
	var user_property_timeout = 3600*3;
	weibo.request_user_property = function (uid, cb){
		if(!user_property_callback[uid]){
			user_property_callback[uid] = [];
		}
		if(cb){
			user_property_callback[uid].push(cb);
		}
		_getUserPropertyHelper(uid);
	};
	function _getUserPropertyHelper(uid){
		var dfd = userStore.get(uid);
		var now = time();
		dfd.done(function (obj){
			var expire = now - user_property_timeout;
			if(obj && obj._cache > expire){
				if(!user_property_callback[uid]){
					console.error('更新用户信息出错(1)，用户[' + uid + ']无法调用回调函数');
					return;
				}
				call_func_list(user_property_callback[uid], obj);
				delete(user_property_callback[uid]);
				$(document).trigger('mirai.user.property', [obj]);
			} else if(!_getUserPropertyHelperRunner.isset){
				setTimeout(_getUserPropertyHelperRunner, 0);
				_getUserPropertyHelperRunner.isset = true;
			}
		});
		dfd.fail(function (){
			if(!_getUserPropertyHelperRunner.isset){
				setTimeout(_getUserPropertyHelperRunner, 0);
				_getUserPropertyHelperRunner.isset = true;
			}
		});

	}

	function _getUserPropertyHelperRunner(){
		_getUserPropertyHelperRunner.isset = false;
		var not_found = [];
		var cblist = user_property_callback;
		user_property_callback = {};
		for(var i in cblist){
			if(cblist.hasOwnProperty(i)){
				not_found.push(i);
			}
		}
		if(!not_found.length){
			return;
		}
		var dfd = $.ajax({
			url : window.Think.URL_MAP['u-user-list-uid'],
			data: {list: not_found}
		});
		LogStandardReturn(dfd, '请求新的用户列表');
		dfd.done(function (ret){
			var list = ret.list;
			if(ret['notexist']){
				for(var id in ret['notexist']){
					list.push({
						_id  : ret['notexist'][id],
						exist: false
					});
				}
			}
			var now = time();
			$(list).each(function (_, property){
				property._cache = now;
				if(property.exist === undefined){
					property.exist = true;
				}
				if(!cblist[property._id]){
					console.error('更新用户信息出错(2)，用户[' + property._id + ']无法调用回调函数');
					return;
				}
				call_func_list(cblist[property._id], property);
				delete cblist[property._id];
				userStore.put(property);
				$(document).trigger('mirai.user.property', [property]);
			});
			for(var i in cblist){
				if(cblist.hasOwnProperty(i)){
					if(!cblist[i]){
						console.error('更新用户信息出错(3)，用户[' + i + ']无法调用回调函数');
						return;
					}
					call_func_list(cblist[i], {_id: id, exist: false});
				}
			}
			cblist = {};
		});
	}
})(window.denpa || (window.denpa = {}));

