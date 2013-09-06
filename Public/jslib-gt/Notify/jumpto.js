(function (window){
	var $div = null, $label, $counter, $ln, $message,line;
	var _timeout = 0;
	var _interval;

	function generate_top(){
		var div = $('<div/>');
		line = $('<h3/>').addClass('alert alert-success').css({'display': 'inline', 'paddingLeft': '40px', 'paddingRight': '40px'});
		$counter = $('<span/>').appendTo(line);
		$label = $('<span/>').css({'position': 'inline-block', 'max-width': '240px'}).appendTo(line);
		$ln = $('<a/>').appendTo(line);
		line.append('……');
		$('<small/>').text('取消').addClass('text-muted').css({'cursor': 'pointer'}).appendTo(line).click(function (){
			$div.hide();
			_timeout = -1;
		});
		$message = $('<div/>').insertAfter(line.appendTo(div));

		$div = new Notify('jumper', div);
	}

	var open_in_newwin;

	function countdown(){
		if(_timeout-- > 0){
			$counter.text(_timeout);
			if(!_timeout){
				$counter.html('');
				$label.text('正在跳转…… ');
				if(open_in_newwin){
					window.open($ln.attr('href'));
				} else{
					window.location.href = $ln.attr('href');
				}
			}
		} else{
			if(_interval){
				clearInterval(_interval);
				_interval = null;
			}
		}
	}

	/**
	 *
	 * @param url 跳转到
	 * @param timeout 跳转延时
	 * @param name 连接名称（如“下一页”
	 * @param message 提示信息
	 * @param type 背景颜色(alert-*)
	 * @param newwin 新窗口打开
	 */
	window.location.jumpto = function (url, timeout, name, message, type, newwin){
		if(!newwin){
			newwin = false
		}
		if(!type){
			type="success";
		}
		if(!$div){
			generate_top();
		}
		if(!name){
			name = url;
		}
		$message.html(message);
		$ln.attr('href', url).text(name);

		open_in_newwin = newwin;
		if(newwin){
			$ln.attr('target', '_blank');
		} else{
			$ln.removeAttr('target');
		}
		line.removeClasses('alert-\\*').addClass('alert-'+type);

		$label.text(' 秒后跳转到： ');

		_timeout = timeout;
		$counter.text(_timeout);
		_interval = setInterval(countdown, 1000);

		$div.show();
	};
})(window);

