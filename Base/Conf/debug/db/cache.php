<?php
return array(
	'dsn' => 'memcached://127.0.0.1:11211', //没用
	'dbms' => 'memcache',
	'hosts' => [
		['127.0.0.1',11211]
	],
	'params'   => [
		Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_IGBINARY,
		Memcached::OPT_HASH=>Memcached::HASH_MD5,
		Memcached::OPT_BUFFER_WRITES => false,
		Memcached::OPT_BINARY_PROTOCOL => true,
	], // 数据库类构造选项
);
