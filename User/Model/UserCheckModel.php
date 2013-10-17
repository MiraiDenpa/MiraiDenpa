<?php
/**
 * 检测用户是否存在
 *
 * @author ${USER}
 */
class UserCheckModel extends UserListModel{
	public function emailNotUse($email){
		return !$this->cache('user.check.email'.$email,60)->where(array('email'=>$email))->count();
	}
	
	public function uidNotUse($uid){
		if( $this->isDenyUid($uid) ){
			return false;
		}
		return !$this->cache('user.check.uid'.$uid,60)->where(array('uid'=>$uid))->count();
	}
}
