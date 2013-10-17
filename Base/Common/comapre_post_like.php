<?php
function comapre_post_like($a, $b){
	return sample_string($a) == sample_string($b);
}

function sample_string($str){
	$str = preg_replace('/[0-9]+/', '', $str);
	return count_chars_unicode($str, true);
}

function count_chars_unicode($str, $x = false){
	$tmp = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
	$chr = [];
	foreach($tmp as $c){
		$chr[$c] = isset($chr[$c])? $chr[$c] + 1 : 1;
	}
	return is_bool($x)? ($x? $chr : count($chr)) : $chr[$x];
}
