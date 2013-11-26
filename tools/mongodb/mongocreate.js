"use strict";

var files = ls('databases');
var map = {};
var conlist = {};

// 登录管理员
function auth_admin(db_conn){
	var db = db_conn.getDB('admin');
	db.auth('admin', 'admin');
}

for(var i = 0; i < files.length; i++){
	var dbname = files[i].match(/^databases\/(.*)\.json$/);
	if(!dbname || !dbname[1]){
		print('错误： 不能解析数据库名 \n\t' + files[i]);
		quit();
	}
	dbname = dbname[1];
	map[dbname] = fetch_json_file(files[i]);

	// 函数列表
	if(map[dbname].functions && map[dbname].functions.length){
		var functions = {};
		for(var j = 0; j < map[dbname].functions.length; j++){
			var fname = map[dbname].functions[j];
			var fn = cat('functions/' + fname + '.js');
			if(!fn){
				print('错误： 不能解析数据库名 \n\t' + files[i]);
				quit();
			}
			eval('functions.' + fname + '=' + fn + ';');
		}
		map[dbname].functions = functions;
	}
}
for(var name in map){
	fetch_db(name, map[name]);
}

function fetch_json_file(fn){
	var json = cat(fn);
	try{
		json = JSON.parse(json);
	} catch(e){
		print('错误： mongo.json解析失败 \n\t' + e.message);
		quit();
	}
	return json;
}

function fetch_db(name, db_define){
	var i , log;
	print('数据库' + name);
	var link;
	if(conlist[name]){
		link = conlist[name];
	} else{
		conlist[name] = link;
		link = new Mongo(db_define.db);
		auth_admin(link);
	}
	var db = link.getDB(name);

	// 处理用户名
	if(db_define.user){
		var user_collect = db.getCollection("system.users");
		for(i = 0; i < db_define.user.length; i++){
			if(db.auth(db_define.user[i].user, db_define.user[i].pwd)){
				continue; // 存在的跳过
			}
			if(user_collect.find({"user": db_define.user[i].user}).count()){
				print("\t用户密码变更： " + db_define.user[i].user + " -> " + db_define.user[i].pwd);
				db.changeUserPassword(db_define.user[i].user, db_define.user[i].pwd);
			} else{
				print("\t添加用户： " + db_define.user[i].user + " -> " + db_define.user[i].pwd);
				db.addUser(db_define.user[i]);
			}
		}
	}

	// 现有集合
	var collections = db.getCollectionNames();
	for(i = 0; i < collections.length; i++){
		if(collections[i].indexOf('system') === 0){ // system开头的集合直接跳过
			collections.splice(i, 1);
			i--;
		}
	}
	print('\t现有集合：' + collections.join(', '));

	// 循环该集合的所有index
	var expect = db_define.indexes;
	for(var collection_name in expect){
		print('\t集合：' + collection_name);
		if(collections.indexOf(collection_name) < 0){
			// 如果现有集合中不存在
			db.createCollection(collection_name);
			print('\t\t首次建立');
		}
		var collection = db.getCollection(collection_name);

		// 处理index
		var expect_indexes = expect[collection_name];
		var current_indexes = collection.getIndexes();
		var indexes = {};
		log = [];
		for(i = 0; i < current_indexes.length; i++){
			// 过滤名字是id的index，其他index存到 名字为key 值为true 的数组中
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
				// 如果是现有的index，则跳过（更新key还需要手动删除之）
				delete(indexes[index_name]);
				continue;
			}
			// 否则添加它
			var index_options = expect_indexes[index_name];
			var index_type = index_options.type;
			delete index_options.type;
			index_options.name = index_name;
			print('\t\t添加index：' + index_name);
			var tmp = {};
			tmp[index_name] = index_type;
			collection.ensureIndex(tmp, index_options)
		}
		// 删除所有没被定义的index， indexes来自当前集合，所以不会影响系统index
		for(index_name in indexes){
			print('\t\t删除index：' + index_name);
			collection.dropIndex(index_name + '_1');
		}
	}
	// 循环该集合的所有index END

	// 处理用户函数
	if(db_define.functions){
		for(i in db_define.functions){
			print('\t添加函数：' + i);
			db.system.js.save({ _id: i, value: db_define.functions[i]});
		}
	}
}
