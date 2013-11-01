<?php
class WeiboListModel extends Mongoo{
	protected $collectionName = 'content';
	protected $connection = 'mongo-weibo';

	/** @var  WeiboCacheModel */
	protected $cacheObj;

	public $perPage = 10;

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
			//return $this->cacheObj->data;
		}
		$itr = $this->find([
						   'app'          => $app,
						   'channel'      => $channel,
						   'forward.type' => ['$ne' => 'mirai/denpa']
						   ])
				->sort(['time' => -1]);
		$this->pageCursor($itr, $page);
		$data = [];
		foreach($itr as $weibo){
			$wb       = WeiboEntity::buildFromArray($weibo);
			$cur      = $this->find(['forward.type' => 'mirai/denpa', 'forward.original' => $wb->_id->{'$id'}])
					->sort(['time' => -1])
					->limit(5);
			$wb->list = iterator_to_array($cur, false);
			$data[]   = $wb;
		}

		$this->cacheObj->setChannel($app, $channel, $page, $data, $this->page);
		return $data;
	}

	public function getReply($wid, $page){
		if($this->cacheObj->weiboreply($wid, $page, $this->page)){
			//return $this->cacheObj->data;
		}
		$itr = $this->find(['forward.type' => 'mirai/denpa', 'forward.original' => $wid])
				->sort(['time' => -1]);
		$this->pageCursor($itr, $page);

		$list = iterator_to_array($itr, false);
		$this->cacheObj->setWeiboreply($wid, $page, $list, $this->page);

		return $list;
	}
}
