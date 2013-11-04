<?php
class InfoVoteModel extends Mongoo{
	protected $collectionName = 'vote';
	protected $connection = 'mongo-info';

	public function getVote($entryid, $uid){
		return $this->findOne(array(
								   '_id' => ['uid' => $uid, 'entry' => $entryid]
							  ));
	}

	public function vote($entryid, $uid, $voteArr){
		$allow = TaglibReplaceVote_catelog(true);
		foreach($voteArr as $key => $val){
			if(!in_array($key, $allow) || !is_numeric($val)){
				unset($voteArr[$key]);
			} else{
				$voteArr[$key] = floatval($val);
			}
		}
		$ret = $this->execute('return updateUserVote.apply(this,arguments)', [$entryid, $uid, $voteArr]);
		var_dump($ret);
		return $ret['retval'];
	}
}
