<?php
trait UserAuthedAction{
	protected $token;
	/** @var $uol UserOnlineModel */
	protected $uol;

	protected $user;
	
	protected $special = 0;

	public function __UserAuthedAction(){
		if(!isset($_GET['token'])){
			$this->error(ERR_INPUT_REQUIRE, 'token');
			exit;
		}
		$this->token = $_GET['token'];
		
		switch($this->token){
		case 'public':
			$this->special = SpecialUser::TYPE_PUBLIC;
			$this->user = getPublicUser();
			if(!$this->allow_public){
				$this->error(ERR_FAIL_AUTH, 'public not allow');
				exit;
			}
			break;
		default:
			$uol = ThinkInstance::D('UserOnline');
			$this->user = $uol->findOne(['_id'=>$_GET['token']]);

			if(!$this->user){
				$this->error(ERR_FAIL_AUTH, 'token error');
				exit;
			}
			break;
		}
		
		
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
