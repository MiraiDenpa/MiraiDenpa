<?php
require 'wikiboot.php';
require '../includes/curl.php';
require '../includes/output.php';
require '../includes/glob.php';

$curl = new BangumiCurl('http://www.wikipedia.org/');

wiki_boot(1,
	function ($bid, $name) use (& $curl){
		$url = 'http://zh.wikipedia.org/zh-cn/' . $name;
		$doc = $curl->get($url);
		return [$curl->code, $doc];
	});
