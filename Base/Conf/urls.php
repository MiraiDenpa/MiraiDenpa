<?php
$map                          = & $GLOBALS['URL_MAP'];
$map['p-user-login-ip']       = _UC('user', 'Token', 'addip', '', ['token' => ''], 'php');
$map['p-user-login-ip']       = _UC('user', 'Token', 'addip', '', ['token' => ''], 'php');
$map['u-user-login-token']    = _UC('user', 'Token', 'info', '', [], 'json');
$map['u-user-login-property'] = _UC('user', 'Property', 'me', '', [], 'json');
$map['u-user-login-settings'] = _UC('user', 'Setting', '', '', [], 'json');
