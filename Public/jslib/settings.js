/**
 * Created with JetBrains WebStorm.
 * User: GongT
 * Date: 13-9-20
 * Time: 上午11:46
 * To change this template use File | Settings | File Templates.
 */
(function (window){
	"use strict";
	var Settings = function (){
	};
	var CONFIG_KEY = 'MiraiSetting';
	var data = window.localStorage.getItem(CONFIG_KEY);
	if(!data){
		data = {
			"timestamp": 0
		};
	}

	var token = $.cookie('token');
	if(token){
		var mod = {
			app      : 'user',
			action   : 'Setting',
			method   : CONFIG_KEY,
			extension: 'jsonp',
			param    : {
				token: token
			}
		};
		$.ajax({
			url     : $.modifyUrl('/', mod),
			dataType: 'jsonp'
		}).done(function (data){
					console.log(data);
				});
		mod = null;
	}

	Settings.get = function (name){
		return data[name];
	};
	Settings.set = function (name, value){
		data[name] = value;
		window.localStorage.setItem(CONFIG_KEY, data);
	};
	Settings.unset = function (name){
		delete data[name];
		window.localStorage.setItem(CONFIG_KEY, data);
	};

	window.Settings = Settings;
})(window);
