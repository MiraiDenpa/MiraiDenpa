<?php
/**
 * User: GongT
 * Create On: 13-10-30 下午2:47
 *
 */
class WeiboListEntity extends Entity{
	public $list;
	public $nowPage;
	public $totalPage;
	public $totalRows;
	public $length;
	public $filter;
	public $sort;

	protected $idlist;

	private static $mdl;

	public function doQuery(){
		if(!self::$mdl){
			self::$mdl = ThinkInstance::D('WeiboList');
		}
		$cur = self::$mdl->find($this->filter)
				->sort($this->sort)
				->skip($this->nowPage*$this->length);

		$this->idlist = [];
		foreach($cur as $data){
			$weibo          = WeiboEntity::buildFromArray($data);
			$this->list[]   = $weibo;
			$this->idlist[] = $weibo->_id->{'$id'};
		}
	}

	public function buildList($page){
		if(!self::$mdl){
			self::$mdl = ThinkInstance::D('WeiboList');
		}
		$this->idlist = array_unique($this->idlist);
		$cur          = self::$mdl->find(['forward.type'    => 'mirai/denpa',
										 'forward.original' => ['$in' => $this->idlist]
										 ])
				->sort(['level' => 1]);
		foreach($cur)
	}

	public function buildTree(){
	}

	public function getPage(){
		return $this->p;
	}

	public function getList(){
		return $this->list;
	}
} 
