/**
 * User: GongT
 * Date: 13-9-20
 * Time: 上午11:46
 */
function SyncStorage(key_name){
	var time = time();
	var st = window.localStorage;
	var data = {};
	var callbacks = {};

	function serilize(){
		st.setItem(key_name, read());
	}

	function unserilize(){
		var d = st.getItem(key_name);
		if(d){
			write(JSON.parse(d));
		} else{
			st.setItem(key_name, '{}');
		}
	}

	function read(name){
		if(name === undefined){
			return data;
		} else{
			return data[name];
		}
	}

	function write(_data){
		var trigger = $.extend({}, data, _data);
		var i;
		for(i in trigger){
			if(this.hasOwnProperty(i) && !_data.hasOwnProperty(i)){
				delete this[i];
			}
			if(data[i] == _data[i]){
				delete trigger[i];
			}
		}
		data = _data;
		$.extend(this, data);

		for(i in trigger){
			if(trigger.hasOwnProperty(i) && callbacks.hasOwnProperty(i)){
				call_func_list(callbacks[i], this[i]);
			}
		}
	}

	function upload(url){

	}

	function download(url){

	}

	function onchange(name, callback){

	}

	return this;
}
(function (window){
	"use strict";
	if(!window.user){
		window.user = {};
	}
	var validate_time = 86400;
	var Settings = function (){
	};
	var CONFIG_KEY = 'MiraiSetting';
	var data = window.localStorage.getItem(CONFIG_KEY);
	if(!data){
		data = {
			"update": 0
		};
	} else{
		data = JSON.parse(data);
	}
	window.getSetting = function (){
		var df = $.Deferred();
		var r = $.ajax({
			url     : window.Think.URL_MAP['u-user-login-settings'],
			dataType: 'json',
			data    : {x: 1}
		}).done(function (ret){
					data = ret.settings;
					if(ret.code){
						df.reject();
						return;
					}
					//data.timestamp += validate_time;
					window.localStorage.setItem(CONFIG_KEY, JSON.stringify(data));
					df.resolve(data);
				});
		JS_DEBUG && CheckStandardReturn(r, '更新用户配置');
		return df.promise();
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
		var r = $.ajax({
			url     : setUrl.toString(),
			dataType: 'json',
			data    : {
				value: value
			}
		});
		JS_DEBUG && CheckStandardReturn(r, '更新用户配置');
		window.localStorage.setItem(CONFIG_KEY, JSON.stringify(data));
	};
	Settings.unset = function (name){
		delete data[name];
		window.localStorage.setItem(CONFIG_KEY, JSON.stringify(data));
	};

	window.user.settings = Settings;
})(window);
