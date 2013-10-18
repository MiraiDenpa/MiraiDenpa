<?php
class InfoListModel extends Mongoo{
	protected $collectionName = 'entry';
	protected $connection = 'mongo-info';
	public static $types = [
		TYPE_ANIME,
		TYPE_COMIC,
		TYPE_GAME,
		TYPE_NOVEL,
		TYPE_MUSIC,
		TYPE_DRAMA,
		TYPE_PERIO,
	];

	public function getHpTopList(){
		$ret = [];
		foreach(self::$types as $type){
			$itr        = $this
					->find(['catalog' => $type])
					->sort(['_update' => -1])
					->limit(10);
			$ret[$type] = iterator_to_array($itr, false);
		}
		return $ret;
	}
}
