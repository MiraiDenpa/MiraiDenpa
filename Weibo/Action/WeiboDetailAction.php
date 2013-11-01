<?php
/**
 * @default_method index
 * @class          WeiboDetailAction
 * @author         GongT
 */
class WeiboDetailAction extends Action{
	use UserAction;

	final public function show($id){
		$mdl   = ThinkInstance::D('Weibo');
		$weibo = $mdl->getWeiboById($id);
		if(!$weibo){
			$this->error(ERR_NF_WEIBO, $id);
		}
		$this->assign('weibo', $weibo->toArray());

		if(!isset($_GET['noreply'])){
			$mdl          = ThinkInstance::D('WeiboList');
			$mdl->perPage = 10;
			$reply        = $mdl->getReply($id, isset($_GET['rp'])? $_GET['rp'] : 1);
			$this->assign('reply', $reply);
		}

		$this->display('view_weibo');
	}
}
