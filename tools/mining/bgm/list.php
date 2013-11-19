<?php
require '../includes/curl.php';
require '../includes/output.php';

/**  */
class MainListIterator{
	private $listType = '';
	private $current_page = '';
	private $count = '';
	private $url = '';

	private $_curl = '';
	private $last_ret = '';

	/**  */
	public function __construct($listType){
		log_to_file($listType);
		$this->_curl = new BangumiCurl();
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		@mkdir('raw/' . $listType . '/listing/error', 0777, true);
		
		$this->listType = $listType;
		$this->url      = self::$lists[$listType];
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$current_data = json_decode(@file_get_contents('raw/' . $listType . '/status.txt'), true);

		if($current_data){
			normallog("读取list： " . $this->listType);
			$this->current_page = intval($current_data['page']);
			$this->count        = intval($current_data['count']);
		} else{
			normallog("初始化list： " . $this->listType);
			$document = $this->_curl->get($this->url . '1');
			$code     = $this->_curl->code;
			echo "\t\tHTTP: {$code}.\n";
			preg_match_all('#\<a href\=".*\?page\=(\d+)" class\="p"\>&rsaquo;\|\</a\>#', $document, $mats);

			if(isset($mats[1][0])){
				echo "\t\t最大页码: {$mats[1][0]}.\n";
				$this->current_page = 1;
				$this->count        = intval($mats[1][0]);
				$this->save_current();
			} else{
				file_put_contents('error.html', $document);
				error_and_log("不能分析最大页码[{$this->listType}]", ['文档保存至' => 'error.html']);
				exit;
			}
		}
	}

	/**  */
	public function save_current(){
		$data          = [];
		$data['page']  = $this->current_page;
		$data['count'] = $this->count;
		file_put_contents('raw/' . $this->listType . '/status.txt', json_encode($data));
	}

	/**  */
	public function hasNext(){
		return $this->count >= $this->current_page;
	}

	/**  */
	public function getMoveNext(){
		$url            = $this->url . $this->current_page;
		$this->last_ret = $ret = $this->_curl->get($url);
		$code           = $this->_curl->code;

		$list_found = strpos($ret, 'id="browserItemList"');
		if($list_found===false){
			$list_found = strpos($ret, 'id="columnCrtBrowserB"');
		}
		
		if($code == 200 && $list_found){
			$save_to = 'raw/' . $this->listType . '/listing/' . $this->current_page . '.html';
			file_put_contents($save_to, $ret);
			success_and_log("HTTP请求返回[{$code}:P:{$list_found}] -- {$this->listType}[{$this->current_page}/{$this->count}]",
							['请求URL' => $url, '保存到' => $save_to]);
			$this->current_page++;
			$this->save_current();
		} else{
			$save_to = 'raw/' . $this->listType . '/listing/error/' . $this->current_page . '.html';
			file_put_contents($save_to, $ret);
			error_and_log("HTTP请求返回[{$code}:P:{$list_found}] -- {$this->listType}[{$this->current_page}/{$this->count}]",
						  ['请求URL' => $url, '保存到' => $save_to]);
		}
		return $code;
	}

	static private $lists = [
		'anime' => "http://bangumi.tv/anime/browser?sort=title&page=",
		'music' => "http://bangumi.tv/music/browser?sort=title&page=",
		'game'  => "http://bangumi.tv/game/browser?sort=title&page=",
		'real'  => "http://bangumi.tv/real/browser/platform/all?sort=title&page=",
		'comic'  => "http://bangumi.tv/book/browser/comic/series?page=",
	];
}

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@mkdir('raw');
/** @noinspection PhpUsageOfSilenceOperatorInspection */
@mkdir('log');

$itr_list = [];
boot(function ($lname){
	$item = new MainListIterator($lname);
	normallog("开始请求： [$lname]");
	while($item->hasNext()){
		$item->getMoveNext();
	}
	$files = glob('raw/' . $lname . '/listing/*.html');
	success("请求完毕： [$lname]， 共计页数：" . count($files) . ' [ x24 = ' . (count($files)*24) . ' ]');
	sort($files);
	$url_to_title = [];
	foreach($files as $fn){
		echo "$fn             \r";
		$content = file_get_contents($fn);
		$must24  = preg_match_all('#<a href="/subject/(.+)" class="l">(.*)</a>( <small class="grey">(.*)</small>)?#',
								  $content,
								  $mats);
		if($must24 != 24){
			echo "\n";
			error('本页少于24行。[ = ' . $must24 . ']');
		}

		foreach($mats[1] as $i => $url){
			$title = $mats[4][$i]? $mats[4][$i] : $mats[2][$i];
			if(isset($url_to_title[$url])){
				error_and_log('url冲突', ['原始' => $url_to_title[$url], '新的' => $title]);
				if(strpos($url, '?')){
					$url .= '&conflict=' . $url_to_title[$url];
				} else{
					$url .= '?conflict=' . $url_to_title[$url];
				}
			}
			$url_to_title[$url] = $title;
		}
	}
	ksort($url_to_title);
	success("完毕！共计条目：" . count($url_to_title));
	echo "\n";
	file_put_contents('raw/' . $lname . '/listing.map',
					  json_encode($url_to_title, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES));
});
