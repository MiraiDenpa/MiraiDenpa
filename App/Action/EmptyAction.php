<?php
/**
 * @default_method index
 * @class AppEmptyAction
 * @author GongT
 */
class EmptyAction extends Action{
	final public function index(){
		redirect(U('List','index',''));
	}
}
