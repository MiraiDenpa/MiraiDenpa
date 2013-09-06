<?php
class UserLoginModel extends UserListModel{
	public function __construct($name = ''){
		parent::__construct($name);
		$this->register_callback('before_insert', [&$this, 'InitUser']);
		$this->register_callback('before_update', [&$this, 'EncryptPassword']);
	}

	public function checkPassword($check){
		$real = $this->data['passwd'];
		$dec  = mdecrypt($real, self::KEY);

		return $dec === $check;
	}

	protected function InitUser(&$data, $opt){
		if(isset($data['passwd']) && $data['passwd']){
			$data['passwd'] = UserEntity::encrypt($data['passwd']);
		}
		$data['regdate'] = $_SERVER['REQUEST_TIME'];
	}

	protected function EncryptPassword(&$data, $opt){
		if(isset($data['passwd']) && $data['passwd']){
			$data['passwd'] = UserEntity::encrypt($data['passwd']);
		}
	}

	public function getUser(){
		if(empty($this->options['where'])){
			Think::fail_error(ERR_SQL, '没有搜索条件');
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
