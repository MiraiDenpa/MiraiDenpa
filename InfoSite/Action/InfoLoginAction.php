<?php
/**
 * @default_method index
 * @class          InfoCallbackAction
 * @author         GongT
 */
class InfoLoginAction extends Action{
	final public function index(){
		$post = ThinkInstance::InStream('Post');
		$data = $post->requireAll(['email', 'passwd'])->getAll();

		$data['app_auth'] = INFOSITE_APP_PUB;

		$ret  = SimpleCURL::POST(MIRAI_LOGIN_URL, $data);
		$auth = unserialize($ret);

		if(!$auth){
			$this->error(ERR_SERVER_FAULT_CURL, 'request on MIRAI_LOGIN_URL');
		}
		if($auth['code']){
			$this->assign($auth);
			$this->display('!user_error');
		} else{
			$auth['token'] = md5(INFOSITE_APP_KEY . $auth['token']);
			
			$ret   = SimpleCURL::POST(MIRAI_AUTHIP_URL, ['ip' => get_client_ip(), 'token' => $auth['token']]);
			$addip = unserialize($ret);
			if(!$addip){
				$this->error(ERR_SERVER_FAULT_CURL, 'request on MIRAI_AUTHIP_URL');
			}
			if($addip['code']){
				$this->assign($addip);
				$this->display('!user_error');
			} else{
				cookie('token', $auth['token'], 31190400, '/'); // 记录一年
				$this->assign($auth);
				$this->display('!success');
			}
		}
	}
}
