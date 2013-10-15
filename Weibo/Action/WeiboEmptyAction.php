<?php
/**
 * @default_method index
 * @class WeiboEmptyAction
 * @author GongT
 */
class WeiboEmptyAction extends Action{
	use UserAction;

	final public function index(){
		$error = $this->doLogin();
		
	}
}
