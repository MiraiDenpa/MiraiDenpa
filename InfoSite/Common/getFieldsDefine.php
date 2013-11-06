<?php
function TaglibReplaceItem_fields(){
	return json_encode(getItemFields(), JSON_UNESCAPED_SLASHES);
}

function getItemFields(){
	$ret = array(
		"catalog"         => [
			'name'    => '类别',
			'type'    => 'select',
			'subtype' => [
				TYPE_ANIME => '动画',
				TYPE_COMIC => '漫画',
				TYPE_GAME  => '游戏',
				TYPE_NOVEL => '小说',
				TYPE_MUSIC => '音乐',
				TYPE_DRAMA => '剧集',
				TYPE_PERIO => '期刊',
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
		],
		"origin_name"     => [
			'name'    => '作品名称（原文）',
			'type'    => 'input',
			'subtype' => 'text',
			'must'    => true,
		],
		"cover_pic"       => [
			'name'    => '封面图',
			'type'    => 'upload',
			'subtype' => 'picture',
			'text'    => '图片最大不能超过2M',
		],
		"detail"          => [
			'name'    => '简单介绍',
			'type'    => 'input',
			'subtype' => 'textarea',
			'text'    => 'markdown使用可能，剧透、个人观点等禁止。',
		],
		"episodes"        => [
			'name'    => '话数',
			'type'    => 'number',
			'subtype' => '[1,255]',
			'show_name'=>'长度',
		],
		"broadcast_range" => [
			'name'    => '放送时间',
			'type'    => 'dateragne',
			'subtype' => ['yyyy-mm-dd', 'yyyy-mm-dd'],
		],
		"day_of_week"     => [
			'name'    => '放送星期',
			'type'    => 'select',
			'subtype' => [
				'5' => '星期五',
				'6' => '星期六',
				'7' => '星期日',
				'1' => '星期一',
				'2' => '星期二',
				'3' => '星期三',
				'4' => '星期四',
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
				'aria-11'  => '11区',
				'mainland' => '大陆',
				'SAR'      => '特区',
				'west'     => '西方',
			]
		],
		"originalwork"    => [
			'name'      => '同人/改编 原作',
			'type'      => 'input',
			'subtype'   => 'text',
			'text'      => '这是一部同人或改编作品，此处填写其原作名称',
			'show_name' => '原作',
		],
		"robot"           => [
			'name'    => '萝卜',
			'type'    => 'static',
			'subtype' => true,
			'text'    => '这是一部萝卜片',
		],
		"externalize"     => [
			'name'    => '主要內容',
			'type'    => 'inputlist',
			'subtype' => [
				'type'    => 'select',
				'subtype' => [ // 需要同步 getVoteCatelog
					// 感情
					'almostwwww'  => '日常',
					'wwww'        => '爆笑',
					'Q_Q'         => '催泪',
					'derstand'         => '深沉',
					'saya'        => '猎奇',
					'terror'      => '恐怖',
					// 受众
					'801'         => '基',
					'gl'          => '百合',
					'sena'        => '卖肉',
					'sellmoe'     => '卖萌',
					'kuso'        => '恶搞',
					// 题材
					'sf'          => '科幻',
					'newton-died' => '魔法/伪科幻',
					'genji'       => '现实',
					'school'      => '校园',
					'battle'      => '战斗',
				]
			],
		],
	);
	foreach($ret as $id => $data){
		$ret[$id]['id'] = $id;
	}
	return $ret;
}
