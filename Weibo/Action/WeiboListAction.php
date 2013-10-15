<?php
class WeiboListAction extends Action{
	use UserAction;
	
	final public function index(){
		var_dump('显示首页');
	}

	final public function square(){
		var_dump('显示广场');
	}

	final public function user($uid){
		var_dump('显示用户的微博列表',$uid);
	}
}
