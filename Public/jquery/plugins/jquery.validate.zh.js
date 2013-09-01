$.extend($.validator.messages, {
	required   : "必填字段",
	remote     : "输入有误",
	email      : "请输入正确格式的电子邮件",
	url        : "请输入合法的网址",
	date       : "请输入合法的日期",
	dateISO    : "请输入合法的日期 (ISO).",
	number     : "请输入数字",
	digits     : "只能输入整数",
	equalTo    : "两次输入不符",
	accept     : "请输入拥有合法后缀名的字符串",
	maxlength  : $.validator.format("长度不能超过 {0} 字符"),
	minlength  : $.validator.format("长度不能少于 {0} 字符"),
	rangelength: $.validator.format("长度只能介于 {0} 到 {1} 之间"),
	range      : $.validator.format("请输入 {0} 到 {1} 之间的数字"),
	max        : $.validator.format("请输入小于 {0} 的数"),
	min        : $.validator.format("请输入大于 {0} 的数")
});
jQuery.validator.addMethod("regex", function(value, element, regex) {
	return regex.test(value);
}, '不符合要求。');


