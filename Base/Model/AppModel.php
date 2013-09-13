<?php
class AppModel extends Model{
	protected $tableName = 'list';
	protected $connection = 'app';
	protected $pk = 'public';

	public function getData($id){
		return $this
			   ->cache($id)
			   ->find($id);
	}

	/**
	 * @return ApplicationEntity
	 */
	public function getApp(){
		if(empty($this->options['where'])){
			if($this->data){
				return new ApplicationEntity($this->data);
			} else{
				Think::fail_error(ERR_SQL, '没有搜索条件');
			}
		}
		$user = $this->find();
		if(!$user){
			$this->errorCode = ERR_NF_APPLICATION;
			$this->error     = '恩……';
			return null;
		}
		return new ApplicationEntity($user);
	}
}
