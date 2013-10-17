<?php
/**
 * @default_method index
 * @class WeiboEmptyAction
 * @author GongT
 */
class WeiboEmptyAction extends Action{
	use UserAction;

	final public function index(){
		//$error = $this->doLogin();
		xdebug_var_dump('跳转到微博系统');
	}
}
