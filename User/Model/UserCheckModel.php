<?php
class UserCheckModel extends UserListModel{
	public function emailNotUse($email){
		return !$this->cache('user.check.email'.$email,60)->where(array('email'=>$email))->count();
	}
	
	public function unameNotUse($uname){
		return !$this->cache('user.check.uname'.$uname,60)->where(array('uname'=>$uname))->count();
	}

	public function uidNotUse($uid){
		return !$this->cache('user.check.uid'.$uid,60)->where(array('uid'=>$uid))->count();
	}
}
