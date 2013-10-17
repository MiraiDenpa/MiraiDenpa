<?php
/**
 * \!"#\$%&'\(\)\*\+,\-\./0-9\:;\<\=\>\?@A-Z\[\\\]\^_`a-z\{\|\}~
 */
function parse_weibo_at($content){
	preg_match_all('/@([a-zA-Z0-9][\\!-#%-\\+\\-\\/-\\?A-~]{2,11})/', $content, $mats);
	return array_map('trim', array_unique($mats[1]));
}
