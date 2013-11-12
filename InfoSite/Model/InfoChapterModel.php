<?php
class InfoChapterModel extends Mongoo{
	protected $collectionName = 'chapter';
	protected $connection = 'mongo-info';

	public function getChapterList(MongoId $doc_id){
		$list = $this->findOne(['_id' => $doc_id]);
		if($list){
			return $list['chapter'];
		} else{
			return [];
		}
	}
}
