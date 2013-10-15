<?php
/**
 * @default_method index
 * @class          WeiboDetailAction
 * @author         GongT
 */
class WeiboDetailAction extends Action{
	use UserAction;

	final public function show($id){
		xdebug_var_dump('显示微博 ',$id);
	}
}
