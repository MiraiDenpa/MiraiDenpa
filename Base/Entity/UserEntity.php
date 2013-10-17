<?php
class UserEntity extends Entity{
	const KEY = 'A Simple key that will change in few days.';
	public $uid;
	public $passwd;
	public $email;
	public $regdate;

	private $_cache_propertys;
	private $_cache_settings;

	/**
	 * @return UserPropertyEntity
	 */
	public function propertys(){
		if(isset($this->_cache_propertys)){
			return $this->_cache_propertys;
		}
		$mdl = ThinkInstance::D('UserProperty');
		return $this->_cache_propertys = $mdl->getEntity($this->uid);
	}

	/**
	 * @param $app
	 * @return UserSettingEntity
	 */
	public function settings($app){
		if($this->_cache_settings){
			return $this->_cache_settings;
		}
		$mdl = ThinkInstance::D('UserSetting', $app);
		return $this->_cache_settings = $mdl->getEntity($mdl);
	}

	public function relationWith($other_user_id){
	}

	public function decrypt(){
		$this->passwd = mdecrypt($this->passwd, self::KEY);
		return $this;
	}

	public static function decryptPassword($pwd){
		return mdecrypt($pwd, self::KEY);
	}

	public function encrypt(){
		$this->passwd = mencrypt($this->passwd, self::KEY);
		return $this;
	}

	public static function encryptPassword($pwd){
		return mencrypt($pwd, self::KEY);
	}
}
