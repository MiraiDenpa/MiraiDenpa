<?php
class UserRelationModel extends Mongoo{
	protected $collectionName = 'relation';
	protected $connection = 'mongo-user';
	protected $perPage = 30;

	const BLACKLIST = 'blacklist';

	public function getRelation($me, $type){
		$query      = array('_id' => ['me' => $me, 'type' => $type]);
		$ret        = $this->execute('try{
			return db.' . $this->collectionName . '.findOne('.json_encode($query).').users.length;
		}catch(e){
			return 0;
		}');
		$count      = $ret['retval'];
		$this->page = $page = new Page($count, $this->perPage);
		$ret = $this->findOne($query, array('users' => ['$slice' => [$page->firstRow, $this->perPage]]));
		return $ret['users'];
	}

	public function getRelationsBetween($me, $target){
	}

	/**
	 * 添加me对target的关系
	 *
	 * @param string       $me
	 * @param string       $type
	 * @param string|array $target
	 *
	 * @return array
	 */
	public function updateRelation($me, $type, $target){
		if($type === self::BLACKLIST){ // 拉黑
			$this->removeRelationsBetween($me, $target, ['$ne' => self::BLACKLIST]); // 删除所有其他关系
		}
		$is_black = $this
				->find(array('_id' => ['me' => $me, 'type' => self::BLACKLIST], 'users' => $target))
				->count();
		if($is_black){
			return ['ok' => 0, 'err' => '黑名单冲突'];
		}

		$where    = array('_id' => ['me' => $me, 'type' => $type]);
		$is_exist = $this
				->find($where)
				->count();
		if($is_exist){
			if(is_array($target)){
				$target = ['$each' => $target];
			}
			return $this->update($where,
								 array('$addToSet' => ['users' => $target]),
								 ['upsert' => true]
			);
		} else{
			$where['users'] = is_array($target)? $target : [$target];
			return $this->insert($where);
		}
	}

	/**
	 * 清除me和target之间的所有关系（包括黑名单）
	 *
	 * @param string       $me
	 * @param string|array $target
	 * @param string|array $type
	 *
	 * @return bool
	 */
	public function removeRelationsBetween($me, $target, $type){
		if(is_array($target)){
			$target[] = $me;
		} else{
			$target = [$target, $me];
		}
		$update = array('$pullAll' => ['users' => $target]);
		$where  = array('_id.me' => ['$in' => $target]);
		if($type !== '*'){
			$where['_id.type'] = $type;
		}
		return $this->update($where, $update, ['multiple' => true]);
	}

	/**
	 * 清除me对target的关系（包括黑名单）
	 * target不会收到任何影响
	 *
	 * @param string       $me
	 * @param string|array $target
	 * @param string|array $type
	 *
	 * @return bool
	 */
	public function removeRelationTo($me, $target, $type){
		$where = ['_id.me' => $me];
		if($type !== '*'){
			$where['_id.type'] = $type;
		}
		$op         = is_array($target)? '$pullAll' : '$pull';
		$where[$op] = array('users' => $target);
		return $this->remove($where);
	}

	public function getList($me, $type, $page){
	}
}
