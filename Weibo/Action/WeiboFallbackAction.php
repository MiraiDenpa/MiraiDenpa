<?php
/**
 * @default_method index
 * @class          WeiboFallbackAction
 * @author         GongT
 */
class WeiboFallbackAction extends Action{
	use UserAction;

	final public function __call($mtd, $arg){
		$index = $this->dispatcher->action_name;
		if(is_numeric($index)){ // 特定微博
			
		}
		var_dump($this->dispatcher->action_name, $mtd, $arg);
	}
}
