<?php
require __DIR__ . '/BrowserLib.components.php';

//定义前引入依赖
$requirements = array(
	'jslib-gt/formhandler.js'    => ['Notify'],
	'Notify'                     => ['jquery/jquery.transit.js'],
	'bootstrap/bootstrap.js'     => ['jquery'],
	'jslib-gt/enhanced_link.js'  => ['artDialog', 'jquery/purl.js', 'jslib-gt/murl.js'],
	'jslib-gt/floatbox.js'       => ['jslib-gt/array.remove.js'],
	'jslib-gt/login.js'          => ['jslib-gt/murl.js', 'jslib/sha1.js', 'jslib-gt/settings.js'],
	'jslib-gt/murl.js'           => ['jquery/purl.js'],
	'scripts/upload-inputgen.js' => ['UI','upload'],
	'UI'                         => ['jquery/jquery.mousewheel.js'],
	'jquery/jquery.fileupload.js'       => ['jquery/jquery.ui.widget.js'],
);

//定义后引入组件
$component = array(
	'jquery/jquery.validate.js'         => ['jquery/jquery.validate.zh.js'],
	'jquery/jquery.js'                  => [
		'jquery/jquery.cookie.js',
		'jquery/jquery.remove_classes.js',
		'jquery/json3.js',
		'jquery/jquery.history.js',
	],
	'bootstrap/bootstrap-datepicker.js' => ['bootstrap/datepicker.css'],
	'jquery/jquery.fileupload.js'       => ['jquery/jquery.fileupload.css'],
);

// 定义“库”
$userlib   = array(
	'global'    => ['basevar.less', 'jslib/basevar.js', 'styles/global.less', 'scripts/global.js', 'jslib/debug.js'],
	'validate'  => ['jquery/jquery.validate.js',],
	'login'     => ['Notify', 'jslib-gt/settings.js',],
	'jquery'    => ['jquery/jquery.js',],
	'jqueryui'  => ['jquery-ui/jquery-ui.js', 'jquery-ui/jquery-ui.css'],
	'bootstrap' => ['bootstrap/bootstrap.js', 'bootstrap/bootstrap.css'],
	'debug'     => ['styles/gray_background.css'],
	'artDialog' => ['artDialog/jquery.artDialog.js', 'artDialog/artDialog.plugins.js', '/artDialog/get_skin.js'],
	'upload'    => [
		'jquery/canvas-to-blob.js',
		'jquery/load-image.min.js',
		'jquery/jquery.fileupload.js',
		'jquery/jquery.fileupload-process.js',
		'jquery/jquery.fileupload-audio.js',
		'jquery/jquery.fileupload-image.js',
		'jquery/jquery.fileupload-video.js',
	],
);
$libraries = array_merge($userlib, (array)$libraries);

// UI组件
foreach(glob(__DIR__ . '/UI/*') as $file){
	$libraries['UI'][] = 'UI/' . basename($file);
}

// 定义“全局包含”
$globals = array(
	'phpjs',
	'jquery',
	'bootstrap',
	'global',
);

if(LESS_DEBUG){
	$globals[] = 'less';
}

return array(
	'requirements' => $requirements,
	'libraries'    => $libraries,
	'globals'      => $globals,
	'fileset'      => $fileset,
	'component'    => $component,
);
