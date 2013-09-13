<?php
class SettingAction extends Action{
	use UserAuthedAction;

	final function __call($mtd, $args){
		if($this->dispatcher->default_method){
			$path = '';
		} else{
			array_unshift($args, $mtd);
			$path = implode('.', $args);
		}
		$permission = $this->user['pm_user'];

		$user = $this->getUser();
		if(strpos($path, '$') !== false){
			return $this->error(ERR_INPUT_DENY, 'path may not include $ sign.');
		}

		try{
			$pp = $user->property($path);
			// 检查要修改的变量是否存在
			$exist = $this->getValue($pp);
			$this->assign('exist', $exist);

			// 根据请求方式决定执行路径
			switch($this->dispatcher->request_method){
			case 'GET': // 读取
				break;
			case 'POST':
				if($exist){ // 修改
					if(!$permission[PERM_UPDATE]){
						return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
					}
				} else{ // 添加
					if(!$permission[PERM_CREATE]){
						return $this->error(ERR_FAIL_PERMISSION, PERM_CREATE);
					}
				}
				if(!isset($_POST['value'])){
					return $this->error(ERR_INPUT_REQUIRE, 'value');
				}
				if(!is_scalar($_POST['value'])){
					return $this->error(ERR_INPUT_DENY, 'value must scalar.');
				}
				$this->postValue($pp, $_POST['value']);
				break;
			case 'DELETE': // 删除
				if(!$permission[PERM_DELETE]){
					return $this->error(ERR_FAIL_PERMISSION, PERM_DELETE);
				}
				if($exist){
					$this->deleteValue($pp);
				}
				break;
			default:
				return $this->error(ERR_NALLOW_HTTP_METHOD);
			}
		} catch(MongoException $e){
			return $this->error(ERR_NO_SQL, $e->getMessage());
		}
		return $this->display('!data');
	}

	private function getValue(UserPropertyHelper &$pp){
		$ret = $pp->get();
		$this->assign('value', $ret);
		$this->assign('code', 0);
		return $ret !== null;
	}

	private function postValue(UserPropertyHelper &$pp, $new){
		$ret = $pp->set($new);
		if($ret['ok']){
			$this->assign('code', 0);
		} else{
			$this->assign('code', -1);
			$this->assign('message', $ret['err']);
		}
	}

	private function deleteValue(UserPropertyHelper &$pp){
		$ret = $pp->remove();
		if($ret['ok']){
			$this->assign('code', 0);
		} else{
			$this->assign('code', -1);
			$this->assign('message', $ret['err']);
		}
	}
}
