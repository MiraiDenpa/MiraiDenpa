<?php
/**
 * @default_method index
 * @class          UserPasswordAction
 * @author         GongT
 */
class PasswordAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;

	final public function index(){
		$user = $this->user;
		if(!$user['pm_account'][PERM_UPDATE]){
			return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
		}
		$this->assign('app_url', $this->getApp()->mainurl);
		return $this->display('index');
	}

	final public function update(){
		$user = $this->user;
		if(!$user['pm_account'][PERM_UPDATE]){
			return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
		}
		$user      = $this
				->getUser()
				->decrypt();
		$post      = ThinkInstance::InStream('Post');
		$newpasswd = $post
				->requireAll(['oldpasswd', 'email', 'passwd', 'repasswd'])
				->valid('passwd', 'length', [6, 0], ERR_RANGE_PASSWORD)
				->valid('passwd', 'is_same', 'repasswd', ERR_MISS_REPASSWORD)
				->valid('oldpasswd', 'equal', $user->passwd, ERR_MISS_PASSWORD)
				->valid('email', 'equal', $user->email, ERR_MISS_EMAIL)
				->get('passwd');

		$userMdl = ThinkInstance::D('UserLogin');
		$ret     = $userMdl
				->where($user->uid)
				->data(['passwd' => $newpasswd])
				->save();
		if($ret){
			$this->success('修改成功！请检查当前登录状况。', UG('user', 'login', 'index', '', ['email' => $user->email]));
		} else{
			$this->modelError($userMdl);
		}
	}
}
