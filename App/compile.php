#!/bin/php
<?php
define('APP_NAME', 'app');
define('APP_PATH', __DIR__ . '/');
define('LIB_PATH', APP_PATH);

define('APP_DEBUG', false);
define('CORE_DEBUG', true);
define('SHOW_TRACE', false);
define('TRACE_DEBUG', false);
define('TMPL_NO_CACHE', false);
define('TMPL_READABLE', false);
define('JS_DEBUG', false);
define('STATIC_DEBUG', false);
define('MEMORY_DEBUG', false);
define('LESS_DEBUG', true);



require dirname(__DIR__).'/defines.php';
include 'mytp_include.php';
