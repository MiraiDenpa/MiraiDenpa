$(function (){
	"use strict";
	var current_data = window.doc;
	var container = $('#WeiboContainer');

	// 评分
	var maininfo = $('#mainInfo');
	var vote_instanced = false;
	$('.vote_small').click(function (){
		if(!vote_instanced){
			instance_vote();
		}
		maininfo.toggleClass('vote_show');
	});

	container.register_middle_hack('.display_full', function (e){
		console.log(this);
		var id = $(this).data('id');
		if(!id){
			return;
		}
		if(e.which === 2){
			window.open('http://' + window.Think.URL_MAP['weibo'] + '/' + id);
		} else{

		}
	});

	function instance_vote(){
		"use strict";
		vote_instanced = true;
		if(!current_data || !current_data._id){
			return;
		}
		var container = $('.vote_large');
		$(vote_catelog).each(function (_, catelog){
			if(catelog.when){
				var ret = new MongoLike(catelog.when).test(current_data);
				// console.log(catelog.when, ret);
				if(!ret){
					delete(vote_catelog[_]);
					return;
				}
			}

			var bar;
			switch(catelog.type){
			case 0:
				bar = new window.components.ValueBar('success');
				// 中央文字 - 评分名字
				covertext(catelog.name, 'center').addClass('revert').appendTo(bar);
				// 好评差评
				covertext(catelog.values[0], 'left').appendTo(bar);
				covertext(catelog.values[1], 'right').appendTo(bar);
				break;
			case 1:
				bar = new window.components.CenterBar('warning', 'success');
				// 中央文字 - 评分名字
				covertext(catelog.name, 'center').addClass('revert').appendTo(bar);
				// 好评
				covertext(catelog.values[0], 'left').appendTo(bar);
				// 中评
				covertext(catelog.values[1], 'center').appendTo(bar);
				// 差评
				covertext(catelog.values[2], 'right').appendTo(bar);
				break;
			case 2:
				bar = new window.components.TwoSideBar('danger', 'success');
				// 中央文字 - 评分名字
				covertext(catelog.name, 'center').addClass('revert').appendTo(bar);
				// 好评
				covertext(catelog.values[0], 'left').appendTo(bar);
				// 中评
				covertext(catelog.values[1], 'center').appendTo(bar);
				// 差评
				covertext(catelog.values[2], 'right').appendTo(bar);
				break;
			default :
				throw new Error('未知类型：' + catelog.type);
			}

			bar.addClass('hovershow trans-opacity');

			bar.offset = catelog.offset;
			bar.appendTo($('<div class="voteitem col-md-6"/>').appendTo(container));

			window.bars.push(bar);
		});
	}

	function covertext(text, textdir){
		return $('<span class="full_cover"/>').addClass('text-' + textdir + ' hovershow-item').text(text);
	}

	var dg = new window.components.DragPointer(80);

	window.test = function (){
		for(var i in bars){
			bars[i].left = parseInt(Math.random()*100);
			bars[i].right = parseInt(Math.random()*100);
			bars[i].value = parseInt(Math.random()*100);
		}
	};

	window.bars = [];
	setTimeout(instance_vote, 0);
});
