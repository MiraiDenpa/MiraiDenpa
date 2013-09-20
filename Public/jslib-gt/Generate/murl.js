(function ($){
	"use strict";
	var CURRENT = $.url('', true).data.attr;

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
		url.extension = i[1]? '.' + i[1] : '';
		url.param = p.param.query? p.param.query : {};
		url.fragment = p.attr.fragment? '#' + p.attr.fragment : '';
		p = tmp = null;

		url.modify = function (modify){
			var item;
			if(modify.app){
				url.host = window.Think.URL_MAP[modify.app];
			}
			if(modify.action){
				url.action = '/' + modify.action;
			}
			if(modify.method){
				url.method = '/' + modify.method;
			}
			if(modify.extension){
				url.extension = '.' + modify.extension;
			}

			// 修改路径参数
			for(item in url.path){
				if(url.path.hasOwnProperty(item)){
					url.path[item] = url.path[item];
				}
			}
			for(item in modify.path){
				if(item == 0){
					continue;
				}
				if(modify.path.hasOwnProperty(item)){
					if(modify.path[item].constructor === Function){
						url.path[item] = modify.path[item]();
					} else{
						url.path[item] = modify.path[item];
					}
				}
			}

			// 修改get参数 (动态回调)
			var param = [];
			if(modify.append && modify.append.length){
				for(i = 0; i < modify.append.length; i++){
					modify.append[i](url.param)
				}
			}
			// 修改get参数 (静态)
			if(modify.param){
				for(item in modify.param){
					param.push(item + '=' + modify.param[item]);
				}
			}
			for(item in url.param){
				param.push(item + '=' + url.param[item]);
			}
			param = param.join('&');
			url.param = param? '?' + param : '';

			if(url.port && !url.host){
				url.host = CURRENT.host;
			}
			if(!url.protocol && url.host){
				url.protocol = 'http://'
			}
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
			return this.protocol + this.userInfo + this.host + this.port + this.action + this.method + path +
			       this.extension + this.param + this.fragment;
		};

		url.modify(modify);
		if(ret_object){
			return url;
		} else{
			return url.toString();
		}
	}
})(jQuery);
