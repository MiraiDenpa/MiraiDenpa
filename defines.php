<?php
define('ROOT_PATH', __DIR__ . '/');

define('PUBLIC_URL', 'http://p.mirai.localdomain');
define('PUBLIC_PATH', __DIR__ . '/Public/');
define('STATIC_URL', 'http://s0.mirai.localdomain');
define('STATIC_PATH', __DIR__ . '/Static/');
define('PICTURE_URL', 'http://p0.mirai.localdomain');
define('PICTURE_PATH', __DIR__ . '/Pictures/');

define('BASE_LIB_PATH', __DIR__ . '/Base/');

define('LOG_PATH', '/data/log/mirai/' . APP_NAME . '/');
define('RUNTIME_PATH', __DIR__ . '/_runtime/');

define('STATIC_VERSION', date('Y.m.d-H.i.s', intval(file_get_contents(PUBLIC_PATH . 'lastmodify.timestamp'))));

define('APP_STATUS', 'debug');
