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

	public function postNewWeibo(WeiboEntity & $data){
		$cacheObj = ThinkInstance::D('WeiboCache');
		$cacheObj->updateUser($data->user);
		if($data->channel){
			$cacheObj->updateChannel($data->app, $data->channel);
		}
		return $this->insert(get_object_vars($data));
	}

	/**
	 * 
	 *
	 * @param $id
	 *
	 * @return WeiboEntity
	 */
	public function getWeiboById($id){
		$data = $this->findOneById($id);
		if(!$data){
			return null;
		}
		return WeiboEntity::buildFromArray($data);
	}

	/**
	 *
	 * @param WeiboEntity $wb
	 *
	 * @return bool
	 */
	public function forwarded(WeiboEntity &$wb){
		$wb->beforwardcount++;
		return $this->update(['_id' => $wb->_id], ['$inc' => ['beforwardcount' => 1]]);
	}
}
