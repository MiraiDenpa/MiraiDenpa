<?php
class InfoViewAction extends Action{
	final public function name($name){
		$mdl   = ThinkInstance::D('InfoEntry');
		$cur   = $mdl->find(['keyword' => $name]);
		$count = $cur->count();
		if($count <= 0){
			$this->assign('keyword', $name);
			$this->notfound();
			return;
		}
		if($count == 1){
			$this->assign('doc', $cur->getNext());
			$this->showpage();
			return;
		}
		$list = iterator_to_array($cur);
		$this->ambiguous($list);
	}

	final public function id($id, $page = 1){
		$mdl = ThinkInstance::D('InfoEntry');
		try{
			$doc = $mdl->getDocument($id);
		} catch(MongoException $e){
			$doc = null;
		}

		if($doc){
			$mdl             = ThinkInstance::D('InfoChapter');
			$doc['_chapter'] = $mdl->getChapterList($doc->_id);
			$this->assign('doc', $doc);
			$this->showpage();
		} else{
			$this->notfound();
		}
	}

	private function notfound(){
		http_response_code(404);
		$this->display('not_found');
	}

	private function showpage($type = 'common'){
		$this->display('entry_page_' . $type);
	}

	private function ambiguous($list){
		$this->assign('list', $list);
		$this->display('ambiguous');
	}
}
