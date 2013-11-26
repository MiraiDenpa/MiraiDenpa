<?php
function list_dir($dir){
	$d   = dir($dir);
	$ret = [];
	if($d){
		while($file = $d->read()){
			$ret[] = $file;
		}
	}
	return $ret;
}

/**  */
function boot(callable $cb){
	chdir(dirname(__DIR__));
	foreach(explode("\n", file_get_contents('active.lst')) as $lname){
		$lname = trim($lname);
		if(!$lname){
			continue;
		} else{
			$cb($lname);
		}
	}
}

function eacape_filename($name){
	$name = preg_replace('#\s*([' . preg_quote('\\/:*?"<>|', '#') . '])\s*#', '$1', trim($name));
	$name = preg_replace('#\s+#', '_', $name);
	return str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'],
					   ['＼', '／', '：', '＊', '？', '＂', '＜', '＞', '｜'],
					   $name);
}
