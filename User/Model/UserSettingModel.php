<?php
class UserSettingModel extends Model{
	protected $connection = 'user';
	protected $tableName = 'setting';
	protected $pk = 'uid';
	protected $app_pub;

	public function _initialize($pub){
		$this->app_pub = $pub;
		$this->register_callback('before_read',
			function (&$opt){
				$opt['where']['app'] = $this->app_pub;
			}
		);
		$this->register_callback('before_write',
			function ($opt,&$data){
				$data['app'] = $this->app_pub;
			}
		);
	}

	/**
	 *
	 * @param $uid
	 *
	 * @return UserSettingEntity
	 */
	function getEntity($uid){
		$data = $this
				->where($uid)
				->find();
		if($data){
			return new UserSettingEntity($data);
		} else{
			$ret        = new UserSettingEntity([]);
			$ret->exist = false;
			return $ret;
		}
	}
}
