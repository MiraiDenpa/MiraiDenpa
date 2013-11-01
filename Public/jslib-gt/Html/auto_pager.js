$.fn.pager = function (page){
	function replace(text, data){
		return text.replace('%totalPage%', data['totalPage']).replace('%totalRows%', data['totalRows']).replace('%nowPage%', data['nowPage']).replace('%rollPage%', rollPage);
	}
	
	var $this = $(this);
	if(!$this.data('_init_gt_pager')){
		var rollPage = 7;
		var current_page_object = page;
		window.user.setting.onchange('rollpage', function (v){
			rollPage = v? v : 7;
			$this.data('page', '1');
			$this.change(current_page_object);
		});
		var nowPage, totalPage;
		page.rollPage = rollPage;
		var $title = $this.find('li:first>a');

		// 产生中间的分页按钮
		var $items = [];

		function number_item_factory(i){
			if($items[i]){
				return $items[i];
			}
			var last = number_item_factory.last;
			var obj = last.clone().insertAfter(last);
			var title = obj.attr('title');
			obj.value = function (newone){
				var href = page.url.replace('__PAGE__', newone);
				this.attr({title: title.replace('%page%', newone), value: newone}).show().data('href', href)
						.find('>a').html(newone).attr('href', href);
			};
			$items[i] = obj;
			number_item_factory.last = obj;
			return obj;
		}

		var item = number_item_factory.last = $this.find('[data-pager=page]').removeAttr('data-pager').hide();

		for(var i = 0; i < rollPage; i++){
			number_item_factory(i);
		}
		item.remove();
		item = null;

		// 不是数字的分页按钮
		$this.find('[data-pager]').each(function (){
			var li = $(this).css({cursor: 'pointer'});
			li.value = function (newone){
				this.attr({value: newone});
				this.find('a:first').attr('href', page.url.replace('__PAGE__', newone));
			};
			Object.defineProperty($this, li.data('pager'), {
				set: function (page){
					// 确定按钮可用性
					if(page <= 0 || page > totalPage){
						//console.log(li.data('pager') + ' -> ' + page + '   [disabled]');
						li.addClass('disabled');
					} else{
						//console.log(li.data('pager') + ' -> ' + page + '   [enabled]');
						li.removeClass('disabled');
					}
					li.value(page);
				},
				get: function (){
					return li;
				}
			});
			li.attr('title', replace(li.attr('title'), page));
		});

		var header = $title.html();
		var last_page = page;

		$this.change = function (page){
			nowPage = page['nowPage'];
			totalPage = page['totalPage' ];
			if(nowPage == $this.data('page') && totalPage == $this.data('totalPage')){
				return false;
			}
			$this.data('page', nowPage);
			$this.data('totalPage', totalPage);
			// 更新列表头部
			$title.html(replace(header, page));
			current_page_object = page;

			// 已知当前页、显示宽度，求出最左侧和右侧的页码
			var width = Math.floor(rollPage/2);
			var left = nowPage - width;
			var right = nowPage + width;
			var flash_self = (left > 1) && (right < totalPage);
			if(right > totalPage){//当前页码接近最后一页
				left = totalPage - rollPage + 1;
				right = totalPage + 1;
			}
			if(left < 1){ // 当前页码接近第一页
				right = rollPage;
				left = 1;
			}
			if(right > totalPage){ // 总页数不足填充显示
				right = totalPage;
			}
			// 同步中间数字，从左侧页码循环到右侧
			for(var i = left, j = 0; i <= right; i++, j++){
				if(!$items[j]){
					number_item_factory(j);
				}
				$items[j].show().value(i);
				if(i > totalPage){
					$items[j].hide();
				} else if(nowPage == i){
					$items[j].show().addClass('active')
				} else if(flash_self && last_page == i){
					var cache = $items[j].show().addClass('active');
					setTimeout(function (){
						//noinspection JSReferencingMutableVariableFromClosure
						cache.removeClass('active');
					}, 0);
				} else{
					$items[j].show().removeClass('active');
				}
			}
			while($items[++j]){
				$items[j].hide();
			}
			// 同步两侧按钮
			this.prev = nowPage - 1;
			this.next = nowPage + 1;
			this.prevN = nowPage == 1? 0 : Math.max(nowPage - rollPage, 1);
			this.nextN = nowPage + rollPage;
			this.first = nowPage == 1? 0 : 1;
			this.last = totalPage;

			if(totalPage < rollPage){
				this.prevN.hide();
				this.nextN.hide();
			} else{
				this.prevN.show();
				this.nextN.show();
			}

			last_page = nowPage;
		};
		$this.data('_init_gt_pager', $this);

		$this.on('click', 'li:not(.disabled,.active)',function (){
			var li = $(this);
			var value = li.attr('value');
			if(!value || value < 1 || value > totalPage || value == nowPage){
				return false;
			}
			li.trigger('page', [value, li.data('href')]);
			return false;
		}).on('click', 'a', function (e){
					e.preventDefault();
				});
	}
	$this.data('_init_gt_pager').change(page);
	return $this;
};
