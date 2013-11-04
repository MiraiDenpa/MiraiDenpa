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
							 ['upsert' => true]);
	}

	/** 当用户发微博的时候 */
	function postWeiboOccur(){
		return $this->update(['_id' => $this->uid],
							 array(
								  '$inc' => [
									  'weibo' => 1,
								  ],
							 ),
							 ['upsert' => true]);
	}

	/** 当用户发布了微博转发其他人了的时候 */
	function forwardingOccur($direct, $usermap){
		$one_of = false;
		if($direct){
			$ret = $this->update(['_id' => $direct],
								 array(
									  '$inc' => [
										  'bedirectforward' => 1,
									  ],
								 ),
								 ['upsert' => true]);
			if($ret){
				$one_of = true;
			}
		}
		if(!empty($usermap)){
			$ret = $this->update(['_id' => $direct],
								 array(
									  '$inc' => [
										  'beforward' => 1,
									  ],
								 ),
								 ['upsert' => true]);
			if($ret){
				$one_of = true;
			}
		}
		if($one_of){
			$this->update(['_id' => $this->uid],
						  array(
							   '$inc' => [
								   'forwardother' => 1,
							   ],
						  ),
						  ['upsert' => true]);
		}
	}

	public function changeUid($uid){
		$this->uid = $uid;
	}
}
