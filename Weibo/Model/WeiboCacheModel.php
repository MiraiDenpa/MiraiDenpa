<?php
class WeiboCacheModel{
	public $data;

	public function channel($app, $channel, $page, &$pageobj){
		list($this->data, $pageobj) = CacheRead('WeiboCacheChannel' . $app . '$' . $channel, $page);
		return $this->data;
	}

	public function setChannel($app, $channel, $page, $itr, $pageobj){
		$this->data = $itr;
		CacheWrite('WeiboCacheChannel' . $app . '$' . $channel, $page, [$this->data, $pageobj], 0);
	}

	public function updateChannel($app, $channel){
		CacheClear('WeiboCacheChannel' . $app . '$' . $channel);
	}

	public function weiboreply($wid, $page, &$pageobj){
		list($this->data, $pageobj) = CacheRead('WeiboCacheWeiboReply' . $wid, $page);
		return $this->data;
	}

	public function setWeiboreply($wid, $page, $itr, $pageobj){
		$this->data = $itr;
		CacheWrite('WeiboCacheWeiboReply' . $wid, $page, [$this->data, $pageobj], 0);
	}

	public function updateWeiboreply($wid){
		CacheClear('WeiboCacheWeiboReply' . $wid);
	}

	public function user($user, $page, &$pageobj){
		list($this->data, $pageobj) = CacheRead('WeiboCacheUser' . $user, $page);
		return $this->data;
	}

	public function setUser($user, $page, $itr, $pageobj){
		$this->data = $itr;
		CacheWrite('WeiboCacheUser' . $user, $page, [$this->data, $pageobj], 0);
	}

	public function updateUser($user){
		CacheClear('WeiboCacheUser' . $user);
	}
}
