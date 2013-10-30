<?php

function TaglibReplaceItem_fields(){
	$ret = array(
		"catalog"         => [
			'name'    => '类别',
			'type'    => 'select',
			'subtype' => [
				'动画' => TYPE_ANIME,
				'漫画' => TYPE_COMIC,
				'游戏' => TYPE_GAME,
				'小说' => TYPE_NOVEL,
				'音乐' => TYPE_MUSIC,
				'剧集' => TYPE_DRAMA,
				'期刊' => TYPE_PERIO,
			],
			'must'    => true,
		],
		"name"            => [
			'name'    => '作品名称',
			'type'    => 'inputlist',
			'subtype' => [
				'type'    => 'input',
				'subtype' => 'text',
			],
			'must'    => true,
		],
		"origin_name"     => [
			'name'    => '作品名称（原文）',
			'type'    => 'input',
			'subtype' => 'text',
		],
		"cover_pic"       => [
			'name'    => '封面图',
			'type'    => 'upload',
			'subtype' => 'picture',
			'text'    => '图片最大不能超过2M',
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
				'11区' => 'aria-11',
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
		"robot"           => [
			'name'    => '萝卜',
			'type'    => 'static',
			'subtype' => 'on',
			'text'    => '这是一部萝卜片',
		],
		"externalize"     => [
			'name'    => '主要內容',
			'type'    => 'inputlist',
			'subtype' => [
				'type'    => 'select',
				'subtype' => [
					'日常'     => '日常',
					'爆笑'     => '爆笑',
					'催泪'     => '催泪',
					'深沉'     => '深沉',
					'猎奇'     => '猎奇',
					//
					'基'      => '基',
					'百合'     => '百合',
					'卖肉'     => '卖肉',
					//
					'未来/硬科幻' => '未来/硬科幻',
					'魔法/伪科幻' => '魔法/伪科幻',
					'现实'     => '现实',
				]
			],
			'must'    => true,
		],
	);
	foreach($ret as $id => $data){
		$ret[$id]['id'] = $id;
	}
	return json_encode($ret, JSON_UNESCAPED_SLASHES);
}
