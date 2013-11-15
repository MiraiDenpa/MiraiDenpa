(function (weibo){
	var cache = {}, channel_list = [];
	weibo.Channel = function (channel){
		if(cache[channel]){
			return cache[channel];
		} else{
			return cache[channel] = new ChannelObject(channel);
		}
	};

	function ChannelObject(channel){
		channel_list.push(channel);
		this.id = channel;
		var callbackListFn = $.Callbacks('unique');
		var callbackPageFn = $.Callbacks('unique');
		var req_name = 'page_of_channel_' + channel + '';
		var req = {};
		var type = 'tree';

		var autoForward;

		req.url = $.modifyUrl(weibo.baseurl, {action: 'List', method: 'channel', path: [channel], extension: 'json'});
		req.hook = [req_name];

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
		DenpaHistory.addHandler('denpa.weibo.channel.' + channel, req);

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
		DenpaHistory.addHandler('denpa.weibo.channel.' + channel, req);

		this.page = function (p){
			if(p == undefined){
				p = DenpaHistory.map_current(req_name) || 1;
			}
			if(p < 1){
				throw new Error('页码不正确', p);
			}
			var r = {p: p, type: type};
			r[req_name] = p;
			weibo.attachToken(r);
			DenpaHistory.query(r, '更新微博列表[' + channel + '] 第' + p + '页');
			return this;
		};

		this.map = function (param){
			DenpaHistory.unmap(req_name);
			DenpaHistory.map(req_name, param);
		};
		this.map('param.' + req_name);

		this.post = function (content, forward){
			var wb = {content: content, channel: channel};
			if(forward){
				wb.forward = forward;
			} else if(autoForward){
				wb.forward = autoForward
			}

			return weibo.post(wb);
		};
		this.listHandler = function (fnlisteach){
			callbackListFn.add(fnlisteach);
		};
		this.pageHandler = function (fnpage){
			callbackPageFn.add(fnpage);
		};

		this.autoForward = function (fw){
			autoForward = fw;
		};
	}
})(window.denpa || (window.denpa = {}));

