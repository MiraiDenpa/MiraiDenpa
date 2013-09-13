<?php
/**
 * User: GongT
 * Create On: 13-8-24 下午3:42
 *
 */
class LoginAction extends Action{
	final public function index($pubid = 'MiraiDenpaInfo'){
		if(REQUEST_METHOD == 'POST'){
			return $this->act();
		}
		$pubid = strtolower($pubid);
		$model = ThinkInstance::D('App');
		$app   = $model->getData($pubid);
		if(empty($app)){
			return $this->error(ERR_NF_APPLICATION, '/');
		}
		$this->assign('app', $app);
		$this->assign('email', isset($_GET['email'])? $_GET['email'] : '');
		return $this->display('pubkey');
	}
	
	final public function public_key_auth(){
		$post = ThinkInstance::InStream('Post');

		/** @var UserEntity $user */
		$user = null;
		/** @var ApplicationEntity $app */
		$app  = null;
		$data = $post
				->requireAll(['email', 'passwd', 'app_auth'])
				->filter_callback('email', function ($email) use (&$user){
					$usrlist = ThinkInstance::D('UserLogin');
					$user    = $usrlist
							   ->where(['email|uid' => $email])
							   ->getUser();
					if(!$user){
						$this->modelError($usrlist);
						exit;
					} else{
						return true;
					}
				})
				->filter_callback('app_auth', function ($public) use (&$app){
					$applist = ThinkInstance::D('App');
					$app     = $applist
							   ->where($public)
							   ->getApp();
					if(!$app){
						$this->modelError($applist);
						exit;
					} else{
						return true;
					}
				})
				->getAll();

		$user->decrypt();
		if($user->passwd !== $data['passwd']){
			return $this->error(ERR_MISS_PASSWORD);
		}

		$uol                    = ThinkInstance::D('UserOnline');
		$saveData               = [];
		$saveData['time']       = time();
		$saveData['ip']         = get_client_ip();
		$saveData['user']       = $user->uid;
		$saveData['email']      = $user->email;
		$saveData['ahash']      = md5($user->email);
		$saveData['app']        = $app->public;
		
		foreach($app->getPermissions() as $perm){
			$saveData[$perm]    = parse_permission($perm);
		}

		$token           = sha1(time() . $user->uid . $app->public);
		$saveData['_id'] = md5($app->key . $token);

		$query = ['user' => $user->uid, 'app' => $app->public];

		try{
			if($del = $uol->count($query, 1)){
				$d = $uol->remove($query, ['justOne' => true]);
				if($d['n']){
					$this->assign('kick', true);
				}
			}
			$ret = $uol->insert($saveData);

			if(!$ret['ok'] || $ret['err']){
				$this->error(ERR_NO_SQL, $ret['err'] . $ret['errmsg']);
			} else{
				$this->assign('token', $token);
				$this->success('登录成功', [$app->name, $app->callback . '?token=' . $token]);
			}
		} catch(MongoException $e){
			$this->error(ERR_NO_SQL, $e->getMessage());
		}
	}
}
