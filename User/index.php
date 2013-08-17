<?php /* [SIG_GENERATE] */
$GLOBALS['_beginTime'] = microtime(TRUE);
require(RUNTIME_PATH.'functions.php');
set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);
alias_import(array (
  'Action' => '/data/web/MyThink/Core/Lib/Core/Action.class.php',
  'App' => '/data/web/MyThink/Core/Lib/Core/App.class.php',
  'Behavior' => '/data/web/MyThink/Core/Lib/Core/Behavior.class.php',
  'Cache' => '/data/web/MyThink/Core/Lib/Core/Cache.class.php',
  'Db' => '/data/web/MyThink/Core/Lib/Core/Db.class.php',
  'Dispatcher' => '/data/web/MyThink/Core/Lib/Core/Dispatcher.class.php',
  'Log' => '/data/web/MyThink/Core/Lib/Core/Log.class.php',
  'Model' => '/data/web/MyThink/Core/Lib/Core/Model.class.php',
  'OutputBuffer' => '/data/web/MyThink/Core/Lib/Core/OutputBuffer.class.php',
  'Think' => '/data/web/MyThink/Core/Lib/Core/Think.class.php',
  'ThinkException' => '/data/web/MyThink/Core/Lib/Core/ThinkException.class.php',
  'ThinkInstance' => '/data/web/MyThink/Core/Lib/Core/ThinkInstance.class.php',
  'View' => '/data/web/MyThink/Core/Lib/Core/View.class.php',
  'Widget' => '/data/web/MyThink/Core/Lib/Core/Widget.class.php',
  'ThinkTemplate' => '/data/web/MyThink/Core/Lib/Template/ThinkTemplate.class.php',
  'TagLib' => '/data/web/MyThink/Core/Lib/Template/TagLib.class.php',
  'TagLibCx' => '/data/web/MyThink/Core/Lib/Driver/TagLib/TagLibCx.class.php',
  'org\\crypt\\base64' => '/data/web/MyThink/Extend/Library/ORG/Base64.class.php',
  'org\\crypt\\crypt' => '/data/web/MyThink/Extend/Library/ORG/Crypt.class.php',
  'org\\crypt\\des' => '/data/web/MyThink/Extend/Library/ORG/Des.class.php',
  'org\\crypt\\hmac' => '/data/web/MyThink/Extend/Library/ORG/Hmac.class.php',
  'org\\crypt\\rsa' => '/data/web/MyThink/Extend/Library/ORG/Rsa.class.php',
  'org\\crypt\\xxtea' => '/data/web/MyThink/Extend/Library/ORG/Xxtea.class.php',
  'org\\net\\http' => '/data/web/MyThink/Extend/Library/ORG/Http.class.php',
  'org\\net\\iplocation' => '/data/web/MyThink/Extend/Library/ORG/IpLocation.class.php',
  'org\\net\\uploadfile' => '/data/web/MyThink/Extend/Library/ORG/UploadFile.class.php',
  'org\\util\\arraylist' => '/data/web/MyThink/Extend/Library/ORG/ArrayList.class.php',
  'org\\util\\authority' => '/data/web/MyThink/Extend/Library/ORG/Authority.class.php',
  'org\\util\\codeswitch' => '/data/web/MyThink/Extend/Library/ORG/CodeSwitch.class.php',
  'org\\util\\cookie' => '/data/web/MyThink/Extend/Library/ORG/Cookie.class.php',
  'org\\util\\date' => '/data/web/MyThink/Extend/Library/ORG/Date.class.php',
  'org\\util\\debug' => '/data/web/MyThink/Extend/Library/ORG/Debug.class.php',
  'org\\util\\htmlextractor' => '/data/web/MyThink/Extend/Library/ORG/HtmlExtractor.class.php',
  'org\\util\\image' => '/data/web/MyThink/Extend/Library/ORG/Image.class.php',
  'org\\util\\input' => '/data/web/MyThink/Extend/Library/ORG/Input.class.php',
  'org\\util\\page' => '/data/web/MyThink/Extend/Library/ORG/Page.class.php',
  'org\\util\\rbac' => '/data/web/MyThink/Extend/Library/ORG/RBAC.class.php',
  'org\\util\\session' => '/data/web/MyThink/Extend/Library/ORG/Session.class.php',
  'org\\util\\socket' => '/data/web/MyThink/Extend/Library/ORG/Socket.class.php',
  'org\\util\\stack' => '/data/web/MyThink/Extend/Library/ORG/Stack.class.php',
  'org\\util\\string' => '/data/web/MyThink/Extend/Library/ORG/String.class.php',
));

require CORE_PATH.'Core/Think.class.php';
G('loadTime');// 载入时间
Think::Start();// 初始化
App::run();// 启动应用
