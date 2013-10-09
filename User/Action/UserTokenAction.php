<?php
/**
 * @default_method index
 * @class          UserLoginAction
 * @author         GongT
 */
class UserTokenAction extends Action{

	final public function addip($token){
		$user = UserLogin($token, false);
		if(!isset($_POST['ip'])){
			return $this->error(ERR_INPUT_REQUIRE, 'ip');
		}
		if(!filter_var($_POST['ip'], FILTER_VALIDATE_IP)){
			return $this->error(ERR_INPUT_TYPE, 'ip');
		}
		if(in_array($_POST['ip'], $user['ip'])){
			$this->assign('exist', true);
			$this->assign('list', $user['ip']);
			$this->success();
		} else{
			$this->assign('exist', false);
			$user['ip'][] = $_POST['ip'];
			$this->assign('list', $user['ip']);
			$uol = ThinkInstance::D('UserOnline');
			$ret = $uol->update(['_id' => $token], ['$push' => ['ip' => $_POST['ip']]]);

			if(!$ret['ok']){
				$this->error(ERR_NO_SQL, $ret['err']);
			} else{
				$this->success();
			}
		}
	}
	
	final public function info(){
		$user = UserLogin($_GET['token'], false);
		unset($user['email']);
		$this->assign('info',$user);
		$this->assign('code',0);
		$this->display('!data');
	}
}
