<?php
class IndexAction extends Action {
    public function index(){
		$mdl = ThinkInstance::D('UserList');
		$data = $mdl->select();
		
		$this->assign('data', $data);
		$this->display();
	}
}
