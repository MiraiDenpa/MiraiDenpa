<?php
class WeiboListAction extends Action{
	use UserAction;

	final public function index(){
		var_dump('显示首页');
	}

	final public function square(){
		var_dump('显示广场');
	}

	final public function user($uid){
		var_dump('显示用户的微博列表', $uid);
	}

	final public function channel($channel){
		$login = $this->doLogin(false);
		if($login === ERR_NO_ERROR){
			$app = $this->token_data->app;
		} elseif($_GET['app']){
			$app = $_GET['app'];
		} else{
			$this->error(ERR_INPUT_REQUIRE, 'app or token');
			return;
		}
		if(isset($_GET['p'])){
			$p = $_GET['p'];
			if($p < 1){
				$this->error(ERR_RANGE_PAGE, $p);
				return;
			}
		} else{
			$p = 1;
		}
		$listmdl      = ThinkInstance::D('WeiboList');
		$ret          = $listmdl->getChannel($app, $channel, $p);
		$listmdl->url = UI(METHOD_NAME, ['p' => '__PAGE__']);

		$this->assign('list', $ret);
		$this->assign('page', $listmdl->getPage());
		$this->assign('code', ERR_NO_ERROR);
		
		$this->display(':list/default.html');
	}
}
