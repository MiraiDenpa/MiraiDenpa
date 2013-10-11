<?php
/**
 * @default_method index
 * @class          UserLogoutAction
 * @author         GongT
 */
class UserLogoutAction extends Action{
	use UserAuthedAction;

	final public function index(){
		$uol = ThinkInstance::D('UserOnline');
		//$r   = $uol->remove(['_id' => $this->token]);
		//$this->mongo_ret($r,'成功退出');
		$this->success('OK');
	}
}
