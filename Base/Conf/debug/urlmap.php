<?php
$GLOBALS['URL_MAP'] = array(
	'user' => 'user.mirai.localdomain',
	'home'  => 'u.mirai.localdomain',
	'app'  => 'app.mirai.localdomain',
	'info' => 'info.mirai.localdomain',
	'www'  => 'www.mirai.localdomain',
	'weibo' => 't.mirai.localdomain',
	'lafi'  => 'lafi.mirai.localdomain',
);
$GLOBALS['DOMAIN_MAP'] = array(
	'user'  => 'mirai.localdomain',
	'home'  => 'mirai.localdomain',
	'app'   => 'mirai.localdomain',
	'info'  => 'mirai.localdomain',
	'www'   => 'mirai.localdomain',
	'weibo' => 'mirai.localdomain',
	'lafi'  => 'mirai.localdomain',
);

require dirname(__DIR__).'/urls.php';
