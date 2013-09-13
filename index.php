<?php /* [SIG_GENERATE] */
$GLOBALS['_beginTime'] = microtime(TRUE);
require(RUNTIME_PATH.'functions.php');
global $_think_import_alias;$_think_import_alias = array (
  'Action' => '/data/web/MyThink/Core/Lib/Action.class.php',
  'App' => '/data/web/MyThink/Core/Lib/App.class.php',
  'Behavior' => '/data/web/MyThink/Core/Lib/Behavior.class.php',
  'Cache' => '/data/web/MyThink/Core/Lib/Cache.class.php',
  'Db' => '/data/web/MyThink/Core/Lib/Db.class.php',
  'Dispatcher' => '/data/web/MyThink/Core/Lib/Dispatcher.class.php',
  'Entity' => '/data/web/MyThink/Core/Lib/Entity.class.php',
  'Error' => '/data/web/MyThink/Core/Lib/Error.class.php',
  'HTML' => '/data/web/MyThink/Core/Lib/HTML.class.php',
  'InputStream' => '/data/web/MyThink/Core/Lib/InputStream.class.php',
  'Log' => '/data/web/MyThink/Core/Lib/Log.class.php',
  'Model' => '/data/web/MyThink/Core/Lib/Model.class.php',
  'Mongoo' => '/data/web/MyThink/Core/Lib/Mongoo.class.php',
  'OutputBuffer' => '/data/web/MyThink/Core/Lib/OutputBuffer.class.php',
  'Page' => '/data/web/MyThink/Core/Lib/Page.class.php',
  'Think' => '/data/web/MyThink/Core/Lib/Think.class.php',
  'ThinkException' => '/data/web/MyThink/Core/Lib/ThinkException.class.php',
  'ThinkInstance' => '/data/web/MyThink/Core/Lib/ThinkInstance.class.php',
  'UrlHelper' => '/data/web/MyThink/Core/Lib/UrlHelper.class.php',
  'View' => '/data/web/MyThink/Core/Lib/View.class.php',
  'Widget' => '/data/web/MyThink/Core/Lib/Widget.class.php',
  'ThinkTemplate' => '/data/web/MyThink/Core/Lib/ThinkTemplate.class.php',
  'TagLib' => '/data/web/MyThink/Core/Lib/TagLib.class.php',
  'ORG\\Crypt\\Rsa' => '/data/web/MyThink/Extend/Library/ORG/Crypt/Rsa.class.php',
  'ORG\\Crypt\\Xxtea' => '/data/web/MyThink/Extend/Library/ORG/Crypt/Xxtea.class.php',
  'ORG\\Crypt\\Crypt' => '/data/web/MyThink/Extend/Library/ORG/Crypt/Crypt.class.php',
  'ORG\\Crypt\\Des' => '/data/web/MyThink/Extend/Library/ORG/Crypt/Des.class.php',
  'ORG\\Crypt\\Base64' => '/data/web/MyThink/Extend/Library/ORG/Crypt/Base64.class.php',
  'ORG\\Crypt\\Hmac' => '/data/web/MyThink/Extend/Library/ORG/Crypt/Hmac.class.php',
  'ORG\\Net\\UploadFile' => '/data/web/MyThink/Extend/Library/ORG/Net/UploadFile.class.php',
  'ORG\\Net\\IpLocation' => '/data/web/MyThink/Extend/Library/ORG/Net/IpLocation.class.php',
  'ORG\\Net\\Http' => '/data/web/MyThink/Extend/Library/ORG/Net/Http.class.php',
  'ORG\\Util\\Input' => '/data/web/MyThink/Extend/Library/ORG/Util/Input.class.php',
  'ORG\\Util\\Date' => '/data/web/MyThink/Extend/Library/ORG/Util/Date.class.php',
  'ORG\\Util\\String' => '/data/web/MyThink/Extend/Library/ORG/Util/String.class.php',
  'ORG\\Util\\Authority' => '/data/web/MyThink/Extend/Library/ORG/Util/Authority.class.php',
  'ORG\\Util\\ArrayList' => '/data/web/MyThink/Extend/Library/ORG/Util/ArrayList.class.php',
  'ORG\\Util\\Socket' => '/data/web/MyThink/Extend/Library/ORG/Util/Socket.class.php',
  'ORG\\Util\\Session' => '/data/web/MyThink/Extend/Library/ORG/Util/Session.class.php',
  'ORG\\Util\\CodeSwitch' => '/data/web/MyThink/Extend/Library/ORG/Util/CodeSwitch.class.php',
  'ORG\\Util\\Cookie' => '/data/web/MyThink/Extend/Library/ORG/Util/Cookie.class.php',
  'ORG\\Util\\Image' => '/data/web/MyThink/Extend/Library/ORG/Util/Image.class.php',
  'ORG\\Util\\RBAC' => '/data/web/MyThink/Extend/Library/ORG/Util/RBAC.class.php',
  'ORG\\Util\\Stack' => '/data/web/MyThink/Extend/Library/ORG/Util/Stack.class.php',
  'ORG\\Util\\Debug' => '/data/web/MyThink/Extend/Library/ORG/Util/Debug.class.php',
  'ORG\\Util\\HtmlExtractor' => '/data/web/MyThink/Extend/Library/ORG/Util/HtmlExtractor.class.php',
  'COM\\MyThink\\Strings' => '/data/web/MyThink/Extend/Library/COM/MyThink/Strings.php',
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
