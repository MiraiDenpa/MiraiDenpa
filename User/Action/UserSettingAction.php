<?php
/**
 * @default_method
 * @class UserSettingAction
 * @author GongT
 */
class UserSettingAction extends Action{
	use UserAuthedAction;

	final function __call($option_name, $args){
		$user = $this->currentUser();
		$pp = $user->settings();
		
		if($option_name == '*'  && $this->dispatcher->request_method=='GET'){
			$this->assign('settings', $pp->getAll());
			return $this->success('OK');
		}
		
		if(!isset($pp->$option_name)){
			return $this->error(ERR_NALLOW_PATH, $option_name);
		}
		
		// 根据请求方式决定执行路径
		switch($this->dispatcher->request_method){
		case 'GET': // 读取
			$this->assign('value', $pp->$option_name);
			$this->assign('code', 0);
			return $this->display('!data');
		case 'POST':
			if(!isset($_POST['value'])){
				return $this->error(ERR_INPUT_REQUIRE, 'value');
			}
			if(!is_scalar($_POST['value'])){
				return $this->error(ERR_INPUT_DENY, 'value must scalar.');
			}
			if(!$this->user['pm_user'][PERM_UPDATE]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
			}
			$pp->$option_name = $_POST['value'];
			if($pp->force_save()){
				$this->success('OK');
			}else{
				$this->modelError(ThinkInstance::D('UserSetting'));
			}
			return null;
		default:
			return $this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}
}
