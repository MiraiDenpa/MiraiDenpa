<?php
class UserPropertyModel extends Mongoo{
	protected $collectionName = 'property';
	protected $connection = 'mongo-user';

	function getEntity($uid){
		$data = $this->findOne(['_id'=>$uid]);
		if(null === $data){
			$ret = new UserPropertyEntity();
		} else{
			$ret        = UserPropertyEntity::buildFromArray($data);
			$ret->exist = true;
		}
		return $ret;
	}

	function set($uid, $set){
		return $this->update(['_id' => $uid], ['$set' => $set]);
	}

	function replace($uid, $replace){
		return $this->update(['_id' => $uid], $replace);
	}

	function updatePath($uid, $name, $value){
		return $this->update(['_id' => $uid], ['$set' => [$name => $value]]);
	}

	public function initUser($user){
		return $this->insert(['_id'=>$user['uid']]);
	}
}
