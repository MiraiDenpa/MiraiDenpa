"use strict";
// 需要重写： 把所有回调、动画、延时之类的都抽象成一个类，然后统一接口
(function (module){
	module.Anime = function (obj, properties, duration, easing){
		/* debug */
		if(!window._t) window._t = 0;
		var name = obj.selector + '-> 动画(' + (++window._t) + ')...';
		/* debug */
		var dfd = $.Deferred();
		properties.queue = false;
		var self = function (){
			/* debug */
			console.log('anime ' + name + ' run!');
			/* debug */
			$(obj).transit(properties, duration, easing, function (){
				self.finish = true;
				console.log('anime ' + name + ' finish!');
				dfd.resolve();
			});
			return dfd.promise();
		};
		self.finish = false;
		self.done = function (fn){
			dfd.done(fn);
		};

		return self;
	};

	module.CallbackPacker = function(fn){
		
	};
	
	module.Delay = function(fn){

	};

	// queue
	var countId = 0;
	var aqueue = module.Anime.Queue = function (){
		var prev = null;
		var list = [];
		var waiting = new CallCounter();
		var dfd = new $.Deferred();

		var id = ++countId;
		console.log('QueueConstruct: ' + id);

		function pump(){
			console.log('Queue Pump: ' + id);
			var i = -1;
			var next = function (){
				console.log('Queue Next: ' + id);
				i++;
				setTimeout(function (){
					if(list[i] === undefined){
						console.log('Queue Done: ' + id);
						dfd.resolve();
						dfd = new $.Deferred();
					} else{
						list[i](next);
					}
				}, 0);
			};
			next();
			/*for(var i in list){
			 list[i]();
			 }*/
		}

		$.extend(this, {
			pump         : pump,
			done         : function (fn){
				dfd.done(fn);
				return this;
			},
			delay        : function (msec){
				return this.callByNext(function (n){
					setTimeout(n, msec);
				});
			},
			play         : function (anime){
				prev = function (next){
					anime().done(next);
				};
				list.push(waiting.push(prev));
				return this;
			},
			callByNext   : function (cb){
				prev = function (next){
					cb(next);
				};
				list.push(waiting.push(prev));
				return this;
			},
			callByDone   : function (cb, bind, args){
				prev = function (next){
					cb.apply(bind, args).done(next);
				};
				list.push(waiting.push(prev));
				return this;
			},
			callImmediate: function (cb, bind, args){
				prev = function (next){
					cb.apply(bind, args);
					next();
				};
				list.push(waiting.push(prev));
				return this;
			},
			runByCall    : function (){
				var direct_call = false;
				var _next = false;

				var cb = function (next){
					if(direct_call){ // 函数已经被调用
						next();
					} else{ // 函数还没有被调用
						_next = next;
					}
				};
				list.push(cb);
				return waiting.push(function (n){
					if(_next){ // 调用时，已经在等待
						_next();
					} else{ // 调用时，还没执行到
						direct_call = true;
					}
				});
			},
			fork         : function (){
				var nqueue = new aqueue();
				prev = function (next){
					next();
					nqueue.pump();
				};
				list.push(waiting.push(prev));
				return nqueue;
			},
			merge        : function (target){
				var wait = 2;
				var done = [];
				prev = function (next){// 等待两个队列都执行到当前点
					console.log('merge least ' + wait);
					done.push(next);
					if(0===--wait){
						console.log('merge finish!');
						for(var i in done){
							done[i]();
						}
						return;
					}
				};
				this.callByNext(prev);
				target.callByNext(prev);
				return this;
			}
		});

		return this;
	};

	// call counter
	var uid = 0;
	module.CallCounter = function (){
		var cblist = {};
		var linstenList = {};
		var counter = 0;

		function testCnt(){
			if(linstenList[counter] !== undefined){
				for(var index in linstenList[counter]){
					linstenList[counter][index](this);
				}
			}
		}

		return {
			destroy : function (){
				cblist = null;
				linstenList = null;
				counter = null;
			},
			when    : function (count, action){
				if(undefined === linstenList[count]){
					linstenList[count] = [];
				}
				linstenList[count].push(action);
			},
			/** counter.push( some_func ) 当调用返回的函数时，调用somefunc (this = bind)，计数减一 */
			push    : function (cb, bind){
				var id = uid++;
				counter++;
				return cblist[id] = function (){
					var ret = cb.apply(bind? bind : this, arguments);
					if(cblist[id] !== undefined){
						counter--;
						testCnt();
						delete cblist[id];
					}
					return ret;
				}
			},
			/** counter.pop() 当调用返回的函数时，计数减一 */
			pop     : function (){ // ajax.done( counter.pop() )
				var id = uid++;
				counter++;
				return cblist[id] = function (){
					if(cblist[id] !== undefined){
						counter--;
						testCnt();
						delete cblist[id];
					}
				}
			},
			clearRun: function (){
				for(var index in cblist){
					cblist[index]();
				}
				this.destroy();
			}
		}
	}
})(window);
