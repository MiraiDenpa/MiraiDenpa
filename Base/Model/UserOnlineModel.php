<?php
/**
 * 处理当前登录的用户（和其他在线用户
 *
 * @author ${USER}
 */
class UserOnlineModel extends Mongoo{
	protected $collectionName = 'online';
	protected $connection = 'mongo-user';
}
