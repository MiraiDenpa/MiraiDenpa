<?php
trait UserAuthedAction{
	protected $token;
	/** @var $uol UserOnlineModel */
	protected $uol;
	
	protected $user;

	public function __UserAuthedAction(){
		if(!isset($_GET['token'])){
			Think::fail_error(ERR_INPUT_REQUIRE, 'token');
		}
		$this->token = $_GET['token'];
		$this->user = UserLogin($this->token, $this->allow_public);
	}

	/**
	 * @return ApplicationEntity
	 */
	public function getApp(){
		static $us;
		if($us) return $us;
		$mdl = ThinkInstance::D('AppList');
		return $us = $mdl->where($this->user['app'])->getApp();
	}

	/**
	 * @return UserEntity
	 */
	public function getUser(){
		static $us;
		if($us) return $us;
		$mdl = ThinkInstance::D('UserLogin');
		return $us = $mdl->where($this->user['user'])->getUser();
	}
}
