<?php
require 'wikiboot.php';
require '../includes/curl.php';
require '../includes/output.php';
require '../includes/glob.php';
wiki_boot(0,
	function ($bid, $name){
		var_dump($bid,$name);
		
	});
