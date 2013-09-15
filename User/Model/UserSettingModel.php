<?php
class UserSettingModel extends Model{
	protected $connection = 'user';
	protected $tableName = 'setting';
	protected $pk = 'uid';
}
