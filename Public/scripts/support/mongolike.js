(function (window){
	"use strict";
	function MongoLike(query){
		this.test = compile(query);
	}

	function compile(query){
		var fnlist = {};
		var rootlist = [];

		for(var path in query){
			var cb = dispatch_op(path);
			if(cb){
				rootlist.push(cb(query[path]));
			} else{
				fnlist[path] = (function (query){
					if($.isPlainObject(query)){
						for(var name in query){
							if(name.substr(0, 1) == '$'){
								return compile(query);
							}
						}
					}
					return function (value){
						if($.isArray(value) && !$.isArray(query)){
							return value.indexOf(query) >= 0;
						} else{
							return query == value;
						}
					}
				})(query[path]);
			}
		}
		if(rootlist == {} && !rootlist.length){
			throw new Error('$compile 操作至少有1个条件。');
		}
		return function $$compile(value){
			for(var path in fnlist){
				if(!fnlist[path](findPath(value, path))){
					return false;
				}
			}
			for(var i = 0; i < rootlist.length; i++){
				if(!rootlist[i](value)){
					return false;
				}
			}
			return true;
		};
	}

	function findPath(obj, path){
		var pArr = path.split(/\./);
		for(var i = 0; i < pArr.length; i++){
			if(obj[pArr[i]]){
				obj = obj[pArr[i]];
			} else{
				return undefined;
			}
		}
		return obj;
	}

	function dispatch_op(opname){
		if(oplist[opname]){
			return oplist[opname];
		} else{
			if(!opname){
				throw new Error('MongoLike 不允许空键名');
			}
			if(opname.substr(0, 1) === '$'){
				throw new Error('MongoLike 查询键名不能是美元符号开头。(操作不存在)');
			}
			return false;
		}
	}

	var oplist = {
		$gt       : function (query){
			if(typeof query !== 'number' && !parseFloat(query)){
				throw new Error('$gt 需要参数是数字');
			}
			return function $$gt(value){
				return value > query;
			};
		},
		$gte      : function (query){
			if(typeof query !== 'number' && !parseFloat(query)){
				throw new Error('$gte 需要参数是数字');
			}
			return function $$gte(value){
				return value >= query;
			};
		},
		$in       : function (query){
			if(!$.isArray(query)){
				throw new Error('$in 需要参数是数组');
			}
			return function $$in(value){
				return query.indexOf(value) >= 0;
			};
		},
		$lt       : function (query){
			if(typeof query !== 'number' && !parseFloat(query)){
				throw new Error('$lt 需要参数是数字');
			}
			return function $$lt(value){
				return value < query;
			};
		},
		$lte      : function (query){
			if(typeof query !== 'number' && !parseFloat(query)){
				throw new Error('$lte 需要参数是数字');
			}
			return function $$lte(value){
				return value <= query;
			};
		},
		$ne       : function (query){
			return function $$ne(value){
				return value != query;
			};
		},
		$nin      : function (query){
			if(!$.isArray(query)){
				throw new Error('$in 需要参数是数组');
			}
			return function $$nin(value){
				return query.indexOf(value) == -1;
			};
		},
		$or       : function (query){
			var fnlist = [];
			for(var i in query){
				var cb = dispatch_op(i)(query[i]);
				fnlist.push(cb);
			}
			if(fnlist.length < 2){
				throw new Error('$or 操作至少有两个条件。');
			}
			return function $$or(value){
				for(var i = 0; i < fnlist.length; i++){
					if(fnlist[i](value)){
						return true;
					}
				}
				return false;
			};
		},
		$and      : function (query){
			var fnlist = [];
			for(var i in query){
				var cb = compile(query[i]);
				fnlist.push(cb);
			}
			if(fnlist.length < 2){
				throw new Error('$and 操作至少有两个条件。');
			}
			return function $$and(value){
				for(var i = 0; i < fnlist.length; i++){
					if(!fnlist[i](value)){
						return false;
					}
				}
				return true;
			};
		},
		$not      : function (query){
			var cb = compile(query[i]);
			return function $$not(value){
				return !cb(value);
			};
		},
		$nor      : function (query){
			var fnlist = [];
			for(var i in query){
				var cb = compile(query[i]);
				fnlist.push(cb);
			}
			if(fnlist.length < 2){
				throw new Error('$and 操作至少有两个条件。');
			}
			return function $$nor(value){
				for(var i = 0; i < fnlist.length; i++){
					if(fnlist[i](value)){
						return false;
					}
				}
				return true;
			};
		},
		$exists   : function (query){
			if(query){
				return function $$exists(value){
					return value !== undefined;
				};
			} else{
				return function $$exists(value){
					return value === undefined;
				};
			}
		},
		$type     : function (query){
			if(typeof query !== 'string'){
				throw new Error('$type 操作需要字符串。');
			}
			query = query.toLowerCase();
			return function $$type(value){
				return $.type(value) === query;
			};
		},
		$mod      : function (query){
			if(!$.isArray(query) || query.length !== 2){
				throw new Error('$and 操作需要数组，下标0为运算数，下标1为期待的结果。');
			}
			return function $$mod(value){
				return value%query[0] == query[1];
			};
		},
		$regex    : function (query){
			if(query.constructor !== RegExp){
				if(query && query.$regex){
					query = new RegExp(query['$regex'], query['$options']);
				} else{
					throw new Error('$regex 操作需要正则表达式。');
				}
			}
			return function $$regex(value){
				return query.test(value);
			};
		},
		$where    : function (query){
			throw new Error("暂不支持");
		},
		$all      : function (query){
			if(!$.isArray(query)){
				throw new Error('$all 操作需要数组。');
			}
			return function $$all(value){
				if(!$.isArray(value)){
					return;
				}
				for(var i = 0; i < query.length; i++){
					if(value.indexOf(query[i]) == -1){
						return false;
					}
				}
				return true;
			};
		},
		$elemMatch: function (query){
			var fnlist = {};
			var len = 0;
			for(var i in query){
				fnlist[i] = compile(query[i]);
				len++;
			}
			if(len < 2){
				throw new Error('$elemMatch 操作至少有两个条件。');
			}
			return function $$elemMatch(value){
				if(!$.isArray(value)){
					return;
				}
				var ret = false;
				$(value).each(function (_, sg_value){
					for(var name in fnlist){
						if(!fnlist[name](sg_value)){
							return;
						}
					}
					ret = true;
					return false;
				});
				return ret;
			};
		},
		$size     : function (query){
			if(typeof query !== 'number' && !parseFloat(query)){
				throw new Error('$size 需要参数是数字');
			}
			return function $$size(value){
				if(!$.isArray(value)){
					return;
				}
				return query.length;
			};
		}
	};

	window.MongoLike = MongoLike;
})(window);
