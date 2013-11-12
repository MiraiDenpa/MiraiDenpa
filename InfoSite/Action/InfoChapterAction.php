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
		if(!$this->currentUser()->info_edit_chapter){
			$this->error(ERR_USER_PERM, 'info_edit_chapter');
			return;
		}
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
		$clist = $mdl->findOneById($_GET['id']);

		$this->assign('clist', $clist);
		$this->assign('id', $_GET['id']);
		$this->assign('name', $itr->getNext()['origin_name']);
		$this->display('index');
	}

	final public function save(){
		if(!$this->currentUser()->info_edit_chapter){
			$this->error(ERR_USER_PERM, 'info_edit_chapter');
			return;
		}
		if(!$_POST['id']){
			$this->error(ERR_INPUT_REQUIRE, 'id');
			return;
		}
		$mdl = ThinkInstance::D('InfoEntry');
		$itr = $mdl->findById($_POST['id']);
		if(!$itr->count()){
			$this->error(ERR_NF_TARGET, '文档不存在');
			return;
		}

		$mdl   = ThinkInstance::D('InfoChapter');
		$clist = $mdl->findOneById($_POST['id']);

		if(!$clist){
			$clist = ['_id' => new MongoId($_POST['id'])];
		}

		$clist['chapter'] = $_POST['list'];

		$ret = $mdl->save($clist);
		$success=$this->mongo_ret($ret,'修改成功！');
		if($success){
			$mdl=ThinkInstance::D('ChapterHistory');
			$mdl->add([
					  'id'=>$clist['_id']->{'$id'},
					  'time'=>time(),
					  'user'=>$this->token_data->user,
					  'data'=>serialize($clist['chapter']),
					  ]);
		}
	}
}
