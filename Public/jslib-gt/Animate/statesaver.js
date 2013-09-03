$.StateSaver = function (){
	var cblist = [];
	var is_running = false;
	var switch_on = false;
	var applythis = this;
	var applyarg = [];

	return {
		resolve  : function (){
			switch_on = true;
			applythis = this;
			applyarg = arguments;
			setTimeout(function (){
				is_running = true;
				var fb;
				while(fb = cblist.pop()){
					fb.apply(applythis, applyarg);
				}
				cblist = [];
				is_running = false;
			}, 0);
		},
		unresolve: function (){
			switch_on = false;
		},
		done     : function (fn){
			if(switch_on){
				fn.apply(applythis, applyarg);
			} else{
				cblist.push(fn);
			}
			return this;
		},
		promise  : function (){
			return {
				done: this.done
			};
		}
	}
};
