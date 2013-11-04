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
			return $this->find($where, $fields)
					->sort(['time' => -1])
					->limit(1)
					->getNext();
		} else{
			$data = $this->find($where)
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
		$ret = $this->insert($data->toArray());
		if($ret['ok']){
			$stati = ThinkInstance::D('UserStatistics', $data->user);
			$stati->postWeiboOccur($data);
			if($data->forward && $data->forward->type === 'mirai/denpa'){
				$fwlist     = empty($data->forward->original)? [] : $data->forward->original;
				$query_list = [];
				foreach($fwlist as $i => $fwid){
					try{
						$query_list[$fwid] = new MongoId($fwid);
					} catch(Exception $e){
						unset($fwlist[$i]);
					}
				}
				$itr     = $this->find(['_id' => ['$in' => $query_list]], ['user' => true]);
				$usermap = iterator_to_array($itr, false);
				$usermap = array_column($usermap, 'user', '_id');
				$direct  = $usermap[$data->forward->content];
				$stati->forwardingOccur($direct, $usermap);
			}
		}
		return $ret;
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
