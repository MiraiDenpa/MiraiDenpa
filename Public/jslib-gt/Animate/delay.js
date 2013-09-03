function AnimeDelay(mstime){
	var id = ++AnimeIndex;
	var name = '延迟#' + id + ' -> ' + mstime + 'ms...';

	function ret(data){
		console.log(name + ' 开始!', data);
		/** debug */
		var dfd = $.StateSaver();
		var next = function (){
			console.log(name + ' 结束!', data);
			dfd.resolve(data);
		};
		setTimeout(next, mstime);
		return dfd;
	}

	ret.__defineGetter__('id', function (){
		return id;
	});
	return ret;
}
