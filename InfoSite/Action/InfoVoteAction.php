<?php
/**
 * @default_method index
 * @class          InfoVoteAction
 * @author         GongT
 */
class InfoVoteAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;

	final public function index(){
		if($this->dispatcher->request_method == 'POST'){
			$this->uservote();
		} else{
			if(!isset($_GET['id'])){
				$this->error(ERR_INPUT_REQUIRE, 'id');
				return;
			}

			$ret = ThinkInstance::D('InfoVote')
					->getVote($_GET['id'], $this->token_data->user);

			$this->assign('vote', $ret? $ret : []);
			$this->display('!data');
		}
	}

	private function uservote(){
		if(!isset($_POST['id'])){
			$this->error(ERR_INPUT_REQUIRE, 'id');
			return;
		}
		if(!isset($_POST['vote'])){
			$this->error(ERR_INPUT_REQUIRE, 'vote');
			return;
		}
		$id  = $_POST['id'];
		$mdl = ThinkInstance::D('InfoVote');

		//$ret = $mdl->vote($id, $this->token_data->user, []);
		$ret = $mdl->vote($id, $this->token_data->user, $_POST['vote']);
		$this->mongo_ret($ret,"保存成功～");
	}
}
