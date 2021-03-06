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
	protected $allow_public = false;

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
	}

	final public function test(){
		$this->dispatcher->request_method = 'POST';
		//
		$userlist = ThinkInstance::D('UserList');
		$list     = array_column($userlist->field('uid')->select(),
								 'uid');
		$users    = function () use ($list){
			static $i;
			$i = $i + 1;
			if($i == count($list)){
				$i = 0;
			}
			return $list[$i];
		};

		$cache = [];
		$post  = function ($forward = false) use (&$cache, $users){
			$data            = [];
			$data['sendto']  = 'square';
			$data['channel'] = 'info526240db7f8b9a891c8b4567';
			if($forward){
				static $fId;
				if(!$fId){
					$fId = 1;
				}
				$data['forward'] = [
					'type'    => 'mirai/denpa',
					'content' => $forward
				];
				$level           = $cache[$forward][1] + 1;
				$title           = '转发' . ($fId++);
				$content         = $title . ' [ {this}';
				$itr             = $cache[$forward];
				do{
					$content .= '->' . $itr[0];
				} while($itr = $cache[$itr[2]]);
				$content = $content . ']';
			} else{
				static $oId;
				if(!$oId){
					$oId = 1;
				}
				$data['forward'] = [
					'type'    => 'site/bilibili',
					'content' => 'av103196'
				];
				$title           = $content = '原创' . ($oId++);
				$level           = 0;
			}
			$data['content'] = '[' . $level . '] ' . $content . ' ' . md5(rand());
			$_POST           = $data;
			$data            = $this->preprocess($data);
			$data->_id       = new MongoId();
			$id              = (string)$data->_id;
			// 随机选用一个用户
			$data->user = $users();

			$ret = $this->publish->postNewWeibo($data);
			echo '发微博 ' . $content;
			var_dump($ret);

			$cache[$id] = [$title, $level, $forward];
			return $id;
		};
		$this->publish->remove([]);
		$pid   = $post(); // 第一条
		$recid = $post(); // 第二条

		$rid = $post($pid); // 转1条
		$i   = 2;
		while($i--){
			$post($pid); // 转1条
		}
		$i = 5;
		while($i--){
			$post($rid); // 转-转1条
		}

		$post(); // 第三条
		$post($pid); // 再转第一条
		$i = 2;
		while($i--){
			$post($pid);
		}

		// 循环转第二条
		$i = 6;
		while($i--){
			$recid = $post($recid);
		}

		exit;
		/*$_POST                            = array(
			'content' => '[0] 原创 [ 原创2 ] ' . md5(rand()),
			'forward' => [
				'type' => 'site/bilibili',
				'content' => 'av103196'
			],
			'sendto'  => 'square',
			'channel' => 'info',
		);*/
		// debug
	}

	/**  */
	final public function next(){
		//$str = 'content=asdsadasd' . dechex(rand(0,
		//										 65535)) . 'asdsad&channel=chap526240db7f8b9a891c8b4567_0&forward%5Btype%5D=mirai%2Fdenpa&forward%5Bcontent%5D=52863f137f8b9aee1f8b456b&forward%5Blist%5D=&forward%5Boriginal%5D=&forward%5Barg1%5D=&forward%5Barg2%5D=';
		//$str = 'content=asdsadasd' . dechex(rand(0,
		//										 65535)) . 'asdsad&channel=chap526240db7f8b9a891c8b4567_0&forward%5Btype%5D=mirai%2Finfo-chapter&forward%5Bcontent%5D=0&forward%5Blist%5D=&forward%5Boriginal%5D=&forward%5Barg1%5D=526240db7f8b9a891c8b4567&forward%5Barg2%5D=';
		//parse_str($str, $_POST);
		//$this->dispatcher->request_method = 'POST';

		if($this->dispatcher->request_method !== 'POST'){
			return $this->error(ERR_NALLOW_HTTP_METHOD, 'only POST');
		}

		if(!$this->filter($_POST)){
			return false;
		}
		$data      = $this->preprocess($_POST);
		$data->_id = new MongoId();

		$ret = $this->publish->postNewWeibo($data);
		$this->assign('id', (string)$data->_id);
		$success = $this->mongo_ret($ret);

		if($success){
			$this->assign('_id', (string)$data->_id);
			$this->dispatcher->finish();
			$new_id = (string)$data->_id;
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
	 * arg1: null
	 * arg2: null
	 * content: "526240db7f8b9a891c8b4567"
	 * list: null
	 * original: !
	 * type: "mirai/info-entry"
	 *
	 * @return WeiboEntity
	 */
	private function preprocess($arr){
		$data          = new WeiboEntity;
		$data->content = $arr['content'];

		$data->content = htmlentities($data->content);

		if(isset($arr['forward']) && is_array($arr['forward'])){
			// 转发微博
			$data->forward = ForwardEntity::buildFromArray($arr['forward']);
			if(!$data->forward){
				return $this->error(ERR_PARSE_FORWARD, 'content');
			}
			if($data->forward['type'] == 'mirai/denpa'){
				$forward_weibo = $this->publish->getWeiboById($data->forward->content);
				if(!$forward_weibo){
					return $this->error(ERR_TARGET_NOT_EXIST, $data->forward->content);
				}

				$data->level = $forward_weibo->level + 1;
				if(empty($forward_weibo->forward->original)){ // 转发目标是条原创
					$data->forward->original = [];
				} else{ // 转发目标也是转发
					$data->forward->original = $forward_weibo->forward->original;
				}
				$data->forward->original[] = (string)$forward_weibo->_id;

				$this->publish->forwarded($forward_weibo);
			} else{
				// 转发内容不是另一条微博
				$data->level = 0;
				if(!is_array($data->forward->original)){
					if(!empty($data->forward->original) || is_numeric($data->forward->original)){
						$data->forward->original = [$data->forward->original];
					} else{
						$data->forward->original = null;
					}
				}
			}
		} else{
			// 原创微博
			$data->forward->original = null;
			$data->level             = 0;
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
				$this->error(ERR_INPUT_RANGE, 'content least ' . strlen($arr['content']));
				return false;
			}
		} else if(strlen($arr['content']) < 30){ // 10个
			$this->error(ERR_INPUT_RANGE, 'content least ' . strlen($arr['content']));
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
