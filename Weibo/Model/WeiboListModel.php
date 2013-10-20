<?php
class WeiboListModel extends Mongoo{
	protected $collectionName = 'content';
	protected $connection = 'mongo-weibo';

	/** @var  WeiboCacheModel */
	protected $cacheObj;

	public function _initialize($arg1, $arg2){
		$this->cacheObj = ThinkInstance::D('WeiboCache');
	}

	/**
	 *
	 * @param string $app
	 * @param string $channel
	 * @param string $page
	 *
	 * @return MongoCursor
	 */
	public function getChannel($app, $channel, $page){
		if($page < 1){
			$page = 1;
		}
		if($this->cacheObj->channel($app, $channel, $page, $this->page)){
			return $this->cacheObj->data;
		}
		$itr = $this->find([
						   'app'          => $app,
						   'channel'      => $channel,
						   'forward.type' => ['$ne' => 'mirai/denpa']
						   ]
		);
		$this->pageCursor($itr, $page);
		$data = [];
		foreach($itr as $weibo){
			$wb       = WeiboEntity::buildFromArray($weibo);
			$wb->tree = $wb->buildTree();
			$data[]   = $wb;
		}

		$this->cacheObj->setChannel($app, $channel, $page, $data, $this->page);
		return $data;
	}
}
