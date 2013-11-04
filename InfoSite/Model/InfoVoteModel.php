<?php
class InfoVoteModel extends Mongoo{
	protected $collectionName = 'vote';
	protected $connection = 'mongo-info';

	public function getByUser($uid){
	}

	public function vote($entryid, $uid, $voteArr){
		
	}
}
