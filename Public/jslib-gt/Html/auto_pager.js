$.fn.pager = function (page){
	var $this = $(this);
	if(!$this.data('_init_gt_pager')){
		var rollPage = window.Settings.get('rollPage');
		if(!rollPage){
			rollPage = 7;
		}
		page.rollPage = rollPage;
		$this.info = $this.find('li:first>a');

		// 产生中间的分页按钮
		var item = $this.find('[data-pager=page]').removeAttr('data-pager').hide();
		$this.items = [];
		for(var i = 0; i < rollPage; i++){
			$this.items.unshift(item.clone().insertAfter(item));
			$this.items[0].value = function (newone){
				this.attr({title: this.attr('title').replace('%page%', newone), value: newone}).show().find('>a').html(newone);
				this.find('a:first').attr('href',page.url.replace('__PAGE__',newone));
			}
		}
		item.remove();
		item = null;

		// 不是数字的分页按钮
		$this.find('[data-pager]').each(function (){
			var li = $(this).css({cursor: 'pointer'});
			var role = li.data('pager');
			li.value = function (newone){
				this.attr({value: newone});
				this.find('a:first').attr('href',page.url.replace('__PAGE__',newone));
			};
			$this.__defineSetter__(role, function (page){
				console.log(page);
				if(page<=0){
					li.removeClass('disabled');
				} else{
					li.addClass('disabled');
				}
				li.value(page);
			});
			li.attr('title', replace(li.attr('title'), page));
		});

		var header = $this.info.html();

		$this.change = function (page){
			var nowPage = page.nowPage;
			if(nowPage == $this.data('page') && page.totalPage == $this.data('totalPage')){
				return false;
			}
			$this.data('page', nowPage);
			$this.data('totalPage', page.totalPage);
			// 更新列表头部
			this.info.html(replace(header, page));
			
			var width = Math.floor(rollPage/2);
			var left = nowPage - width;
			var right = nowPage + width;
			if(right > page.totalPage){
				left = page.totalPage - rollPage + 1;
				right = page.totalPage + 1;
			}
			if(left < 1){
				right = rollPage;
				left = 1;
			}
			if(right > page.totalPage){
				right = page.totalPage;
			}
			// 同步中间数字
			for(var i = left, j = 0; i <= right; i++, j++){
				this.items[j].show().value(i);
				if(i>page.totalPage){
					this.items[j].hide();
				}else if(nowPage == i){
					this.items[j].show().addClass('active')
				}else{
					this.items[j].show().removeClass('active');
				}
			}
			while(this.items[++j]){
				this.items[j].hide();
			}
			// 同步两侧按钮
			this.prev = nowPage - 1;
			this.next = (nowPage + 1 <= page.totalPage)? nowPage + 1 : 0;
			this.prevN = nowPage - rollPage;
			this.nextN = (nowPage + rollPage < page.totalPage)? nowPage + rollPage : 0;
			this.first = nowPage == 1? 0 : 1;
			this.last = nowPage == page.totalPage? 0 : page.totalPage;
		};
		$this.data('_init_gt_pager', $this);

		function replace(text, data){
			return text.replace('%totalPage%', data.totalPage).replace('%totalRows%', data.totalRows).replace('%nowPage%', data.nowPage).replace('%rollPage%', rollPage);
		}

		$this.on('click', 'li:not(.disabled,.active)', function (){
			var value = $(this).attr('value');
			if(value && value == 0){
				return false;
			}
			$this.trigger('page', value);
			return false;
		});
	}
	$this.data('_init_gt_pager').change(page);
	return $this;
}
/*
 li.active -> 当前页
 li -> 可用
 li.disabled -> 禁用

 */
