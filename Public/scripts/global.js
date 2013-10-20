function call_func_list(fnlist, arg){
	var args = Array.prototype.slice.call(arguments, 1);
	var i;
	if(fnlist.length === undefined){
		for(i in fnlist){
			if(fn.hasOwnProperty(i)){
				fnlist[i].apply(fnlist[i], args);
			}
		}
	} else{
		var cnt = fnlist.length;
		for(i = 0; i < cnt; i++){
			fnlist[i].apply(fnlist[i], args);
		}
	}
}
function call_func_list_with(bind, fnlist, arg){
	var i;
	var args = Array.prototype.slice.call(arguments, 2);
	if(fnlist.length === undefined){
		for(i in fnlist){
			if(fn.hasOwnProperty(i)){
				fnlist[i].apply(bind, args);
			}
		}
	} else{
		var cnt = fnlist.length;
		for(i = 0; i < cnt; i++){
			fnlist[i].apply(bind, args);
		}
	}
}
function call_func_list_until(fnlist, arg){
	var i;
	var args = Array.prototype.slice.call(arguments, 1);
	if(fnlist.length === undefined){
		for(i in fnlist){
			if(fn.hasOwnProperty(i)){
				if(false === fnlist[i].apply(fnlist[i], args)){
					return;
				}
			}
		}
	} else{
		var cnt = fnlist.length;
		for(i = 0; i < cnt; i++){
			if(false === fnlist[i].apply(fnlist[i], args)){
				return;
			}
		}
	}
}
function call_func_list_until_with(bind, fnlist, arg){
	var i;
	var args = Array.prototype.slice.call(arguments, 2);
	if(fnlist.length === undefined){
		for(i in fnlist){
			if(fn.hasOwnProperty(i)){
				if(false === fnlist[i].apply(bind, args)){
					return;
				}
			}
		}
	} else{
		var cnt = fnlist.length;
		for(i = 0; i < cnt; i++){
			if(false === fnlist[i].apply(bind, args)){
				return;
			}
		}
	}
}

function LogStandardReturn(dfd, title){
	/*function dispatch_standard_object(obj){
	 var msg = obj.message + '，' + obj.info;
	 if(obj.redirect){
	 msg += '<div style="padding-top:24px;"></div><div style="position:absolute;right:10px;bottom:0px;">';
	 for(var i in obj.redirect){
	 if(obj.redirect.hasOwnProperty(i)){
	 msg += '<a class="btn btn-link" href="' + obj.redirect[i] + '">' + i + '</a>';
	 }
	 }
	 msg += '</div>';
	 }
	 return SimpleNotify(time()).error(msg, title + '失败').hideTimeout(1000).autoDestroy();
	 }*/

	dfd.done(function (ret){
		if(typeof ret === 'string'){
			try{
				ret = JSON.parse(ret);
			} catch(e){
				console.groupCollapsed('△失败：' + title + '，返回内容不是json。');
				console.dir(e);
				console.groupEnd();
			}
		}
		LogStandardReturnObject(ret, title);
	});
	if(JS_DEBUG){
		dfd.fail(function (xhr, state, error){
			error = typeof error == 'string'? error : error.message;
			console.groupCollapsed('△失败:' + title + '，HTTP错误 [' + state + ']: ' + error);
			console.dir({response: xhr.responseText});
			console.groupEnd();
		});
	}
	return dfd;
}
function LogStandardReturnObject(ret, title){
	if(ret.code != window.Think.ERR_NO_ERROR){
		console.groupCollapsed('△失败： ' + title + '，返回消息： ' + ret.message + '，' + ret.info);
		console.dir(ret);
		console.groupEnd();
		//dispatch_standard_object(ret);
	} else if(JS_DEBUG){
		console.groupCollapsed('●成功： ' + title);
		console.dir(ret);
		console.groupEnd();
	}
}
