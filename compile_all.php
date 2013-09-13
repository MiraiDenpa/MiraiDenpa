#!/bin/php
<?php
if(PHP_SAPI != 'cli'){
	exit("请用CLI方式执行");
}
$e = chr(27);

if(0 !== posix_getuid()){
	$cmd = 'sudo php ';
	foreach($argv as $arg){
		$cmd .= ' ' . escapeshellarg($arg);
	}
	echo("{$e}[38;5;9m权限提升...{$e}[0m\n");
	passthru($cmd, $ret);
	exit($ret);
}

$compile_folder = glob(__DIR__ . '/*/compile.php');

$count = count($compile_folder) - 1;
foreach($compile_folder as $num => $file){
	$folder = dirname($file);
	chdir($folder);
	echo "{$e}[38;5;9m正在编译 {$folder}: {$e}[0m\n";
	passthru('chmod a+x ./compile.php');
	if($num == $count){
		passthru('./compile.php');
	}else{
		passthru('./compile.php -n');
	}
	echo "{$e}[38;5;9m编译完毕 {$folder}。{$e}[0m\n";
}
