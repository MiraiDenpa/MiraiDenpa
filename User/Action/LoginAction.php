<?php
/**
 * User: GongT
 * Create On: 13-8-24 下午3:42
 * 
 */
class LoginAction extends Action{
	public function __call($pubid,$args){
		$this->display('base');
	}
}
