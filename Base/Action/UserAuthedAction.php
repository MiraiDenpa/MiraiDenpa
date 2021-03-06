<?php
trait UserAuthedAction{
	use UserAction;

	/**
	 * @constructor
	 * @return null`
	 */
	protected function __UserAuthedAction(){
		$error = $this->doLogin($this->allow_public);
		if($error){
			$this->error($error, $this->token_data? $this->token_data : 'token doLogin fail');
			exit;
		}
	}

	/**
	 * @return ApplicationEntity
	 */
	protected function currentApp(){
		if($this->current_app){
			return $this->current_app;
		}
		$mdl = ThinkInstance::D('AppList');
		return $this->current_app = $mdl->where($this->token_data['app'])->getApp();
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
		$this->assign('_sync_login_token_data', json_encode($this->token_data));
		$this->assign('_sync_login_property',
					  json_encode($this->currentUser()->propertys()->toArray()));
	}
}
