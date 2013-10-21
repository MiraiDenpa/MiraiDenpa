(function (weibo){
	var cache = {};
	var url = weibo.url;
	weibo.Channel = function (channel){
		if(cache[channel]){
			return cache[channel];
		} else{
			return cache[channel] = new ChannelObject(channel);
		}
	};
	function ChannelObject(channel){
		var callbackListFn = $.Callbacks('unique');
		var callbackPageFn = $.Callbacks('unique');
		var req_name = 'page_of_channel_' + channel + '';
		var req = {};
		req.url = $.modifyUrl(weibo.baseurl, {action: 'List', method: 'channel', path: [channel], extension: 'json'});
		req.url = url(req.url);
		req.hook = [req_name];
		req.map = {};
		req.map[req_name] = 'path.1';

		req.fetch = function (ret){
			if(ret.code === window.Think.ERR_NO_ERROR){
				return ret.list;
			} else{
				return false;
			}
		};
		req.handler = function (list){
			callbackListFn.fire(list);
		};
		DenpaHistory.addHandler('denpa.weibo.channel', req);

		req.fetch = function (ret){
			if(ret.code === window.Think.ERR_NO_ERROR){
				return ret.page;
			} else{
				return false;
			}
		};
		req.handler = function (page){
			callbackPageFn.fire(page);
		};
		DenpaHistory.addHandler('denpa.weibo.channel', req);

		this.page = function (p){
			if(p == undefined){
				p = DenpaHistory.map_current(req_name) || 1;
			}
			if(p < 1){
				throw new Error('页码不正确', p);
			}
			var r = {p: p};
			r[req_name] = p;
			DenpaHistory.query(r, '更新微博列表[' + channel + '] 第' + p + '页');
			return this;
		};

		this.listHandler = function (fnlisteach){
			callbackListFn.add(fnlisteach);
		};
		this.pageHandler = function (fnpage){
			callbackPageFn.add(fnpage);
		};
	}
})(window.denpa || (window.denpa = {}));

