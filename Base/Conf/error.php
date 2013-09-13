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
	['NF_APPLICATION', '应用不存在', '请检查url是否正确', ['登录默认应用' => _UC('user', 'login', 'index'), '应用列表' => '']],
	['NF_ACTION', '地址有误', '请检查url是否正确'],
	['NF_FILE', '文件未找到', '就是木有找到！'],
	['NF_USER', '用户不存在', '请检查拼写。', ['注册' => _UC('user', 'register', 'index')]],
	
	['NALLOW_HTTP_METHOD', '不允许的请求方式', '客户端错误。'],
	['NALLOW_PATH', '不允许的请求路径', '客户端错误。'],
	['NALLOW_EDIT_OTHER', '不能修改其他用户的信息', '都是年轻时犯下的错误。'],
	
	['MISS_PASSWORD', '密码错误', '请重新输入。', ['找回密码' => _UC('user', 'forget', 'index')]],
	['MISS_REPASSWORD', '重复密码错误', '请确认两次密码输入相同。'],
	
	['INPUT_TYPE', '输入变量类型不符', '客户端程序问题。'],
	['INPUT_REQUIRE', '缺少输入', '请检查。'],
	['INPUT_DENY', '输入中包含不允许的字符', '请重新输入。'],
	
	['FAIL_REGISTER', '注册失败', '请联系管理员解决。'],
	['FAIL_SEND_MAIL', '邮件发送失败', '请稍后再试。'],
	['FAIL_VERIFY', '验证失败', '找不到对应的验证码。'],
	['FAIL_AUTH', '认证失败', '请重新登录。',
		 ['登录官方应用' => _UC('user', 'login', 'index'), '浏览应用' => _UC('app', 'List', 'index')]
	],
	['FAIL_PERMISSION', '权限不够', '请更换应用。'],
	
	['TIMEOUT', '操作超时', '请重新请求。'],
	['OP_TOO_FAST', '操作过快', '喵～'],
	
	['SQL', '数据库错误', '请联系管理员解决。'],
	['NO_SQL', '数据库错误', '请联系管理员解决。'],
	
	['RANGE_PAGE', '不是正确的页码', '页码最低为 1。'],
	
	['JSON_SERIALIZE', '序列化失败', '这是个严重错误。'],
	
);
