<?php
namespace PHPSTORM_META {                                                 // we want to avoid the pollution
	/** @noinspection PhpUnusedLocalVariableInspection */
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	$STATIC_METHOD_TYPES = [
		\ThinkInstance::D('') => [
			'App' instanceof \AppModel,     // argument value and return type
			'AppList' instanceof \AppListModel,
			'UserList' instanceof \UserListModel,
			'UserCheck' instanceof \UserCheckModel,
			'UserRegister' instanceof \UserRegisterModel,
			'UserLogin' instanceof \UserLoginModel,
			'UserOnline' instanceof \UserOnlineModel,
			'UserRelation' instanceof \UserRelationModel,
			'InfoEntry' instanceof \InfoEntryModel,
			'InfoHistory' instanceof \InfoHistoryModel,
			'UserProperty' instanceof \UserPropertyModel,
			'UserSetting' instanceof \UserSettingModel,
		],
		\ThinkInstance::InStream('') => [
			'Post' instanceof \PostInputStream,
			'Get' instanceof \GetInputStream,
		],
	];

}
