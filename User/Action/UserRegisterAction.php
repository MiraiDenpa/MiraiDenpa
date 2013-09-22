<?php
/**
 * @default_method index
 * @class          UserRegisterAction
 * @author         GongT
 */
class UserRegisterAction extends Action{
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
					}
				)
				->optionalAll([
							  'email' => '',
							  'uid'   => '',
							  ]
				)
				->filter('email', FILTER_VALIDATE_EMAIL)
				->valid('uid', 'length', [3,12], ERR_RANGE_UID)
				->filter_callback('email', [$usrlist, 'emailNotUse'])
				->filter_callback('uid', [$usrlist, 'uidNotUse'])
				->filter_callback('email', [$register, 'emailNotUse'])
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
							 'passwd',
							 'repasswd',
							 'agree',
							 ]
				)
				->filter('email', FILTER_VALIDATE_EMAIL)
				->valid('passwd', 'length', [6,0], ERR_RANGE_PASSWORD)
				->valid('uid', 'length', [3,12], ERR_RANGE_UID)
				->valid('passwd', 'is_same', 'repasswd', ERR_MISS_REPASSWORD)
				->filter_callback('email', [$usrlist, 'emailNotUse'])
				->filter_callback('uid', [$usrlist, 'uidNotUse'])
				->filter_callback('email', [$register, 'emailNotUse'])
				->filter_callback('uid', [$register, 'uidNotUse'])
				->getAll();

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
		$email = isset($_GET['email'])? $_GET['email'] : $_COOKIE['vmail'];

		$this->assign('email', $email);

		$this->display('mail');
	}

	final public function vmail_send(){
		$register = ThinkInstance::D('UserRegister');
		$post     = ThinkInstance::InStream('Post');
		$email    = $post
				->required('email')
				->filter('email', FILTER_VALIDATE_EMAIL)
				->get('email');

		$code = $register->getCode($email);
		if(!$code){
			return $this->error($register->getErrorCode(), $register->getError());
		}
		$user = $register->data();

		$mail = new \org\net\PHPMailer;
		$mail->IsHTML();
		$mail->IsSMTP();
		$mail->AddAddress($user['email'], $user['uname']);
		$mail->SMTPAuth = true;

		$mail->Username    = MAIL_USER;
		$mail->Password    = MAIL_PASSWORD;
		$mail->From        = MAIL_FROM;
		$mail->FromName    = MAIL_FROM_NAME;
		$mail->Host        = MAIL_HOST;
		$mail->SMTPSecure  = MAIL_HOST_SECURE;
		$mail->CharSet     = 'UTF-8';
		$mail->ContentType = 'text/html';

		$mail->Subject = '来自电波网的验证邮件';
		$mail->AltBody = '验证码： ' . $code;

		$view = ThinkInstance::View();
		$view->assign($user);
		$mail->Body = $view->fetch(':mail_template/verify');

		$success = $mail->Send();
		if($success){
			$register->saveCode($user['uid'], $user['vcode']);
			$this->success('请登录邮箱，点击确认连接。', 'http://' . mail_to_provider($user['email']));
		} else{
			$this->error(ERR_FAIL_SEND_MAIL, $mail->ErrorInfo);
		}
	}

	final public function vmail_check(){
		if(empty($_GET['vcode'])){
			$this->error(ERR_INPUT_REQUIRE, 'vcode');
		}
		$register = ThinkInstance::D('UserRegister');
		$user     = $register->getUserByCode($_GET['vcode']);

		if(!$user){
			return $this->error($register->getErrorCode(),
								$register->getError(),
								['重发邮件', UI('vmail_send', [], false)]
			);
		}
		$usrlist = ThinkInstance::D('UserLogin');

		$succ = $usrlist->add($user);
		if($succ){
			$register->delete();
			$this->success('帐号可以使用！', U('login', 'index'));
		} else{
			$this->error(ERR_SQL, '请联系管理员解决。');
		}
		return null;
	}
}
