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

	$from.centerWidget().datepicker({format: sub}).datepicker('setValue', 1000* value['start']);
	var $to = (new $bui.FormControl()).addClass('col-xs-6').attr('name', id + '[end]').appendTo(container);
	$to.append($bui.Icon('calendar'));
	console.log(value['end']);
	$to.centerWidget().datepicker({format: sub}).datepicker('setValue', 1000*value['end']);
	return container;
}

function create_number(id, sub, value, text){
	var ii = (new $bui.IntInput(sub)).attr('name', id).val(value);
	ii.centerWidget().attr('id', id);

	return ii;
}

function create_oneof(id, sub, value, text){
	var $ret = new $bui.OneOf();
	for(var i in sub){
		var data = sub[i];
		var fn = type_widget(data.type);
		var $obj = fn(i, data['subtype'], data['value'], data['text']);
		$ret.addItem($obj);
	}
	console.log(id, sub, value, text);
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
	return select;
}

function create_static(id, sub, value, text){
	var field = new $bui.FormControl();
	var input = $('<input class="disabled"/>').attr({'type': 'hidden', 'name': id, 'title': text}).val(sub);
	field.centerWidget(input);
	return field;
}
function create_input(id, sub, value, text){
	return $('<input class="form-control"/>').attr({'type': sub, 'name': id, 'title': text}).val(value);
}
function create_inputlist(id, sub, value, text){
	var $center = $('<input/>').attr('type', sub);
	var ret = new $bui.InputList();
	ret.addClass('inline').attr('name', id).centerWidget($center);
	ret.val(value);
	return ret;
}

var delete_btn = $('<a/>').append($('<i class="glyphicon glyphicon-trash"/>'));
function form_item(title, obj){
	var td = $('<td class="text-right field-title col-xs-3"/>')
			.append($('<label class="control-label value"/>')
					.attr({for: obj.find('input').attr('id')})
					.css({"paddingRight": 20})
					.text(title));
	return $('<tr class="row"/>')
			.append(td)
			.append($('<td class="text-left field-input col-xs-8"/>').append(obj))
			.append($('<td class="text-left field-delete col-xs-1"/>').append(delete_btn.clone()));
}
