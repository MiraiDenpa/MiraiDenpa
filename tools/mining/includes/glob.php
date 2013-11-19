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
	foreach(explode("\n", file_get_contents('active.lst')) as $lname){
		$lname = trim($lname);
		if(!$lname){
			continue;
		}else{
			$cb($lname);
		}
	}
}
