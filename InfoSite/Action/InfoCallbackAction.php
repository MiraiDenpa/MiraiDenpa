<?php
/**
 * @default_method index
 * @class InfoCallbackAction
 * @author GongT
 */
class InfoCallbackAction extends Action{
	final public function index(){
		if(!isset($_GET['token']) || !$_GET['token']){
			return $this->error(ERR_INPUT_REQUIRE, 'token');
		}
		$auth  = $_GET['token'];
		$token = md5(INFOSITE_APP_KEY . $auth);

		$ret = SimpleCURL::POST(map_url('u-user-login-ip') . URL_PATHINFO_DEPR . $token . '.php',
								['ip' => get_client_ip()]
		);
		if(!$ret || !($data = unserialize($ret))){
			return $this->error(ERR_FAIL_AUTH, '数据库无法连接。这是一个严重的错误，请联系管理员。');
		}
		if($data['code']){
			return $this->error($data['code'], $data['message']);
		}

		cookie('token', $token, 31190400, '/'); // 记录一年

		redirect(U('Index', 'index'));
	}
}
