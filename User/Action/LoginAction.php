<?php
/**
 * @default_method index
 * @class          UserLoginAction
 * @author         GongT
 */
class LoginAction extends Action{
	final public function index($pubid = 'MiraiDenpaInfo'){
		if(REQUEST_METHOD == 'POST'){
			return $this->public_key_auth();
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
				->optional('add_fast_login')
				->requireAll(['email', 'passwd', 'app_auth'])
				->valid('add_fast_login', FILTER_VALIDATE_BOOLEAN)
				->filter_callback('email',
					function ($email) use (&$user){
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
					}
				)
				->filter_callback('app_auth',
					function ($public) use (&$app){
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
					}
				)
				->getAll();

		$user->decrypt();
		if($user->passwd !== $data['passwd']){
			return $this->error(ERR_MISS_PASSWORD);
		}

		// 允许登录

		if($data['add_fast_login']){
			session_start();
			if(!$_SESSION['current_login'] || !in_array($user->email, $_SESSION['current_login'])){
				$_SESSION['current_login'][] = [$user->uid, md5(strtolower(trim($user->email)))];
			}
		}
		$this->saveLoginState($user, $app);
	}

	final public function get_session(){
		header('Access-Control-Allow-Origin: ' . map_url('user'));
		session_start();
		$this->assign('list', $_SESSION['current_login']);
		$this->display('!data');
	}

	final public function erase_fast_login(){
		session_start();
		$_SESSION['current_login'] = [];
		$this->success('清除成功', ['登录', UI('index')]);
	}

	final public function fast_login(){
		$post = ThinkInstance::InStream('Post');
		session_start();

		/** @var UserEntity $user */
		$user = null;
		/** @var ApplicationEntity $app */
		$app = null;

		$fast = $_SESSION['current_login'];
		$data = $post
				->requireAll(['fast_login', 'app_auth'])
				->filter_callback('fast_login',
					function ($ahash) use (&$user, $fast){
						$usrlist = ThinkInstance::D('UserLogin');
						foreach($fast as $item){
							if($item[1] == $ahash){
								$user = $usrlist
										->where(['uid' => $item[0]])
										->getUser();
								if(!$user){
									$this->modelError($usrlist);
									exit;
								} else{
									return true;
								}
							}
						}
						return false;
					}
				)
				->filter_callback('app_auth',
					function ($public) use (&$app){
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
					}
				)
				->getAll();
		$this->saveLoginState($user, $app);
	}

	private function saveLoginState(UserEntity $user, ApplicationEntity $app){
		$uol               = ThinkInstance::D('UserOnline');
		$saveData          = [];
		$saveData['time']  = time();
		$saveData['ip']    = explode(',', $app->bind_ip);
		$saveData['user']  = $user->uid;
		$saveData['email'] = $user->email;
		$saveData['ahash'] = md5(strtolower(trim($user->email)));
		$saveData['app']   = $app->public;

		foreach(ApplicationEntity::getPermissions() as $perm => $name){
			$saveData[$perm] = parse_permission((int)$app->$perm);
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
