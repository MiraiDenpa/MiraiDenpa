<?php
/**
 * @default_method index
 * @class          UserLogoutAction
 * @author         GongT
 */
class UserLogoutAction extends Action{
	use UserAction;

	final public function index(){
		$error = $this->doLogin($this->allow_public);
		if(!$error){
			$uol = ThinkInstance::D('UserOnline');
			$r   = $uol->remove(['_id' => $this->token]);
			$this->mongo_ret($r, '成功退出');
		}else{
			$this->success('没有登录');
		}
	}
}
