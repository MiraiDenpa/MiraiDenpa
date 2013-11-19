$(function (){ // 配合 loader.less 使用
	$('.loader.loading-blockfly').each(function (){
		var loader = {};
		var $this = $(this);
		var anime = $this.data();
		var dir = anime.speed/(anime['blockCount'] + 20);
		var blocks = [];
		var tm;

		for(var i = 0; i < anime['blockCount']; i++){
			blocks[i] = $('<div class="block hide"/>').appendTo(this).css('animation-duration', anime.speed + 'ms')[0];
		}
		blocks = $(blocks);

		loader.show = function (){
			blocks.removeClass('hide');
			blocks.each(function (i, e){
				$(e).css('animation-delay', (i*dir) + 'ms');
			});

			tm = setTimeout(function (){
				blocks.each(function (i, e){
					$(e).css('animation-delay', (i*dir) + 'ms');
				});
			}, anime.speed);
		};
		loader.hide = function (){
			blocks.addClass('hide');
			if(tm){
				clearTimeout(tm);
			}
		};

		$this.data('loader', loader);
	});
});
