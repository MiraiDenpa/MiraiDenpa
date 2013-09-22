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
	window.token = $.cookie('token');
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

	if(JS_DEBUG){
		console.log('当前TOKEN为：' + window.token);
	}

	var is_login;
	var onLogin = $.Callbacks();
	var onLogout = $.Callbacks();
	var property = {};
	window.onlogin = function (fn){
		if(is_login === true){
			fn(property);
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
			$.ajax({
				url     : window.Think.URL_MAP['u-user-login-property'],
				dataType: 'json'
			}).done(function (ret){
						CheckStandardReturn(ret, '获取用户信息');
						if(0 == ret.code){
							loginSuccess(ret.property);
						} else{
							notLogin();
						}
					});
		} else{
			notLogin();
		}

		function loginSuccess(prop){
			if(JS_DEBUG){
				console.log('已经登录： ', prop);
			}
			property = prop;
			is_login = true;
			onLogin.fire(prop);
			loginIcon.icon('off').alert('success').title(prop.nick);
			$(document).trigger('mirai.login', prop);
		}

		function notLogin(){
			is_login = false;
			onLogout.fire();
			loginIcon.icon('off').alert('error').title('未登录');
			$(document).trigger('mirai.login');
			if(JS_DEBUG){
				console.log('未登录');
			}
			$(document).trigger('mirai.logout');
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
})(window);
