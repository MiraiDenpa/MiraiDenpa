<?php
return array(
	'hostname' => '', // 服务器地址
	'hostport' => '', // 端口
	'database' => '', // 数据库名
	'dsn'      => 'mysql:host=localhost;dbname=infosite', // 连接字符串（优先于上面的）
	'username' => 'root', // 用户名
	'password' => 'Ezreal_LOL', // 密码
	'dbms'     => 'pdo', // 数据库类型
	'params'   => [PDO::ATTR_PERSISTENT => true], // 数据库类构造选项
);
