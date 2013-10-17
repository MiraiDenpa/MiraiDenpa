<?php
class WeiboModel extends Mongoo{
	protected $collectionName = 'content';
	protected $connection = 'mongo-weibo';

	/**
	 *
	 * @param      $uid
	 * @param      $app
	 * @param null $fields
	 *
	 * @return WeiboEntity
	 */
	public function getUserLast($uid, $app, $fields = null){
		$where = [
			'user' => $uid,
		];
		if($app){
			$where['app'] = $app;
		}

		if($fields){
			return $this
					->find($where, $fields)
					->sort(['time' => -1])
					->limit(1)
					->getNext();
		} else{
			$data = $this
					->find($where)
					->sort(['time' => -1])
					->limit(1)
					->getNext();
			return WeiboEntity::buildFromArray($data);
		}
	}
}
