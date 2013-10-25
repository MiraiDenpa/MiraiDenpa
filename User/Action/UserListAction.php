<?php
/**
 * @default_method
 * @class  UserListAction
 * @author GongT
 */
class UserListAction extends Action{

	/**
	 * 关键词搜索（昵称、简介、签名、etc）： keyword
	 * 搜昵称： nick
	 * 签名和签名： info
	 *
	 * 全都可以是数组
	 *
	 * @return void
	 */
	final public function search(){
		$where = [];
		if(isset($_GET['nick'])){
			$nick = is_scalar($_GET['nick'])? [$_GET['nick']] : $_GET['nick'];
		}
		if(isset($_GET['info'])){
			$info = is_scalar($_GET['info'])? [$_GET['info']] : $_GET['info'];
		}
	}

	/**
	 * 用于@后的提示、补全用户信息、etc
	 *
	 * @return void
	 */
	final public function uidlist(){
		$uidlist = $_REQUEST['list'];
		if(!$uidlist){
			$this->error(ERR_INPUT_REQUIRE, 'list');
			return;
		}
		$ppmdl   = ThinkInstance::D('UserProperty');
		$uidlist = array_unique($uidlist);
		$list    = $ppmdl->getList(['_id' => ['$in' => $uidlist]], false);
		if(count($list) < count($uidlist)){
			$exists = [];
			foreach($list as $item){
				$exists[] = $item['_id'];
			}
			$notexist =array_diff($uidlist,$exists);
			$this->assign('notexist', $notexist);
		}
		$this->assign('list', $list);
		
		$this->success();
	}
}
