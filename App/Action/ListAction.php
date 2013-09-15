<?php
/**
 * @default_method index
 * @class AppListAction
 * @author GongT
 */
class ListAction extends Action{
	const OrderPopular = 0;
	const OrderUpdate  = 1;
	const OrderPublish = 2;

	const SearchContent = 0;
	const SearchAuthor  = 1;
	const SearchTag     = 2;

	final public function index($search_for = null, $pattern = null, $order = self::OrderPopular, $desc = true){
		$list = ThinkInstance::D('AppList');

		$data = $list
				->field('key', true)
				->where($this->parseCondition($search_for, $pattern))
				->order($this->parseOrder($order, $desc))
				->page()
				->select();
		$page = $list->getPage();

		$this->assign('page', $page);
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

	private function parseOrder($order, $desc){
		switch($order){
		case self::OrderPublish:
			$ret= 'reg_date';
			break;
		case self::OrderUpdate:
			$ret= 'date';
			break;
		case self::OrderPopular:
		default:
			$ret= 'popular';
			break;
		}
		return $ret.($desc?' desc':' asc');
	}
}
