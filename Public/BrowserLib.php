<?php
require __DIR__ . '/BrowserLib.components.php';

//定义前引入依赖
$requirements = array(
	'jslib-gt/formhandler.js'   => ['Notify'],
	'Notify'                    => ['jquery/jquery.transit.js'],
	'bootstrap/bootstrap.js'    => ['jquery'],
	'jslib-gt/enhanced_link.js' => ['artDialog', 'jquery/purl.js', 'jslib-gt/murl.js'],
	'jslib-gt/floatbox.js'      => ['jslib-gt/array.remove.js'],
	'jslib/settings.js'         => ['jslib-gt/murl.js'],
	'jslib-gt/murl.js'          => ['jquery/purl.js'],
);

//定义后引入组件
$component = array(
	'jquery/jquery.validate.js' => ['jquery/jquery.validate.zh.js'],
	'jquery/jquery.js'          => [
		'jquery/jquery.cookie.js',
		'jquery/jquery.remove_classes.js',
		'jquery/json3.js',
		'jquery/jquery.history.js',
	],
);

$d = dir(__DIR__ . '/jquery/plugins');
while($file = $d->read()){
	if($file == '.' || $file == '..'){
		continue;
	}
	$requirements['jquery/' . $file][] = 'jquery/jquery.js';
}

// 定义“库”
$libraries = array_merge(array(
							  'global'    => ['basevar.less', 'basevar.js', 'styles/global.less'],
							  'validate'  => ['jquery/jquery.validate.js',],
							  'login'     => ['Notify', 'jslib-gt/login.js',],
							  'jquery'    => ['jquery/jquery.js',],
							  'jqueryui'  => ['jquery-ui/jquery-ui.js', 'jquery-ui/jquery-ui.css'],
							  'bootstrap' => ['bootstrap/bootstrap.js', 'bootstrap/bootstrap.css'],
							  'debug'     => ['styles/gray_background.css'],
							  'artDialog' => [
								  'artDialog/jquery.artDialog.js',
								  'artDialog/artDialog.plugins.js',
								  '/artDialog/skins/{:art_skin()}.css'
							  ]
						 ),
						 (array)$libraries
);

// 定义“全局包含”
$globals = array(
	'jquery',
	'bootstrap',
	'global',
	'jslib/settings.js',
	'login',
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
