<?php
class EmptyAction extends Action{
	final public function index(){
		redirect(U('List','index',''));
	}
}
