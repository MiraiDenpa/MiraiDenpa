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

	function serilize(){
		st.setItem(key_name, read.call(this));
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
			write.call(this, d);
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
			this[name] = value;
			trigger.call(this, name);
			if(murl){
				murl.modify({method: [name]});
				LogStandardReturn($.ajax({
					url : murl.toString(),
					type: 'POST',
					data: {
						value: value
					}
				}), '修改单个设置（' + key_name + '.' + name + '）');
			}
			serilize.call(this);
		}
		return this;
	}

	function property_define(name){
		return {
			get: function (){
				return get.call(this, name);
			},
			set: function (v){
				return set.call(this, name, v);
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

		var old = data;
		data = _data;
		for(i in changed){
			if(data[i] != old[i] && changed.hasOwnProperty(i)){ // 不变不要触发change事件
				trigger.call(this, i);
			}
		}
		serilize.call(this);
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
			data: read.call(this),
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
						write.call(this, ret.setting);
					}
				});
	}

	function clear(){
		write.call(this, {});
		serilize.call(this);
	}

	function sync(){
		var dfd = new $.Deferred();
		if(!murl){
			return dfd.resolve(data).promise();
		}
		if(data && data.update){ // 如果本地有缓存
			if(data.update + validate_time < time()){ // 但是超时了
				if(JS_DEBUG){
					console.log('Storage: ' + key_name + ' 本地缓存超时');
				}
				download.call(this).done(function (){
					dfd.resolve.apply(dfd, arguments);
				}).fail(function (){
							dfd.reject.apply(dfd, arguments);
						})
			} else{ // 直接使用
				if(JS_DEBUG){
					console.log('Storage: ' + key_name + ' 使用本地缓存');
				}
				dfd.resolve($.extend({code: 0}, data));
			}
		} else{ // 没有缓存，下载新的
			if(JS_DEBUG){
				console.log('Storage: ' + key_name + ' 更新');
			}
			download().done(function (){
				dfd.resolve.apply(dfd, arguments);
			}).fail(function (){
						dfd.reject.apply(dfd, arguments);
					})
		}
		return dfd.promise();
	}

	function onchange(name, callback){
		if(!callbacks[name]){
			callbacks[name] = [];
		}
		if(data[name]){
			callback(data[name]);
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

	if(url === undefined){
		murl = null;
	} else{
		murl = $.modifyUrl(url, {}, true);
	}
	unserilize.call(this);
	//this.sync();

	return this;
}
