#!/bin/php
<?php
define('APP_NAME', 'user');
define('APP_PATH', __DIR__ . '/');
define('LIB_PATH', APP_PATH);

define('APP_DEBUG', true);
define('CORE_DEBUG', true);
define('SHOW_TRACE', true);
define('TRACE_DEBUG', false);
define('TMPL_NO_CACHE', true);
define('TMPL_READABLE', false);
define('JS_DEBUG', false);
define('STATIC_DEBUG', true);
define('MEMORY_DEBUG', false);
define('LESS_DEBUG', true);


require dirname(__DIR__) . '/defines.php';
include 'mytp_include.php';
