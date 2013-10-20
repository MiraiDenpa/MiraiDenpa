<?php
class WeiboCacheModel{
	public $data;

	public function channel($app, $channel, $page, &$pageobj){
		list($this->data, $pageobj) = CacheRead('WeiboCacheModel' . $app . '$' . $channel, $page);
		return $this->data;
	}

	public function setChannel($app, $channel, $page, $itr, $pageobj){
		$this->data = $itr;
		CacheWrite('WeiboCacheModel' . $app . '$' . $channel, $page, [$this->data, $pageobj], 0);
	}

	public function updateChannel($app, $channel){
		CacheClear('WeiboCacheModel' . $app . '$' . $channel);
	}

	public function user($user, $page, &$pageobj){
		list($this->data, $pageobj) = CacheRead('WeiboCacheModel' . $user, $page);
		return $this->data;
	}

	public function setUser($user, $page, $itr, $pageobj){
		$this->data = $itr;
		CacheWrite('WeiboCacheModel' . $user, $page, [$this->data, $pageobj], 0);
	}

	public function updateUser($user){
		CacheClear('WeiboCacheModel' . $user);
	}
}
