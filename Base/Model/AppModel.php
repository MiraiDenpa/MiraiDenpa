<?php
class AppModel extends Model{
	protected $tableName = '`index`';
	protected $connection       =   'app';
	protected $pk = 'public';
	
	public function getData($id){
		return $this->cache($id)->find($id);
	}
}
