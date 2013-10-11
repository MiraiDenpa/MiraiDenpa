<?php
trait UserAuthedAction{
	use UserAction;

	protected $token;
	/** @var $uol UserOnlineModel */
	protected $uol;

	/**
	 * @var array
	 */
	protected $token_data;

	/* cache */
	private $current_user;
	private $current_app;

	/**
	 * @constructor
	 * @return void
	 */
	protected function __UserAuthedAction(){
		if(isset($_GET['token'])){
			$this->token = $_GET['token'];
		} elseif(isset($_COOKIE['token'])){
			$this->token = $_COOKIE['token'];
		} else{
			Think::fail_error(ERR_INPUT_REQUIRE, 'token');
		}
		$this->token_data = UserLogin($this->token, $this->allow_public);
	}

	/**
	 * @return ApplicationEntity
	 */
	protected function currentApp(){
		if($this->current_app){
			return $this->current_app;
		}
		$mdl = ThinkInstance::D('AppList');
		return $this->current_app = $mdl
				->where($this->token_data['app'])
				->getApp();
	}

	/**
	 * @return UserEntity
	 */
	protected function currentUser(){
		if($this->current_user){
			return $this->current_user;
		}
		return $this->current_user = $this->getUser($this->token_data['user']);
	}
	
	protected function syncLogin(){
		$this->assign('_sync_login_token_data',json_encode($this->token_data));
		$this->assign('_sync_login_property',json_encode($this->currentUser()->propertys()->toArray()));
	}
}
