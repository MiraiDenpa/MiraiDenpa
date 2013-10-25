<?php
/**
 * 用户ID必须是3-12位
 * 不能包含：
 *     $@,.
 * 不能是保留的
 * 第一位必须是字母或数字
 *
 * @param $uid
 *
 * @return bool
 */
function filter_uid_allow($uid){
	static $denys = array(
		'admin',
		'auth',
		'relation',
		'property',
		'proxy',
		'password',
		'login',
		'empty',
		'forget',
		'setting',
		'square',
		'search',
		'fallback',
		'empty',
		'blacklist',
		'follow',
		'follower',
		'friend',
		'group',
		'tag',
		'denpa',
		'dianbo',
		'user',
		'add',
		'get',
		'set',
		'post',
		'delete',
	);
	if(in_array(strtolower($uid), $denys)){
		return false;
	}

	return preg_match('/^[a-zA-Z0-9][\\!"#\\$%&\'\\(\\)\\*\\+,\\-\\.\\/0-9\\:;\\<\\=\\>\\?@A-Z\\[\\\\\\]\\^_`a-z\\{\\|\\}~]{2,11}$/', $uid);
}

/**
 * \!"#\$%&'\(\)\*\+,\-\./0-9\:;\<\=\>\?@A-Z\[\\\]\^_`a-z\{\|\}~
 */
