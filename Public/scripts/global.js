function call_func_list(fnlist, arg){
	var args = Array.slice.call(arguments, 1);
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
	var args = Array.slice.call(arguments, 2);
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
	var args = Array.slice.call(arguments, 1);
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
	var args = Array.slice.call(arguments, 2);
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
