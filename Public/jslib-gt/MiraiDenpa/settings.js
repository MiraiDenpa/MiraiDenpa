/**
 * Created with JetBrains WebStorm.
 * User: GongT
 * Date: 13-9-20
 * Time: 上午11:46
 * To change this template use File | Settings | File Templates.
 */
(function (window){
	"use strict";
	var validate_time = 86400;
	var Settings = function (){
	};
	var CONFIG_KEY = 'MiraiSetting';
	var data = window.localStorage.getItem(CONFIG_KEY);
	if(!data){
		data = {
			"timestamp": 0
		};
	} else{
		data = JSON.parse(data);
	}
	if(JS_DEBUG){
		console.log('用户配置  => ', data);
		console.log("有效性验证： ", intval(data.timestamp) + '<' + time() + ' = 过期: ' + (intval(data.timestamp) < time()));
	}
	if(intval(data.timestamp) < time()){
		window.onlogin(function (){
			$.ajax({
				url     : window.Think.URL_MAP['u-user-login-settings'],
				dataType: 'json',
				data    : {x: 1}
			}).done(function (ret){
						data = ret.settings;
						if(ret.code){
							CheckStandardReturn(ret, '更新用户配置');
							return;
						}
						console.log('更新用户配置完成');
						data.timestamp = time() + validate_time;
						window.localStorage.setItem(CONFIG_KEY, JSON.stringify(data));
					});
		});
	}

	Settings.get = function (name){
		return data[name];
	};
	var setUrl = $.modifyUrl('', {
		app      : 'user',
		action   : 'Setting',
		method   : 'fake',
		extension: 'json'
	}, true);
	Settings.set = function (name, value){
		data[name] = value;
		setUrl.method = name;
		$.ajax({
			url     : setUrl.toString(),
			dataType: 'json',
			data    : {
				value: value
			}
		}).done(function (ret){
					CheckStandardReturn(ret, '修改用户配置');
				});
		window.localStorage.setItem(CONFIG_KEY, JSON.stringify(data));
	};
	Settings.unset = function (name){
		delete data[name];
		window.localStorage.setItem(CONFIG_KEY, JSON.stringify(data));
	};

	window.Settings = Settings;
})(window);
