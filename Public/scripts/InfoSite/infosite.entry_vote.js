(function (components){
	"use strict";

	components.ValueBar = function (cls){
		var $ret = $('<div class="progress" style="">');
		var $bar = $('<div class="progress-bar progress-bar-' + cls + '" style="width:0;"/>');
		$ret.append($bar);

		var value = 0;
		Object.defineProperty($ret, 'value', {
			get: function (){
				return value;
			},
			set: function (set){
				value = set;
				$bar.css({width: set + '%'});
			}
		});

		return $ret;
	};

	components.CenterBar = function (class1, class2){
		var $ret = new components.TwoSideBar(class1, class2);

		var value = 0;
		Object.defineProperty($ret, 'value', {
			get: function (){
				return value;
			},
			set: function (set){
				if(set >= 50){
					$ret.left = 0;
					$ret.right = set;
				} else{
					$ret.left = -set;
					$ret.right = 0;
				}
				value = set;
			}
		});

		var offset = 0;
		Object.defineProperty($ret, 'offset', {
			get: function (){
				return offset/10;
			},
			set: function (set){
				offset = set*10;
			}
		});

		return $ret;
	};

	components.TwoSideBar = function (class1, class2){
		var $ret = $('<div class="progress" style="padding-left: 50%;">');
		var $left = $('<div class="progress-bar progress-bar-' + class1 + ' revert" style="width:0;margin-left:0;"/>');
		var $right = $('<div class="progress-bar progress-bar-' + class2 + '" style="width:0"/>');

		$ret.append($left).append($right);

		$ret.mapPosition = function ($obj, value){
			if(value > 50){
				$obj.css({width: (value - 50)*2 + '%'});
			} else{
				$obj.css({width: value + '%', marginLeft: '-' + value + '%'});
			}
		};

		var left = 0;
		Object.defineProperty($ret, 'left', {
			get: function (){
				return left;
			},
			set: function (set){
				left = set;
				$left.css({width: set + '%', marginLeft: '-' + set + '%'});
			}
		});

		var right = 0;
		Object.defineProperty($ret, 'right', {
			get: function (){
				return right;
			},
			set: function (set){
				right = set;
				$right.css({width: set + '%'});
			}
		});

		return $ret;
	};

	components.DragPointer = function DragPointer(size){
		var container = $('<div class="drag_pointer">');
		var content = $('<div class="circle"/>').appendTo(container);
		var arrow = $('<div class="arrow"/>').appendTo(container);

		Object.defineProperty(this, 'content', {
			get: function (){
				return content;
			}
		});

		// 计算 vvv
		var x = 0;
		Object.defineProperty(this, 'x', {
			set: function (ox){
				x = ox;
				recalc();
			},
			get: function (){
				return x;
			}
		});
		var y = 0;
		Object.defineProperty(this, 'y', {
			set: function (oy){
				y = oy;
				recalc();
			},
			get: function (){
				return y;
			}
		});
		var d = size/2;
		Object.defineProperty(this, 'size', {
			set: function (osize){
				size = osize;
				d = size/2;
				recalc();
			},
			get: function (){
				return size;
			}
		});
		var rotate = 0, alpha = (135 - rotate)*Math.PI/180;
		Object.defineProperty(this, 'rotate', {
			set: function (orotate){
				rotate = orotate;
				alpha = (135 - orotate)*Math.PI/180;
				recalc();
			},
			get: function (){
				return rotate;
			}
		});

		var sqrt2 = Math.sqrt(2);

		function recalc(){
			var xx = x - sqrt2*d*Math.cos(alpha) - d;
			var yy = y + sqrt2*d*Math.sin(alpha) - d;

			container.css({height: size, width: size, left: xx, top: yy});
			arrow.css('transform', 'rotate(' + rotate + 'deg)');
		}

		var s = this;
		$(['appendTo', 'prependTo', 'insertAfter', 'insertBefore', 'remove',
			'data', 'removeData', 'css',
			'anime', 'queue', 'dequeue', 'trigger', 'delay', 'on', 'off',
			'fadeIn', 'fadeTo', 'fadeOut', 'fadeToggle', 'show', 'hide', 'toggle',
			'class', 'addClass', 'hasClass', 'removeClass', 'toggleClass',
			'parent', 'closest', 'offset'
		])
				.each(function (_, name){
					s[name] = function (){
						return $.fn[name].apply(container, arguments);
					}
				});
		s = null;

		recalc();
		return this;
	}
})(window.components || (window.components = {}));
