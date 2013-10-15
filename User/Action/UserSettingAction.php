<?php
/**
 * @default_method _all_setting_data
 * @class          UserSettingAction
 * @author         GongT
 */
class UserSettingAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;

	/**
	 * @return null
	 */
	final function _all_setting_data(){
		$mdl = ThinkInstance::D('UserSetting', $this->token_data['app']);

		switch($this->dispatcher->request_method){
		case 'GET': // 读取
			if(!$this->token_data['pm_user'][PERM_READ]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_READ);
			}
			$data = $mdl->getByUid($this->token_data['user']);
			if(empty($data)){
				$this->assign('exist', false);
				$this->assign('setting', []);
			} else{
				$this->assign('exist', true);
				$this->assign('setting', $data);
			}
			$this->assign('code', 0);
			return $this->display('!data');
		case 'POST':
			if(empty($_POST)){
				return $this->error(ERR_INPUT_REQUIRE, 'POST');
			}
			if(!$this->token_data['pm_user'][PERM_UPDATE]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
			}
			unset($_POST['uid'], $_POST['id'], $_POST['app']);
			$ret = $mdl->replaceByUid($this->token_data['user'], $_POST);
			$this->mongo_ret($ret);
			break;
		default:
			return $this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}

	/**
	 *
	 * @param $option_name
	 * @param $args
	 *
	 * @return null
	 */
	final function __call($option_name, $args){
		$mdl                              = ThinkInstance::D('UserSetting', $this->token_data['app']);

		// 根据请求方式决定执行路径
		switch($this->dispatcher->request_method){
		case 'GET': // 读取
			if(!$this->token_data['pm_user'][PERM_READ]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_READ);
			}
			$data = $mdl->getByUid($this->token_data['user'], [$option_name => true]);
			$this->assign('setting', $data);
			$this->assign('code', 0);
			return $this->display('!data');
		case 'POST':
			if(!isset($_POST['value'])){
				return $this->error(ERR_INPUT_REQUIRE, 'value');
			}
			if(!$this->token_data['pm_user'][PERM_UPDATE]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
			}
			$ret = $mdl->setByUid($this->token_data['user'], [$option_name => $_POST['value']]);
			$this->mongo_ret($ret);
			break;
		default:
			return $this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}
}
