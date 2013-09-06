<?php
class UserEntity extends Entity{
	const KEY = 'A Simple key that will change in few days.';
	public $uid;
	public $passwd;
	public $email;
	public $uname;
	public $regdate;
	
	public static function decrypt($data){
		return mdecrypt($data, self::KEY);
	}
	
	public static function encrypt($data){
		return mencrypt($data, self::KEY);
	}
}
