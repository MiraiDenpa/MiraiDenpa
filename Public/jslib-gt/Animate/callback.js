function AnimeCallback(cb, bind, argument){
	var id = ++AnimeIndex;
	var name = '回调#' + id + ' -> ' + cb.toString().match(/^.*$/m)[0] + '...';
	function ret(data){
		console.log(name + ' 开始!', data);
		/** debug */
		var dfd = $.StateSaver();
		var next = function (){
			console.log(name + ' 结束!', data);
			dfd.resolve(data);
		};
		cb.apply(bind, [next, data].concat(argument));
		return dfd;
	}
	ret.__defineGetter__('id', function (){
		return id;
	});
	return ret;
}
function AnimeDfd(cb, bind, argument){
	var id = ++AnimeIndex;
	var name = 'Dfd #' + id + ' -> ' + cb.toString().match(/^.*$/m)[0] + '...';
	
	function ret(data){
		console.log(name + ' 开始!', data);
		/** debug */
		var dfd = $.StateSaver();
		cb.apply(bind, argument).always(function (){
			console.log(name + ' 结束!', data);
			dfd.resolve(data);
		});
		return dfd;
	}
	ret.__defineGetter__('id', function (){
		return id;
	});
	return ret;
}
