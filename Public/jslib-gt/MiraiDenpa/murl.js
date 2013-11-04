(function ($){
	"use strict";
	$.modifyUrl = function (href, modify, ret_object){
		var i, tmp, url = {}, p;
		if(typeof href === 'string'){
			p = $.url(href, true).data;
		} else{
			p = href;
		}
		url.protocol = p.attr.protocol? p.attr.protocol + '://' : '';
		url.userInfo = p.attr.userInfo? p.attr.userInfo + '@' : '';
		url.host = p.attr.host? p.attr.host : '';
		url.port = p.attr.port? ':' + p.attr.port : '';
		i = p.attr.path.replace(/^\//, '').split(/\.([^\.]*)$/);
		tmp = i[0].split('/');
		url.action = tmp.length? '/' + tmp.shift() : '/';
		url.method = tmp.length? '/' + tmp.shift() : '/';
		url.path = tmp;
		url.suffix = i[1]? '.' + i[1] : '';
		url.param = p.param.query? p.param.query : {};
		url.fragment = p.attr.fragment? '#' + p.attr.fragment : '';
		p = tmp = null;

		url.modify = function (cnt_modify){
			var item;
			if(cnt_modify.app){
				url.host = window.Think.URL_MAP[cnt_modify.app];
			}
			if(cnt_modify.action){
				url.action = '/' + cnt_modify.action;
			}
			if(cnt_modify.method){
				url.method = '/' + cnt_modify.method;
			}
			if(cnt_modify.suffix){
				url.suffix = '.' + cnt_modify.suffix;
			}else if(cnt_modify.extension){
				url.suffix = '.' + cnt_modify.extension;
			}

			// 修改路径参数
			for(item in url.path){
				if(url.path.hasOwnProperty(item)){
					url.path[item] = url.path[item];
				}
			}
			for(item in cnt_modify.path){
				if(cnt_modify.path.hasOwnProperty(item)){
					if(cnt_modify.path[item].constructor === Function){
						url.path[item] = cnt_modify.path[item]();
					} else{
						url.path[item] = cnt_modify.path[item];
					}
					if(!url.path[item]){
						url.path[item] = undefined;
					}
				}
			}

			// 修改get参数 (动态回调)
			if(cnt_modify.append && cnt_modify.append.length){
				if(!modify.append){
					modify.append = [];
				}
				$.merge(modify.append, cnt_modify.append);
			}
			// 修改get参数 (静态)
			if(cnt_modify.param){
				if(!modify.param){
					modify.param = {};
				}
				$.extend(url.param, modify.param, cnt_modify.param);
			}

			if(url.port && !url.host){
				url.host = location.current.host;
			}
			if(!url.protocol && url.host){
				url.protocol = 'http://'
			}
			return url.toString();
		};

		url.toString = function (){
			var path = '';
			if(this.path.length){
				for(i = 0; i < this.path.length; i++){
					if(this.path[i] === undefined){
						path += '/null';
					} else{
						path += '/' + this.path[i];
					}
				}
			}
			if(modify.append && modify.append.length){
				for(i = 0; i < modify.append.length; i++){
					modify.append[i](url.param)
				}
			}
			var param = [];
			for(i in url.param){
				if(url.param.hasOwnProperty(i)){
					param.push(i + '=' + encodeURIComponent(url.param[i]));
				}
			}
			if(param.length){
				param = '?' + param.join('&');
			} else{
				param = '';
			}

			return this.protocol + this.userInfo + this.host + this.port + this.action + this.method + path +
			       this.suffix + param + this.fragment;
		};

		if(ret_object){
			url.modify(modify);
			return url;
		} else{
			return url.modify(modify);
		}
	}

})(jQuery);

// 当前页面的url
(function (){
	var CURRENT = $.modifyUrl(location.href, {}, true);
	location.modify = function (mod){
		location.href = CURRENT.modify(mod);
	};
	Object.defineProperty(location, 'current', {
		get: function (){
			return CURRENT;
		}
	})
})();
