(function (components){
	"use strict";
	
	
	
	components.ValueBar = function (cls){

	};

	components.CenterBar = function (cls){

	};

	components.TwoSideBar = function (class1, class2){
		var $ret = $('<div class="progress" style="padding-left: 50%">');
		var $left = $('<div class="progress-bar progress-bar-' + class1 + '" style="width:0;margin-left:0;"/>');
		var $right = $('<div class="progress-bar progress-bar-' + class2 + '" style="width:0"/>');
		var $lLabel = $('<span/>').appendTo($left);
		var $rLabel = $('<span/>').appendTo($right);

		$ret.append($left).append($right);

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
})(window.components || (window.components = {}));
