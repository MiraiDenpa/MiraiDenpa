<?php
/**
 * @default_method index
 * @class          InfoUploadAction
 * @author         GongT
 */
class InfoUploadAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;

	final public function index(){
		if($_GET['id']){
			$mdl  = ThinkInstance::D('InfoEntry');
			$data = $mdl->getDocument($_GET['id']);
			if($data){
				$this->assign('data', $data);
			}
		}
		$this->display('index');
	}

	final public function submit(){
		foreach($_POST as $name => $value){
			if($name != '_id' && $name{0} == '_'){
				return $this->error(ERR_INPUT_DENY, $name);
			}
		}
		// 获取必须字段
		if(!$_POST['origin_name']){
			return $this->error(ERR_INPUT_REQUIRE, 'origin_name');
		}
		// 处理name，吧origin放在第一位
		$names = isset($_POST['name'])? $_POST['name'] : [];
		if(isset($_POST['origin_name'])){
			array_unshift($names, $_POST['origin_name']);
			$names = array_unique($names);
		}
		$data = $_POST;

		// 处理日期
		foreach($data['broadcast_range'] as $k => $v){
			$data['broadcast_range'][$k] = strtotime($v);
		}

		$data['name'] = $names;
		if(!isset($data['id'])){
			$data['_id'] = new MongoId();
		}

		$data['_update'] = array(
			'user' => $this->currentUser()->uid,
			'time' => time(),
		);

		$mdl = ThinkInstance::D('InfoEntry');
		ksort($data);
		try{
			$ret = $mdl->PreSavePage($data);
			if($ret['ok']){
				$this->success('编辑成功，请等待管理员审核。');
			} else{
				$this->error(ERR_NO_SQL, $ret['err']);
			}
		} catch(MongoException $e){
			return $this->error(ERR_NO_SQL, $e->getMessage());
		}
	}
}
