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
		container.prepend($('<pre/>').text(JSON3.stringify(vote_catelog, null, 4)));
		console.log(current_data);
		$(vote_catelog).each(function (_, catelog){
			if(catelog.when){
				var ret = new MongoLike(catelog.when).test(current_data);
				console.log(catelog.when, ret);
				if(!ret){
					//delete(vote_catelog[_]);
				}
			}
		});
	}

	setTimeout(instance_vote, 0);
});
