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
	var stack = printStackTrace();
	var lines = 'LogStandardReturn: ';
	$(stack).each(function (i, line){
		if(i > 3){
			lines += '\n\t' + (i - 3) + ': ' + line;
		}
	});
	
	dfd.done(function (ret){
		if(typeof ret === 'string'){
			try{
				ret = JSON.parse(ret);
			} catch(e){
				console.groupCollapsed('△失败：' + title + '，返回内容不是json。');
				console.dir(e);
				lines &&console.log(lines);
				console.groupEnd();
				return;
			}
		}
		LogStandardReturnObject(ret, title, lines);
	});
	if(JS_DEBUG){
		dfd.fail(function (xhr, state, error){
			error = typeof error == 'string'? error : error.message;
			console.groupCollapsed('△失败:' + title + '，HTTP错误 [' + state + ']: ' + error);
			console.dir({response: xhr.responseText});
			lines &&console.log(lines);
			console.groupEnd();
		});
	}
	return dfd;
}
function LogStandardReturnObject(ret, title, debug){
	if(ret.code !== window.Think.ERR_NO_ERROR){
		console.groupCollapsed('△失败： ' + title + '，返回消息： ' + ret.message + '，' + ret.info);
	} else if(JS_DEBUG){
		console.groupCollapsed('●成功： ' + title);
	}
	console.dir(ret);
	debug && console.log(debug);
	console.groupEnd();
}

function avatar_url(hash, avatar_size){
	if(hash){
		if(/^https?:\/\//i.test(hash)){
			return hash;
		} else if(/[0-9a-z]{32}/i.test(hash)){
			return $bui.Gravatar.build({size: avatar_size, hash: hash});
		} else{
			return $bui.Gravatar.build({size: avatar_size, hash: window.Think.DEFAULT_AVATAR});
		}
	} else{
		return $bui.Gravatar.build({size: avatar_size, hash: window.Think.DEFAULT_AVATAR});
	}
}
