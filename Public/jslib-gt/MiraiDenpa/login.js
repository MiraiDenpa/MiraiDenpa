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
	window.user.token = window.token = $.cookie('token');
	if(JS_DEBUG){
		console.log('当前TOKEN为：' + window.token);
	}
	var $document = $(document);
	$document.on({
		'mirai.login' : function (a, b, c){
			$('.login_visable').show();
			$('.login_unvisable').hide();
		},
		'mirai.logout': function (){
			$('.login_visable').hide();
			$('.login_unvisable').show();
		}
	});

	var token_domain = new RegExp('' + preg_quote(window.Think.URL_MAP['user']) + '', 'i');
	var exist = /\btoken\b=/;
	var loginIcon;

	// 发往user的请求在get里自动加token
	$(document).ajaxSend(function (event, jqxhr, settings){
		if(!window.token){
			return;
		}
		if(token_domain.test(settings.url) && !exist.test(settings.url)){
			if(settings.url.indexOf('?') > 0){
				settings.url += '&';
			} else{
				settings.url += '?';
			}
			settings.url += 'token=' + window.token
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

	$(function (){
		"use strict";
		loginIcon = new TrayIcon('user-login', 'transfer', '载入中...', login_action);

		if(window.token){
			$.when(getProperty(), getSetting(), getToken()).done(function (a1, a2, a3){
				window.user.property = a1;
				window.user.token_data = a3;
				loginSuccess();
			}).fail(notLogin);
		} else{
			notLogin();
		}

		function login_action(){
			var wnd = loginWindow();

		}
	});

	function loginWindow(){
		if(loginWindow.$div){
			return loginWindow.$div;
		}
		var $div = $('<div/>');

		return loginWindow.$div = $div;
	}

	function loginSuccess(){
		if(JS_DEBUG){
			console.log('◎ 登录成功！');
		}
		is_login = true;
		loginIcon.icon('off').alert('success').title('欢迎，' + window.user.property.nick + '！');
		onLogin.fire();
		$(document).trigger('mirai.login');
	}

	function notLogin(){
		if(JS_DEBUG){
			console.log('○ 未登录！');
		}
		is_login = false;
		loginIcon.icon('off').alert('error').title('未登录');
		onLogout.fire();
		$(document).trigger('mirai.logout');
	}

	function getToken(){
		var df = new $.Deferred();
		var r = $.ajax({
			url     : window.Think.URL_MAP['u-user-login-token'],
			dataType: 'json'
		}).done(function (ret){
					if(0 == ret.code){
						df.resolve(ret.info)
					} else{
						df.reject();
					}
				});
		JS_DEBUG && CheckStandardReturn(r, '获取登录token信息');
		return df.promise();
	}

	var pp_url = $.modifyUrl(window.Think.URL_MAP['u-user-login-property'], {}, true);

	function getProperty($uid){
		pp_url.modify({"method": $uid});
		var df = new $.Deferred();
		var r = $.ajax({
			url     : pp_url.toString(),
			dataType: 'json'
		}).done(function (ret){
					if(0 == ret.code){
						df.resolve(ret.property)
					} else{
						df.reject();
					}
				});
		JS_DEBUG && CheckStandardReturn(r, '获取用户信息');
		return df.promise();
	}
})(window);
