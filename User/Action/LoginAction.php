<?php
/**
 * User: GongT
 * Create On: 13-8-24 下午3:42
 * 
 */
class LoginAction extends Action{
	final public function act(){
		$this->assign($_POST);
		$this->error(ERR_NF_USER);
	}
	final public function index($pubid = 'MiraiDenpaInfo'){
		$model = ThinkInstance::D('app');
		$app = $model->getData($pubid);
		if(empty($app)){
			return $this->error(ERR_NF_APPLICATION, '/');
		}
		$this->assign('app', $app);
		$this->assign('email', isset($_GET['email'])?$_GET['email']:'');
		return $this->display('base');
	}
}
