<?php
/**
 * @default_method _all_setting_data
 * @class          UserSettingAction
 * @author         GongT
 */
class UserSettingAction extends Action{
	use UserAuthedAction;

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
			$data = $mdl->find($this->token_data['user']);
			if(empty($data)){
				$this->assign('exist', false);
				$this->assign('value', []);
			} else{
				$this->assign('exist', true);
				$this->assign('value', $data);
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
			$_POST['update'] = time();
			unset($_POST['uid'], $_POST['id'], $_POST['app']);
			$ret = $mdl
					->where($this->token_data['user'])
					->save($_POST);
			if($ret){
				$this->success('OK');
			} else{
				if(!$mdl->find($this->token_data['user'])){
					$_POST['uid'] = $this->token_data['user'];
					$ret          = $mdl->add($_POST);
				}
				if($ret){
					$this->success('OK');
				} else{
					$this->error(ERR_SQL, $mdl->getDbError() . $mdl->getError());
				}
			}
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
		$mdl = ThinkInstance::D('UserSetting', $this->token_data['app']);
		if(!in_array($option_name, UserSettingEntity::$fields)){
			return $this->error(ERR_INPUT_DENY, 'no field ' . $option_name);
		}

		// 根据请求方式决定执行路径
		switch($this->dispatcher->request_method){
		case 'GET': // 读取
			if(!$this->token_data['pm_user'][PERM_READ]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_READ);
			}
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
			$ret = $mdl
					->where($this->token_data['user'])
					->setField($option_name, $_POST['value']);
			if($ret){
				$this->success('OK');
			} else{
				if(!$mdl->find($this->token_data['user'])){
					$w        = [$option_name => $_POST['value']];
					$w['uid'] = $this->token_data['user'];
					$ret      = $mdl->add($w);
				}
				if($ret){
					$this->success('OK');
				} else{
					$this->error(ERR_SQL, $mdl->getDbError() . $mdl->getError());
				}
			}
			return $this->display('!data');
		default:
			return $this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}
}
