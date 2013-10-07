<?php
function filter_uid_spec_char($uid){
	if(strpos($uid, '$') !== false || strpos($uid, ',') !== false || strpos($uid,'.')!==false){
		return false;
	}else{
		return true;
	}
}
