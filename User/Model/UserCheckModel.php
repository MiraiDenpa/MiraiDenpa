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
	
	public function isDenyUid($uid){
		$denys = array(
			'admin',
			'auth',
			'relation',
			'property',
			'proxy',
			'password',
			'login',
			'empty',
			'forget',
			'setting',
			'square',
			'search',
			'fallback',
			'empty',
			'blacklist',
			'follow',
			'follower',
			'friend',
			'group',
			'tag',
			'denpa',
			'dianbo',
			'user',
			'add',
			'get',
			'set',
			'post',
			'delete',
		);
		return in_array($denys, strtolower($uid));
	}
}
