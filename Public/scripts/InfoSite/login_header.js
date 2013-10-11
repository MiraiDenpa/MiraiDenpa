(function (window, $){
	var loginIcon;

	// 初始化右上角的小图标
	loginIcon = new TrayIcon('user-login-launch-icon', 'transfer', '载入中...', login_action).show();

	function login_action(){
		var wnd = loginWindow();
		wnd.toggleClass('active');
	}

	function loginWindow(){
		if(loginWindow.$div){
			return loginWindow.$div;
		}
		var $div = $('<div id="loginWindow"/>').appendTo($('body'));

		// init login window 

		return loginWindow.$div = $div;
	}

	$(document).on({
		'mirai.login' : function (){
			loginIcon.icon('off').alert('success').title('欢迎，' + user.property.nick + '！');
			$('.login_visable').show();
			$('.login_unvisable').hide();

			var UserBox = $('#UserBox').find('.show');
			var userdataurl = $.modifyUrl('', {
				app   : 'user',
				action: 'editor',
				method: 'property',
				param : {
					token: window.token
				}
			});
			UserBox.empty();
			var avatar = $bui.Gravatar(48, window.user.token_data.ahash).addClass('pull-left').css({'margin': '0 7px'});
			var title = $('<div/>').append(avatar).append('欢迎回来，').append($('<span/>').text(window.user.property.nick).attr('class', 'user_id')).append('。');
			UserBox.append(title);
			UserBox.append($('<a/>').html('修改用户资料').attr('href', userdataurl));
		},
		'mirai.logout': function (){
			loginIcon.icon('off').alert('error').title('未登录');
			$('.login_visable').hide();
			$('.login_unvisable').show();
		}
	});
})(window, jQuery);
