<?php
class InfoEntryModel extends Mongoo{
	protected $collectionName = 'entry';
	protected $connection = 'mongo-info';

	function cast_entry(&$entry){
		$entry['broadcast_range'] = array(
			'start' => intval($entry['broadcast_range']['start']),
			'end'   => intval($entry['broadcast_range']['end'])
		);
		unset($entry['_id'], $entry['_history']);
		ksort($entry);
	}

	public function getDocument($id, $history = false){
		$data = $this->findOneById($id, ['_history' => $history]);
		if(!$data){
			return null;
		}
		$data['_id'] = (string)$data['_id'];

		return $data;
	}

	public function getChangedList(){
		return $this
				->find(['_change' => true],
					   [
					   '_id'           => true,
					   '_last_update'  => true,
					   '_history.user' => true,
					   'name'          => true,
					   'origin_name'   => true
					   ]
				)
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
							  ]
		);
	}

	public function PreSavePage($data){
		$id          = $data['_id'];
		$update_info = $data['_update'];
		unset($data['_id']);
		unset($data['_update']);
		unset($data['_history']);

		$last = $this->findOne(['_id' => $id], ['_history' => false]);
		ksort($last);
		if(!$last){
			$update_info['_data'] = & $data;
			$ret                  = $this->insert([
												  '_id'      => $id,
												  '_history' => [&$update_info],
												  '_change'  => true,
												  'name'     => $data['name']
												  ]
			);
		} else{
			$update_info['_data'] = & $data;
			$ret                  = $this->diff($last, $data);
			if(empty($ret)){
				return ['ok' => 0, 'err' => '没有改变'];
			}
			$ret = $this->update(['_id' => $id],
								 array('$push' => ['_history' => $update_info], '$set' => ['_change' => true])
			);
		}

		return $ret;
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
