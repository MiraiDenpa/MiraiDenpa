(function (window){
	"use strict";
	$.build.Gravatar = function (hash, opt){

	};

	function getUrl(hash, opt){
		var param = '?';
		if(opt.size){
			param += 's=' + opt.size;
		} else{
			param += 's=64';
		}
		if(opt.default){
			param += '&d=' + opt.default;
		} else{
			param += '&d=' + encodeURIComponent(window.Think.PUBLIC_URL) + '%2Fpublic%2Favatar.png';
		}

		return 'http://www.gravatar.com/avatar/' + hash + param;
	}

	$.fn.Gravatar
})(window);
