<?php
/**
 * @default_method index
 * @class          WeiboPostAction
 * @author         GongT
 */
class WeiboPostAction extends Action{
	use UserAuthedAction;
	protected $allow_public = false;

	final public function index(){
		
	}
}
