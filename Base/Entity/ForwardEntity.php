<?php
class ForwardEntity extends Entity{
	public $mime;
	public $content;

	function resolve(){
	}

	static function parse($string){
		$obj          = new ForwardEntity;
		$obj->content = $string;
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
