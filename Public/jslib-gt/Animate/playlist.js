function PlayList(){
	var pending = [];
	var queueStack = [];
	
	var playing = 0;
	var play_data = {};

	var dfd = new $.StateSaver();

	function pump(){
		var current = queueStack[playing];
		if(!current){
			dfd.done();
			return;
		}
		var stackSize = current.length;
		var remaining = stackSize;

		for(var i = 0; i < stackSize; i++){
			var queue = current[i];
			queue.play(play_data).done(function (){
				remaining--;
				if(!remaining){
					playing++;
					setTimeout(pump, 0);
				}
			});
		}
	}

	this.appendQueue = function (queue){
		pending.push(queue);
		return this;
	};
	this.wait = function (){
		queueStack.push(pending);
		pending = [];
		return this;
	};
	this.play = function (data){
		play_data = data;
		if(pending.length){
			this.wait();
		}
		pump();
		return this;
	};
	this.done = dfd.done;
	return this;
}
