<?php
/**
 *
 *
 * @param $token
 * @param $allow_public
 * @param $error
 *
 * @return array
 */
function UserLogin($token, $allow_public, &$error = null){
	switch($token){
	case 'public':
		$user = getPublicUser();
		if(!$allow_public){
			$error = ERR_FAIL_AUTH;
			return null;
		}
		break;
	default:
		$uol  = ThinkInstance::D('UserOnline');
		$user = $uol->findOne(['_id' => $token]);

		if(!$user){
			$error = ERR_FAIL_AUTH;
			return null;
		}
		if(!in_array(get_client_ip(), $user['ip'])){
			$error = ERR_NALLOW_IP;
			return null;
		}
		break;
	}
	$error = ERR_NO_ERROR;
	return LoginTokenEntity::buildFromArray($user);
}
