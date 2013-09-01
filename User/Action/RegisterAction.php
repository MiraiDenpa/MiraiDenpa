<?php
/**
 * User: GongT
 * Create On: 13-8-24 下午3:42
 *
 */
class RegisterAction extends Action{
	final public function index(){
		$this->assign('preset', $_GET);

		return $this->display('base');
	}

	final public function check(){
		$usrlist = ThinkInstance::D('UserCheck');
		if(isset($_GET['email'])){
			$ret = $usrlist->emailNotUse($_GET['email']);
		} elseif(isset($_GET['uname'])){
			$ret = $usrlist->unameNotUse($_GET['uname']);
		} elseif(isset($_GET['uid'])){
			$ret = $usrlist->uidNotUse($_GET['uid']);
		} else{
			$ret = true;
		}
		if($ret){
			$usrlist = ThinkInstance::D('UserRegister');
			if(isset($_GET['email'])){
				$ret = $usrlist->emailNotUse($_GET['email']);
			} elseif(isset($_GET['uname'])){
				$ret = $usrlist->unameNotUse($_GET['uname']);
			} elseif(isset($_GET['uid'])){
				$ret = $usrlist->uidNotUse($_GET['uid']);
			} else{
				$ret = true;
			}
		}
		if($ret){
			echo 'true';
		} else{
			echo 'false';
		}
	}

	final public function act(){
		$post    = ThinkInstance::InStream('Post');
		$usrlist = ThinkInstance::D('UserCheck');
		$data= $post
		->requireAll([
					 'email',
					 'uid',
					 'password',
					 'repassword',
					 'uname',
					 'agree',
					 ])
		->filter('email', FILTER_VALIDATE_EMAIL)
		->filter_callback('email', [$usrlist, 'emailNotUse'])
		->filter_callback('uname', [$usrlist, 'unameNotUse'])
		->filter_callback('uid', [$usrlist, 'uidNotUse'])
		->getAll();
		if($data['password'] !== $data['repassword']){
			return $this->error(ERR_MISS_REPASSWORD);
		}
		
		$register = ThinkInstance::D('UserRegister');
		if( $register->register($data) ){
			return $this->success('注册成功',UI('vmail'));
		}else{
			return $this->error(ERR_FAIL_REGISTER, $register->getDbError());
		}
	}
}
