(function (window){
	"use strict";
	var container;
	var objlist = {};
	var fields = window.fields;
	var current_data = window.doc;

	var url = /^http:\/\/\S+$/i;

	function dispatcher(id, value){
		if(dispatcher[id]){
			dispatcher[id](value);
		} else{
			var field = fields[id];
			if(!field){
				console.error('Error : 文档包含未定义的字段[' + id + ']');
				return;
			}
			if(!dispatcher['_normal_' + field.type]){
				console.error('Error : 字段[' + id + '](' + field.type + ')不能被正确显示。');
				console.groupCollapsed(id);
				console.log(current_data[id]);
				console.log(dispatcher[id]);
				console.groupEnd();
				return;
			}
			dispatcher['_normal_' + field.type](id, value)
		}
	}

	function nameTip(id){
		if(objlist[id]){
			return objlist[id].$name;
		}
		objlist[id] = {
			tr: $('<tr/>').attr('id', 'field_' + id).appendTo(container)
		};

		return objlist[id].$name = $('<td class="key"/>')
				.text((fields[id].show_name? fields[id].show_name : fields[id].name) + ': ')
				.appendTo(objlist[id].tr);
	}

	function valueTip(id){
		if(objlist[id] && objlist[id].$value){
			return objlist[id].$value;
		}
		return objlist[id].$value = $('<td class="value"/>').insertAfter(objlist[id].$name);
	}

	var urlmap = {
		item   : $.modifyUrl('http://dianbo.me', {
			app   : 'info',
			action: 'View',
			method: 'name',
			suffix: 'html'
		}, true),
		catelog: $.modifyUrl('http://dianbo.me', {
			app   : 'info',
			action: 'List',
			method: '',
			suffix: 'html'
		}, true),
	};
	$.extend(dispatcher, {
		_normal_input  : function (id, value){
			nameTip(id);
			var $a = $('<a target="_blank"/>').text(value).appendTo(valueTip(id));
			if(url.test(value)){
				$a.attr('href', value);
			} else{
				$a.attr('href', urlmap.item.modify({path: [value]}).toString());
			}
		},
		_normal_select : function (id, value){
			nameTip(id);
			var $a = $('<a target="_blank"/>').text(mapSelect(id, value)).appendTo(valueTip(id));
			$a.attr('href', urlmap.catelog.modify({method: id, path: [value]}).toString());
		},
		broadcast_range: function (value){
			nameTip('broadcast_range');
			var txt = '';
			if(value.start){
				txt += date('Y年m月d日', value.start);
			} else{
				txt += '未知'
			}
			txt += '&nbsp;至<br/>';
			if(value.end){
				txt += date('Y年m月d日', value.end);
			} else{
				txt += '未知'
			}
			valueTip('broadcast_range').html(txt);
		},
		classification : function (value){
			nameTip('classification');
			if(value[0] === 'limit'){
				valueTip('classification').html(value[1] + '以上');
			} else{
				valueTip('classification').html('全年龄');
			}
		},
		episodes       : function (value){
			nameTip('episodes');
			valueTip('episodes').text(value + '话');
		},
		externalize    : function (value){
			nameTip('externalize');
			var map = fields['externalize'].subtype.subtype;
			var list = $('<ul/>').appendTo(valueTip('externalize'));
			urlmap.catelog.modify({method: 'externalize'});
			$(value).each(function (_, name){
				var $a = $('<a target="_blank"/>').text(map[name]).appendTo($('<li/>').appendTo(list));
				$a.attr('href', urlmap.catelog.modify({path: [name]}).toString());
			});
		},
	});

	$(function (){
		container = $('#detail_info');
		window.onlogin(init_doc_detail);
		window.onlogout(init_doc_detail);
	});
	var inited = false;

	function init_doc_detail(){
		if(inited){
			return;
		}
		inited = true;
		var dir = [
			'day_of_week',
			'episodes',
			'externalize',
			'classification',

			'come_from',
			'broadcast_range',
			'official_site',

			'originalwork',
			'robot'
		];
		$(dir).each(function (_, id){
			dispatcher(id, current_data[id]);
		});

		sort(dir);
	}

	function mapSelect(id, value){
		if(!fields[id]){
			console.error('Error : mapSelect[' + id + '] 定义不存在');
			return '[' + id + ' #' + value + ']';
		}
		return fields[id].subtype[value];
	}

	function sort(){

	}
})
		(window);
/**
 broadcast_range: Object
 catalog: 1
 classification: Array[2]
 come_from: "aria-11"
 cover_pic: "http://p0.mirai.localdomain/infosite/entrys/6694d9575e2a4fdc83e837540a0be2e9.jpeg"
 day_of_week: 7
 doujin: true
 episodes: 11
 externalize: Array[3]
 name: Array[1]
 official_site: "http://a.com"
 origin_name: "测试"
 robot: true
 */
