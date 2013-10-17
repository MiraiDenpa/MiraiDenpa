/**
 * $(document).on('mirai.login'); 每次登录信息改变都会触发
 * $(document).on('mirai.logout'); 登出的时候触发（然后就刷新了）页面打开的时候没有登录也会触发一次
 *
 * window.onlogin; 根mirai.login事件一样，不过每个回调之后会自动清除
 * window.onlogout; 同上
 *
 */
(function (window){
	"use strict";
	if(!window.user){
		window.user = {};
	}
	var user = window.user;
	user.setting = new SyncStorage('MiraiSetting', window.Think.URL_MAP['u-user-login-settings']);
	var token;
	if(window.token){
		token = user.token = window.token;
	} else{
		token = user.token = window.token = $.cookie('token');
	}
	if(JS_DEBUG){
		console.log('当前TOKEN为：' + token);
	}
	var $document = $(document);

	var token_domain = new RegExp('' + preg_quote(window.Think.URL_MAP['user']) + '', 'i');
	var exist = /\btoken\b=/;

	// 发往user的请求在get里自动加token
	$document.ajaxSend(function (event, jqxhr, settings){
		if(!token){
			return;
		}
		if(token_domain.test(settings.url) && !exist.test(settings.url)){
			if(settings.url.indexOf('?') > 0){
				settings.url += '&';
			} else{
				settings.url += '?';
			}
			settings.url += 'token=' + token
		}
	});

	var is_login;
	var onLogin = $.Callbacks();
	var onLogout = $.Callbacks();
	window.onlogin = function (fn){
		if(is_login === true){
			fn();
		} else{
			onLogin.add(fn);
		}
	};
	window.onlogout = function (fn){
		if(is_login === false){
			fn();
		} else{
			onLogout.add(fn);
		}
	};

	user.initUser = function (){
		if(token){
			$.when(getProperty(), getSetting(), getToken()).done(loginSuccess).fail(notLogin);
		} else{
			notLogin();
		}
	};
	$(user.initUser);
	user.passwordLogin = function (email, passwd){
		var url = $.modifyUrl('', {action: 'Login', method: 'index', extension: 'json'});
		passwd = CryptoJS.SHA1(passwd).toString(CryptoJS.enc.Hex).toLowerCase();
		var r = $.ajax({
			url     : url,
			dataType: 'json',
			data    : {email: email, passwd: passwd},
			method  : 'post'
		});
		LogStandardReturn(r, '用户登录');
		r.done(function (ret){
			if(ret.code == 0){
				token = user.token = window.token = ret.token;
				user.initUser();
			} else{
				(new SimpleNotify('userLogin')).autoDestroy(true).error('登录失败', ret.message);
			}
		})
	};

	var logoutUrl = $.modifyUrl('', {
		app      : 'user',
		action   : 'Logout',
		method   : 'index',
		extension: 'json'
	});
	var removeTokenUrl = $.modifyUrl(location.href, {
		action   : 'Logout',
		method   : 'index',
		extension: 'json'
	});
	user.logout = function (){
		var r = $.ajax({
			url     : logoutUrl,
			dataType: 'json'
		});
		$.ajax({
			url     : removeTokenUrl,
			dataType: 'json'
		});
		LogStandardReturn(r, '退出登录');
		r.done(function (ret){
			if(ret.code == 0){
				notLogin();
			}
		})
	};

	function loginSuccess(){
		if(JS_DEBUG){
			console.log('◎ 登录成功！');
		}
		is_login = true;
		onLogin.fire();
		$document.trigger('mirai.login');
	}

	function notLogin(){
		if(JS_DEBUG){
			console.groupCollapsed('○ 未登录！');
			console.trace();
			console.groupEnd();
		}
		user.token_data = {};
		user.property = {};
		if(user.setting){
			user.setting.clear();
		}else{
			user.setting = new SyncStorage('MiraiSetting', window.Think.URL_MAP['u-user-login-settings']);
		}
		token = user.token = window.token = null;
		$.removeCookie('token');

		is_login = false;
		onLogout.fire();
		$document.trigger('mirai.logout');
	}

	function getToken(){
		var df = new $.Deferred();
		if(user.token_data){ // 在页面里用同步方式登录
			console.groupCollapsed('●成功：获取登录token信息(同步)');
			console.log('页内传输。');
			console.log(user.token_data);
			console.groupEnd();
			return df.resolve(user.token_data).promise();
		}
		var r = $.ajax({ // 异步请求登录信息
			url     : window.Think.URL_MAP['u-user-login-token'],
			dataType: 'json'
		});
		LogStandardReturn(r, '获取登录token信息');
		r.done(function (ret){
			if(0 == ret.code){
				user.token_data = ret.info;
				df.resolve(ret.info)
			} else{
				df.reject();
			}
		});
		return df.promise();
	}

	function getSetting(){
		if(!user.setting){
			user.setting = new SyncStorage('MiraiSetting', window.Think.URL_MAP['u-user-login-settings']);
		}
		var r = user.setting.sync();
		LogStandardReturn(r, '获取配置信息');
		return r;
	}

	var pp_url = $.modifyUrl(window.Think.URL_MAP['u-user-login-property'], {}, true);

	function getProperty($uid){
		var df = new $.Deferred();
		if(user.property){ // 在页面里用同步方式登录
			if(JS_DEBUG){
				console.groupCollapsed('●成功：获取用户信息(同步)');
				console.log(user.property);
				console.groupEnd();
			}
			return df.resolve(user.property).promise();
		}

		pp_url.modify({"method": $uid});
		var r = $.ajax({
			url     : pp_url.toString(),
			dataType: 'json'
		});
		LogStandardReturn(r, '获取用户信息');
		r.done(function (ret){
			if(0 == ret.code){
				user.property = ret.property;
				if(!user.property){
					user.property = {};
				}
				df.resolve(ret.property)
			} else{
				df.reject();
			}
		});
		return df.promise();
	}
})(window);
