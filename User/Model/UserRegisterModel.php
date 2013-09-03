<?php
class UserRegisterModel extends Model{
	protected $tableName = 'verify';
	protected $connection = 'user';

	public function emailNotUse($email){
		return !$this
				->cache('user.check.r.email' . $email, 60)
				->where(array('email' => $email, 'etime' => ['GT', $_SERVER['REQUEST_TIME']]))
				->count();
	}

	public function unameNotUse($uname){
		return !$this
				->cache('user.check.r.uname' . $uname, 60)
				->where(array('uname' => $uname, 'etime' => ['GT', $_SERVER['REQUEST_TIME']]))
				->count();
	}

	public function uidNotUse($uid){
		return !$this
				->cache('user.check.r.uid' . $uid, 60)
				->where(array('uid' => $uid, 'etime' => ['GT', $_SERVER['REQUEST_TIME']]))
				->count();
	}

	public function register($data){
		$this
		->where(array('etime' => ['LT', $_SERVER['REQUEST_TIME']]))
		->delete();
		$data['etime'] = $_SERVER['REQUEST_TIME'] + 60*60*24*2;
		return $this->add($data);
	}

	public function getCode($email){
		$user = $this
				->cache('user.check.vcode' . $email, 60)
				->where(array('email' => $email, 'etime' => ['GT', $_SERVER['REQUEST_TIME']]))
				->find();
		if(!$user){
			$this->errorCode = ERR_NF_USER;
			$this->error = '可能没有注册或者已经验证。';
			return null;
		}
		if($user['vtime']<$_SERVER['REQUEST_TIME']){
			$this->errorCode = ERR_TIMEOUT;
			$this->error = '帐号验证超时，请重新注册。';
			return null;
		}
	}
	
	public function sendMail($email){
		
	}
}
