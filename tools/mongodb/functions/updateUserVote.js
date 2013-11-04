function updateUserVote(entryid, $uid, $voteArr){
	var entry = db.getCollection('entry');
	var vote = db.getCollection('vote');
	var isempty = true;

	var $entryid = ObjectId(entryid);
	if(!entry.find({_id: $entryid}).count()){
		return {
			"ok"  : 0,
			"n"   : 0,
			"code": 99999,
			"err" : "DocumentNotExist: " + $entryid
		};
	}
	var doc_query = {_id: {uid: $uid, entry: entryid}};
	var user_vote, vote_name;
	for(i in $voteArr){
		isempty = false;
		break;
	}
	if(isempty){
		user_vote = vote.findAndModify({
			query : doc_query,
			remove: true,
			upsert: false
		});
	} else{
		user_vote = vote.findAndModify({
			query : doc_query,
			update: $voteArr,
			upsert: false
		});
	}
	if(!user_vote){
		user_vote = {};
		for(vote_name in $voteArr){
			doc_query[vote_name] = $voteArr[vote_name];
		}
		vote.save(doc_query);
	}
	delete user_vote['_id'];

	var vote_good = {};
	var vote_bad = {};
	var vote_set = {};
	for(vote_name in user_vote){
		if(!$voteArr.hasOwnProperty(vote_name)){//以前已经评过的分现在没了（设为0还算有）
			vote_set[vote_name] = -1;
			$voteArr[vote_name] = 0;
		}
	}
	for(vote_name in $voteArr){
		if(vote_name.substr(0, 1) == '_'){
			continue;
		}
		var new_value = $voteArr[vote_name];
		var old_value;
		if(user_vote.hasOwnProperty(vote_name)){// 现在评分以前已经评过
			old_value = user_vote[vote_name]
		} else{ // 没评过，添加新的分数
			old_value = 0;
			vote_set[vote_name] = 1;
		}
		if(new_value == old_value){
			continue;
		}
		if(old_value >= 0 && new_value >= 0){ // 两个都正数（valuebar必然的）
			vote_good[vote_name] = parseInt(10*(new_value - old_value))/10;
		} else if(old_value <= 0 && new_value <= 0){ // 两次都差评
			vote_bad[vote_name] = parseInt(10*(old_value - new_value))/10;
		} else if(old_value < 0 && new_value > 0){ // 以前是差评，改成好评
			vote_bad[vote_name] = parseInt(10*(old_value))/10;
			vote_good[vote_name] = parseInt(10*(new_value))/10;
		} else if(old_value > 0 && new_value < 0){ // 以前是好评，改成差评
			vote_good[vote_name] = -parseInt(10*(old_value))/10;
			vote_bad[vote_name] = -parseInt(10*(new_value))/10;
		}
	}
	
	isempty = true;
	var incList = {}, i;
	for(i in vote_good){
		incList['_vote.' + i + '.good'] = vote_good[i];
		isempty = false;
	}
	for(i in vote_bad){
		incList['_vote.' + i + '.bad'] = vote_bad[i];
		isempty = false;
	}
	for(i in vote_set){
		incList['_vote.' + i + '.count'] = vote_set[i];
		isempty = false;
	}

	if(isempty){
		return {
			"ok" : 1,
			"n"  : 0,
			"err": "",
			"msg": "empty update (not change)"
		};
	}

	entry.update({_id: $entryid}, {$inc: incList});
	return db.getLastErrorObj();
}
