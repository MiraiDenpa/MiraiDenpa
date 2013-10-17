<?php
class WeiboEntity extends Entity{
	public $_id;
	public $content;

	/** @var ForwardEntity */
	public $forward;
	public $sendto;
	public $channel;
	public $at;

	public $app;
	public $user;
	public $time;
}
