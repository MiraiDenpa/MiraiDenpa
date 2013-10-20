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
			$ret[$type] = [];
			foreach($itr as $item){
				$ret[$type][] = InfoEntryEntity::buildFromArray($item);
			}
		}
		$w   = array(
			'catalog' => TYPE_ANIME,
			'$or'     => [
				['broadcast_range.end' => ['$gt' => time()]],
				['broadcast_range.end' => 0]
			]
		);
		$itr = $this
				->find($w)
				->sort(['_hot' => -1])
				->limit(10);
		$ret[TYPE_BANGUMI] = [];
		foreach($itr as $item){
			$ret[TYPE_BANGUMI][] = InfoEntryEntity::buildFromArray($item);
		}
		return $ret;
	}
}
