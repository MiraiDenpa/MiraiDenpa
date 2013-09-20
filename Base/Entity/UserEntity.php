<?php
class UserEntity extends Entity{
	const KEY = 'A Simple key that will change in few days.';
	public $uid;
	public $passwd;
	public $email;
	public $regdate;

	private $_cache_property = [];
	private $_cache_settings;

	/**
	 * @param $name
	 *
	 * @return UserPropertyHelper
	 */
	public function property($name){
		require_once BASE_LIB_PATH . 'Helper/UserPropertyHelper.php';
		if(isset($this->_cache_property[$name])){
			return $this->_cache_property[$name];
		}
		return $this->_cache_property[$name] = new UserPropertyHelper($this, $name);
	}

	/**
	 * @return UserSettingEntity
	 */
	public function settings(){
		if($this->_cache_settings){
			return $this->_cache_settings;
		}
		return $this->_cache_settings = new UserSettingEntity($this);
	}

	public function relationWith($other_user_id){
		
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
