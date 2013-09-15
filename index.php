<?php /* [SIG_GENERATE] */
$GLOBALS['_beginTime'] = microtime(TRUE);
require(RUNTIME_PATH.'functions.php');
global $_think_import_alias;$_think_import_alias = array (
  'Action' => '/home/MyThink/Core/Lib/Action.class.php',
  'App' => '/home/MyThink/Core/Lib/App.class.php',
  'Behavior' => '/home/MyThink/Core/Lib/Behavior.class.php',
  'Cache' => '/home/MyThink/Core/Lib/Cache.class.php',
  'Db' => '/home/MyThink/Core/Lib/Db.class.php',
  'Dispatcher' => '/home/MyThink/Core/Lib/Dispatcher.class.php',
  'Entity' => '/home/MyThink/Core/Lib/Entity.class.php',
  'Error' => '/home/MyThink/Core/Lib/Error.class.php',
  'HTML' => '/home/MyThink/Core/Lib/HTML.class.php',
  'InputStream' => '/home/MyThink/Core/Lib/InputStream.class.php',
  'Log' => '/home/MyThink/Core/Lib/Log.class.php',
  'Model' => '/home/MyThink/Core/Lib/Model.class.php',
  'Mongoo' => '/home/MyThink/Core/Lib/Mongoo.class.php',
  'OutputBuffer' => '/home/MyThink/Core/Lib/OutputBuffer.class.php',
  'Page' => '/home/MyThink/Core/Lib/Page.class.php',
  'Think' => '/home/MyThink/Core/Lib/Think.class.php',
  'ThinkException' => '/home/MyThink/Core/Lib/ThinkException.class.php',
  'ThinkInstance' => '/home/MyThink/Core/Lib/ThinkInstance.class.php',
  'UrlHelper' => '/home/MyThink/Core/Lib/UrlHelper.class.php',
  'View' => '/home/MyThink/Core/Lib/View.class.php',
  'Widget' => '/home/MyThink/Core/Lib/Widget.class.php',
  'ThinkTemplate' => '/home/MyThink/Core/Lib/ThinkTemplate.class.php',
  'TagLib' => '/home/MyThink/Core/Lib/TagLib.class.php',
  'COM\\MyThink\\Strings' => '/home/MyThink/Extend/Library/COM/MyThink/Strings.php',
  'ORG\\Util\\Debug' => '/home/MyThink/Extend/Library/ORG/Util/Debug.class.php',
  'ORG\\Util\\Image' => '/home/MyThink/Extend/Library/ORG/Util/Image.class.php',
  'ORG\\Util\\Date' => '/home/MyThink/Extend/Library/ORG/Util/Date.class.php',
  'ORG\\Util\\CodeSwitch' => '/home/MyThink/Extend/Library/ORG/Util/CodeSwitch.class.php',
  'ORG\\Util\\Socket' => '/home/MyThink/Extend/Library/ORG/Util/Socket.class.php',
  'ORG\\Util\\RBAC' => '/home/MyThink/Extend/Library/ORG/Util/RBAC.class.php',
  'ORG\\Util\\Stack' => '/home/MyThink/Extend/Library/ORG/Util/Stack.class.php',
  'ORG\\Util\\Cookie' => '/home/MyThink/Extend/Library/ORG/Util/Cookie.class.php',
  'ORG\\Util\\Session' => '/home/MyThink/Extend/Library/ORG/Util/Session.class.php',
  'ORG\\Util\\ArrayList' => '/home/MyThink/Extend/Library/ORG/Util/ArrayList.class.php',
  'ORG\\Util\\Input' => '/home/MyThink/Extend/Library/ORG/Util/Input.class.php',
  'ORG\\Util\\String' => '/home/MyThink/Extend/Library/ORG/Util/String.class.php',
  'ORG\\Util\\Authority' => '/home/MyThink/Extend/Library/ORG/Util/Authority.class.php',
  'ORG\\Util\\HtmlExtractor' => '/home/MyThink/Extend/Library/ORG/Util/HtmlExtractor.class.php',
  'ORG\\Net\\UploadFile' => '/home/MyThink/Extend/Library/ORG/Net/UploadFile.class.php',
  'ORG\\Net\\IpLocation' => '/home/MyThink/Extend/Library/ORG/Net/IpLocation.class.php',
  'ORG\\Net\\Http' => '/home/MyThink/Extend/Library/ORG/Net/Http.class.php',
  'ORG\\Crypt\\Des' => '/home/MyThink/Extend/Library/ORG/Crypt/Des.class.php',
  'ORG\\Crypt\\Hmac' => '/home/MyThink/Extend/Library/ORG/Crypt/Hmac.class.php',
  'ORG\\Crypt\\Base64' => '/home/MyThink/Extend/Library/ORG/Crypt/Base64.class.php',
  'ORG\\Crypt\\Rsa' => '/home/MyThink/Extend/Library/ORG/Crypt/Rsa.class.php',
  'ORG\\Crypt\\Xxtea' => '/home/MyThink/Extend/Library/ORG/Crypt/Xxtea.class.php',
  'ORG\\Crypt\\Crypt' => '/home/MyThink/Extend/Library/ORG/Crypt/Crypt.class.php',
);

require $_think_import_alias['Think'];
G('loadTime');// 载入时间
Think::Start();// 初始化
ini_set('display_errors', 0);/* 启动应用 */

define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
/** @var Dispatcher $dispatcher */
global $dispatcher;
$dispatcher = new Dispatcher();
// 项目初始化标签
tag('app_init', $dispatcher);
// 定义当前请求的系统常量
//define('NOW',$_SERVER['REQUEST_TIME']);
// URL调度
$error = $dispatcher->parse_path($_SERVER['PATH_INFO']);
$dispatcher->setData($_GET);
if($error){ // 調度出錯
	Think::fail_error(ERR_NF_ACTION, $error);
}
define('ACTION_NAME', $dispatcher->action_name);
define('METHOD_NAME', $dispatcher->method_name);
define('EXTENSION_NAME', $dispatcher->extension_name);
// 项目开始标签
tag('app_begin');
// 记录应用初始化时间
G('initTime');
$ret = $dispatcher->run();
// 项目结束标签
tag('app_end');
