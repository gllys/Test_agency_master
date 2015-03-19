<?php
/**
 * 消息 
 * 票审核通过、驳回。
 * 机构启用、禁用。
 * 退票审核、驳回。
 * 新结款单通知。
 * 已结款通知。
 * 
 * 2014-03-06
 *
 * @package commons
 * @author  cuiyulei
 */
class MessageCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["post data is null"]}}',
		'-2'  => '{"errors":{"msg":["删除消息失败"]}}',
		'-3'  => '{"errors":{"msg":["删除消息失败，缺少必要参数"]}}',
		'-4'  => '{"errors":{"msg":["消息内容不能为空"]}}',
		'-5'  => '{"errors":{"msg":["请选择要发送的机构"]}}',
		'-6'  => '{"errors":{"msg":["发送消息失败"]}}',

		'-21'  => '{"errors":{"msg":["null post"]}}',
        '-22'  => '{"errors":{"msg":["缺少必要的参数"]}}',
        '-23'  => '{"errors":{"msg":["错误的状态"]}}',
        '-24'  => '{"errors":{"msg":["不存在的公告信息"]}}',
        '-25'  => '{"errors":{"msg":["公告已经审核，无需重复操作"]}}',
        '-26'  => '{"errors":{"msg":["公告已经拒绝，无需重复操作"]}}',
        '-27'  => '{"errors":{"msg":["保存至数据库失败"]}}',
        '-28'  => '{"errors":{"msg":["公告已经通过，拒绝删除"]}}',
        '-29'  => '{"errors":{"msg":["删除公告失败"]}}',
	);

	//审核状态
	public static $verifyStatus = array(
		'unaudited' => '未审核',
		'normal'    => '已审核',
		'failed'    => '拒绝'
	);

	//发送者身份
	public static $organization_type = array(
        'government' 	=>'政府机构',
        'agency' 		=> '旅行社',
        'landscape' 	=> '景区',
        'ota' 			=> 'ota',
        'system'        => '系统'
    );


	/**
	 * 获取审核状态
	 *
	 * @param string $key
	 *
	 * @return mixed string | array
	 * @author cuiyulei
	 **/
	public static function getVerifyStatus($key = '')
	{
		if ($key) {
			return self::$verifyStatus[$key];
		} else {
			return self::$verifyStatus;
		}
	}

	/**
	 * 获取审核状态
	 *
	 * @param string $key
	 *
	 * @return mixed string | array
	 * @author cuiyulei
	 **/
	public static function getOrganizationType($key = '')
	{
		if ($key) {
			return self::$organization_type[$key];
		} else {
			return self::$organization_type;
		}
	}

	/**
	 * 获取公告
	 *
	 * @param array $post
	 *
	 * @return array $data
	 * @author cuiyulei
	 **/
	public function getNotice(&$get)
	{
		$messageModel = $this->load->model('message');

		$param['page']  = $this->getGet('p') ? $this->getGet('p') : ($get['p'] ? $get['p'] : 1);
        $param['items'] = 10;
        $param['relate']= 'from_organization';
        $param['order'] = 'created_at desc';
        $param['filter'][$messageModel->table.'.deleted_at'] = null;
		//查询时间
		if ($get['time']) {
			$time = explode(' - ', $get['time']);
			$time[0] = date('Y-m-d', strtotime($time[0]))." 00:00:00";
			$time[1] = date('Y-m-d', strtotime($time[1]))." 23:59:59";
			$param['filter'][$messageModel->table.'.created_at|between'] = $time;
		}

		//必须是机构发的公告、或者系统发的公告
		$param['filter'][$messageModel->table.'.msg_type|in'] = array('all','system_all');

		//机构类型
		if ($get['organization_type']) {
			if ($get['organization_type'] == 'system') {
				$param['filter'][$messageModel->table.'.from_organization_id'] = 0;
			} else {
				$organizationModel = $this->load->model('organizations');
				$sql = "SELECT `id` FROM ".$organizationModel->table." WHERE `type`='".$get['organization_type']."'";
				$organization_ids = $organizationModel->getList($sql);
				if ($organization_ids) {
					$oids = array();
					foreach ($organization_ids as $key => $value) {
						$oids[] = $value['id'];
					}
					$param['filter'][$messageModel->table.'.from_organization_id|in'] = $oids;
				}
			}
		}

		//状态
		if ($get['status']) {
			$param['filter'][$messageModel->table.'.status'] = $get['status'];
		}

		$data = $messageModel->commonGetList($param);

		return $data;
	}

	/**
	 * 审核系统公告
	 *
	 * @param array $post
	 *
	 * @return json
	 * @author cuiyulei
	 **/
	public function verify($post)
    {
        if($post) {
            //机构id和状态
            if(!$post['id'] || !$post['status']) {
                return $this->_getUserError(-22);
            }

            //状态对应
            if(!array_key_exists($post['status'], self::getVerifyStatus())) {
                return $this->_getUserError(-23);
            }

            $MessageModel = $this->load->model('message');
            $where        = "`id`=".$post['id']." AND `msg_type` IN ('all', 'system_all')";
            $oldInfo      = $MessageModel->getOne($where, 'status');
            if($oldInfo) {
                if($post['status'] == $oldInfo['status'] && $post['status'] == 'normal') {
                    return $this->_getUserError(-25);
                }

                if($post['status'] == $oldInfo['status'] && $post['status'] == 'failed') {
                    return $this->_getUserError(-26);
                }

            } else {
                return $this->_getUserError(-24);
            }

            $updateArray = array(
                'verify_by'     => $_SESSION['backend_userinfo']['id'],
                'status'        => $post['status'],
                'verify_time'   => date('Y-m-d H:i:s')
            );

            $result       = $MessageModel->update($updateArray, array('id' => $post['id']));
            $affectedRows = $MessageModel->affectedRows();
            if($result && $affectedRows >= 1) {
                return json_encode(array('data' => array($updateArray)));                
            } else {
                return $this->_getUserError(-27);
            }
        } else {
            return $this->_getUserError(-21);
        }
    }


    /**
     * 删除公告 
     */
    public function deleteNotice($post){
    	$MessageModel = $this->load->model('message');
        $where        = "`id`=".$post['id']." AND `msg_type` IN ('all', 'system_all')";
        $oldInfo      = $MessageModel->getOne($where);
        if($oldInfo){
        	if($oldInfo['status'] == 'failed' && $post['status'] == 'delete'){
        		$updateArray = array(
	                'verify_by'     => $_SESSION['backend_userinfo']['id'],
	                'verify_time'   => date('Y-m-d H:i:s'),
	                'deleted_at'    => date('Y-m-d H:i:s')
            	);
        	}else{
        		return $this->_getUserError(-28);
        	}
        }
        $result = $MessageModel->update($updateArray, array('id' => $post['id']));
        if($result){
        	return json_encode(array('data' => array($updateArray))); 
        }else{
        	return $this->_getUserError(-29);
        }
    }

	/**
	 * 删除信息  逻辑删除
	 * @param array $post  
	 * @return json
	 */
	public function delete($post)
	{
		if(!$post['id']) {
			return $this->_getUserError(-3);
		}

		$userMessageStatusModel    = $this->load->model('userMessageStatus');

		$uid = $_SESSION['backend_userinfo']['id'];
		$userMessageStatusModel->deleteMsgs($uid, $post['id']);
		return '{"succ":"succ"}';
	}

	// public function getUnreadMsgNums()
	// {
	// 	$messageModel = $this->load->model('message');
	// 	$nums         = $messageModel->getOne(array('to_uid' => $_SESSION['backend_userinfo']['id'], 'has_read' => 'false'), '', 'count(id) as nums');
	// 	return $nums['nums'];
	// }

	/**
	 * 读消息
	 * @param array $post 
	 * @return json
	 */
	public function read($post)
	{
		if(!$post['id']) {
			return $this->_getUserError(-3);
		}

		$userMessageStatusModel    = $this->load->model('userMessageStatus');
		$exists                    = $userMessageStatusModel->getOne(array('user_id' => $_SESSION['backend_userinfo']['id'], 'message_id' => $post['id'], 'user_type' => 'system'));
		if($exists){
			return '{"succ":"succ"}';
		}else{
			$userMessageStatusModel->add(array('user_id' => $_SESSION['backend_userinfo']['id'], 'message_id' => $post['id'], 'user_type' => 'system', 'status' => 'read'));
			$affectedRows = $userMessageStatusModel->affectedRows();
			if($affectedRows >= 1) {
				return '{"succ":"succ"}';
			}
			return $this->_getUserError(-2);
		}
	}

	/**
	 * 自己发消息
	 * @param  array $post 
	 */
	public function addmsg($post)
	{
		if(!$post['content'] || !isset($post['content'])) {
			return $this->_getUserError(-4);
		}

		if(!isset($post['user_type']) && !$post['receive']) {
			return $this->_getUserError(-5);
		}

		$uid = $_SESSION['backend_userinfo']['id'];
		$oid = 0;
		if(!isset($post['user_type'])) {
			$params = array(
				'from_user'            => $uid,
				'from_organization_id' => $oid,
				'msg_type'             => 'system_org',
				//'create_time'          => time(),
				'status'               => 'normal',
				'content'              => $post['content'],
			);
			$messageModel = $this->load->model('message');
			$messageModel->begin();
			$messageModel->add($params);
			$addId        = $messageModel->getAddID();
			$params['id'] = $addId;
			if(!$addId) {
				$messageModel->rollback();
			} else {
				$messageReceiveModel = $this->load->model('messageReceive');
				$result              = $messageReceiveModel->addReceives($addId, array_unique($post['receive']));
				if(!$result) {
					$messageModel->rollback();
					return $this->_getUserError(-6);
				}
				$messageModel->commit();
			}

			return json_encode(array('data' => array($params)));
		} else {
			$params = array(
				'from_user'            => $uid,
				'from_organization_id' => $oid,
				'msg_type'             => 'system_all',
				//'create_time'          => time(),
				'status'               => 'normal',
				'content'              => $post['content'],
			);
			$messageModel = $this->load->model('message');
			$messageModel->add($params);
			$addId        = $messageModel->getAddID();
			if(!$addId) {
				return $this->_getUserError(-6);
			}
			$params['id'] = $addId;
			return json_encode(array('data' => array($params)));
		}
	}


	/**
	 * 发送一条消息
	 *
	 * @param int $organization_id
	 * @param string $msg
	 *
	 * @return boolean
	 * @author cuiyulei
	 **/
	public function send($organization_id, $msg)
	{
		$messageModel = $this->load->model('message');
		$messageData  = array(
			'from_user'            => $_SESSION['backend_userinfo']['id'],
			'from_organization_id' => 0,
			'msg_type'             => 'system_org',
			'content'              => $msg,
			'status'               => 'normal',
			//'create_time'          => time()
		);

		$messageModel->begin();
		$messageModel->add($messageData);
		$addId = $messageModel->getAddID();
		if(!$addId) {
			$messageModel->rollback();
		} else {
			$messageReceiveModel = $this->load->model('messageReceive');
			$result              = $messageReceiveModel->addReceives($addId, array('organization_id' => $organization_id));
			if(!$result) {
				$messageModel->rollback();
				return $this->_getUserError(-6);
			}
			$messageModel->commit();
		}
	}
}