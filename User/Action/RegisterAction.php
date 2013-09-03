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
		$get      = ThinkInstance::InStream('Get');
		$usrlist  = ThinkInstance::D('UserCheck');
		$register = ThinkInstance::D('UserRegister');
		$get
		->handel(function (){
					exit('false');
				})
		->optionalAll([
					  'email' => '',
					  'uid'   => '',
					  'uname' => '',
					  ])
		->filter('email', FILTER_VALIDATE_EMAIL)
		->filter_callback('email', [$usrlist, 'emailNotUse'])
		->filter_callback('uname', [$usrlist, 'unameNotUse'])
		->filter_callback('uid', [$usrlist, 'uidNotUse'])
		->filter_callback('email', [$register, 'emailNotUse'])
		->filter_callback('uname', [$register, 'unameNotUse'])
		->filter_callback('uid', [$register, 'uidNotUse']);
		exit('true');
	}

	final public function act(){
		$post     = ThinkInstance::InStream('Post');
		$usrlist  = ThinkInstance::D('UserCheck');
		$register = ThinkInstance::D('UserRegister');
		$data     = $post
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
					->filter_callback('email', [$register, 'emailNotUse'])
					->filter_callback('uname', [$register, 'unameNotUse'])
					->filter_callback('uid', [$register, 'uidNotUse'])
					->getAll();
		if($data['password'] !== $data['repassword']){
			return $this->error(ERR_MISS_REPASSWORD);
		}

		if($register->register($data)){
			return $this->success('注册成功', UI('vmail', ['email' => $data['email']]));
		} else{
			return $this->error(ERR_FAIL_REGISTER, $register->getDbError());
		}
	}

	final public function vmail(){
		if(isset($_GET['email'])){
			cookie('vmail', $_GET['email'], 1800);
		}
		$email = isset($_GET['email'])?$_GET['email']:$_COOKIE['vmail'];
		
		$this->assign('email', $email);
		
		$this->display('mail');
	}
	
	final public function vmail_send(){
		$register = ThinkInstance::D('UserRegister');
		$post = ThinkInstance::InStream('Post');
		$email = $post->required('email')->filter('email', FILTER_VALIDATE_EMAIL)->get('email');
		
		$ret = $register->getCode($email);
		if($ret){
			
		}else{
			$this->success('aa', '/');
			exit;
			$this->error($register->getErrorCode(),$register->getError(), '/');
			exit;
		}
		$this->success('请登录邮箱，点击确认连接。（或者复制认证码到下方文本框）');
	}
}
