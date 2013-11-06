<?php
class InfoEntryEntity extends Entity{
	public $_id;
	public $_oid;
	public $_url;
	public $_update;
	public $_history;
	public $_change = false;
	//<GEN>
	public $catalog;
	public $name;
	public $origin_name;
	public $cover_pic;
	public $detail;
	public $episodes;
	public $broadcast_range;
	public $day_of_week;
	public $official_site;
	public $classification;
	public $come_from;
	public $originalwork;
	public $robot;
	public $externalize;
	//</GEN>

	//<DEBUG>
	/**  */
	public function __construct(){
		$json = TaglibReplaceItem_fields();
		if(S(md5($json))){
			return;
		}
		$fields  = array_keys(json_decode($json, true));
		$delcare = '';
		foreach($fields as $field){
			$delcare .= "\tpublic \${$field};\n";
		}

		$content = file_get_contents(__FILE__);
		$content = preg_replace('#<GEN>.*?</GEN>#s', "<GEN>\n{$delcare}\t//</GEN>", $content, 1);
		S(md5($json), file_put_contents(__FILE__, $content));
	}

	//</DEBUG>
	protected function _init(){
		$this->_oid = (string)$this->_id;
		$this->_url = UG('info', 'View', 'id', $this->_oid);
	}
}
