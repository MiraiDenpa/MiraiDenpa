<?php
/**
 * User: GongT
 * Create On: 13-9-10 下午4:42
 *
 */
class UserSettingEntity extends Entity{
	protected $uid;

	private $data;
	private $change;

	/** @var UserSettingModel */
	private static $mdl;

	public function __construct(UserEntity &$ent){
		$this->uid = $ent->uid;
		if(!self::$mdl){
			self::$mdl = ThinkInstance::D('UserSetting');
		}
		$this->data = self::$mdl
				->where($this->uid)
				->find();
		if(empty($this->data)){
			self::$mdl
					->data(['$pk' => $this->uid])
					->add();
			$this->data = self::$mdl
					->where($this->uid)
					->find();
		}
	}

	public function __get($name){
		if(isset($this->data[$name])){
			return $this->data[$name];
		} else{
			return null;
		}
	}

	public function __set($name, $value){
		if(!isset($this->data[$name])){
			return false;
		}
		if($this->data[$name] == $value){
			return true;
		}
		if(!$this->change){
			$this->change = ['$pk' => $this->uid];
			register_shutdown_function('UserSettingEntity::_shutdown', $this);
		}
		$this->change[$name] = $value;
		$this->data[$name]   = $value;
	}

	public function __isset($name){
		return isset($this->data[$name]);
	}

	public static function _shutdown(UserSettingEntity  &$self){
		if(!empty($self->change)){
			self::$mdl
					->data($self->change)
					->save();
		}
	}

	public function force_save(){
		if(empty($self->change)){
			return true;
		}
		$ret = self::$mdl
				->data($self->change)
				->save();
		$self->change = null;
		return $ret;
	}
}
