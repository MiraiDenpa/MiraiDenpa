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
			$id = $_GET['id'];
			$this->assign('vote', []);
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
		$id    = $_POST['id'];
		$vote  = $_POST['vote'];
		$allow = TaglibReplaceVote_catelog(true);
		foreach($vote as $key => $val){
			if(!in_array($key, $allow) || !is_numeric($val)){
				unset($vote[$key]);
			} else{
				$vote[$key] = floatval($val);
			}
		}

		$mdl = ThinkInstance::D('InfoVote');
		$ret=$mdl->vote($id, $this->token_data->_id, $vote);
		var_dump($ret);
	}
}
