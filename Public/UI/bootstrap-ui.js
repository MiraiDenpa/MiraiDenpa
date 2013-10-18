(function($){"use strict";

var module = {
	exports: window,
	name   : '$bui'
};
/* //$.bui.xxx
 var module = {
 exports: jQuery,
 name: 'bui'
 };*/
var $bui = function (call, arg){
	return this[call].apply(this, Array.slice.call(arguments, 1));
};

function plugin(name, constructor){
	function BuiItemConstructor(){
		if(p.init){
			p.init();
			delete p.init;
		}
		var $obj;
		if(this.__proto__ == $.fn){
			$obj = this;
		} else{
			$obj = $('<div/>');
		}

		$obj.addClass(p.class? ' bui-' + p.class : '');
		$obj.data('bui', name.toLowerCase());
		$obj.data('_bui', $obj);

		$obj.large = function (){
			if($obj.hasClass('bui-' + p.class + '-sm')){
				$obj.removeClass('bui-' + p.class + '-sm');
			}
			$obj.addClass('bui-' + p.class + '-lg');
			return $obj;
		};
		$obj.small = function (){
			if($obj.hasClass('bui-' + p.class + '-lg')){
				$obj.removeClass('bui-' + p.class + '-lg');
			}
			$obj.addClass('bui-' + p.class + '-sm');
			return $obj;
		};

		if(p.proxyInput){
			jquery_function_replace($obj, 'attr')('name', function (n){
				$obj.$input.attr('name', n);
			}, function (){
				return $obj.$input.attr('name');
			});
			$obj.val = function (v){
				if(arguments.length < 1){//get
					v = $obj.$input.val();
					return $obj.$input.getValue? $obj.$input.getValue(v) : v;
				} else{// set
					if($obj.$input.setValue){
						v = $obj.$input.setValue(v);
						if(v === false){
							return this;
						}
					}
					$obj.$input.val(v);
					return this;
				}
			}
		}

		modify_jquery($obj);
		var fn;
		for(var type in handles){
			fn = jquery_function_replace($obj, type);
			for(var n in handles[type]){
				fn(n, handles[type][n]['set'], handles[type][n]['get']);
			}
		}
		for(type in proxis){
			$obj[type] = proxis[type];
		}

		constructor.apply($obj, arguments);
		if(p.proxyInput && !$obj.$input){
			throw new Error("目标输入域没有定义（$bui." + name + ".$input）");
		}
		return $obj;
	}

	var handles = {};
	var proxis = {};
	var p = BuiItemConstructor;

	p.class = name.toLowerCase();
	p.hook = function (type, name, gs, func){
		if(!handles[type]){
			handles[type] = {};
		}
		if(!handles[type][name]){
			handles[type][name] = {};
		}
		handles[type][name][gs] = func;
		return this;
	};
	p.proxy = function (func, cb){
		proxis[func] = cb;
		return this;
	};

	return p;
}
$bui.plugin = plugin;

function trigger_change(ths, new_value){
	return ths.trigger('change', Array.prototype.slice.call(arguments, 1));
}

/**
 * 劫持 $obj 对象的 jquery 方法，转发到自定义函数
 *
 * @param $obj 属性被转发的物体
 * @param func_name 要转发的方法名字
 *
 * $obj[func].handle(name,get,set);
 *
 * 两个函数里this都是$obj本身，而不是对应的dom对象
 * 其中set返回值用来调用原始jq方法，不返回值则原始的方法不会被调用
 * get方法返回值就是最终结果，不返回值则调用原始函数
 *
 * 例如：
 *   get: function (){
 *       return testInput.val();
 *   }
 *   set: function (v){
 *      testInput.val(v);
 *      // no return -> 不要改变this的属性
 *   }
 *
 * 只能hook双参数的东西（data,attr,css...）
 *
 */
function jquery_function_replace($obj, func_name){
	if($.fn[func_name] && !$obj[func_name]){
		if(window.JS_DEBUG){
			console.error($obj);
		}
		throw new Error(' * 参数不是jquery对象。');
	}
	if($.fn[func_name] != $obj[func_name]){
		return $obj[func_name].handle; // 已经注册过了
	}
	//console.log('replace jquery method: '+func_name);
	function processor(elem, name, value){
		var handle = '_' + name;
		var isGet = value === undefined;
		if($obj[func_name].handle[handle] !== undefined &&
		   $obj[func_name].handle[handle][isGet? 'get' : 'set'] !== undefined){
			// 有hook的属性
			if(isGet){ // 获取
				//console.log('access get : '+func_name+'('+name+','+value+')');
				value = $obj[func_name].handle[handle].get.call($obj);
				if(value !== undefined){
					return value;
				}
			} else{ // 设置
				//console.log('access set : '+func_name+'('+name+','+value+')');
				value = $obj[func_name].handle[handle].set.call($obj, value);
				if(value === undefined){
					return false;
				}
			}
		}
		// 没有hook(调用jq原始方法)
		return $.fn[func_name].apply($obj, Array.prototype.slice.call(arguments, 1));
	}

	$obj[func_name] = function (name, value){
		return jQuery.access(this, processor, name, value, false);
	};
	$obj[func_name].handle = function (name, setter, getter){
		if(typeof name === 'object'){
			for(var n in name){
				$obj[func_name].handle(n, name[n].set, name[n].get);
			}
		}
		var handle = '_' + name;
		if(!$obj[func_name].handle[handle]){
			$obj[func_name].handle[handle] = {};
		}
		if(setter){
			//console.log('create set handle: '+func_name+'('+name+')');
			$obj[func_name].handle[handle].set = setter;
		}
		if(getter){
			//console.log('create get handle: '+func_name+'('+name+')');
			$obj[func_name].handle[handle].get = getter;
		}
		return $obj[func_name].handle[handle];
	};
	return $obj[func_name].handle;
}

// 防止调用后属性消失，但是这样就不能同时appendTo多个目标了（使用0下标的那个）
function modify_jquery($obj){
	$.each({
		appendTo    : "append",
		prependTo   : "prepend",
		insertBefore: "before",
		insertAfter : "after",
		replaceAll  : "replaceWith"
	}, function (name, original){
		$obj[ name ] = function (selector){
			var target = $(selector);
			if(target.length == 0){
				return this;
			}
			$(target[0])[original](this);
			return this;
		};
	});
}

module.exports[module.name] = $bui;

(function ($bui){
	var Button = $bui.Button = plugin('Button', construct);

	Button.hook('attr', 'text', 'get', function (){
		this._text();
		return false;
	});
	Button.hook('attr', 'text', 'set', function (text){
		this._text(text);
	});
	Button.hook('attr', 'type', 'get', function (){
		return this.ctype;
	});
	Button.hook('attr', 'type', 'set', function (value){
		if(this.ctype){
			this.self.removeClass('btn-' + this.ctype);
		}
		this.self.addClass('btn-' + value);
		this.ctype = value;
	});

	function construct(inner, domtype, type){
		var that = this;
		var input;
		switch(domtype){
		case 'submit':
			input = $('<input class="btn"/>').attr('type', 'submit');
			break;
		case 'input':
			input = $('<input class="btn"/>').attr('type', 'button');
			break;
		case 'button':
			that = $('<button class="btn"/>').appendTo(this);
			this._text = function (item){
				input.val(item);
			};
			break;
		default :
			this.addClass('btn');
		}
		if(input){
			this.append(input);
			this.self =input;
			this._text = function (item){
				if(item === undefined){
					input.val();
				} else{
					input.val(item);
				}
			};
		}else{
			this.self =that;
			this._text = function (item){
				if(item === undefined){
					return that.text();
				} else{
					return that.empty().append(item);
				}
			};
		}

		if(!type){
			type = 'default';
		}
		this.attr({
			type        : type,
			text        : inner,
			'data-style': domtype
		});
	}
})($bui);
(function ($bui){
	var CheckBox = $bui.CheckBox = plugin('CheckBox', construct);
	CheckBox.proxyInput = true;
	CheckBox.hook('attr', 'label', 'set', function (v){
		this.$text.text(v);
	});
	CheckBox.hook('attr', 'label', 'get', function (){
		return this.$text.text();
	});

	function construct(label){
		var $label = $('<label>').appendTo(this);
		$('<span class="checkbox-show">').appendTo($label).html('<span class="ico"/>');
		this.$input = $('<input/>').val('false').attr('type', 'hidden').appendTo($label);
		this.$text = $('<span class="checkbox-title"/>').appendTo($label);
		this.addClass('checkbox');

		var checked = false;
		this.$input.setValue = function (v){
			checked = bui_bool(v);
			$this[(checked? 'add' : 'remove') + 'Class']('active');
			return checked?'true':'false';
		};
		this.$input.getValue = function (){
			return checked;
		};

		var $this = this;
		this.click(function (){
			$this.val(!checked);
			trigger_change($this,checked);
			return false;
		});

		if(label){
			this.attr('label', label);
		}
	}
})($bui);
(function ($bui){
	var allow_type = /^warning|success|error$/i;
	var FormControl = $bui.FormControl = plugin('FormControl', construct);
	FormControl.proxyInput = true;

	function parseNewElement($item){
		var type = $item.data('bui');
		if(type == 'button'){
		} else if(type == 'icon'){
			$item = $('<span class="bui-icon-outer"/>').append($item);
		} else if($item.filter('input:not(:button,:submit)').length){
			$item.addClass('form-control')
		} else if($item.filter('span,a').length){
			$item.addClass('bui-icon-outer')
		}
		return $item;
	}

	function construct(){
		var $this = this;
		var $center = $('<div class="center-widget"/>').appendTo($this);
		$this.$input = $('<input class="form-control"/>').attr('type', 'text').appendTo($center);
		var $prepend, $append;

		$this.addClass('input-group');

		var hidden = false;
		$this.centerWidget = function (newvalue){
			if(arguments.length == 1){
				if(hidden){
					hidden.remove();
					hidden = null;
				}
				if(newvalue.attr('type') == 'hidden'){
					hidden = $('<input class="form-control"/>').attr({
						'disabled': 'disabled',
						'type'    : 'text'
					}).val(newvalue.attr('title'));
					$this.$input.replaceWith(hidden);
					newvalue.insertAfter(hidden);
				} else{
					if(!newvalue.hasClass('bui-formcontrol')){
						newvalue.addClass('form-control');
					}
					$this.$input.replaceWith(newvalue);
				}
				$this.$input = newvalue;
			}
			return $this.$input;
		};

		var alert = '';
		$this.alert = function (type){
			if(alert == type){
				return;
			}
			$this.removeClass('alert-' + alert);
			if(allow_type.test(type)){
				$this.addClass('alert-' + type.toLowerCase());
				alert = type;
			} else{
				alert = '';
			}
		};

		$this.append = function ($item){
			if(!$append){
				$append = $('<div class="input-group-btn btn-group bui-append"/>');
				$.fn.append.call($this, $append);
			}
			$item = parseNewElement($item);
			$item.appendTo($append);
			return $item;
		};

		$this.prepend = function ($item){
			if(!$prepend){
				$prepend = $('<div class="input-group-btn bui-prepend"/>');
				$.fn.prepend.call($this, $prepend);
			}
			$item = parseNewElement($item);
			return $item.prependTo($prepend);
		};
	}
})($bui);

(function ($bui){
	var gravatar = $bui.Gravatar = plugin('Gravatar', Gravatar);
	gravatar.hook('attr', 'hash', 'set', function (hash){
		this._gravatar.hash = hash;
		this._reset();
		return hash;
	});
	gravatar.hook('attr', 'size', 'set', function (size){
		this._gravatar.size = size;
		this._reset();
		return size;
	});

	function buildUrl(data){
		var ret = 'http://www.gravatar.com/avatar/';
		ret += data.hash;
		ret += '.' + (data.type? data.type : 'png');
		ret += '?';
		ret += 'd=' + (data.default? encodeURIComponent(data.default) : 'identicon');
		ret += '&s=' + data.size;

		if(data.rating){
			ret += '&r=' + data.rating;
		}
		return ret;
	}

	gravatar.build = buildUrl;

	function Gravatar(size, hash){
		this.addClass('gravatar');
		this._gravatar = {};
		this._reset = function (){
			var href = buildUrl(this._gravatar);
			this.css({'backgroundImage': 'url(' + href + ')',
				'height'               : this._gravatar.size,
				'width'                : this._gravatar.size
			});
		};
		this.attr({size: size, hash: hash});
	}
})($bui);
function intval(val){
	var tmp;

	var type = typeof val;

	if(type === 'boolean'){
		return +val;
	} else if(type === 'string'){
		tmp = parseFloat(val);
		return (isNaN(tmp) || !isFinite(tmp))? 0 : tmp;
	} else if(type === 'number' && isFinite(val)){
		return val|0;
	} else{
		return 0;
	}
}

function keycodefilter(plogic_and, pspecial_keys){
	var filter = [], equal = [], i = 0;
	var logic_and, special_keys = false;
	if(typeof plogic_and === 'string'){
		logic_and = !(plogic_and == 'or');
		i++;
	} else if(typeof plogic_and === 'boolean'){
		logic_and = false;
		special_keys = plogic_and;
		i++;
	}
	if(typeof pspecial_keys === 'boolean'){
		i++;
		special_keys = pspecial_keys;
	}
	var fn = null;
	var len = arguments.length
	for(; i < len; i++){
		if(typeof arguments[i] == 'function'){
			fn = arguments[i];
			continue;
		}
		if(parseInt(arguments[i]) == arguments[i]){
			equal.push(parseInt(arguments[i]));
		} else{
			filter.push(Range(arguments[i]));
		}
	}
	function testSpecial(code){
		if(code < 47){
			return true;
		}
		if(90 < code && code < 94){
			return true;
		}
		if(111 < code && code < 146){
			return true;
		}
		return false;
	}

	return function (event){
		if(event.shiftKey || event.ctrlKey || event.altKey){
			return fn?fn.apply(this, arguments):true;
		}
		if(special_keys){
			if(testSpecial(event.which)){
				return fn?fn.apply(this, arguments):true;
			}
		}
		var in_range = false;
		for(var i = equal.length - 1; i >= 0; i--){
			in_range = equal[i] === event.which;
			if(logic_and && !in_range){
				event.preventDefault();
				return;
			} else if(in_range){
				return fn?fn.apply(this, arguments):true;
			}
		}
		for(i = filter.length - 1; i >= 0; i--){
			in_range = filter[i].test(event.which);
			if(logic_and && !in_range){
				event.preventDefault();
				return;
			} else if(in_range){
				return fn?fn.apply(this, arguments):true;
			}
		}
		if(in_range){
			return fn?fn.apply(this, arguments):true;
		}
		event.preventDefault();
		return;
	}
}

function Range(range){
	var r = [-Infinity, Infinity], rr = [true, true];
	var ret = {
		test      : function (value){
			return (rr[0]? r[0] <= value : r[0] < value) && (rr[1]? r[1] >= value : r[1] > value);
		},
		fit       : function (value){
			if((rr[0]? r[0] > value : r[0] >= value)){
				return rr[0]? r[0] : r[0] + 1;
			}
			if((rr[1]? r[1] < value : r[1] <= value)){
				return rr[1]? r[1] : r[1] - 1;
			}
			return value;
		},
		toString  : function (){
			return (rr[0]? '[' : '(') + r[0] + ',' + r[1] + (rr[1]? ']' : ')');
		},
		fromString: function (str){
			try{
				if(parseFloat(str) == str){
					rr[1] = rr[0] = true;
					r[0] = r[1] = parseFloat(str);
					return ret;
				}
				if(!str){
					r[0] = -Infinity;
					r[1] = Infinity;
					return ret;
				}
				r = str.split(',');

				rr[0] = r[0].substr(0, 1) == '[';
				rr[1] = r[1].substr(-1) == ']';
				r[0] = r[0].substr(1);
				r[1] = r[1].substr(0, r[1].length - 1);
				r[0] = r[0].length? intval(r[0]) : -Infinity;
				r[1] = r[1].length? intval(r[1]) : Infinity;

				return ret;
			} catch(e){
				throw new Error('范围错误：' + str);
			}
		}
	};
	Object.defineProperty(ret, 'min', {
		get: function (){
			return r[0];
		},
		set: function (value){
			return r[0] = value;
		}
	});
	Object.defineProperty(ret, 'max', {
		get: function (){
			return r[1];
		},
		set: function (value){
			return r[1] = value;
		}
	});
	Object.defineProperty(ret, 'left', {
		get: function (){
			return rr[0];
		},
		set: function (value){
			return rr[0] = value;
		}
	});
	Object.defineProperty(ret, 'right', {
		get: function (){
			return rr[1];
		},
		set: function (value){
			return rr[1] = !!value;
		}
	});
	range = $.trim(range);

	if(range.length){
		return ret.fromString(range);
	} else{
		return ret;
	}
}

function bui_bool(value){
	if(typeof value == 'string'){
		value = value.toLowerCase();
		value = (value == 'on' ) || (value == 'yes');
	}
	if(typeof value != 'boolean'){
		value = !!value;
	}
	return value;
}

function onmousedown($obj, down, up){
	function set(e){
		return down.call($obj, e);
	}

	$obj.on('mousedown', set);
	function clear(e){
		var ret = up.call($obj, e);
		$obj.off('mousedown', set);
		$(document).off('mouseup', clear);
		return ret;
	}

	$(document).on('mouseup', clear);
}

function mouse_button(expect, fn){
	if(typeof expect == 'string'){
		expect = expect.toLowerCase();
		if(expect == 'left'){
			expect = 1;
		} else if(expect == 'right'){
			expect = 3;
		} else if(expect == 'middle'){
			expect = 2;
		}
	}
	if(typeof expect === 'function'){
		fn = expect;
		expect = 1;
	}
	return function (e){
		if(e.which !== expect){
			return;
		}
		fn.apply(this, arguments);
	}
}
(function ($bui){
	var list = 'adjust,align-center,align-justify,align-left,align-right,arrow-down,arrow-left,arrow-right,arrow-up,asterisk,backward,ban-circle,barcode,bell,bold,book,bookmark,briefcase,bullhorn,calendar,camera,certificate,check,chevron-down,chevron-left,chevron-right,chevron-up,circle-arrow-down,circle-arrow-left,circle-arrow-right,circle-arrow-up,cloud,cloud-download,cloud-upload,cog,collapse-down,collapse-up,comment,compressed,copyright-mark,credit-card,cutlery,dashboard,download,download-alt,earphone,edit,eject,envelope,euro,exclamation-sign,expand,export,eye-close,eye-open,facetime-video,fast-backward,fast-forward,file,film,filter,fire,flag,flash,floppy-disk,floppy-open,floppy-remove,floppy-save,floppy-saved,folder-close,folder-open,font,forward,fullscreen,gbp,gift,glass,globe,hand-down,hand-left,hand-right,hand-up,hd-video,hdd,header,headphones,heart,heart-empty,home,import,inbox,indent-left,indent-right,info-sign,italic,leaf,link,list,list-alt,lock,log-in,log-out,magnet,map-marker,minus,minus-sign,move,music,new-window,off,ok,ok-circle,ok-sign,open,paperclip,pause,pencil,phone,phone-alt,picture,plane,play,play-circle,plus,plus-sign,print,pushpin,qrcode,question-sign,random,record,refresh,registration-mark,remove,remove-circle,remove-sign,repeat,resize-full,resize-horizontal,resize-small,resize-vertical,retweet,road,save,saved,screenshot,sd-video,search,send,share,share-alt,shopping-cart,signal,sort,sort-by-alphabet,sort-by-alphabet-alt,sort-by-attributes,sort-by-attributes-alt,sort-by-order,sort-by-order-alt,sound-5-1,sound-6-1,sound-7-1,sound-dolby,sound-stereo,star,star-empty,stats,step-backward,step-forward,stop,subtitles,tag,tags,tasks,text-height,text-width,th,th-large,th-list,thumbs-down,thumbs-up,time,tint,tower,transfer,trash,tree-conifer,tree-deciduous,unchecked,upload,usd,user,volume-down,volume-off,volume-up,warning-sign,wrench,zoom-in,zoom-out'.split(',');

	var Icon = $bui.Icon = plugin('Icon', construct);
	Icon.list = list;
	Icon.hook('attr','icon','get',function (){
		return this.change();
	});
	Icon.hook('attr','icon','set',function (text){
		this.change(text);
	});
	
	function construct(icon){
		this.addClass('glyphicon');
		var last = '';
		this.change = function (icon){
			if(!icon){
				return last;
			}
			if(last){
				this.removeClass('glyphicon-' + last);
			}
			this.addClass('glyphicon-' + icon);
			last = icon;
			return this;
		};
		if(icon){
			this.change(icon);
		}
	}
})($bui);
(function ($bui){
	var InputList = $bui.InputList = plugin('InputList', construct);
	InputList.hook('attr', 'name', 'set', function (v){
		this.$list.find('input').attr('name', v + '[]');
		return v;
	});

	function item(value, name){
		var li = $('<li/>');
		$('<span/>').addClass('bui-label').text(value).appendTo(li);
		$('<input/>').attr({'type': 'hidden', 'name': name + '[]'}).val(value).appendTo(li);
		$('<a/>').addClass('bui-delete').appendTo(li);
		return li;
	}

	function unserilize(val, name){
		if(val.constructor !== Array){
			throw new TypeError("$bui.InputList.val() 参数必须是数组。");
		}
		var list = [];
		for(var i = 0, cnt = val.length; i < cnt; i++){
			list[i] = item(val[i], name)[0];
		}
		return $(list);
	}

	function construct(){
		var $this = this;
		var value = [];
		var $list = $('<ul class="list"/>').appendTo(this);
		var control = (new $bui.FormControl()).appendTo($('<div class="control"/>').appendTo(this));
		var $center = control.centerWidget($('<input/>').attr('type', 'text'));
		var addBtn = new $bui.Button(new $bui.Icon('plus'));
		this.$list = $list;
		control.append(addBtn);

		$center.on('keydown', handler);
		this.centerWidget = function (newinput){
			$center.off('keydown', handler);
			$center = control.centerWidget(newinput);
			newinput.attr('name','');
			newinput.on('keydown', handler);
		};
		this.on('click','.bui-delete',function(e){
			var v= $(this).prev().val();
			var i  = value.indexOf(v);
			if(i>-1){
				value.splice(i, 1);
			}
			$(this).parent().remove();
			e.preventDefault();
		});

		function handler(event){
			if(event.shiftKey || event.ctrlKey || event.altKey){
				return;
			}
			if(event.which == 13){
				var val = $center.val();
				$center.val('').focus();
				addItem(val);
				return false;
			}
		}

		this.val = function (v){
			if(arguments.length == 0){
				return value;
			} else{
				var name = $this.attr('name');
				value = v;
				var o = unserilize(v, name);
				o.appendTo($list.empty());
				return this;
			}
		};

		addBtn.on('click', mouse_button('left', function (){
			var val = $center.val();
			$center.val('').focus();
			addItem(val);
		}));

		this.addVal = addItem;
		function addItem(val){
			if(!val){
				return false;
			}
			if($this.data('unique') && value.indexOf(val) >= 0){
				return false;
			}
			var name = $this.attr('name');
			item(val, name).appendTo($list);
			value.push(val);
			trigger_change($this, value);
		}
	}
})($bui);
(function ($bui){
	var IntInput = $bui.IntInput = plugin('IntInput', IntInputConstruct);

	IntInput.hook('attr', 'range', 'get', function (){
		return this.range.toString();
	});
	IntInput.hook('attr', 'range', 'set', function (rangeStr){
		this.range.fromString(rangeStr);
		var value = this.val();
		var fitvalue = this.range.fit(value);
		if(value != fitvalue){
			this.val(fitvalue);
			trigger_change(this, fitvalue);
		}
	});
	IntInput.proxyInput = true;

	function IntInputConstruct(range){
		if(!range){
			range = '';
		}
		//初始化变量
		var last_success_value = 0;
		var $this = $bui.FormControl.call(this);
		$this.val(0);
		var r = $this.range = new Range();
		$this.prop('speed', 1);

		// 输入框和左右按钮
		var $input = $this.$input = $this.centerWidget().addClass('text-center').attr('type', 'number')
				.on('keydown', keycodefilter('or', '[96,105]', '[48,57]', '109', '189', function (e){
					$this.removeClass('has-error');
				})).change(function(){
					$this.val($(this).val());
					trigger_change($this, $this.val());
				});
		$this.$left = $this.prepend($bui.Button(new $bui.Icon('arrow-left'), 'span', 'default'));
		$this.$right = $this.append($bui.Button(new $bui.Icon('arrow-right'), 'span', 'default'));

		$input.setValue = function (val){
			var value = parseInt(val);
			if(value == val && r.test(value)){
				if($this.hasClass('has-error')){
					$this.removeClass('has-error');
					return false;
				}
				last_success_value = val;
				return val;
			}
			value = r.fit(value);
			$this.addClass('has-error');
			return value + '';
		};
		$input.getValue = intval;

		$this.attr('range', range);
		$this.alert('');

		//自动增减
		function go(){
			if($this.pressed){
				//console.log($this.pressed);
				if($this.pressed > 50){//最大速度
					$this.pressed -= Math.round($this.pressed/3);//加速度
				}
				var v = $this.val() + go.dir*$this.prop('speed');
				$this.val(v);
				if(v == last_success_value){
					trigger_change($this, v);
				}
				go._time = setTimeout(go, $this.pressed);
			}
		}

		//左右按钮
		$this.$left.mousedown(function (event){
			if(event.which > 2){
				return;
			}

			$this.pressed = 500; //初始速度
			go.dir = -(1 + (event.which - 1)*9);
			go();
			function up(){
				clearTimeout(go._time);
				$this.pressed = false;
				$(document).off('mouseup', up);
			}

			$(document).on('mouseup', up);
		});
		$this.$right.mousedown(function (){
			if(event.which > 2){
				return;
			}

			$this.pressed = 500; //初始速度
			go.dir = 1 + (event.which - 1)*9;
			go();
			function up(){
				clearTimeout(go._time);
				$this.pressed = false;
				$(document).off('mouseup', up);
			}

			$(document).on('mouseup', up);
		});

		// 滚轮调整
		$this.on('mousewheel', function (e, delta){
			$this.pressed = 1000;
			go.dir = delta;
			go();
			$this.pressed = false;
			return false;
		});

		return $this.removeClass('has-error');
	}
})($bui);
(function ($bui){

	var construct = function (){
		var _self = this;
		this.$list = {};
		this.$select = {};
		this.options = [];
		if(arguments.length > 0){
			var args = Array.prototype.slice.call(arguments);
			var args1 = args.shift();
			this.name = args1.name;
			this.lines = args1.lines;
			for(var i = 0; i < args1.options.length; i++){
				this.options.push(args1.options[i]);
			}
			init.call(this);
		}
		function init(){
			var _ms = '<ul multiple class="bui-select list-group">';
			var ms_ = '</ul>';
			var ss = '<select multiple name="' + this.name + '[]" style="display:none;">';
			var se = '</select>';
			var options = '', lis = '';
			for(var i = 0; i < this.options.length; i++){
				options += ' <option value="' + this.options[i][1] + '">' + this.options[i][0] + '</option>';
				lis += ' <li class="btn  list-group-item bui-select-option">' + this.options[i][0] + '</li>';
			}

			_self.$list = $(_ms + lis + ms_).appendTo(_self);
			_self.$select = $(ss + options + se).appendTo(_self);

		}

		this.appendTo = function (s){
			$.fn.appendTo.call(this, s);
			this.adjustSize();
		};
		this.addItem = function (n, v){
			var arr = new Array(n, v);
			this.options.push(arr);
			if(this.$list){
				this.$select.append(' <option value="' + v + '">' + n + '</option>');
				this.$list.append(' <li class="btn  list-group-item bui-select-option">' + n + '</li>');
			} else{
				this.init();
			}
		};
		this.adjustSize = function (){
			this.$select.css({height: this.lines*this.$list.find('li').get(0).clientHeight});
		};

	};

	function setValue(value){

	}

	plugin('MultiSelect', construct, setValue);
})($bui);
$(document).on('click', '.bui-select-option', function (){
	var $this = $(this);
	var $select = $this.parent().next();
	$select.focus();

	$select.find('option').eq($this.index()).click();
	console.log($select);
	var item = $select.find('option').get($this.index());

	item.selected = !item.selected;

	$this.toggleClass('btn-primary');
});
(function ($bui){
	var OneOf = $bui.OneOf = plugin('OneOf', construct);

	function construct(){
		var $this = this;
		var items = [];
		var name_list = [];
		var current = -1;

		function doSelect(name){
			if(!name){
				return;
			}
			var new_index = name_list.indexOf(name);
			if(current === new_index){
				return;
			}
			var new_item = items[new_index];
			trigger_change($this, new_index, name, new_item);

			new_item._item.attr('name', name);
			new_item.addClass('active');

			if(current != -1){
				items[current]._item.attr('name', '');
				items[current].removeClass('active');
			}

			current = new_index;
		}

		this.addItem = function (buiItem){
			var name = buiItem.attr('name');
			if(!name){
				throw new Error('bui.OneOf.addItem 参数必须是其他bui object，或者有name属性的元素');
			}
			buiItem.attr('name', '');
			buiItem.attr('tabindex', '-1');
			
			var $div = $('<div class="line"/>');
			var ico = (new $bui.Icon('unchecked')).addClass('one_state');
			$('<div class="icon"/>').append(ico).appendTo($div);
			$('<div class="control"/>').append(buiItem).appendTo($div);
			$div._item = buiItem;
			$div.appendTo(this);

			items.push($div);
			name_list.push(name);
			if(current == -1){
				doSelect(name);
			}
			$div.on('click', function (){
				doSelect(name);
			});
			buiItem.on('change', function (){
				doSelect(name);
			});
			return this;
		};
		this.focus(function (){
			items[current].focus();
		});
	}
})($bui);
$(document).on('shown.bs.tab', function (e){
	var $this = $(e.target);
	var $that = $this.closest('ul:not(.dropdown-menu)>.active');
	if(!$that.length){
		$this.addClass('active');
	}
});
(function ($bui){

	var Progress = $bui.Progress = plugin('Progress', ProgressConstructor);
	Progress.hook('attr', 'progress', 'set', function (progress){
		this._bui_bar._setvalue(progress);
		this._progress = progress;
	});
	Progress.hook('attr', 'progress', 'get', function (){
		return this._progress;
	});

	function ProgressConstructor(){
		this.addClass('progress');
		var $bar = $('<div class="progress-bar"  role="progressbar"/>').appendTo(this);
		var $acc = $('<span class="sr-only"/>').appendTo($bar);
		$bar._setvalue = function (v){
			$bar.css('width', v + '%');
			$acc.text('已经完成百分之' + v);
		};
		this._progress = 0;
		this._bui_bar = $bar;
		var lastalert = '';
		this.alert = function (type){
			if(lastalert){
				this.removeClass(lastalert);
				lastalert = '';
			}
			if(type){
				lastalert = 'progress-bar-'+type;
				this.addClass(lastalert);
			}
		}
	}
})($bui);
(function ($bui){
	var plug = $bui.Select = plugin('Select', Select);
	plug.proxyInput = true;
	plug.init = function (){
		$(document).on('click', hide);
	};
	plug.hook('attr', 'title', 'set', function (v){
		if(this.$input && !this.$input.val()){
			this.$show.text(v);
		}
		return v;
	});

	var current = null;

	function hide(){
		if(current){
			current.removeClass('active');
			current = null;
		}
	}

	function item(title, value){
		return $('<li/>').attr('value', value).append($('<a/>').html(title));
	}

	function Select(){
		var item_list = {};
		var $this = this.addClass('form-control btn btn-default');
		var $list = $('<ul class="dropdown-menu" role="menu"/>');

		this.addOption = function (title, value){
			item_list[value] = title;
			$list.append(item(title, value));
		};
		this.find('option').each(function (){
			$this.addOption($(this).val(), $(this).text());
			$(this).remove();
		});
		$list.append(this.children()).appendTo(this);

		var $btn = $('<div class="bui-toggle"/>').prependTo($this);
		$this.$show = $('<span/>').appendTo($btn);
		$('<span class="caret"/>').appendTo($btn);

		$this.$input = $('<input/>').attr('type', 'hidden').val('').prependTo($this);
		$this.$input.setValue = function (v){
			if(!item_list.hasOwnProperty(v)){
				$this.$show.text($this.attr('title'));
				return '';
			}
			$this.$show.text(item_list[v]);
			return v;
		};
		if(!this.attr('title')){
			this.attr('title', '请选择');
		}

		// active
		$this.click(function (){
			if($this.hasClass('active')){
				hide();
			} else{
				hide();
				current = $this.addClass('active');
			}
			return false;
		});
		$list.on('click', 'li', function (){
			var v = $(this).attr('value');
			$this.$input.val(v);
			$this.$show.text($(this).text());
			hide();
			trigger_change($this, v);
			return false;
		});

	}

})($bui);
(function ($bui){
	var Switch = $bui.Switch = plugin('Switch', construct);
	Switch.proxyInput = true;

	Switch.hook('attr', 'disabled', 'set', function (v){
		if(typeof v === 'string'){
			v = v.toLowerCase() == 'disabled';
		}
		if(v){
			this.$btn.addClass('disabled');
			this.addClass('disabled');
		} else{
			this.$btn.removeClass('disabled');
			this.removeClass('disabled');
		}
		this._lock = v;
		return v;
	});

	function boolval(value){
		value = bui_bool(value);
		$(this)[(value? 'add' : 'remove') + 'Class']('on');
		if(value){
			this.$input.removeAttr('disabled');
		} else{
			this.$input.attr('disabled', 'disabled');
		}
		this.current_status = value;
		return false;
	}

	function construct(state){
		var $this = this;
		this.current_status = !!state;
		this.icon = new $bui.Icon('');
		this.$input = $('<input/>').val('true').attr('type', 'hidden').appendTo(this);
		this.$input.setValue = function (value){
			value = bui_bool(value);
			$this[(value? 'add' : 'remove') + 'Class']('on');
			$this.current_status = value;
			return value? 'true' : 'false';
		};
		this.$input.getValue = function (){
			return $this.current_status;
		};

		var $switcher = $('<div/>').addClass('bui-switch-container').appendTo(this);
		var $btn = $('<span class="btn btn-default bui-switch-btn"/>').appendTo($switcher);
		var $mask = $('<div class="bui-switch-mask"/>').appendTo($switcher);
		var $clip = $('<span class="bui-switch-blue"/>');

		this.$btn = $btn;
		var dragstate = false;

		function dragHandler(e){
			if($this._lock){
				return;
			}
			$this.addClass('drag');
			var max = $this.width();
			var start = e.pageX - parseFloat($btn.css('left'));
			var left = 0;
			$(document).on('mouseup', dropHandler);
			$(document).on('mousemove', moveHandler);
			$btn.on('mouseleave', preventToggle);
			function moveHandler(e){
				left = e.pageX - start;
				if(left <= 0 || left >= max){
					return;
				}
				$btn.css('left', e.pageX - start);
				$mask.css('left', e.pageX - start);
			}

			function dropHandler(e){
				if(($this.current_status && left < max/2) || (!$this.current_status && left > max/2)){
					$this.val(!$this.current_status);
					trigger_change($this, $this.current_status);
				}
				$btn.css('left', '');
				$mask.css('left', '');
				$this.removeClass('drag');
				$(document).off('mouseup', dropHandler);
				$btn.off('mouseleave', preventToggle);
				$(document).off('mousemove', moveHandler);
			}
		}

		function preventToggle(){
			dragstate = true;
		}

		$btn.on('mousedown', dragHandler);

		this.val(this.current_status);

		this.on('click', function (){
			if(dragstate){
				dragstate = false;
				return;
			}
			if($this._lock){
				return;
			}
			$this.val(!$this.current_status);
			trigger_change($this, $this.current_status);
		});
	}
})($bui);
(function ($bui){

	var UploadSingle = $bui.UploadSingle = plugin('UploadSingle', UploadSingleConstructor);
	UploadSingle.proxyInput = true;

	function UploadSingleConstructor(option){
		if(typeof option === 'string'){
			option = {url: option};
		}
		if(!option || !option.url){
			throw new Error('bui.UploadSingle 构造必须指定一个url');
		}
		option = $.extend({
			dataType          : 'json',
			autoUpload        : false,
			acceptFileTypes   : /(\.|\/)(gif|jpe?g|png)$/i,
			maxFileSize       : 5000000, // 5 MB
			disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
			previewMaxWidth   : 100,
			previewMaxHeight  : 100,
			maxChunkSize      : 4000000, // 10 MB
			multipart         : false,
			previewCrop       : true,
			current           : null
		}, option);

		var $container = (new $bui.FormControl()).appendTo(this);

		var current = option.current;
		var $this = this;
		this.$input = $('<input style="display: none!important;"/>').attr('type', 'text').appendTo(this);
		this.$input.getValue = function (){
			return current;
		};
		this.$input.setValue = function (v){
			return current = v;
		};
		var $input = $container.centerWidget($('<input/>').attr('type', 'file'));
		var progress = new $bui.Progress();
		progress.addClass('progress-striped active').css({'width': '200px'});
		$container.append(progress);

		var current_preview = $('<span/>').append(new $bui.Icon('eye-open'));
		$container.prepend(current_preview);
		current_preview.popover({
			html     : true,
			title    : '当前图片',
			content  : function (){
				return current
						? '<img src="' + current + '" width="100" height="100"/>'
						: '<div style="width:100px;height:100px;text-align:center;">无</div>';
			},
			placement: 'auto',
			trigger  : 'hover'
		});

		var preview_content = '';
		var preview = $input.parent().popover({
			html     : true,
			title    : '选中的图像',
			content  : function (){
				return preview_content? preview_content : '没有选中';
			},
			placement: 'auto bottom',
			trigger  : 'manual hover'
		});

		var upload_instance = {};
		var uploadStart = (new $bui.Button(new $bui.Icon('cloud-upload')))
				.click(function (){
					if($(this).hasClass('disabled')){
						return;
					}
					upload_instance.submit();
					state_upload();
				});
		var clearUpload = (new $bui.Button(new $bui.Icon('remove')))
				.click(function (){
					if($(this).hasClass('disabled')){
						return;
					}
					if(state == 2){
						upload_instance.abort();
					}
					state_empty();
				});
		$container.append(uploadStart);
		$container.append(clearUpload);

		$input.fileupload(option).on('fileuploadadd',function (e, data){
			console.log('add', data);
			state_ready();
		}).on('fileuploadprocessalways',function (e, data){
					upload_instance = data;
					var index = data.index;
					var file = data.files[index];
					if(!file){
						return state_empty();
					}
					if(file.preview){
						preview_content = file.preview;
						preview.popover('show');
					}
					if(file.error){
						preview_content = '上传失败';
						preview.popover('show');
					} else{
						state_ready();
					}
				}).on('fileuploadprogressall',function (e, data){
					var pers = parseInt(data.loaded*1000/data.total, 10)/10;
					progress.attr('progress', pers);
				}).on('fileuploaddone',function (e, data){
					var file = data.result.files[0];
					if(file.url){
						$this.val(file.url);
						current_preview.popover('show');
					} else if(file.error){
						$this.val(option.current);
						preview_content = '上传失败';
						preview.popover('show');
					}
					state_empty();
				}).on('fileuploadfail', function (e, data){
					preview_content = '发生网络错误，上传失败，请重试。';
					preview.popover('show');
					state_empty();
				});

		var state = 0;
		state_empty();

		function state_empty(){
			preview.popover('hide');
			uploadStart.addClass('disabled');
			clearUpload.addClass('disabled');
			$input.val('').removeAttr('disabled');
			progress.attr('progress', 0);
			state = 0;
		}

		function state_ready(){
			uploadStart.removeClass('disabled');
			clearUpload.removeClass('disabled');
			$input.attr('disabled', 'disabled');
			state = 1;
		}

		function state_upload(){
			preview_content = null;
			preview.popover('hide');
			uploadStart.addClass('disabled');
			clearUpload.removeClass('disabled');
			$input.attr('disabled', 'disabled');
			state = 2;
		}
	}
})($bui);

})(window.jQuery||jQuery);
