<?php
class UserRegisterModel extends Model{
	protected $tableName = 'verify';
	protected $connection = 'user';
	
	public function emailNotUse($email){
		return !$this->cache('user.check.r.email'.$email,60)->where(array('email'=>$email))->count();
	}

	public function unameNotUse($uname){
		return !$this->cache('user.check.r.uname'.$uname,60)->where(array('uname'=>$uname))->count();
	}

	public function uidNotUse($uid){
		return !$this->cache('user.check.r.uid'.$uid,60)->where(array('uid'=>$uid))->count();
	}

	public function register($data){
		return $this->add($data);
	}
}
