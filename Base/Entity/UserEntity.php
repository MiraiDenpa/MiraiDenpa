<?php
class UserEntity extends Entity{
	const KEY = 'A Simple key that will change in few days.';
	public $uid;
	public $passwd;
	public $email;
	public $regdate;

	/**
	 * @param $name
	 * @return UserPropertyHelper
	 */
	public function property($name){
		require_once BASE_LIB_PATH . 'Helper/UserPropertyHelper.php';
		static $c = [];
		if(isset($c[$name])){
			return $c[$name];
		}
		return $c[$name] = new UserPropertyHelper($this, $name);
	}

	/**
	 * @return UserSettingEntity
	 */
	public function settings(){
		static $c = null;
		if($c){
			return $c;
		}
		return $c = new UserSettingEntity($this);
	}
	
	public function decrypt(){
		$this->passwd = mdecrypt($this->passwd, self::KEY);
		return $this;
	}

	public function encrypt(){
		$this->passwd = mencrypt($this->passwd, self::KEY);
		return $this;
	}
}
