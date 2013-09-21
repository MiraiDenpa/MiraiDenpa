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
	window.login = function (fn){
		if(is_login === true){
			fn(property);
		} else{
			onLogin.add(fn);
		}
	};
	window.nologin = function (fn){
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
		}

		function notLogin(){
			is_login = false;
			onLogout.fire();
			loginIcon.icon('off').alert('error').title('未登录');
			if(JS_DEBUG){
				console.log('未登录');
			}
		}

		function login_action(){
			console.log('login_action clicked');
		}
	});
})(window);
