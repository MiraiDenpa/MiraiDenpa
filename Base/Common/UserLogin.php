<?php
function UserLogin($token, $allow_public){
	switch($token){
	case 'public':
		$user    = getPublicUser();
		if(!$allow_public){
			Think::fail_error(ERR_FAIL_AUTH, 'public not allow');
		}
		break;
	default:
		$uol        = ThinkInstance::D('UserOnline');
		$user = $uol->findOne(['_id' => $_GET['token']]);

		if(!$user){
			Think::fail_error(ERR_FAIL_AUTH, 'token error');
		}
		break;
	}
	return $user;
}
