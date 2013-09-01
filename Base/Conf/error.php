<?php
/**
 * 放在 BASE_CONF_PATH 里，每行一个定义
 * 错误码就是所在行数+10000
 * 每一行的定义分为：
 * name -> 如 FILE_NOT_FOUND
 * message -> 错误信息
 * info -> 详细的介绍
 * url -> 发生错误时，推荐跳转的url（数组列表）
 */
return array(
	['NF_APPLICATION','应用不存在','请检查url是否正确',['登录默认应用'=>_UC('user','login','index'),'应用列表'=>'']],
	['NF_ACTION','地址有误','请检查url是否正确'],
	['NF_FILE','文件未找到','就是木有找到！'],
	['NF_USER','用户不存在','请检查拼写。',['注册'=>_UC('user','register','index')]],
	
	['MISS_PASSWORD','密码错误','请重新输入。',['找回密码'=>_UC('user','forget','index')]],
	['MISS_REPASSWORD','重复密码错误','请确认两次密码输入相同。'],

	['INPUT_TYPE','输入变量类型不符','客户端程序问题。'],
	['INPUT_REQUIRE','缺少输入','请检查。'],
	['INPUT_DENY','输入中包含不允许的字符','请重新输入。'],
	
	['FAIL_REGISTER','注册失败','请联系管理员解决。']
);
