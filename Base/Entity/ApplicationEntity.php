<?php
class ApplicationEntity extends Entity{
	public $public;
	public $key;
	public $mainurl;
	public $icon;
	public $callback;
	public $authtype;
	public $bind_ip;
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
	public $pm_relation;

	public static function getPermissions(){
		return ['pm_user'     => '用户资料',
				'pm_file'     => '文件',
				'pm_weibo'    => '分享内容',
				'pm_account'  => '帐号',
				'pm_relation' => '好友列表',
		];
	}
}
