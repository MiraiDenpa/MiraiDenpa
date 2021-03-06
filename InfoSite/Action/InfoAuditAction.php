<?php
/**
 * @default_method index
 * @class          InfoIndexAction
 * @author         GongT
 */
class InfoAuditAction extends Action{
	final public function index(){
		$mdl   = ThinkInstance::D('InfoEntry');
		$cur   = $mdl->getChangedList();
		$g     = '$id';
		$array = [];
		while($cur->hasNext()){
			$item       = $cur->getNext();
			$item['id'] = $item['_id']->$g;
			$array[]    = $item;
		}

		$this->assign('count', $cur->count());
		$this->assign('list', $array);
		$this->display('list');
	}

	final public function edit(){
		session_start();
		$_SESSION['check'] = md5(time() . rand());
		$this->assign('check', $_SESSION['check']);

		if(!isset($_GET['id']) || !$_GET['id']){
			$this->error(ERR_INPUT_REQUIRE, 'id');
			return;
		}
		$mdl         = ThinkInstance::D('InfoEntry');
		$data        = $mdl->getDocument($_GET['id']);
		$item        = $mdl->getChangedById($_GET['id']);
		$item['_id'] = (string)$item['_id'];

		if(!$data->_change){
			$this->error(ERR_MISCELLANEOUS, '这个ID下没有未提交的更改', UI('index'));
			return;
		}

		$this->assign('item', $item);
		$this->assign('data', $data);
		$this->display('edit');
	}

	final function loadjson(){
		$id   = $_GET['id'];
		$mdl  = ThinkInstance::D('InfoEntry');
		$data = $mdl->getDocument($id);
		echo json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
	}

	final function do_update(){
		$force = isset($_POST['_force'])? !!$_POST['_force'] : false;
		unset($_POST['_force']);
		session_start();
		if(!$_GET['hash'] || $_SESSION['check'] != $_GET['hash']){
			$this->error(ERR_INPUT_DENY, 'hash mismatch');
			return;
		}
		unset($_SESSION['check']);

		$updateUsers = $_POST['_update_users'];
		unset($_POST['_update_users']);
		if(empty($updateUsers)){
			$this->error(ERR_INPUT_REQUIRE, '_update_users');
			return;
		}
		$id = $_POST['id'];
		unset($_POST['id']);
		if(empty($id)){
			$this->error(ERR_INPUT_REQUIRE, 'id');
			return;
		}

		$data = $_POST;
		foreach($data as $k => $v){
			if($k{0} == '_'){
				continue;
			}
			if(is_array($v) && isset($v[0])){
				$data[$k] = array_values($v);
			}
		}

		$save  = ThinkInstance::D('InfoEntry');
		$odata = $save->getDocument($id);
		if($_GET['time'] < $odata['_update']['time'] && !$force){
			$this->error(ERR_TIMEOUT, '操作超时，可能其他人修改了本页。点击更新当前状态检查。');
			return;
		}
		foreach($odata as $k => $v){
			if($k{0} == '_' && $k != '_id'){
				$data[$k] = $v;
			}
		}
		$data['_update'] = array(
			'user' => $updateUsers,
			'time' => time(),
		);
		unset($data['_change']);
		$save->cast_entry($data);

		$ret = $save->update(['_id' => new MongoId($id)], $data);
		if($ret['ok']){
			unset($data['_update']);
			$logData = array(
				'id'    => $id,
				'time'  => time(),
				'users' => serialize($updateUsers),
				'data'  => serialize($data),
			);
			$log     = ThinkInstance::D('InfoHistory');
			$ret     = $log->add($logData);
			if($ret){
				$this->success('编辑成功，已经生效，请关闭页面或后退到列表。', ['后退到列表', UI('index')], 0);
			} else{
				$this->error('编辑成功，已经生效，但编辑历史保存失败：' . $log->getDbError() . $log->getError());
			}
		} else{
			$this->error(ERR_NO_SQL, $ret['err']);
		}
	}

	final function do_delete(){
		session_start();
		if(!$_GET['hash'] || $_SESSION['check'] != $_GET['hash']){
			$this->error(ERR_INPUT_DENY, 'hash mismatch');
			return;
		}
		unset($_SESSION['check']);

		if(empty($_GET['id'])){
			$this->error(ERR_INPUT_REQUIRE, 'id');
			return;
		}
		$id = $_GET['id'];

		$save  = ThinkInstance::D('InfoEntry');
		$odata = $save->discardChange($id);
		$this->mongo_ret($odata, '', UI('index'));
	}
}
