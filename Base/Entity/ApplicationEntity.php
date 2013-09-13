<?php
class ApplicationEntity extends Entity{
	public $public;
	public $key;
	public $mainurl;
	public $icon;
	public $callback;
	public $authtype;
	public $name;
	public $description;
	public $author;
	public $version;
	public $subname;
	public $date;
	public $reg_date;
	public $popular;

	public $pm_user;
	public $pm_social;
	public $pm_file;
	public $pm_weibo;
	public $pm_account;
	
	protected $_permissions = [];

	public function __construct($data){
		foreach($data as $name => $value){
			if(strpos($name, 'pm_')){
				$this->_permissions[] = $name;
			}
		}
		parent::__construct($data);
	}
	
	public function getPermissions(){
		return $this->_permissions;
	}
}
