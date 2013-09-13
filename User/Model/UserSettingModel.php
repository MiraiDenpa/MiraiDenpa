<?php
class UserSettingModel extends Mongoo{
	protected $collectionName = 'setting';
	protected $connection = 'mongo-user';
}
