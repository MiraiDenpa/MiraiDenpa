<?php
class UserSettingModel extends Mongoo{
	protected $collectionName = 'setting';
	protected $connection = 'mongo-user';

	protected $app_pub = '';

	public function _initialize($pub, $unused){
		$this->app_pub = $pub;
	}

	public function getByUid($uid, $field = []){
		$field['_id'] = false;
		return $this->findOne(['app' => $this->app_pub, 'uid' => $uid], $field);
	}

	public function replaceByUid($uid, $data){
		$data['update'] = time();
		return $this->update(['app' => $this->app_pub, 'uid' => $uid], $data, ['upsert' => true]);
	}

	public function setByUid($uid, $data){
		$data['update'] = time();
		return $this->update(['app' => $this->app_pub, 'uid' => $uid], ['$set' => $data], ['upsert' => true]);
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
			return UserSettingEntity::buildFromArray($data);
		} else{
			$ret        = new UserSettingEntity();
			$ret->exist = false;
			return $ret;
		}
	}
}
