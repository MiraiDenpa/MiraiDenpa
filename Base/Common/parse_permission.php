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
