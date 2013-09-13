<?php
class CallbackAction extends Action{
	final public function index(){
		if(!isset($_GET['token']) || !$_GET['token']){
			return $this->error(ERR_INPUT_REQUIRE, 'token');
		}
		$auth  = $_GET['token'];
		$token = md5(INFOSITE_APP_KEY . $auth);

		$uol  = ThinkInstance::D('UserOnline');
		$user = $uol->findOne(['_id' => $token]);

		if(!$user){
			return $this->error(ERR_FAIL_AUTH, 'token');
		}
		
		$user['token'] = $user['_id'];
		unset($user['_id']);
		
		session('login', $user);
		var_dump($_SESSION);exit;
		redirect(U('Index', 'index'));
	}
}
