<?php
/**
 * @default_method all
 * @class          WeiboMyAction
 * @author         GongT
 */
class WeiboMyAction extends Action{
	use UserAuthedAction;

	/** @var WeiboModel */
	private $publish;

	/**
	 * @constructor yes
	 * @return void
	 */
	protected function _in(){
		$this->publish = ThinkInstance::D('Weibo');
	}

	final public function all($page = 1){
		$_GET[VAR_PAGE] = $page;
		if(isset($_GET['perpage'])){
			$perpage = min($_GET['perpage'], 120);
		} else{
			$perpage = 30;
		}
		$page = new Page($total,$perpage);
	}

	/**  */
	final public function next(){
		$_POST                            = array(
			'content' => '测试微博内@a111 @b222容 @ccc ' . rand(),
			'forward' => '',
			'sendto'  => 'square',
			'channel' => 'info',
		);
		$this->dispatcher->request_method = 'POST';
		// debug

		if($this->dispatcher->request_method !== 'POST'){
			return $this->error(ERR_NALLOW_HTTP_METHOD, 'only POST');
		}

		if(!$this->filter($_POST)){
			return false;
		}
		$data      = $this->preprocess($_POST);
		$data->_id = new MongoId();

		$ret     = $this->publish->insert(get_object_vars($data));
		$success = $this->mongo_ret($ret);

		if($success){
			$this->dispatcher->finish();
			$new_id = (string)$data->_id;
			$stati  = ThinkInstance::D('UserStatistics', $this->token_data['user']);
			$stati->postWeiboOccur();
			if(!empty($data->at)){
				$notice = ThinkInstance::D('WeiboNotice');
				$notice->noticeAll('at', $new_id, $data->at);
			}
		}
		return null;
	}

	/**  */
	final public function last(){
		$last = $this->publish->getUserLast($this->token_data['user'], $this->token_data['app']);
		if(null == $last['_id']){
			return $this->error(ERR_MISCELLANEOUS, 'never post anything');
		}

		$oid         = $last['_id'];
		$last['_id'] = (string)$oid;

		$_POST                            = array(
			'content' => '测试微博内@a111 @b222容 @gggc c',
			'forward' => '',
			'sendto'  => 'square',
			'channel' => 'info',
		);
		$this->dispatcher->request_method = 'DELETE';
		// debug

		if($this->dispatcher->request_method == 'GET'){
			$this->assign($last);
			return $this->display(':WeiboDetail');
		} elseif($this->dispatcher->request_method == 'POST'){
			if($last['time'] < time() - 300){
				return $this->error(ERR_TIMEOUT, '5min');
			}
			if(!$this->filter($_POST)){
				return false;
			}
			$data = $this->preprocess($_POST);

			if($data->content == $last->content){
				return $this->error(ERR_MISCELLANEOUS, 'not change');
			}

			$delete = array_diff($last->at, $data->at);
			$add    = array_diff($data->at, $last->at);
			if(!empty($add) || !empty($delete)){
				$notice = ThinkInstance::D('WeiboNotice');
				if(!empty($add)){
					$notice->noticeAll('at', $last['_id'], $add);
				}
				if(!empty($delete)){
					foreach($delete as $uid){
						$notice->delete($uid, 'at', $last['_id']);
					}
				}
			}
			$data['_id'] = $oid;
			$ret         = $this->publish->save(get_object_vars($data));
			return $this->mongo_ret($ret);
		} elseif($this->dispatcher->request_method == 'DELETE'){
			if($last['time'] < time() - 300){
				return $this->error(ERR_TIMEOUT, '5min');
			}
			$ret = $this->publish->remove(['_id' => $oid]);
			return $this->mongo_ret($ret);
		} else{
			return $this->error(ERR_NALLOW_HTTP_METHOD);
		}
	}

	/**
	 * @param $arr
	 *
	 * @return WeiboEntity
	 */
	private function preprocess($arr){
		$data          = new WeiboEntity;
		$data->content = $arr['content'];

		if(isset($arr['forward']) && $arr['forward']){
			$data->forward = ForwardEntity::parse($arr['forward']);
			if(!$data->forward){
				return $this->error(ERR_PARSE_FORWARD, 'content');
			}
		}
		if(isset($arr['sendto']) && $arr['sendto']){
			$data->sendto = explode(',', $arr['sendto']);
		}
		if(isset($arr['channel']) && $arr['channel']){
			$data->channel = $arr['channel'];
		}

		$atlist   = parse_weibo_at($data->content);
		$data->at = $atlist;

		$data->user = $this->token_data['user'];
		$data->app  = $this->token_data['app'];
		$data->time = time();

		return $data;
	}

	/**  */
	private function filter($arr){
		if(!isset($arr['content'])){
			$this->error(ERR_INPUT_REQUIRE, 'content');
			return false;
		}
		if(isset($arr['forward'])){
			if(strlen($arr['content']) < 6){ // 2个汉子
				$this->error(ERR_INPUT_RANGE, 'content');
				return false;
			}
		} else if(strlen($arr['content']) < 30){ // 10个
			$this->error(ERR_INPUT_RANGE, 'content');
			return false;
		}

		$last = $this->publish->getUserLast($this->token_data['user'], '', ['content' => true]);
		if($last['content'] == $arr['content']){
			$this->error(ERR_DUP_POST, '');
			return false;
		}
		if(comapre_post_like($last['content'], $arr['content'])){
			$this->error(ERR_DUP_POST_SAME, '');
			return false;
		}

		return true;
	}
}
