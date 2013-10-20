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

	public function buildTree(){
		static $mdl;
		if(!$mdl){
			$mdl = ThinkInstance::D('WeiboList');
		}
		$itr = $mdl
				->find(['forward.type' => 'mirai/denpa', 'forward.original' => (string)$this->_id])
				->sort(['level' => 1]);
		$ret = [];
		foreach($itr as $wb){
			$ret[] = WeiboEntity::buildFromArray($wb);
		}
		return $ret;
	}

	protected function _init(){
		if(!$this->forward){
			return;
		}
		$this->forward = ForwardEntity::buildFromArray($this->forward);
	}
}
