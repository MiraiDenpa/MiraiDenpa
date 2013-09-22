<?php
/**
 * @default_method
 * @class UserPropertyAction
 * @author GongT
 */
class UserPropertyAction extends Action{
	use UserAuthedAction;
	protected $allow_public = true;

	/**
	 *
	 *
	 * @param $target 用户ID （me表示当前登录用户）
	 * @param $args
	 *
	 * @return null
	 */
	final function __call($target, $args){
		$path = empty($args)? '' : implode('.', $args);
		if(strpos($path, '$') !== false){
			return $this->error(ERR_INPUT_DENY, 'path may not include $ sign.');
		}

		$permission = $this->user['pm_user'];

		if($target == 'me'){
			if($this->user['type'] == SpecialUser::TYPE_PUBLIC){
				return $this->error(ERR_FAIL_AUTH, 'public not allow(at me)');
			}
			$user = $this->currentUser();
		} else{
			if($this->dispatcher->request_method !== 'GET'){
				return $this->error(ERR_NALLOW_EDIT_OTHER, $target);
			}
			$user = ThinkInstance::D('UserLogin')
					->where($target)
					->getUser();
		}

		if(!$user){
			return $this->error(ERR_NF_USER, $target);
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
		$this->assign('property', $ret);
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
