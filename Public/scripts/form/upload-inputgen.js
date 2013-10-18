$.fn.bui = $bui;

function type_widget(type){
	var ret = 'create_' + type;
	if(!window[ret]){
		throw new Error(ret);
	}
	return window[ret];
}

function create_date(id, sub, value, text){
	var container = $('<div class="row"/>');
	var $from = (new $bui.FormControl()).addClass('col-xs-6').attr('name', id + '[start]').appendTo(container);
	$from.append($bui.Icon('calendar'));

	var dp = $from.centerWidget().datepicker({format: sub});
	if(value){
		dp.datepicker('setValue', 1000*value['start']);
	}
	var $to = (new $bui.FormControl()).addClass('col-xs-6').attr('name', id + '[end]').appendTo(container);
	$to.append($bui.Icon('calendar'));
	dp = $to.centerWidget().datepicker({format: sub});
	if(value){
		dp.datepicker('setValue', 1000*value['end']);
	}
	return container;
}

function create_number(id, sub, value, text){
	var ii = (new $bui.IntInput(sub)).attr('name', id);
	if(value !== undefined){
		ii.val(value);
	}
	ii.centerWidget().attr('id', id);

	return ii;
}

function create_oneof(id, sub, value, text){
	var $ret = new $bui.OneOf();
	for(var i in sub){
		var data = sub[i];
		var fn = type_widget(data.type);
		var $obj = fn(id + '[' + i + ']', data['subtype'], data['value'], data['text']);
		$ret.addItem($obj);
		if(value && value[0] == i){
			$obj.val(value[1]).trigger({
				preventDefault: true,
				type          : 'click'
			});
		}
	}
	return $ret;
}

function create_select(id, sub, value, text){
	var select = new $bui.Select();
	select.attr('name', id);
	for(var title in sub){
		if(sub.hasOwnProperty(title)){
			select.addOption(title, sub[title]);
		}
	}
	select.val(value);
	return select;
}

function create_static(id, sub, value, text){
	var field = new $bui.FormControl();
	var input = $('<input class="disabled"/>').attr({'type': 'hidden', 'name': id, 'title': text});
	field.centerWidget(input);
	field.val(value);
	return field;
}
function create_input(id, sub, value, text){
	if(sub == 'textarea'){
		return $('<textarea class="form-control"/>').attr({ 'name': id, 'title': text}).text(value);
	} else{
		return $('<input class="form-control"/>').attr({'type': sub, 'name': id, 'title': text}).val(value);
	}
}
function create_inputlist(id, sub, value, text){
	var $center = $('<input/>').attr('type', sub);
	var ret = new $bui.InputList();
	ret.addClass('inline').attr('name', id).centerWidget($center);
	if(value){
		ret.val(value);
	}
	return ret;
}
function create_upload(id, sub, value, text){
	var opt = {
		url               : window.Think.URL_MAP['u-infosite-cover-upload'],
		dataType          : 'json',
		autoUpload        : false,
		acceptFileTypes   : /(\.|\/)(gif|jpe?g|png)$/i,
		maxFileSize       : 5000000, // 5 MB
		// Enable image resizing, except for Android and Opera,
		// which actually support image resizing, but fail to
		// send Blob objects via XHR requests:
		disableImageResize: /Android(?!.*Chrome)|Opera/
				.test(window.navigator.userAgent),
		previewMaxWidth   : 100,
		previewMaxHeight  : 100,
		dropZone          : null,
		pasteZone         : null,
		replaceFileInput  : false,
		previewCrop       : true
	};
	var $obj = new $bui.UploadSingle(opt);
	$obj.attr({'name': id, title: text}).val(value);
	$obj.on('fileuploaddone',function (e, data){
		var file = data.result.files[0];
		LogStandardReturnObject(file, '上传文件');
		if(file.error){
			(new SimpleNotify('upload')).autoDestroy(true).error(file.message, '上传失败');
		}
	}).on('fileuploadfail', function (e, data){
				(new SimpleNotify('upload')).autoDestroy(true).error('HTTP错误', '上传失败');
			});
	return $obj;
}
