<?php
/**
 * @default_method index
 * @class          UserForgetAction
 * @author         GongT
 */
class UserForgetAction extends Action{
	final public function index(){
		/*if(isset($_GET['token'])){
			$user = UserLogin($_GET['token'], true);
			if($user['type'] != SpecialUser::TYPE_PUBLIC){
				$this->assign('uid', $user['user']);
				$this->assign('email', $user['email']);
				return $this->display('login');
			}
		}*/
		if(isset($_GET['email'])){
			$this->assign('email', $_GET['email']);
		} else{
			$this->assign('email', '');
		}
		$this->display('public');
	}

	final public function fmail_send(){
		$post    = ThinkInstance::InStream('Post');
		$email   = $post
				->required('email')
				->filter('email', FILTER_VALIDATE_EMAIL)
				->get('email');
		$checker = function ($uid) use ($email){
			// 检查用户信息是否匹配，不匹配则不发送邮件
			$user = ThinkInstance::D('UserList')
					->where($uid)
					->find();
			if(!$user){
				return false;
			}
			if($user['email'] !== $email){
				sleep(5);
				$this->success('请登录邮箱，点击确认连接。', 'http://' . mail_to_provider($user['email']));
				exit;
			}
			return true;
		};
		$uid     = $post
				->required('uid')
				->filter_callback('uid', $checker, ERR_NF_USER)
				->get('uid');

		$register = ThinkInstance::D('UserRegister');
		if($register->emailNotUse($email)){
			$user = array(
				'uid'   => $uid,
				'email' => $email,
				'etime' => time()+31104000,
			);
			$ret  = $register->register($user);
			if(!$ret){
				$this->modelError($register);
			}
		}

		$code = $register->getCode($email);
		if(!$code){
			$this->modelError($register);
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

		$mail->Subject = '电波 - 密码找回验证邮件';
		$mail->AltBody = '验证码： ' . $code;

		$view = ThinkInstance::View();
		$view->assign($user);
		$mail->Body = $view->fetch(':mail_template/forget');

		$success = $mail->Send();
		if($success){
			$register->saveCode($user['uid'], $user['vcode']);
			$this->success('请登录邮箱，点击确认连接。', 'http://' . mail_to_provider($user['email']));
		} else{
			$this->error(ERR_FAIL_SEND_MAIL, $mail->ErrorInfo);
		}
	}

	final public function fmail_check(){
		if(empty($_GET['code'])){
			$this->error(ERR_INPUT_REQUIRE, 'code');
		}
		$register = ThinkInstance::D('UserRegister');
		$user     = $register->getUserByCode($_GET['code']);

		if(!$user){
			return $this->modelError($register, ['重发邮件', UI('fmail_send', [], false)]);
		}
		$usrlist = ThinkInstance::D('UserLogin');

		$uid  = $user['uid'];
		$user['passwd'] = (string)(time() . rand());
		$succ = $usrlist
				->where($uid)
				->data(['passwd' => $user['passwd'] ])
				->save();
		if($succ){
			$register->delete();

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

			$mail->Subject = '电波 - 密码重置';
			$mail->AltBody = '新密码： ' . $code;

			$view = ThinkInstance::View();
			$view->assign($user);
			$mail->Body = $view->fetch(':mail_template/getpassword');
			
			$success = $mail->Send();
			if($success){
				$this->success('密码被重置，请查收邮件！', U('login', 'index'));
			} else{
				$this->error(ERR_FAIL_SEND_MAIL, $mail->ErrorInfo);
			}
		} else{
			$this->error(ERR_SQL, '请联系管理员解决。');
		}
	}
}
