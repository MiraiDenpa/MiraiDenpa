<?php
class UserListModel extends Model{
	protected $tableName = 'list';
	protected $connection       =   'user';
	
	public function __construct($name = '', $connection = ''){
		
		parent::__construct($name, $connection);
	}
}
