<?php
class UserPropertyModel extends Mongoo{
	protected $collectionName = 'property';
	protected $connection = 'mongo-user';

	function getEntity($uid){
		$data = $this->findOneById($uid);
		if(null === $data){
			$ret = new UserPropertyEntity([]);
		} else{
			$ret        = new UserPropertyEntity($data);
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
}
