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
				$data->id = $_GET['id'];
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
		if(!$_POST['catalog']){
			return $this->error(ERR_INPUT_REQUIRE, 'catalog');
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

		// 处理OneOf的结果
		$data['classification'] = array_merge(array_keys($data['classification']),
											  array_values($data['classification']));

		$data['name'] = array_values($names);
		if(!isset($data['id']) || !$data['id']){
			$data['_id'] = new MongoId();
		} else{
			$data['_id'] = new MongoId($data['id']);
		}
		unset($data['id']);
		
		$data['_update'] = array(
			'user' => [$this->currentUser()->uid],
			'time' => time(),
		);

		foreach($data as $k => $v){
			if(is_string($v) && is_numeric($v)){
				if(intval($v) == $v){
					$data[$k] = intval($v);
				} else{
					$data[$k] = floatval($v);
				}
			}
		}

		ksort($data);
		try{
			$mdl = ThinkInstance::D('InfoEntry');

			$ret = $mdl->PreSavePage($data);
			if($ret['ok']){
				return $this->success('编辑成功，请等待管理员审核。');
			} else{
				return $this->error(ERR_NO_SQL, $ret['err']);
			}
		} catch(MongoException $e){
			return $this->error(ERR_NO_SQL, $e->getMessage());
		}
	}
}
