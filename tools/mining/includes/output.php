<?php
global $file;
$file = 'main';
/**  */
function log_to_file($basename){
	global $file;
	$file = $basename;
}

/**  */
function success_and_log($msg, $data = []){
	global $file;
	$message = success($msg, $data);
	file_put_contents('log/' . $file . '-process.log', $message . "\n", FILE_APPEND);
}

/**  */
function error_and_log($msg, $data = []){
	global $file;
	$message = error($msg, $data);
	file_put_contents('log/' . $file . '-process.log', $message . "\n", FILE_APPEND);
	file_put_contents('log/' . $file . '-error.log', $message . "\n", FILE_APPEND);
}

global $error;
global $success;
global $normal;
global $reset;
$error   = chr(27) . '[1;38;5;9m';
$success = chr(27) . '[1;38;5;10m';
$normal = chr(27) . '[1m';
$reset   = chr(27) . '[0m';
/**  */
function error($msg, $data = []){
	global $error, $reset;
	$msg = date('[Y-m-d H:i:s]') . ' ' . $error . $msg . $reset . "\n";
	foreach($data as $k => $v){
		$msg .= "\t" . $k . "：" . $v . "\n";
	}
	fwrite(STDERR, $msg);
	return str_replace([$error, $reset], '', $msg);
}

/**  */
function success($msg, $data = []){
	global $success, $reset;
	$msg = date('[Y-m-d H:i:s]') . ' ' . $success . $msg . $reset . "\n";
	foreach($data as $k => $v){
		$msg .= "\t" . $k . "：" . $v . "\n";
	}
	echo $msg;
	return str_replace([$success, $reset], '', $msg);
}

/**  */
function normallog($msg, $data = []){
	global $normal, $reset;
	$msg = date('[Y-m-d H:i:s]') . ' ' . $normal . $msg . $reset . "\n";
	foreach($data as $k => $v){
		$msg .= "\t" . $k . "：" . $v . "\n";
	}
	echo $msg;
	return str_replace([$normal, $reset], '', $msg);
}
