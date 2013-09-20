<?php
trait UserAction{
	/**
	 * @param $id
	 * @return UserEntity
	 */
	protected function getUser($id){
		$mdl = ThinkInstance::D('UserLogin');
		return $mdl->where($id)->getUser();
	}
}
