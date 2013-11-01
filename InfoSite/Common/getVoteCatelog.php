<?php
function TaglibReplaceVote_catelog(){
	$ret = array(
		[
			'name'   => '人物',
			'type'   => 'CenterBar',
			'offset' => 0,
			'values' => ['普通', '俺的嫁']
		],
		[
			'name'   => '音乐',
			'type'   => 'CenterBar',
			'offset' => -5,
			'values' => ['不好听', '想下CD听'],
			'when'   => [
				'catalog' => ['$in'=> [TYPE_ANIME, TYPE_BANGUMI, TYPE_GAME, TYPE_DRAMA]],
			]
		],
		[
			'name'   => '声优阵容',
			'type'   => 'TwoSideBar',
			'offset' => -2,
			'values' => ['糟糕', '一般', '华丽'],
			'when'   => [
				'catalog' => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA]],
			]
		],
		[
			'name'   => '剧情',
			'type'   => 'ValueBar',
			'offset' => -5,
			'values' => ['一般', '扣人心弦'],
			'when'   => [
				'catalog' => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
			]
		],
		[
			'name'   => '搞笑',
			'type'   => 'CenterBar',
			'offset' => -5,
			'values' => ['不好笑', '笑尿'],
			'when'   => [
				'catalog'     => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
				'externalize' => 'wwww'
			]
		],
		[
			'name'   => '卖肉',
			'type'   => 'TwoSideBar',
			'offset' => -5,
			'values' => ['把持住了', '把持不住！'],
			'when'   => [
				'catalog'     => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC]],
				'externalize' =>  'sena'
			]
		],
		[
			'name'   => '基',
			'type'   => 'ValueBar',
			'offset' => 0,
			'values' => ['无爱', '有爱'],
			'when'   => [
				'catalog'     => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
				'externalize' =>  '801'
			]
		],
		[
			'name'   => '萌',
			'type'   => 'ValueBar',
			'offset' => 0,
			'values' => ['激萌'],
			'when'   => [
				'catalog'     => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC]],
				'externalize' => 'sellmoe'
			]
		],
		[
			'name'   => '宅',
			'type'   => 'CenterBar',
			'offset' => 0,
			'values' => ['入门', '专业'],
			'when'   => [
				'catalog'     => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_DRAMA, TYPE_COMIC, TYPE_NOVEL]],
				'externalize' => 'kuso'
			]
		],
		[
			'name'   => '打斗场景',
			'type'   => 'CenterBar',
			'offset' => -5,
			'values' => ['拖沓', '畅快'],
			'when'   => [
				'catalog'     => ['$in'=> [TYPE_ANIME, TYPE_GAME, TYPE_COMIC]],
				'externalize' => 'battle'
			]
		]
	);

	return json_encode($ret, JSON_UNESCAPED_SLASHES);
}
