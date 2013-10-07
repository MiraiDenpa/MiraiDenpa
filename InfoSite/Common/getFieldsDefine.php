<?php
function TaglibReplaceItem_fields(){
	$ret = array(
		"catalog"         => [
			'name'    => '类别',
			'type'    => 'select',
			'subtype' => [
				'动画' => 1,
				'漫画' => 2,
				'游戏' => 3,
				'小说' => 4,
				'音乐' => 5,
				'剧集' => 6,
				'p'  => 7,
			],
			'must'    => true,
		],
		"name"            => [
			'name'    => '作品名称',
			'type'    => 'inputlist',
			'subtype' => 'text',
			'must'    => true,
		],
		"origin_name"     => [
			'name'    => '作品名称（原文）',
			'type'    => 'input',
			'subtype' => 'text',
		],
		"episodes"        => [
			'name'    => '话数',
			'type'    => 'number',
			'subtype' => '[1,255]',
		],
		"broadcast_range" => [
			'name'    => '放送时间',
			'type'    => 'date',
			'subtype' => 'yyyy-mm-dd',
		],
		"day_of_week"     => [
			'name'    => '放送星期',
			'type'    => 'select',
			'subtype' => [
				'星期五' => '5',
				'星期六' => '6',
				'星期日' => '7',
				'星期一' => '1',
				'星期二' => '2',
				'星期三' => '3',
				'星期四' => '4',
			]
		],
		"official_site"   => [
			'name'    => '官方网站',
			'type'    => 'input',
			'subtype' => 'url',
		],
		"classification"  => [
			'name'    => '年龄限制',
			'type'    => 'oneof',
			'subtype' => [
				'all'   => [
					'text'  => '全年龄',
					'type'  => 'static',
					'value' => 0
				],
				'limit' => [
					'text'    => '限制',
					'type'    => 'number',
					'subtype' => '[1,100]'
				]
			],
		],
		"come_from"       => [
			'name'    => '作品地区',
			'type'    => 'select',
			'subtype' => [
				'11区' => 'region-11',
				'大陆'  => 'mainland',
				'特区'  => 'SAR',
				'西方'  => 'west',
			]
		],
		"doujin"          => [
			'name'    => '同人作品',
			'type'    => 'static',
			'subtype' => 'on',
			'text'    => '这是一部同人作品',
		],
	);
	foreach($ret as $id => $data){
		$ret[$id]['id'] = $id;
	}
	return json_encode($ret, JSON_UNESCAPED_SLASHES);
}
