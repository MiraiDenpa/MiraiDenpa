<?php
/**
 * @default_method
 * @class  UserPropertyAction
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

		$permission = $this->token_data['pm_user'];

		if($target == 'me'){
			if($this->token_data['type'] == SpecialUser::TYPE_PUBLIC){
				return $this->error(ERR_FAIL_AUTH, 'public not allow(at me)');
			}
			$uid = $this->token_data['user'];
		} else{
			if($this->dispatcher->request_method !== 'GET'){
				return $this->error(ERR_NALLOW_EDIT_OTHER, $target);
			}
			$user = $this->getUser($target);
			$uid  = $user->uid;
		}

		if(!$uid){
			return $this->error(ERR_NF_USER, $target);
		}

		$mdl = ThinkInstance::D('UserProperty');
		try{
			// 根据请求方式决定执行路径
			switch($this->dispatcher->request_method){
			case 'GET': // 读取
				if(!$permission[PERM_READ]){
					return $this->error(ERR_FAIL_PERMISSION, PERM_READ);
				}
				if($path){
					$ret = $mdl->findOne(['_id' => $uid], [$path => true]);
				} else{
					$ret = $mdl->findOne(['_id' => $uid], ['_id' => false]);
				}
				$this->assign('property', $ret);
				$this->assign('exist', empty($ret));
				return $this->success();
			case 'POST':
				if(!$path && !$permission[PERM_DELETE]){
					return $this->error(ERR_FAIL_PERMISSION, PERM_DELETE);
				}
				if(!$permission[PERM_UPDATE]){
					return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
				}
				if(!isset($_POST['value'])){
					return $this->error(ERR_INPUT_REQUIRE, 'value');
				}
				$v = $_POST['value'];
				$t = $_POST['type'];

				if(!$path){
					if($t && $t != 'replace' && $t != 'set'){
						return $this->error(ERR_CONFLICT_MTD_RES, 'root cannot act as array.');
					}
					if(!is_array($v)){
						return $this->error(ERR_CONFLICT_MTD_RES, 'root cannot set to scalar.');
					}
				}
				if($t == 'replace'){
					if($path){
						$data = ['$set' => [$path => $v]];
					} else{
						if(!is_array($v)){
							return $this->error(ERR_INPUT_DENY, 'root cannot set to scalar.');
						}
						$data = $v;
					}
				} else if($t == 'merge'){
					if(!is_array($v)){
						return $this->error(ERR_INPUT_DENY, 'value must array.');
					}
					$data = ['$pushAll' => [$path => array_values($v)]];
				} else if($t == 'merge_unique'){
					if(!is_array($v)){
						return $this->error(ERR_INPUT_DENY, 'value must array.');
					}
					$data = ['$addToSet' => [$path => ['$each' => array_values($v)]]];
				} else if($t == 'push'){
					$data = ['$push' => [$path => $v]];
				} else if($t == 'push_unique'){
					$data = ['$addToSet' => [$path => $v]];
				} else if($t == 'pull'){
					$data = ['$pull' => [$path => $v]];
				} else if($t == 'pull_all'){
					if(!is_array($v)){
						return $this->error(ERR_INPUT_DENY, 'value must array.');
					}
					$data = ['$pull' => [$path => ['$each' => array_values($v)]]];
				} else{
					if($path){
						$data = ['$set' => [$path => $v]];
					} else{
						if(!is_array($v)){
							return $this->error(ERR_INPUT_DENY, 'root cannot act as array.');
						}
						$data = ['$set' => $v];
					}
				}
				$ret = $mdl->update(['_id' => $uid], $data, ['upsert' => true]);
				return $this->mongo_ret($ret, '保存成功！');
			case
			'DELETE': // 删除
				if(!$permission[PERM_DELETE]){
					return $this->error(ERR_FAIL_PERMISSION, PERM_DELETE);
				}
				break;
			default:
				return $this->error(ERR_NALLOW_HTTP_METHOD, $this->dispatcher->request_method);
			}
		} catch(MongoException $e){
			return $this->error(ERR_NO_SQL, $e->getMessage());
		}
	}

	final public function hash_email(){
		$permission = $this->token_data['pm_user'];
		if(!$permission[PERM_READ]){
			return $this->error(ERR_FAIL_PERMISSION, PERM_READ);
		}
		return $this->success(md5(strtolower(trim($this->token_data->email))));
	}
}
