<?php
/**
 * @default_method index
 * @class          AppListAction
 * @author         GongT
 */
class AppListAction extends Action{
	// order
	const OrderPopular = 0;
	const OrderUpdate  = 1;
	const OrderPublish = 2;

	// search_for
	const SearchContent = 0;
	const SearchAuthor  = 1;
	const SearchTag     = 2;

	final public function index($order = self::OrderPopular, $desc = true, $search_for = null, $pattern = null, $p = 0){
		$list = ThinkInstance::D('AppList');
		if(!isset($_GET['_PAGE_'])){
			$_GET['_PAGE_'] = $p;
		}
		$data = $list
				->field('key', true)
				->where($this->parseCondition($search_for, $pattern))
				->order($this->parseOrder($order, $desc))
				->page('_PAGE_')
				->select();
		$page = $list->getPage();
		$param = $this->dispatcher->param;
		$param[4] = '__PAGE__';
		$page->url = U(ACTION_NAME,METHOD_NAME,$param);
		$this->assign('filters',
					  [
					  'order'      => $order,
					  'desc'       => $desc? 'desc' : 'asc',
					  'search_for' => $search_for?$search_for:'null',
					  'pattern'    => $pattern,
					  'page'    => $p,
					  ]
		);

		$this->assign('page', $page->showArray());
		$this->assign('list', $data);
		$this->display('main');
	}

	private function parseCondition($search_for, $pattern){
		$condition = [];
		if($pattern){
			switch($search_for){
			case self::SearchContent:
				$condition['name|description'] = ['LIKE', '%' . $pattern . '%'];
				break;
			case self::SearchAuthor:
				$condition['author|author_nick'] = ['LIKE', '%' . $pattern . '%'];
				break;
			case self::SearchTag:
				$condition['tag'] = ['LIKE', '%' . $pattern . '%'];
			}
		}
		return $condition;
	}

	private function parseOrder($order, &$desc){
		switch($order){
		case self::OrderPublish:
			$ret = 'reg_date';
			break;
		case self::OrderUpdate:
			$ret = 'date';
			break;
		case self::OrderPopular:
		default:
			$ret = 'popular';
			break;
		}
		if($desc == 'desc'){
			$desc = true;
		}elseif($desc == 'asc'){
			$desc = false;
		}elseif($desc == '1'){
			$desc = true;
		}elseif($desc == '0'){
			$desc = false;
		}
		return $ret . ($desc? ' desc' : ' asc');
	}
}
