<?php
class WeiboEntity extends Entity{
	public $_id;
	public $content;

	/** @var ForwardEntity */
	public $forward;
	public $sendto;
	public $channel;
	public $at;
	public $beforwardcount = 0;
	public $level = 0;

	public $app;
	public $user;
	public $time;

	public $list;

	public function toArray(){
		$data = get_object_vars($this);
		unset($data['list']);
		return $data;
	}

	public function buildList($full){
		static $mdl;
		if(!$mdl){
			$mdl = ThinkInstance::D('WeiboList');
		}
		$itr        = $mdl->find(['forward.type' => 'mirai/denpa', 'forward.original' => (string)$this->_id])
				->sort(['time' => -1]);
		$this->list = [];
		foreach($itr as $wb){
			$this->list[] = WeiboEntity::buildFromArray($wb);
		}
		return $this;
	}

	protected function _init(){
		if(!$this->forward){
			return;
		}
		$this->forward = ForwardEntity::buildFromArray($this->forward);
	}
}
