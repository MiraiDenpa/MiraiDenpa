<?php
/**
 * @default_method index
 * @class          InfoChapterAction
 * @author         GongT
 */
class InfoChapterAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;

	final public function edit(){
		if(!$_GET['id']){
			$this->error(ERR_INPUT_REQUIRE, 'id');
			return;
		}
		$mdl = ThinkInstance::D('InfoEntry');
		$itr = $mdl->findById($_GET['id'], ['origin_name' => true]);
		if(!$itr->count()){
			$this->error(ERR_NF_TARGET, '没有这个文档，是否忘记先保存基础信息？');
			return;
		}

		$mdl   = ThinkInstance::D('InfoChapter');
		$clist = $mdl->findById($_GET['id']);

		$this->assign('clist', $clist);
		$this->assign('id', $_GET['id']);
		$this->assign('name', $itr->getNext()['origin_name']);
		$this->display('index');
	}
}
