(function (exports){
	"use strict";

	// query对象的key到modify的映射
	var query_map = {};
	// 回调列表id => [回调对象数组] 
	var handlers = {};
	// 保存当前状态，比较新的query，只触发改变了的部分
	var current_query = {};
	// query对象的key到回调列表id的映射
	var query_hook = {};
	// ajax过程中的current_query暂存
	var notsubmit_query = {};

	// 同ID的url必须相同
	exports.addHandler = function (id, ho){
		ho = $.extend({}, ho);
		if(!ho.handler){
			throw new Error('HistotyHandler必须有 handler');
		}
		if(!ho.fetch){
			throw new Error('HistotyHandler必须有 fetch');
		}
		if(!ho.url){
			throw new Error('HistotyHandler必须有 url。');
		}
		if(!ho.hook){
			throw new Error('HistotyHandler必须有 hook。');
		}

		// 注册需要的的请求变量
		for(var index = 0; index < ho.hook.length; index++){
			var name = ho.hook[index];
			if(query_hook[name] && query_hook[name] !== id){
				throw new Error('请求变量' + index + '已经被其他回调注册。\n' + query_hook[name] + '\n' + id);
			}
			query_hook[name] = id;
		}

		if(!handlers[id]){
			handlers[id] = [];
		}
		if(!notsubmit_query[id]){
			notsubmit_query[id] = {};
		}
		// 合并两个url的query部分，其他不变
		if(handlers[id].url){
			$.extend(handlers[id].url.param, $.url(ho.url, true).data.param.query);
		} else{
			handlers[id].url = $.modifyUrl(ho.url, {suffix: 'json'}, true);
		}
		handlers[id].push(ho);
	};

	exports.urlChanged = function (id, new_url){
		handlers[id].url = new_url;
	};

	History.Adapter.bind(window, 'statechange', function (){
		var State = History.getState();
		var obj = State.data;
		var triggers = obj._state_triggers; // 还原请求时记录的“已变化”变量名
		var hl = [];
		delete(obj._state_triggers);
		// 根据 triggers 找出对应的主ID
		var handle_id = query_hook[triggers[0]];

		// 主ID下的所有回调检查其 hook 属性，这次请求中改变了的推入 hl 数组
		$(handlers[handle_id]).each(function (_, ho){
			$(ho.hook).each(function (_, name){
				if(triggers.indexOf(name) >= 0){
					hl.push(ho);
					return false;// 这个ho结束，放置重复添加
				}
			});
		});
		if(obj._state_fail){
			$(hl).each(function (_, ho){
				if(ho.fail){
					ho.fail.apply(this, obj.argument);
				}
			});
		} else{
			$.extend(current_query, notsubmit_query[obj._state_id]);
			notsubmit_query[obj._state_id] = {};
			var wattings = [];
			$(hl).each(function (_, ho){
				var ret = ho['fetch'](obj);
				if(ret){
					wattings.push([ret? ret : obj, ho.handler]);
				}
			});
			$(wattings).each(function (_, array){
				array[1](array[0]);
			});
		}
	});

	exports.query = function (new_query){
		// query2modify会改data，所以必须复制一个
		var copied_data = $.extend({}, current_query, new_query);
		var modify = query2modify(copied_data);
		var new_url = location.current.modify(modify);
		var ajax = {
			url     : undefined,
			data    : copied_data,
			type    : 'get',
			dataType: 'json'
		};

		var changed_handlers = {};
		// 根据 query_hook ，找到改变的请求变量对应的主ID，然后把被改变的变量名记录到主ID索引的数组中
		for(var name in new_query){
			var handle_id = query_hook[name];
			if(handle_id && current_query[name] != new_query[name]){ // 循环所有被修改了的变量
				notsubmit_query[handle_id][name] = new_query[name];
				if(!changed_handlers[handle_id]){
					changed_handlers[handle_id] = [];
				}
				changed_handlers[handle_id].push(name);
			}
		}
		// 然后根据记录的主ID，发送请求（之后转到 bind 函数中）
		for(handle_id in changed_handlers){
			var debug_title = arguments[1]? arguments[1] : '页面变更请求[' + handle_id + ']';
			var triggers = changed_handlers[handle_id];
			if(!triggers.length){
				continue;
			}
			// 如果有变量变化，则发送ajax请求
			ajax.url = handlers[handle_id].url;
			(function (dfd, triggers, debug_title){
				LogStandardReturn(dfd, debug_title);
				dfd.done(function (ret){
					ret._state_triggers = triggers;
					History.pushState(ret, $('title').text(), new_url);
				});
				dfd.fail(function (){
					var data = {_state_fail: true, argument: argument, _state_triggers: triggers};
					History.pushState(data, $('title').text(), new_url);
				});
			})($.ajax(ajax), triggers, debug_title);
		}
	};

	exports.map = function (name, info){
		// 把 query 的 name 转移到其他地方（比如path的第2项）
		info = info.split(/\./, 2);
		if(!/^(protocol|userInfo|host|port|action|method|extension|fragment|path|param|append)$/.test(info[0])){
			throw new Error('未知name：' + name);
		}
		if(query_map[name]){
			throw new Error('urlmap不能重复定义', name);
		}
		query_map[name] = info;
	};

	exports.unmap = function (name){
		delete(query_map[name]);
	};
	exports.map_current = function (name){
		var data = query_map[name];
		if(!data){
			return;
		}
		var n = data[0];
		if(/^(protocol|userInfo|host|port|action|method|extension|fragment)$/.test(n)){
			return location.current[n];
		} else if(n == 'path'){ // 处理非线性数据
			return location.current.path[data[1]];
		} else if(n == 'param'){
			return location.current.param[data[1]];
		} else{
			return undefined;
		}
	};

	function query2modify(query){
		var modify = {};
		for(var name in query){
			// 循环请求变量
			if(query_map[name]){
				// 如果请求的变量被映射
				var value = query_map[name];
				if(/^(protocol|userInfo|host|port|action|method|extension|fragment)$/.test(value[0])){
					// 普通映射
					modify[value[0]] = query[name];
					delete(query[name]);
				} else if('path' == value[0]){
					// 映射到path里，需要有第二个参数确定位置 <-- 二参数是数字
					if(!modify.path){
						modify.path = [];
					}
					modify.path[value[1]] = query[name];
					delete(query[name]);
				} else if('param' == value[0]){
					// 映射到param里，需要有第二个参数确定位置 <-- 二参数是字符串 ($GET变量名)
					if(!modify.param){
						modify.param = {};
					}
					modify.param[value[1]] = query[name];
					delete(query[name]);
				}
			} // 没有定义过map的不影响url
		}
		return modify;
	}
})(window.DenpaHistory = {});
