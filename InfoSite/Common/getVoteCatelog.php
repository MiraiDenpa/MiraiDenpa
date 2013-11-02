<?php
function TaglibReplaceVote_catelog(){
	$ret = array(
		[
			'name'   => '人物',
			'type'   => 0,
			'values' => ['普通', '俺的嫁']
		],
		[
			'name'   => '音乐',
			'type'   => 1,
			'offset' => 5,
			'values' => ['不好听', '一般', '想下CD听'],
			'when'   => [
				'catalog' => ['$in' => [TYPE_ANIME, TYPE_BANGUMI, TYPE_GAME, TYPE_DRAMA]],
			]
		],
		[
			'name'   => '声优阵容',
			'type'   => 2,
			'offset' => 5,
			'values' => ['糟糕', '一般', '华丽'],
			'when'   => [
				'catalog' => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA]],
			]
		],
		[
			'name'   => '剧情',
			'type'   => 0,
			'offset' => 5,
			'values' => ['一般', '扣人心弦'],
			'when'   => [
				'catalog' => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
			]
		],
		[
			'name'   => '搞笑',
			'type'   => 1,
			'offset' => 5,
			'values' => ['不好笑', '一般', '笑尿'],
			'when'   => [
				'catalog'     => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
				'externalize' => 'wwww'
			]
		],
		[
			'name'   => '卖肉',
			'type'   => 2,
			'offset' => 5,
			'values' => ['不用把持', '把持住了', '把持不住！'],
			'when'   => [
				'catalog'     => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC]],
				'externalize' => 'sena'
			]
		],
		[
			'name'   => '基',
			'type'   => 0,
			'values' => ['无爱', '无感想', '有爱'],
			'when'   => [
				'catalog'     => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
				'externalize' => '801'
			]
		],
		[
			'name'   => '萌',
			'type'   => 0,
			'values' => ['不萌', '激萌'],
			'when'   => [
				'catalog'     => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC]],
				'externalize' => 'sellmoe'
			]
		],
		[
			'name'   => '宅',
			'type'   => 1,
			'offset' => 0,
			'values' => ['不够宅', '入门', '专业'],
			'when'   => [
				'catalog'     => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
				'externalize' => 'kuso'
			]
		],
		[
			'name'   => '打斗场景',
			'type'   => 1,
			'offset' => 5,
			'values' => ['拖沓', '还可以', '畅快'],
			'when'   => [
				'catalog'     => ['$in' => [TYPE_ANIME, TYPE_GAME, TYPE_COMIC]],
				'externalize' => 'battle'
			]
		]
	);

	return json_encode($ret, JSON_UNESCAPED_SLASHES);
}
