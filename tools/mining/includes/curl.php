<?php
/**
 * @property int code
 */
class BangumiCurl{
	private $curl;
	private $_code;

	/**  */
	public function __construct($referer='http://bangumi.tv/'){
		$this->curl = curl_init();
		register_shutdown_function('curl_close', $this->curl);
		curl_setopt_array($this->curl,
						  array(
							   CURLOPT_FAILONERROR      => true,
							   CURLOPT_FOLLOWLOCATION   => true,
							   CURLOPT_MAXREDIRS        => 5,
							   CURLOPT_HEADER           => true,
							   CURLOPT_NOPROGRESS       => false,
							   CURLOPT_RETURNTRANSFER   => true,
							   CURLOPT_COOKIEFILE       => 'cookie.txt',
							   CURLOPT_COOKIEJAR        => 'cookie.txt',
							   CURLOPT_REFERER          =>$referer,
							   CURLOPT_MAXCONNECTS      => 30,
							   CURLOPT_PROGRESSFUNCTION => [$this, '__progress'],
						  ));
	}

	/**  */
	public function get($url){
		$this->rand_user_agent();
		curl_setopt($this->curl, CURLOPT_URL, $url);
		$document    = $ret = trim(curl_exec($this->curl));
		$this->_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		return $document;
	}

	/**  */
	public function  __get($name){
		if($name == 'code'){
			return $this->_code;
		} else{
			return null;
		}
	}

	/**  */
	public function __progress($ch, $download_size, $downloaded, $upload_size, $uploaded){
		echo "SEND: $downloaded/$download_size                   \r";
	}

	/**  */
	protected function  rand_user_agent(){
		static $ualist = [
			'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1664.3 Safari/537.36',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.9 Safari/536.5',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:25.0) Gecko/20100101 Firefox/25.0',
			'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
			'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
			'Mozilla/5.0 (Windows NT 6.0; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:24.0) Gecko/20100101 Firefox/24.0',
			'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0',
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)',
			'Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; zh-CN))',
			'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; zh-CN)',
			'Mozilla/5.0 (X11; Linux) KHTML/4.9.1 (like Gecko) Konqueror/4.9',
			'Mozilla/5.0 (X11; Linux 3.5.4-1-ARCH i686; es) KHTML/4.9.1 (like Gecko) Konqueror/4.9',
			'Mozilla/5.0 (X11) KHTML/4.9.1 (like Gecko) Konqueror/4.9',
		];
		curl_setopt($this->curl, CURLOPT_USERAGENT, $ualist[intval(rand(0, count($ualist) - 1))]);
	}
}
