$(function (){
	var $div = null, $label, $counter, $ln, $message;
	var _timeout = 0;
	var _interval;

	function generate_top(){
		top_box_init();
		$div = $('<div/>');
		var line = $('<h3/>').addClass('alert alert-success').css({'display': 'inline', 'paddingLeft': '40px', 'paddingRight': '40px'}).appendTo($div);
		$counter = $('<span/>').appendTo(line);
		$label = $('<span/>').css({'position': 'inline-block', 'max-width': '240px'}).appendTo(line);
		$ln = $('<a/>').appendTo(line);
		line.append('……');
		$('<i/>').addClass('glyphicon glyphicon-remove').css({'cursor': 'pointer'}).appendTo(line).click(function(){
			top_box_remove_content('jumper');
			_timeout = -1;
		});

		$message = $('<div/>').insertAfter(line);

		top_box_add_content('jumper', $div);
	}

	function countdown(){
		if(_timeout-- > 0){
			$counter.text(_timeout);
			if(!_timeout){
				window.location.href = $ln.attr('href')
			}
		} else{
			if(_interval){
				clearInterval(_interval);
				_interval = null;
			}
		}
	}

	window.location.jumpto = function (url, timeout, name, message){
		if(!$div){
			generate_top();
		}
		if(!name){
			name = url;
		}
		$message.html(message);
		$ln.attr('href', url).text(name);
		$label.text(' 秒后跳转到： ');

		$div.show().transit({'top': '0', 'opacity': 1});

		_timeout = timeout;
		$counter.text(_timeout);
		_interval = setInterval(countdown, 1000);
	};
});

