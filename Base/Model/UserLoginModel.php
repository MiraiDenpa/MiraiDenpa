<?php
/**
 * 处理用户验证逻辑
 *
 * @author ${USER}
 */
class UserLoginModel extends UserListModel{
	public function _initialize(){
		$this->register_callback('before_insert', [&$this, 'InitUser']);
		$this->register_callback('before_update', [&$this, 'EncryptPassword']);
	}

	protected function InitUser(&$data, $opt){
		if(isset($data['passwd']) && $data['passwd']){
			$data['passwd'] = UserEntity::encryptPassword($data['passwd']);
		}
		$data['regdate'] = $_SERVER['REQUEST_TIME'];
	}

	protected function EncryptPassword(&$data, $opt){
		if(isset($data['passwd']) && $data['passwd']){
			$data['passwd'] = UserEntity::encryptPassword($data['passwd']);
		}
	}

	public function getUser(){
		if(empty($this->options['where'])){
			if($this->data){
				return UserEntity::buildFromArray($this->data);
			} else{
				Think::fail_error(ERR_SQL, '没有搜索条件');
			}
		}
		$user = $this->find();
		if(!$user){
			$this->errorCode = ERR_NF_USER;
			$this->error     = '恩……';
			return null;
		}
		return UserEntity::buildFromArray($user);
	}
}
