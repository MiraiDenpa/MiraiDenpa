var AnimeIndex = 0;
function AnimeTransit(obj, properties, duration, easing){
	properties.queue = false;
	/** debug */
	var id = ++AnimeIndex;
	var name = '动画#' + id + ' -> ' + obj.selector + '...';
	/* debug **/
	function self(data){
		/** debug */
		console.log(name + ' 开始!', data);
		var dfd = $.StateSaver();
		obj.transit(properties, duration, easing, function (){
			self.finish = true;
			/** debug */
			console.log(name + ' 结束!', data);
			dfd.resolve(data);
		});
		return dfd;
	}

	self.__defineGetter__('id', function (){
		return id;
	});
	return self;
}
