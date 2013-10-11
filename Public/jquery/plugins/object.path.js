(function ($){
	var opath = '';
	$.fn.array_path = function (get){
		var obj = this[0];
		if(get === undefined){
			return parse_path(obj);
		} else{
			var gpath = get.split(/\/|\[|\]/);
			for(var i = 0; i < gpath.length; i++){
				if(!obj){
					return undefined;
				}
				if(i){
					obj = obj[i];
				}
			}
		}
	};
	function parse_path(obj){
		var ret = {};
		for(var i in obj){
			if(!obj.hasOwnProperty(i)){
				continue;
			}
			var val = obj[i];
			var type = typeof val;
			var path;
			if(parseInt(i) == i){
				path = opath + '[' + i + ']';
			} else{
				path = opath? opath + '.' + i : i;
			}
			if(/string|number|boolean/.test(type)){
				ret[path] = val;
			} else if(val.constructor === Object || val.constructor === Array){
				var save = opath;
				opath = path;
				$.extend(ret, parse_path(val));
				opath = save;
			} else{
				console.error('未知类型', val);
			}
		}
		return ret;
	}
})(jQuery);
