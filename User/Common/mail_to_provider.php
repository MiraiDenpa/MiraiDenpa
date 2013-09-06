<?php
function mail_to_provider($mail){
	$provider = substr($mail, strpos($mail,'@')+1);
	switch($provider){
	case 'gmail.com':
		return 'mail.google.com';
	default:
		return 'mail.'.$provider;
	}
}
