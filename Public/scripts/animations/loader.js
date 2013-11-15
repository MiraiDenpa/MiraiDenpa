$(function (){ // 配合 loader.less 使用
	$('.loader.loading-blockfly').each(function (){
		var $this = $(this);
		var anime = $this.data();
		var dir = anime.speed/(anime['blockCount'] + 20);
		var loader = {};
		var blocks = [];
		var tm;

		for(var i = 0; i < anime['blockCount']; i++){
			blocks[i] = $('<div class="block"/>').appendTo(this).css('animation-duration', anime.speed + 'ms');
		}

		loader.show = function (){
			$(blocks).each(function (i, e){
				$(e).css('animation-delay', (i*dir) + 'ms');
			});

			tm = setTimeout(function (){
				$(blocks).each(function (i, e){
					$(e).css('animation-delay', (i*dir) + 'ms');
				});
			}, anime.speed);
			$this.removeClass('hide');
		};
		loader.hide = function (){
			$this.addClass('hide');
			if(tm){
				clearTimeout(tm);
			}
		};

		$this.data('loader', loader);
	});
});
