<?php
class SpecialUser{
	const TYPE_PUBLIC = 1;
}

function getPublicUser(){
	return array(
		'_id'        => 'public',
		'type'       => SpecialUser::TYPE_PUBLIC,
		'time'       => 0,
		'ip'         => '0.0.0.0',
		'user'       => '',
		'email'      => '030@dianbo.me',
		'ahash'      => md5('030@dianbo.me'),
		'app'        => '',
		'pm_user'    => array(
			'create' => false,
			'read'   => true,
			'update' => false,
			'delete' => false,
		),
		'pm_social'  => array(
			'create' => false,
			'read'   => true,
			'update' => false,
			'delete' => false,
		),
		'pm_file'    => array(
			'create' => false,
			'read'   => false,
			'update' => false,
			'delete' => false,
		),
		'pm_weibo'   => array(
			'create' => false,
			'read'   => true,
			'update' => false,
			'delete' => false,
		),
		'pm_account' => array(
			'create' => false,
			'read'   => false,
			'update' => false,
			'delete' => false,
		),
	);
}

