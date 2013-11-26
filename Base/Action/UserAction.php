<?php
trait UserAction{
	protected $token;
	/** @var $uol UserOnlineModel */
	protected $uol;

	/**
	 * @var LoginTokenEntity
	 */
	protected $token_data;

	/* cache */
	private $current_user;
	private $current_app;

	/**
	 * @param $id
	 *
	 * @return UserEntity
	 */
	protected function getUser($id){
		$mdl = ThinkInstance::D('UserLogin');
		return $mdl
				->where($id)
				->getUser();
	}

	protected function doLogin($allow_public = false){
		if(isset($_GET['token'])){
			$this->token = $_GET['token'];
		} elseif(isset($_POST['token'])){
			$this->token = $_POST['token'];
		} elseif(isset($_COOKIE['token'])){
			$this->token = $_COOKIE['token'];
		} elseif(!$this->token){
			return ERR_INPUT_REQUIRE;
		}
		$this->token_data = UserLogin($this->token, $allow_public, $errno);
		return $errno;
	}
}
