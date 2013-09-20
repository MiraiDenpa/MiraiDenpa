#!/bin/php
<?php
define('APP_NAME', 'info');
define('APP_PATH', __DIR__ . '/');
define('LIB_PATH', APP_PATH);

if(is_file(dirname(__DIR__) . '/status.php')){
	require dirname(__DIR__) . '/status.php';
} else{
	define('APP_DEBUG', true);
	define('CORE_DEBUG', true);
	define('SHOW_TRACE', true);
	define('TRACE_DEBUG', false);
	define('TMPL_NO_CACHE', true);
	define('TMPL_READABLE', true);
	define('JS_DEBUG', true);
	define('STATIC_DEBUG', true);
	define('MEMORY_DEBUG', true);
	define('LESS_DEBUG', true);
}

require dirname(__DIR__) . '/defines.php';
include 'mytp_include.php';
