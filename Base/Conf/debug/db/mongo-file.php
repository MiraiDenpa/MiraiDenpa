<?php
return array(
	'dsn'    => 'mongodb:///var/run/mongodb/mongodb-27017.sock', // 连接字符串（优先于上面的）
	'dbms'   => 'mongo', // 数据库类型
	'params' => [
		'db'       => 'const_upload', // 数据库名
		'username' => 'api_user', // 用户名
		'password' => 'api_user', // 密码
	], // 数据库类构造选项
);
