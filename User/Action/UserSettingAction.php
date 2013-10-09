<?php
/**
 * @default_method _get_all
 * @class          UserSettingAction
 * @author         GongT
 */
class UserSettingAction extends Action{
	use UserAuthedAction;

	final function _get_all(){
		$mdl  = ThinkInstance::D('UserSetting', $this->token_data['app']);
		$data = $mdl->getEntity($this->token_data['user']);
		$this->assign('exist', $data->exist);
		$this->assign('settings', $data->toArray());
		return $this->success();
	}

	final function __call($option_name, $args){
		$mdl = ThinkInstance::D('UserSetting', $this->token_data['app']);
		if(!in_array($option_name, UserSettingEntity::$fields)){
			return $this->error(ERR_INPUT_DENY, 'no field ' . $option_name);
		}

		// 根据请求方式决定执行路径
		switch($this->dispatcher->request_method){
		case 'GET': // 读取
			$data = $mdl
					->where($this->token_data['user'])
					->getField($option_name);
			$this->assign('value', $data);
			$this->assign('code', 0);
			return $this->display('!data');
		case 'POST':
			if(!isset($_POST['value'])){
				return $this->error(ERR_INPUT_REQUIRE, 'value');
			}
			if(!is_scalar($_POST['value'])){
				return $this->error(ERR_INPUT_DENY, 'value must scalar.');
			}
			if(!$this->token_data['pm_user'][PERM_UPDATE]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
			}
			$pp->$option_name = $_POST['value'];
			if($pp->force_save()){
				$this->success('OK');
			} else{
				$this->modelError(ThinkInstance::D('UserSetting'));
			}
			return null;
		default:
			return $this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}
}
