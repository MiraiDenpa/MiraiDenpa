#!/bin/php
<?php
if(!isset($argv[1])){
	die("参数是文件或者目录\n");
}

$file = $argv[1];
$process = 0;
if(is_dir($file)){
	function loop($dir){
		echo("处理文件夹： {$dir}\n");
		$path = realpath($dir);
		if(!$path){
			die("不能把文件名正常化： {$dir}\n");
		}
		if(is_file($path)){
			echo("找到文件： {$path}\n");
			do_upload($path);
		} elseif(is_dir($path)){
			echo("找到目录： {$path}\n");
			$ext = glob(realpath($dir) . '/*');
			array_map(__FUNCTION__, $ext);
		} else{
			die("既不是文件也不是文件夹： {$dir}\n");
		}
	}
	loop($file);

	exit;
} elseif(is_file($file)){
	do_upload($file);
}else{
	die("找不到文件： {$file}\n");
}

function do_upload($file){
	$base = str_replace(__DIR__, '', $file);

	// 保存文件到 demobucket 空间的根目录下
	$curl = curl_init('http://v0.api.upyun.com/public-dianbo/' . $base);

	echo "正在将文件 $base 上传到又拍云。\n";

	// 上传操作
	curl_setopt($curl, CURLOPT_PUT, 1);
	curl_setopt($curl, CURLOPT_USERPWD, "524702837:W2Awoi33klj434");
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	curl_setopt($curl, CURLOPT_NOPROGRESS, false);

	// 本地待上传的文件
	$fp = fopen($file, 'r');

	// 设置待上传的内容
	curl_setopt($curl, CURLOPT_INFILE, $fp);

	// 设置待上传的长度
	curl_setopt($curl, CURLOPT_INFILESIZE, filesize($file));

	// 设置自动创建父级目录
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:", "mkdir: true"));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	var_dump(curl_exec($curl));

	curl_close($curl);
	fclose($fp);

	echo "\n";
}
