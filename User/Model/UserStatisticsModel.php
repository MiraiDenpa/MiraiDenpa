<?php
class UserStatisticsModel extends Mongoo{
	protected $collectionName = 'statistics';
	protected $connection = 'mongo-user';
	protected $uid;

	/**
	 *
	 * @param string $uid
	 * @param        $arg2
	 *
	 * @return void
	 */
	public function _initialize($uid, $arg2){
		$this->uid = $uid;
	}

	/** 当用户登录$app的时候 */
	function loginOccur($app){
		return $this->update(['_id' => $this->uid],
							 array(
								  '$inc' => [
									  'count_login'      => 1,
									  'used_app.' . $app => 1,
								  ],
							 ),
							 ['upsert' => true]
		);
	}
}
