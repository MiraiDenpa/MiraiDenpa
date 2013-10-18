<?php
/**
 * @default_method index
 * @class          InfoIndexAction
 * @author         GongT
 */
class InfoIndexAction extends Action{
	final public function index(){
		$entry = ThinkInstance::D('InfoList');
		$data  = $entry->getHpTopList();
		xdebug_max_depth(30);
		var_dump($data);
		$this->display('index');
	}
}
