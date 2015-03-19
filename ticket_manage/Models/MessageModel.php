<?php
/**
 *
 *
 * 2014-01-13 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class MessageModel extends BaseModel
{
	// 定义要操作的表名
	public $db     = 'fx';
	public $table  = 'message';
	public $pk     = 'id';

	//可关联的,对应value为model前缀
	public $relateAble = array(
		'receive_organizations' => 'MessageReceive',
		'from_organization'     => 'organizations'
	);

	public $relateField = array(
		'from_organization'  => 'from_organization_id'
	);

	//1对多的关联  model,主表id，关联表id
	public $relateAbleBelongsToMany = array(
		'receive_organizations' => array('MessageReceive', 'message_id', 'id'),
	);

	/**
	 * 获取 未读信息
	 * @return array
	 */
	public function getUnreadMessages($uid)
	{
		$list = $this->getList(array('to_uid' => $uid, 'has_read' => 'false'), '3', 'create_time DESC');
		return $list;
	}

	/**
	 * 获取消息列表 都跟该用户有关 1，接收的消息，非系统发送  2.接收的消息，系统发送 3.已发送消息，自己发送
	 * all:机构发的公告对所有机构 org:机构对某几个机构发消息  system_all:系统对所有机构发的公告  system_org:系统对某些机构发的消息
	 * 
	 * @param int $viewType 视图 
	 * @param int $page 页码
	 * @return 
	 */
	public function getMessageListByViewType($viewType, $page)
	{
		$userMessageStatusModel = $this->load->model('userMessageStatus');
		$messageReceiveModel    = $this->load->model('messageReceive');
		$organizationsModel     = $this->load->model('organizations');

		$fields  = $messageModel->table.'.*,ums.status as ums_status';
		$where   = '(ums.status != \'deleted\' OR ums.status IS NULL)';
		$orderBy = 'create_time DESC';
		$uid     = $_SESSION['backend_userinfo']['id'];
		$userOid = 0;
		if($viewType == 1) {
			//1，接收的消息，非系统发送
			$where .= ' AND '.$this->table.'.status = \'normal\' 
			AND ('.$this->table.'.msg_type=\'all\' OR ('.$this->table.'.msg_type=\'org\' AND mr.to_organization_id='.$userOid.'))';
		} elseif($viewType == 2) {
			//2.接收的消息，系统发送
			$where .= ' AND '.$this->table.'.status = \'normal\' 
			AND ('.$this->table.'.msg_type=\'system_all\' OR ('.$this->table.'.msg_type=\'system_org\' AND mr.to_organization_id='.$userOid.')) ';
		} elseif($viewType == 3) {
			//3.已发送消息，自己发送
			$where .= ' AND from_organization_id='.$userOid;
		}

		$params                 = array(
			'page'   => $page,
			'items'  => 15,
			'order'  => $this->table.'.create_time DESC',
			'group'  => $this->table.'.id',
			'fields' => $this->table.'.*,ums.status as ums_status,org.name as organization_name',
			'filter' => $where,
			'join' => array(
				array(
					'left_join' => $userMessageStatusModel->table.' ums on ums.message_id='.$this->table.'.id AND ums.user_id='.$uid.' AND ums.user_type=\'system\'',
				),
				array(
					'left_join' => $messageReceiveModel->table.' mr on mr.message_id='.$this->table.'.id',
				),
				array(
					'left_join' => $organizationsModel->table.' org on org.id='.$this->table.'.from_organization_id',
				),
			),
			'relate' => 'receive_organizations',
		);

		$list = $this->commonGetList($params);
		if($list['data']) {
			foreach($list['data'] as $key => $value) {
				if($value['msg_type'] == 'system_org') {
					if($value['receive_organizations']) {
						$receiveIds = array();
						foreach($value['receive_organizations'] as $val) {
							$receiveIds[] = $val['to_organization_id'];
						}
						$list['data'][$key]['organization_names'] = implode(',', $organizationsModel->getOrganizationNames($receiveIds));
					}
				}
			}
		}
		return $list;
	}
}