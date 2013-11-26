<?php
function wiki_boot($type, callable $cb){
	boot(function ($lname) use ($type, &$cb){
		log_to_file('wiki' . $lname);
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		@mkdir('output/' . $lname);
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		@mkdir('raw/' . $lname . '/wiki-pages', 0777, true);

		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$data = json_decode(@file_get_contents('output/' . $lname . '.json'), true);
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$processed = explode("\n", @file_get_contents('raw/' . $lname . '/fails-wiki.txt'));

		$count = count($data);
		$now   = count(glob('raw/' . $lname . '/wiki-pages/*')) + count($processed);

		foreach($data as $bid => $name_pair){
			$now++;
			$title   = $name_pair[$type];
			$save_to = 'raw/' . $lname . '/wiki-pages/' . eacape_filename($title) . '.html';
			if(in_array($bid, $processed, false) || is_file($save_to)){
				continue;
			}
			if(!$title){
				error_and_log('没有这个语言的版本：[' . $lname . ']' . $bid . ':' . $name_pair[abs($type - 1)]);
				file_put_contents('raw/' . $lname . '/fails-wiki.txt', $bid . "\n", FILE_APPEND);
				continue;
			}
			list($code, $document) = $cb($bid, $title);
			if($code != 200){
				error_and_log('没有wiki页面(' . $code . ')：[' . $lname . ']' . $bid . ':' . $title);
				file_put_contents('raw/' . $lname . '/fails-wiki.txt', $bid . "\n", FILE_APPEND);
				continue;
			}

			success('成功获取wiki页面[' . $now . '/' . $count . ']', ['保存到' => $save_to]);
			file_put_contents($save_to, $document);
		}
	});
}
