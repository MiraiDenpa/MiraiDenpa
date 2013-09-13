<?php
class IndexAction extends Action{
	final public function index(){
		$this->assign('login', session('login'));
		$this->display('index');
	}
}
