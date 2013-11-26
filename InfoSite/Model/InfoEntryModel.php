<?php
class InfoEntryModel extends Mongoo{
	protected $collectionName = 'entry';
	protected $connection = 'mongo-info';

	function cast_entry(&$entry){
		$entry['broadcast_range'] = array(
			'start' => intval($entry['broadcast_range']['start']),
			'end'   => intval($entry['broadcast_range']['end'])
		);
		if(isset($entry['classification'][1])){
			$entry['classification'][1] = intval($entry['classification'][1]);
		}
		unset($entry['_id'], $entry['_history']);
		ksort($entry);
		foreach($entry as $k => $v){
			if(is_numeric($v)){
				if(intval($v) == $v){
					$entry[$k] = intval($v);
				} elseif(doubleval($v) == $v){
					$entry[$k] = doubleval($v);
				}
			} elseif(is_string($v)){
				$l = strtolower($v);
				if($l === 'false' || $l === 'off'){
					$entry[$k] = false;
				} elseif($l === 'true' || $l === 'on'){
					$entry[$k] = true;
				}
			}
		}
	}

	/**
	 * @param      $id
	 * @param bool $history
	 *
	 * @return InfoEntryEntity
	 */
	public function getDocument($id, $history = false){
		$data = $this->findOneById($id, ['_history' => $history]);
		if(!$data){
			return null;
		}
		return InfoEntryEntity::buildFromArray($data);
	}

	public function getChangedList(){
		return $this->find(['_change' => true],
						   [
						   '_id'           => true,
						   '_last_update'  => true,
						   '_history.user' => true,
						   'name'          => true,
						   'origin_name'   => true
						   ])
				->limit(5);
	}

	public function getChangedById($id){
		return $this->findOne([
							  '_id'     => new MongoId($id),
							  '_change' => true
							  ],
							  [
							  '_id'          => true,
							  '_last_update' => true,
							  '_history'     => true,
							  'name'         => true,
							  'origin_name'  => true
							  ]);
	}

	public function PreSavePage($data){
		$id          = $data['_id'];
		$update_info = $data['_update'];
		unset($data['_id']);
		unset($data['_update']);
		unset($data['_history']);


		$last = $this->findOne(['_id' => $id], ['_history' => false]);

		if(!$last){
			$update_info['_data'] = & $data;
			$ret                  = $this->insert([
												  '_id'      => $id,
												  '_history' => [&$update_info],
												  '_change'  => true,
												  'name'     => $data['name']
												  ]);
		} else{
			ksort($last);
			$update_info['_data'] = & $data;
			$ret                  = $this->diff($last, $data);
			if(empty($ret)){
				return ['ok' => 0, 'err' => '没有改变'];
			}
			$ret = $this->update(['_id' => $id],
								 array('$push' => ['_history' => $update_info], '$set' => ['_change' => true]));
		}

		return $ret;
	}

	public function discardChange($id){
		return $this->update(['_id' => new MongoId($id)], ['$unset' => ['_history' => true, '_change' => true]]);
	}

	/**
	 * @param $last
	 * @param $new
	 *
	 * @return array
	 */
	private function diff($last, $new){
		$statement = [];
		$this->diff_plat(array_values($last['name']), array_values($new['name']), 'name', $statement);
		ksort($last['broadcast_range']);
		ksort($new['broadcast_range']);
		$this->diff_plat($last['broadcast_range'], $new['broadcast_range'], 'broadcast_range', $statement);
		unset($last['name'], $new['name'], $last['broadcast_range'], $new['broadcast_range']);

		$this->diff_plat($last, $new, '', $statement);
		return $statement;
	}

	private function diff_plat($a, $b, $name, &$diff){
		foreach($a as $key => $val){
			if($key{0} == '_' || $key{0} == '$'){
				continue;
			}
			if(isset($b[$key])){
				if($b[$key] != $val){
					$pp = ($name? $name . '.' : '');
					if(is_numeric($key)){
						$diff['array_set'][$pp][$key] = $b[$key];
					} else{
						$diff['set'][$pp . $key] = $b[$key];
					}
				} else{
				}
			} else{
				if(is_numeric($key)){
					$diff['pull'][$name][] = $val;
				} else{
					$pp                        = ($name? $name . '.' : '');
					$diff['unset'][$pp . $key] = true;
				}
			}
		}
		foreach($b as $key => $val){
			if($key{0} == '_' || $key{0} == '$'){
				continue;
			}
			if(!isset($a[$key])){
				if(is_numeric($key)){
					$diff['push'][$name][] = $val;
				} else{
					$pp                      = ($name? $name . '.' : '');
					$diff['set'][$pp . $key] = $val;
				}
			}
		}
	}
}
