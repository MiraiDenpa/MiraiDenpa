#!/bin/php
<?php
if(!isset($argv[1])){
	die("参数是文件或者目录\n");
}

$loop_level = 0;
$hash_table = unserialize(@file_get_contents('.file_hash'));
if(!$hash_table){
	$hash_table = [];
}
// 初始化curl
$curl = curl_init();
curl_setopt($curl, CURLOPT_PUT, 1);
curl_setopt($curl, CURLOPT_USERPWD, "524702837:W2Awoi33klj434");
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_TIMEOUT, 60);
curl_setopt($curl, CURLOPT_NOPROGRESS, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, 'print_progress');

// 设置自动创建父级目录
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:", "mkdir: true"));

// 注册清理函数
register_shutdown_function('curl_close', $curl);
$esc = chr(27) . '[';
echo $esc . '7l';
register_shutdown_function("shutdown");
pcntl_signal(SIGTERM, "shutdown");
pcntl_signal(SIGINT, "shutdown");

function shutdown($signal = false){
	global $esc, $hash_table;
	static $called = false;
	if($called){
		return;
	}
	$called = true;
	echo $esc . 'c';
	echo $esc . '[J';
	file_put_contents('.file_hash', serialize($hash_table));
	exit;
}

$file = $argv[1];
loop($file);
// Program End 

function loop($path){
	static $level = -1;
	global $esc;
	$level++;
	$path = realpath($path);
	if(!$path){
		throw new Exception("\n找不到文件： {$path}\n");
	}
	if($level){
		echo "{$esc}2K" . str_repeat("\t", $level - 1);
	}
	if(is_dir($path)){
		echo "\t[+] {$path}\n";
		$d = dir($path);
		while($file = $d->read()){
			if($file == '.' || $file == '..'){
				continue;
			}
			loop($path . '/' . $file);
		}
		$d->close();
	} elseif(is_file($path)){
		echo " |\t{$path}";
		do_upload($path);
	}
	$level--;
}

function do_upload($file){
	global $curl, $esc;
	$file = pingFile($file);
	if(!$file){
		echo " -- SKIP\n";
		return;
	}

	// 保存文件到 demobucket 空间的根目录下
	$base = str_replace(__DIR__, '', $file);

	$url = 'http://v0.api.upyun.com/public-dianbo/' . $base;
	// 上传操作
	curl_setopt($curl, CURLOPT_URL, $url);
	// 本地待上传的文件
	$fp = fopen($file, 'r');
	// 设置待上传的内容
	curl_setopt($curl, CURLOPT_INFILE, $fp);
	// 设置待上传的长度
	curl_setopt($curl, CURLOPT_INFILESIZE, filesize($file));

	$code   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$result = curl_exec($curl);
	fclose($fp);

	echo " -- ";
	result($result);

	if($code == 200){
		pongFile($file);
	}
}

function result($message){
	global $esc;
	$message = str_replace("\r", "\n", $message);
	$message = str_replace("\n\n", "\n", $message);
	echo str_replace("\n", "{$esc}K\n", $message);
	$mover = count_chars($message, 1);
	moveup(@$mover[10]);
	echo "\n";
}

function moveup($n = 1){
	global $esc;
	echo "{$esc}{$n}A";
}

/**  */
function print_progress($ch, $download_size, $downloaded, $upload_size, $uploaded){
	global $esc;
	echo "{$esc}s 正在上传[CURL]: $downloaded/$download_size{$esc}K{$esc}u";
}

function pingFile($path){
	global $hash_table;
	$path = realpath($path);
	if(!$path){
		return false;
	}
	$path = str_replace(__DIR__, '', $path);
	if(!isset($hash_table[$path]) || $hash_table[$path] !== md5_file($path)){
		return $path;
	} else{
		return false;
	}
}

function pongFile($path){
	global $hash_table;
	$path = realpath($path);
	if(!$path){
		return;
	}
	$path = str_replace(__DIR__, '', $path);
	$hash_table[$path] = md5_file($path);
}
	

