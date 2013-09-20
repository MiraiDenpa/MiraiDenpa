$.fn.pager = function (page){
	var $this = $(this);
	if(!$this.data('_init_gt_pager')){
		var rollPage = window.Settings.get('rollPage');
		var nowPage, totalPage;
		if(!rollPage){
			rollPage = 7;
		}
		page.rollPage = rollPage;
		var $title = $this.find('li:first>a');

		// 产生中间的分页按钮
		var item = $this.find('[data-pager=page]').removeAttr('data-pager').hide();
		var $items = [];
		for(var i = 0; i < rollPage; i++){
			$items.unshift(item.clone().insertAfter(item));
			$items[0].value = function (newone){
				this.attr({title: this.attr('title').replace('%page%', newone), value: newone}).show().find('>a').html(newone);
				this.find('a:first').attr('href', page.url.replace('__PAGE__', newone));
			}
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
			$this.__defineSetter__(li.data('pager'), function (page){
				// 确定按钮可用性
				if(page <= 0 || page > totalPage){
					console.log(li.data('pager') + ' -> ' + page + '   [disabled]');
					li.addClass('disabled');
				} else{
					console.log(li.data('pager') + ' -> ' + page + '   [enabled]');
					li.removeClass('disabled');
				}
				li.value(page);
			});
			li.attr('title', replace(li.attr('title'), page));
		});

		var header = $title.html();

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

			var width = Math.floor(rollPage/2);
			var left = nowPage - width;
			var right = nowPage + width;
			if(right > totalPage){
				left = totalPage - rollPage + 1;
				right = totalPage + 1;
			}
			if(left < 1){
				right = rollPage;
				left = 1;
			}
			if(right > totalPage){
				right = totalPage;
			}
			// 同步中间数字
			for(var i = left, j = 0; i <= right; i++, j++){
				$items[j].show().value(i);
				if(i > totalPage){
					$items[j].hide();
				} else if(nowPage == i){
					$items[j].show().addClass('active')
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
			this.nextN = Math.min(nowPage + rollPage, totalPage);
			this.first = 1;
			this.last = totalPage;
		};
		$this.data('_init_gt_pager', $this);

		function replace(text, data){
			return text.replace('%totalPage%', data['totalPage']).replace('%totalRows%', data['totalRows']).replace('%nowPage%', data['nowPage']).replace('%rollPage%', rollPage);
		}

		$this.on('click', 'li:not(.disabled,.active)', function (){
			var value = $(this).attr('value');
			if(!value || value < 1 || value > totalPage || value == nowPage){
				return false;
			}
			$this.trigger('page', value);
			return false;
		}).on('click','a',function(e){
					e.preventDefault();
				});
	}
	$this.data('_init_gt_pager').change(page);
	return $this;
};
