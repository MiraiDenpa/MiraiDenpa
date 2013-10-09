<?php
/**
 * 
 *
 * @param $token
 * @param $allow_public
 *
 * @return array
 */
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
		$user = $uol->findOne(['_id' => $token]);

		if(!$user){
			Think::fail_error(ERR_FAIL_AUTH, 'token error');
		}
		if(!in_array(get_client_ip(), $user['ip'])){
			Think::fail_error(ERR_NALLOW_IP, 'deny access');
		}
		break;
	}
	return $user;
}
