<?php
/**
 * User: GongT
 * Create On: 13-9-10 下午4:42
 *
 */
class UserSettingEntity extends Entity{
	public $uid;
	public $app;
	public $head_gicon;
	public $revert_mouse;
	public $base_color;
	public $exist = true;

	public static $fields = ['head_gicon', 'revert_mouse', 'base_color'];
}
