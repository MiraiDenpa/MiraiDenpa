<?php
class ForwardEntity extends Entity{
	public $type;
	public $content;

	public $list;
	public $original;

	public $arg1;
	public $arg2;

	function _init(){
		$this->content;
	}

	static function parse($string){
		$obj          = new ForwardEntity;
		$obj->content = $string;
		$obj->type    = '';
		$obj->_init();
		if($obj){
			return $obj;
		} else{
			return null;
		}
	}

	function stringify(){
	}
}
/**
 * mime list
 * mirai/denpa
 * mirai/info-entry
 * mirai/user
 *
 * site/bilibili
 * site/acfun
 * site/mio
 * site/moeapk
 *
 * video/youku
 * video/iqiyi
 * video/sina
 * video/tudou
 * video/letv
 *
 *
 */
