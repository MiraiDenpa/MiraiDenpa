function AnimeQueue(){
	var id = ++AnimeIndex;
	var queue = [], playing, finished = [], fdfd = [];

	var dfd = new $.StateSaver();

	function pump(data){
		if(playing){
			finished.push(playing);
		}
		playing = queue.shift();
		if(playing){
			fdfd.push(playing(data).done(pump));
		} else{
			dfd.resolve();
			queue = finished;
			finished = [];
			var tmp;
			while(tmp = fdfd.pop()){
				tmp.unresolve();
			}
		}
		return this;
	}

	this.__defineGetter__('id', function (){
		return id;
	});
	this.append = function (anime){
		queue.push(anime);
		return this;
	};
	this.play = pump;
	this.done = dfd.done;
	this.destroy = function (){
		queue = null;
		playing = null;
		finished = null;
		dfd = null;
		fdfd = null;
	};

	return this;
}
