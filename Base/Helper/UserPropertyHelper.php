<?php
/**
 * User: GongT
 * Create On: 13-9-10 下午4:42
 *
 */
class UserPropertyHelper{
	/** @var \UserEntity */
	protected $ent;
	protected $name;

	/** @var UserPropertyModel */
	protected static $mdl;
	protected static $change;

	public function __construct(UserEntity &$ent, $pname){
		$this->ent  = & $ent;
		$this->name = $pname;
		$this->uid  = $ent->uid;

		if(!self::$mdl){
			self::$mdl = new UserPropertyModel('_helper');
		}
		if(!self::$mdl->count(['_id' => $this->uid])){
			self::$mdl->insert(['_id' => $this->uid]);
		}
	}

	public function set($value){
		if(!$this->name){
			Think::fail_error(ERR_NALLOW_PATH, '不能修改根路径');
		}
		return self::$mdl->update(['_id' => $this->uid], ['$set' => [$this->name => $value]]);
	}

	public function push($value, $unique = true){
		if(!$this->name){
			Think::fail_error(ERR_NALLOW_PATH, '不能修改根路径');
		}
		$op = $unique? '$addToSet' : '$push';
		return self::$mdl->update(['_id' => $this->uid], [$op => [$this->name => $value]]);
	}

	public function get(){
		if($this->name){
			$ret = self::$mdl->findOne(['_id' => $this->uid], ['_id' => false, $this->name => true]);
		}else{
			$ret = self::$mdl->findOne(['_id' => $this->uid], ['_id' => false]);
		}
		$path = explode('.', $this->name);
		while($item = array_shift($path)){
			$ret = $ret[$item];
		}
		return $ret;
	}

	public function remove(){
		if(!$this->name){
			Think::fail_error(ERR_NALLOW_PATH, '不能修改根路径');
		}
		return self::$mdl->update(['_id' => $this->uid], ['$unset' => [$this->name => $value]], ['upsert' => true]);
	}
}
