<?php
namespace PHPSTORM_META {                                                 // we want to avoid the pollution
	/** @noinspection PhpUnusedLocalVariableInspection */
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	$STATIC_METHOD_TYPES = [
		\ThinkInstance::D('') => [
			'App' instanceof \AppModel,     // argument value and return type
			'UserList' instanceof \UserListModel,
			'UserCheck' instanceof \UserCheckModel,
			'UserRegister' instanceof \UserRegisterModel,
			
		],
		\ThinkInstance::InStream('') => [
			'Post' instanceof \PostInputStream,
			'Get' instanceof \GetInputStream,
		],
	];

}
