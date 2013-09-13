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
	 * @param $name
	 * @return UserSettingHelper
	 */
	public function setting($name){
		require_once BASE_LIB_PATH . 'Helper/UserSettingHelper.php';
		static $c = [];
		if(isset($c[$name])){
			return $c[$name];
		}
		return $c[$name] = new UserSettingHelper($this, $name);
	}
	
	public function decrypt(){
		$this->passwd = mdecrypt($this->passwd, self::KEY);
	}

	public function encrypt(){
		$this->passwd = mencrypt($this->passwd, self::KEY);
	}
}
