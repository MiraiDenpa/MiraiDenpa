<?php
/**
 * @default_method
 * @class          UserRelationAction
 * @author         GongT
 */
class RelationAction extends Action{
	use UserAuthedAction;

	protected $allow_public = false;
	/** @var UserRelationModel */
	private $mdl;

	/**
	 * @constructor
	 * @return void
	 */
	protected function __init(){
		$this->mdl = ThinkInstance::D('UserRelation');
	}

	final public function me(){
	}

	final public function my($target){
		$method = $this->dispatcher->request_method;
		$permission = $this->user['pm_relation'];
		

		//$method         = 'POST';
		$_POST['value'] = 'xxx';
		switch($method){
		case 'GET':
			if(!$permission[PERM_READ]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_READ);
			}
			$ret  = $this->mdl->getRelation($this->user['user'], $target);
			break;
		case 'POST':
			if(!$permission[PERM_UPDATE]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_UPDATE);
			}
			$ret = $this->mdl->updateRelation($this->user['user'], $target, $_POST['value']);
			break;
		case 'DELETE':
			if(!$permission[PERM_DELETE]){
				return $this->error(ERR_FAIL_PERMISSION, PERM_DELETE);
			}
			$ret = $this->mdl->removeRelationTo($this->user['user'], $target, $_POST['value']);
			break;
		}
		var_dump($ret,$this->mdl->getPage()->showArray());
	}

	final public function __call($user, $arg){
	}
}
