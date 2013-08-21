<?php
define('APP_NAME', 'user');
define('ROOT_PATH', __DIR__ . '/');
define('APP_PATH', __DIR__ . '/');
define('LIB_PATH', APP_PATH);
define('BASE_LIB_PATH', dirname(__DIR__) . '/Base/');

define('PUBLIC_URL', 'http://pub.mirai');
define('PUBLIC_PATH', dirname(__DIR__) . '/Public/');
define('STATIC_URL', 'http://data.mirai');
define('STATIC_PATH', dirname(__DIR__) . '/Static/');

define('LOG_PATH', '/data/log/mirai/test/');
define('RUNTIME_PATH', dirname(__DIR__) .'/_runtime/');

define('APP_DEBUG', true);
define('SHOW_TRACE', true);
define('TRACE_DEBUG', false);
define('TMPL_DEBUG', true);
define('MEMORY_DEBUG', true);

define('APP_STATUS', 'debug');

include 'mytp_include.php';
