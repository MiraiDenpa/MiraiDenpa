<?php
function parse_permission($oct){
	$perm = intval($oct);
	return [
		PERM_CREATE => (bool)($perm&8),
		PERM_READ   => (bool)($perm&4),
		PERM_UPDATE => (bool)($perm&2),
		PERM_DELETE => (bool)($perm&1),
	];
}

function parse_permission_html($name, $oct, $class){
	$perm = intval($oct);
	if(!$perm){
		return;
	}
	$ret = '<li class="permission_show '.$class.'">';
	$ret .= '<div class="title">' . $name . '</div><ul>';
	if((bool)($perm&8)){ // Create
		$ret .= '<li class="pm_create">增加</li>';
	}
	if((bool)($perm&4)){ // Read
		$ret .= '<li class="pm_read">读取</li>';
	}
	if((bool)($perm&2)){ // Update
		$ret .= '<li class="pm_update">修改</li>';
	}
	if((bool)($perm&1)){ // Delete
		$ret .= '<li class="pm_delete">删除</li>';
	}
	$ret .= '</ul></li>';
	return $ret;
}
