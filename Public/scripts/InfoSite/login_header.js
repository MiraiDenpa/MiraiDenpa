(function (window, $){
	var loginIcon;
	//$bui.Gravatar.default = window.Think.DEFAULT_AVATAR;

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
			$('body').addClass('login').removeClass('logout');

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

			var avatar;
			var avatar_size = 48;
			if(window.user.property.avatar){
				if(/^https?:\/\//i.test(window.user.property.avatar)){
					avatar = $('<img/>').attr('src', window.user.property.avatar).css({'height': avatar_size, 'width': avatar_size});
				} else if(/[0-9a-z]{32}/i.test(window.user.property.avatar)){
					avatar = $bui.Gravatar(avatar_size, window.user.property.avatar).addClass('pull-left').css({'margin': '0 7px'});
				}
			} 
			if(!avatar){
				avatar = $bui.Gravatar(avatar_size, window.Think.DEFAULT_AVATAR).addClass('pull-left').css({'margin': '0 7px'});
			}

			var title = $('<div/>').append(avatar).append('欢迎回来，').append($('<span/>').text(window.user.property.nick).attr('class', 'user_id')).append('。');
			UserBox.append(title);
			UserBox.append($('<a/>').html('修改用户资料').attr('href', userdataurl));
		},
		'mirai.logout': function (){
			loginIcon.icon('off').alert('error').title('未登录');
			$('body').addClass('logout').removeClass('login');
		}
	});
})(window, jQuery);
