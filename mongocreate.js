"use strict";

var i, log;
var json = cat('mongo.json');
try{
	var db_defines = JSON.parse(json);
} catch(e){
	print('错误： mongo.json解析失败 \n\t' + e.message);
	quit();
}

//var conn = new Mongo();
var conlist = {};
for(var name in db_defines){
	print('数据库' + name);
	var def = db_defines[name];
	var link;
	if(conlist[name]){
		link = conlist[name];
	} else{
		conlist[name] = link;
		link = new Mongo(def.db);
	}
	var db = link.getDB(name);
	if(def.user){
		for(i = 0; i < def.user.length; i++){
			if(!db.auth(def.user[i].user, def.user[i].pwd)){
				if(db.getCollection("system.users").find({"user": def.user[i].user}).count()){
					print("\t用户密码变更： " + def.user[i].user + " -> " + def.user[i].pwd);
					db.changeUserPassword(def.user[i].user, def.user[i].pwd);
				} else{
					print("\t添加用户： " + def.user[i].user + " -> " + def.user[i].pwd);
					db.addUser(def.user[i]);
				}
			}
		}
	}

	var collections = db.getCollectionNames();
	for(i = 0; i < collections.length; i++){
		if(collections[i].indexOf('system') === 0){
			collections.splice(i, 1);
			i--;
		} else{
		}
	}
	print('\t现有集合：' + collections.join(', '));

	var expect = def.indexes;
	for(var collection_name in expect){
		print('\t集合：' + collection_name);
		if(collections.indexOf(collection_name) < 0){
			db.createCollection(collection_name);
			print('\t\t首次建立');
		}
		var collection = db.getCollection(collection_name);
		var expect_indexes = expect[collection_name];
		var current_indexes = collection.getIndexes();
		var indexes = {};
		log = [];
		for(i = 0; i < current_indexes.length; i++){
			if(current_indexes[i].key.hasOwnProperty("_id")){
				current_indexes.splice(i, 1);
				i--;
			} else{
				for(var iname in current_indexes[i].key){
					indexes[iname] = true;
					log.push(iname);
				}
			}
		}
		print('\t\t现有index：' + log.join(', '));

		for(var index_name in expect_indexes){
			if(indexes[index_name]){
				delete(indexes[index_name]);
				continue;
			}
			var index_options = expect_indexes[index_name];
			var index_type = index_options.type;
			delete index_options.type;
			index_options.name = index_name;
			print('\t\t添加index：' + index_name);
			var tmp = {};
			tmp[index_name] = index_type;
			collection.ensureIndex(tmp, index_options)
		}
		for(index_name in indexes){
			print('\t\t删除index：' + index_name);
			collection.dropIndex(index_name + '_1');
		}
	}
}
//db = conn.getDB("core");
