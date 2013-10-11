/**
 * User: GongT
 * Date: 13-9-20
 * Time: 上午11:46
 */
function SyncStorage(key_name, url){
	"use strict";
	var st = window.localStorage;
	var data = {};
	var last_update = 0;
	var callbacks = {};
	var validate_time = 86400;
	var murl;
	if(url === undefined){
		murl = null;
	} else{
		murl = $.modifyUrl(url, {}, true);
	}
	unserilize();
	sync();

	function serilize(){
		st.setItem(key_name, read());
	}

	function unserilize(){
		var d = st.getItem(key_name);
		if(d){
			try{
				d = JSON.parse(d);
			} catch(e){
				st.setItem(key_name, '{}');
				data = {};
				return;
			}
			write(d);
		} else{
			st.setItem(key_name, '{}');
			data = {};
		}
	}

	function get(name){
		return data[name];
	}

	function set(name, value){
		if(data[name] != value){
			last_update = time();
			data[name] = value;
			trigger(name);
			if(murl){
				murl.modify({path: [name]});
				LogStandardReturn($.ajax({
					url : url,
					type: 'POST',
					data: {
						value: value
					}
				}), '修改单个设置（' + key_name + '.' + name + '）');
			}
			serilize();
		}
		return this;
	}

	function property_define(name){
		return {
			get: function (){
				return get(name);
			},
			set: function (v){
				return set(name, value);
			}
		}
	}

	function read(){
		return data;
	}

	function write(_data){
		var changed = $.extend({}, data, _data);
		var i;
		if(changed['update']){
			last_update = changed['update'];
			delete  changed['update'];
		} else{
			last_update = time();
		}
		for(i in changed){
			if(this.hasOwnProperty(i) && !_data.hasOwnProperty(i)){ // 删除- 以前有现在没有
				delete this[i];
			}
			if(!this.hasOwnProperty(i) && _data.hasOwnProperty(i)){// 添加- 以前没有
				Object.defineProperty(this, i, property_define(i));
			}
			if(data[i] == _data[i]){ // 不变，不要触发change事件
				delete changed[i];
			}
		}
		data = _data;

		for(i in changed){
			if(changed.hasOwnProperty(i)){
				trigger(i);
			}
		}
		serilize();
	}

	function trigger(item){
		if(callbacks.hasOwnProperty(item)){
			call_func_list(callbacks[item], data[item]);
		}
	}

	function upload(){
		if(!murl){
			throw new Error('没有指定Url的SyncSotrage不能调用upload。');
		}
		var e = $.ajax({
			url : url,
			data: read(),
			type: 'POST'
		});
	}

	function download(){
		if(!murl){
			throw new Error('没有指定Url的SyncSotrage不能调用download。');
		}
		return $.ajax({
			url : url,
			type: 'GET'
		}).done(function (ret){
					if(!ret.code){
						write(ret.setting);
					}
				});
	}
	
	function clear(){
		write({});
		serilize();
	}

	function sync(){
		var dfd = new $.Deferred();
		if(!murl){
			return dfd.resolve(data).promise();
		}
		if(data){ // 如果本地有缓存
			if(data.update + validate_time < time()){ // 但是超时了
				if(JS_DEBUG){
					console.log('Storage: ' + key_name + ' 本地缓存超时');
				}
				download().done(function (){
					dfd.resolve(data);
				});
			} else{ // 直接使用
				if(JS_DEBUG){
					console.log('Storage: ' + key_name + ' 使用本地缓存');
				}
				dfd.resolve(data);
			}
		} else{ // 没有缓存，下载新的
			if(JS_DEBUG){
				console.log('Storage: ' + key_name + ' 更新');
			}
			download().done(function (){
				dfd.resolve(data);
			});
		}
		return dfd.promise();
	}

	function onchange(name, callback){
		if(!callbacks[name]){
			callbacks[name] = [];
		}
		callbacks[name].push(callback);
		return this;
	}

	this.onchange = onchange;
	this.getItem = get;
	this.setItem = set;
	this.readAll = read;
	this.writeAll = write;
	this.sync = sync;
	this.clear = clear;

	return this;
}
