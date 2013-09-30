<?php
class InfoEntryModel extends Mongoo{
	protected $collectionName = 'entry';
	protected $connection = 'mongo-info';

	public function getDocument($id){
		$data = $this->findOneById($id, ['_history' => false]);
		if(!$data){
			return null;
		}
		$g           = '$id';
		$data['_id'] = $data['_id']->$g;
		
		return $data;
	}

	public function PreSavePage($data){
		$data['_id'] = new MongoId('52495c597f8b9a99358b4569');

		$id          = $data['_id'];
		$update_info = $data['_update'];
		unset($data['_id']);
		unset($data['_update']);
		unset($data['_history']);

		$last = $this->findOne(['_id' => $id], ['_history' => false]);
		ksort($last);
		if(!$last){
			$update_info['_data'] = & $data;
			$ret                  = $this->insert(['_id' => $id, '_history' => [&$update_info]]);
		} else{
			$ret = $this->diff($last, $data);
			if(empty($ret)){
				return ['ok' => 0, 'err' => '没有改变'];
			}
			$update_info['_data'] = & $ret;
			$ret                  = $this->update(['_id' => $id], array('$push' => ['_history' => $update_info]));
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
		$this->diff_plat($last['name'], $new['name'], 'name', $statement);
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
					$pp                       = ($name? $name . '.' : '');
					$diff['$set'][$pp . $key] = $b[$key];
				}
			} else{
				if(is_numeric($key)){
					$diff['$pullAll'][$name][] = $val;
				} else{
					$pp                         = ($name? $name . '.' : '');
					$diff['$unset'][$pp . $key] = "";
				}
			}
		}
		foreach($b as $key => $val){
			if($key{0} == '_' || $key{0} == '$'){
				continue;
			}
			if(!isset($a[$key])){
				if(is_numeric($key)){
					$diff['$push'][$name]['$each'][] = $val;
				} else{
					$pp                       = ($name? $name . '.' : '');
					$diff['$set'][$pp . $key] = $val;
				}
			}
		}
	}
}
