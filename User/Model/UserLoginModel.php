<?php
/**
 * 处理用户验证逻辑
 *
 * @author ${USER}
 */
class UserLoginModel extends UserListModel{
	public function __construct($name = ''){
		parent::__construct($name);
		$this->register_callback('before_insert', [&$this, 'InitUser']);
		$this->register_callback('before_update', [&$this, 'EncryptPassword']);
	}

	protected function InitUser(&$data, $opt){
		if(isset($data['passwd']) && $data['passwd']){
			$user = new UserEntity($data);
			$user->encrypt();
			$data['passwd'] = $user->passwd;
		}
		$data['regdate'] = $_SERVER['REQUEST_TIME'];
	}

	protected function EncryptPassword(&$data, $opt){
		if(isset($data['passwd']) && $data['passwd']){
			$data['passwd'] = UserEntity::encrypt($data['passwd']);
		}
	}

	public function checkPassword($check){
		$real = $this->data['passwd'];
		$dec  = mdecrypt($real, self::KEY);

		return $dec === $check;
	}

	public function getUser(){
		if(empty($this->options['where'])){
			if($this->data){
				return new UserEntity($this->data);
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
		return new UserEntity($user);
	}
}
