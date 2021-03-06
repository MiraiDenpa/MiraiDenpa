<?php
/**
 * 处理预注册逻辑
 *
 * @author ${USER}
 */
class UserRegisterModel extends UserListModel{
	protected $tableName = 'verify';

	public function emailNotUse($email){
		return !$this
				->cache('user.check.r.email' . $email, 60)
				->where(array('email' => $email, 'etime' => ['GT', $_SERVER['REQUEST_TIME']]))
				->count();
	}

	public function uidNotUse($uid){
		return !$this
				->cache('user.check.r.uid' . $uid, 60)
				->where(array('uid' => $uid, 'etime' => ['GT', $_SERVER['REQUEST_TIME']]))
				->count();
	}

	public function register($data){
		if(!isset($data['etime'])){
			$this
					->where(array('etime' => ['LT', $_SERVER['REQUEST_TIME']]))
					->delete();
			$data['etime'] = $_SERVER['REQUEST_TIME'] + 60*60*24*2; // 注册帐号超时时间，超时后会被删除，需要重新注册
		}
		return $this->add($data);
	}

	public function getCode($email){
		$user = $this
				->where(array('email' => $email))
				->find();
		if(!$user){
			$this->errorCode = ERR_NF_USER;
			$this->error     = '试图验证的邮箱并没有提交过请求（没有注册？）。';
			return null;
		}
		if($user['etime'] < $_SERVER['REQUEST_TIME']){
			$this->errorCode = ERR_TIMEOUT;
			$this->error     = '帐号验证超时。';
			$this->delete($user['uid']);
			$this->clear();
			return null;
		}
		if($user['vtime'] > $_SERVER['REQUEST_TIME']){
			$this->errorCode = ERR_OP_TOO_FAST;
			$this->error     = '暂时不能再次发送验证邮件，等待' . date('i分s秒', $user['vtime'] - $_SERVER['REQUEST_TIME']) . '。';
			return null;
		}
		$code                = sha1(rand() . serialize($user));
		$this->data['vcode'] = $code;
		$this->data['vtime'] = $_SERVER['REQUEST_TIME'] + 300; // 验证邮件有效期，同时是2个邮件之间的间隔

		return $code;
	}

	public function saveCode($uid, $code){
		$codeArr = [
			'uid'   => $uid,
			'vcode' => $code,
			'vtime' => $_SERVER['REQUEST_TIME'] + 300,
		];
		if($this->save($codeArr)){
			return true;
		} else{
			$this->errorCode = ERR_SQL;
			$this->error     = '系统错误，请稍后再试';
			return false;
		}
	}

	public function getUserByCode($code){
		$user = $this
				->where(array('vcode' => $code))
				->find();
		if($user && $user['vtime'] < $_SERVER['REQUEST_TIME']){
			$this->errorCode = ERR_TIMEOUT;
			$this->error     = '超时，请重发邮件。';
			return null;
		} elseif($user){
			return $user;
		} else{
			$this->errorCode = ERR_FAIL_VERIFY;
			$this->error     = '';
			return null;
		}
	}
}
