<?php
/**
 * User: GongT
 * Create On: 13-8-24 下午3:42
 *
 */
class LoginAction extends Action{
	final public function index($pubid = 'MiraiDenpaInfo'){
		if(REQUEST_METHOD == 'POST'){
			return $this->act();
		}
		$model = ThinkInstance::D('app');
		$app   = $model->getData($pubid);
		if(empty($app)){
			return $this->error(ERR_NF_APPLICATION, '/');
		}
		$this->assign('app', $app);
		$this->assign('email', isset($_GET['email'])? $_GET['email'] : '');
		return $this->display('base');
	}

	public function act(){
		$post    = ThinkInstance::InStream('Post');
		$usrlist = ThinkInstance::D('UserLogin');
		
		/** @var UserEntity $user */
		$user    = null;
		$data    = $post
				   ->optional('remember', 'off')
				   ->filter('remember', FILTER_VALIDATE_BOOLEAN)
				   ->requireAll(['email','passwd'])
				   ->filter_callback('email', function ($email) use (&$user, &$usrlist){
					$user = $usrlist
							->where(['email|uid' => $email])
							->getUser();
					return true;
				})->getAll();
		if(!$user){
			return $this->modelError($usrlist);
		}
		
		$compare = UserEntity::decrypt($user->passwd);

		if($compare !== $data['passwd']){
			return $this->error(ERR_MISS_PASSWORD);
		}
		
		$this->assign($user);
		$this->error(ERR_NF_USER);
	}
}
