<?php
class UserNoticeModel extends Mongoo{
	protected $collectionName = 'notice';
	protected $connection = 'mongo-user';

	public function noticeAll($type, $content, $whos){
		// 分开已有的和新加的
		$where       = array(
			'_id.user' => ['$in' => $whos],
			'_id.type' => $type,
		);
		$exist       = iterator_to_array($this->find($where, ['_id' => true]), false);
		$exist_users = array_column(array_column($exist, '_id'), 'user');
		$whos        = array_diff($whos, $exist_users);

		//在原有上增加
		if(!empty($exist_users)){
			$what                     = array(
				'$push' => [
					'unread' => $content,
					'list'   => $content,
				]
			);
			$where['_id.user']['$in'] = $exist_users;
			$this->update($where, $what, ['multiple' => true]);
		}

		// 添加新的
		foreach($whos as $uid){
			$list[] = array(
				'_id'    => ['user' => $uid, 'type' => $type],
				'list'   => [$content],
				'unread' => [$content],
			);
		}
		if(!empty($list)){
			$this->batchInsert($list);
		}
	}

	/**
	 * 通知状态改为已读
	 * 不传 $items 则全部已读
	 *
	 * @param string       $uid
	 * @param string       $type
	 * @param array|string $items
	 *
	 * @return bool
	 */
	public function read($uid, $type, $items = null){
		$where = array(
			'_id.user' => $uid,
			'_id.type' => $type,
		);
		if($items){
			if(is_array($items)){
				$update = array(
					'$pullAll' => [
						'unread' => $items,
					],
				);
			} else{
				$update = array(
					'$pull' => [
						'unread' => $items,
					],
				);
			}
		} else{
			$update = array(
				'unread' => [],
			);
		}

		return $this->update($where, $update);
	}

	/**
	 * 删除通知
	 * 不传 $items 则清空收件箱
	 *
	 * @param string       $uid
	 * @param string       $type
	 * @param array|string $items
	 *
	 * @return bool
	 */
	public function delete($uid, $type, $items = null){
		$where = array(
			'_id.user' => $uid,
			'_id.type' => $type,
		);
		if($items){
			if(is_array($items)){
				$update = array(
					'$pullAll' => [
						'list'   => $items,
						'unread' => $items,
					],
				);
			} else{
				$update = array(
					'$pull' => [
						'list'   => $items,
						'unread' => $items,
					],
				);
			}
		} else{
			$update = array(
				'list'   => [],
				'unread' => [],
			);
		}

		return $this->update($where, $update);
	}
}
