<?php
/**
 * @default_method index
 * @class WeiboEmptyAction
 * @author GongT
 */
class WeiboSearchAction extends Action{

	final public function index(){
		$keywords = explode(' ',$_GET['kw']);
		xdebug_var_dump('显示搜索结果',$keywords);
	}
}
