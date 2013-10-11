/*Array.prototype.remove = function (){
	var what, L = arguments.length, ax;
	while(L && this.length){
		what = arguments[--L];
		while((ax = this.indexOf(what)) !== -1){
			this.splice(ax, 1);
		}
	}
	return this;
};*/
array_remove=function(a){
	var what, L = arguments.length-1, ax;
	while(L && a.length){
		what = arguments[L--];
		while((ax = a.indexOf(what)) !== -1){
			a.splice(ax, 1);
		}
	}
	return this;
};
