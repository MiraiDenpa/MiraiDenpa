Array.prototype.remove = function (){
	var what, L = arguments.length, ax;
	while(L && this.length){
		what = arguments[--L];
		while((ax = this.indexOf(what)) !== -1){
			this.splice(ax, 1);
		}
	}
	return this;
};
