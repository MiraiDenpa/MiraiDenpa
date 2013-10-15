$(function (){
	var defaultCss = {transform: 'perspective(600px) rotateX(90deg)'};
	var autoInterval;
	var register_ok = false;

	function getInterval(){
		if(!register_ok){
			register();
		}
		return autoInterval;
	}

	function register(){
		autoInterval = 4;
		if(window.user && window.user.setting){
			window.user.setting.onchange('slide_interval', function (v){
				autoInterval = v? v : 4;
			});
			register_ok = true;
		}
	}

	register();

	$('#CarouselTopic').each(function (i, e){
		var $this = $(e);
		var bindArray = [], pictures = [];
		var visable_item = null;

		(function ($this){
			var i = 0;

			init();
			var first_active = $this.find('.item.active').first();
			if(first_active.length){
				i = first_active.data('index');
			}
			bringUp(i);
			first_active = null;
		})($this);

		$this.on({
			'mouseenter': function (){
				var i = $(this).data('index');
				if(i === undefined){
					init();
				}
				i = $(this).data('index');
				bringUp(i);
			}
		}, '.control .item');

		// 自动滚动
		$this.on({
			'mouseenter': function (){
				auto_slide.lock = true;
				clearTimeout(auto_slide.timer);
				auto_slide.timer = null;
			},
			'mouseleave': function (){
				auto_slide.lock = false;
				if(!auto_slide.timer){
					auto_slide.timer = setTimeout(auto_slide, getInterval()*1000);
				}
			}
		});
		function auto_slide(){
			if(auto_slide.lock){
				return;
			}
			if(document.hasFocus()){
				var next = visable_item + 1;
				if(next < pictures.length){
					bringUp(next);
				} else{
					bringUp(0);
				}
			}
			auto_slide.timer = setTimeout(auto_slide, getInterval()*1000);
		}

		auto_slide.timer = setTimeout(auto_slide, getInterval()*1000);

		var trans_state = 'normal', transforming_target, auto_show_item;

		function bringUp(item_index){
			if(item_index === visable_item){
				return;
			}

			// 切换所有切换区
			$(bindArray).each(function (i, arr){
				try{
					if(visable_item !== null){
						arr[visable_item].removeClass('active');
					}
					arr[item_index].addClass('active');
				} catch(e){
				}
			});
			adjust_side(item_index);
			adjust_bottom(item_index);

			// 切换图片
			if(visable_item === null){
				// 第一次
				pictures[item_index].show().css('transform', 'perspective(600px) rotateX(0deg)');
				visable_item = item_index;
			} else{
				switch(trans_state){
				case 'showing':
					if(auto_show_item){
						return;
					}
					transforming_target = undefined;
					if(visable_item == item_index){
						console.error('图片轮播试图切换到正在显示的序号。');
					}
					auto_show_item = item_index;
					break;
				case 'normal':
					pictures[visable_item].removeClass('active').clearQueue().transit({'perspective': '600px', rotateX: '-90deg'}, '300ms', 'in', function (){
						$(this).css(defaultCss);
						if(transforming_target !== undefined){
							var will_viszble = transforming_target;
							trans_state = 'showing';
							pictures[transforming_target].show().addClass('active').clearQueue().transit({'perspective': '600px', rotateX: '0deg'}, '300ms', 'out', function (){
								trans_state = 'normal';
								visable_item = will_viszble;
							});
						} else if(auto_show_item !== undefined){
							trans_state = 'normal';
							var t = auto_show_item;
							auto_show_item = undefined;
							bringUp(t);
						} else{
							console.error('图片轮播试：这不可能！');
						}
					});
					trans_state = 'hiding';
					transforming_target = item_index;
					break;
				case 'hiding':
					// 正在隐藏当前项目，替换掉目标
					transforming_target = item_index;
					break;
				default :
					throw new Error('no this state.');
				}
			}
		}

		function init(){
			$(['inner', 'side', 'bottom']).each(function (i, select){
				var $obj = $this.find('.carousel-' + select + ' .item');
				bindArray[i] = [];
				$obj.each(function (index, e){
					bindArray[i][index] = $(e).data('index', index);
				});
			});
			pictures = bindArray.shift();
			$(pictures).each(function (i, e){
				e.css(defaultCss);
			});
		}

		var side_container = false, side_slider, sideitem_height;
		var offset_top = 0;

		function adjust_side(show){
			var $obj = bindArray[0][show];
			if(!side_container){
				side_slider = $obj.parent();
				side_container = side_slider.parent();
				sideitem_height = $obj.height();
			}
			if(side_container.css('display') == 'none'){
				return;
			}
			var top = sideitem_height*show;
			var bottom = sideitem_height + top;
			var cHeight = side_container.height();
			if(bottom + offset_top > cHeight){
				/*console.log('当前项目底端' + (bottom + offset_top) + '超出容器下边缘(' + (cHeight) + ')，调整为: ' +
				 ( offset_top + cHeight - bottom));*/
				offset_top = cHeight - bottom;
				side_slider.css('top', offset_top);
			}
			if(-offset_top > top){
				/*console.log('当前项目顶端' + (top) + '超出容器上边缘(' + (-offset_top) + ')，调整为: ' + -top);*/
				offset_top = -top;
				side_slider.css('top', offset_top);
			}
		}

		var bottom_container = false, bottom_slider, bottomitem_width;
		var offset_left = 0;

		function adjust_bottom(show){
			var $obj = bindArray[1][show];
			if(!bottom_container){
				bottom_slider = $obj.parent();
				bottom_container = bottom_slider.parent();
				bottomitem_width = $obj.width();
			}
			if(bottom_container.css('display') == 'none'){
				return;
			}
			var left = bottomitem_width*show;
			var right = bottomitem_width + left;
			var cWidth = bottom_container.width();
			if(right + offset_left > cWidth){
				//console.log('当前项目底端' + (right + offset_left) + '超出容器下边缘(' + (cWidth) + ')，调整为: ' +
				//            ( offset_left + cWidth - right));
				offset_left = cWidth - right;
				bottom_slider.css('left', offset_left);
			}
			if(-offset_left > left){
				//console.log('当前项目顶端' + (left) + '超出容器上边缘(' + (-offset_left) + ')，调整为: ' + -left);
				offset_left = -left;
				bottom_slider.css('left', offset_left);
			}
		}
	});
});
