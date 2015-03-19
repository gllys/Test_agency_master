<?php
/**
 *  门票相关数据 
 *  
 * 2013-09-24
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class TicketCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
		'-2'  => '{"errors":{"msg":["缺少必要的参数"]}}',
		'-3'  => '{"errors":{"msg":["错误的状态"]}}',
		'-4'  => '{"errors":{"msg":["不存在的二级票务信息"]}}',
		'-5'  => '{"errors":{"msg":["二级票务已经审核，无需重复操作"]}}',
		'-6'  => '{"errors":{"msg":["二级票务已经拒绝，无需重复操作"]}}',
		'-7'  => '{"errors":{"msg":["保存至数据库失败"]}}',
	);

	//门票状态
	static public $ticketStatus = array(
		'normal'    => '审核通过',
		'unaudited' => '未审核',
		'failed'    => '驳回',
	);

	//门票支付方式
	static public $ticketPayment = array(
		'online'  => '在线支付',
		'offline' => '景区支付',
	);

	//星期
	static public $ticketWeek = array(
		'1' => '周一',
		'2' => '周二',
		'3' => '周三',
		'4' => '周四',
		'5' => '周五',
		'6' => '周六',
		'0' => '周日',
	);

	static public $orderTicketUsed = array(
		'used'    => '已使用',
		'unused'  => '未使用',
		'useless' => '已作废',
	);

	//获取票的常用属性的定义
	public function getTicketProperty()
	{
		$result['ticketWeek']    = self::$ticketWeek;
		$result['ticketPayment'] = self::$ticketPayment;
		$result['ticketStatus']  = self::$ticketStatus;
		$tickteTypesModel        = $this->load->model('ticketTypes');
		$param                   = array(
			'filter' => array(
				'deleted_at' => null
			)
		);
		$ticketType              = $tickteTypesModel->commonGetList($param);
		if($ticketType['data'][0]['id']){
			$result['ticketType']    = $ticketType['data'];
		}
		return $result;
	}

	/**
	 * 添加门票类型  
	 *
	 *     门票类型字段         字段类型                     含义
	 *     name                 varchar(255)                 名称
	 * @param array $post 
	 * @return json
	 */
	public function addTicketType($post)
	{
		if($post){
			$ticketTypesModel = $this->load->model('ticketTypes');

			//组织数据
			$data['name']       = $post['name'];
			$data['created_by'] = $_SESSION['backend_userinfo']['id'];
			$data['created_at'] = date('Y-m-d H:i:s', time());

			$ticketTypesModel->add($data);
			return json_encode(array('succ'=>true));
		}else{
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 更新门票类型
	 * @param array $post 门票类型数据 
	 * @return json
	 */
	public function updateTicketType($post)
	{
		if($post){
			$postData = array(
				'name' => $post['name'],
				'updated_at'=>date('Y-m-d H;i:s', time())
			);
			$ticketTypesModel = $this->load->model('ticketTypes');
			$ticketTypesModel->update($postData, array('id'=>intval($post['id'])));
			return json_encode(array('succ'=>true));
		}else{
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 删除门票类型  逻辑删除
	 * @param array $post 机构数据 
	 * @return json
	 */
	public function deleteTicketType($post)
	{
		if ($post) {
			$ticketTypesModel = $this->load->model('ticketTypes');
			$ticketTypesModel->update(array('deleted_at'=>date('Y-m-d H:i:s', time())), array('id'=>intval($post['id'])));
			return json_encode(array('succ'=>true));
		} else {
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 获取的门票类型列表
	 * @param  array $param filter
	 * @return array
	 */
	public function getTicketType($param = array())
	{	
		//加载票种模型
		$ticketTypesModel = $this->load->model('ticketTypes');

		//组织数据
		$data = $ticketTypesModel->commonGetList($param);

		//返回
		return $data;
	}

	//获取景区的状态
	public static function getTicketStatus($status = '')
	{
		if($status){
			return self::$ticketStatus[$status];
		}else{
			return self::$ticketStatus;
		}
	}

	//获取二级票务的星期
	public static function getTicketWeek($week = '')
	{
		if(!empty($week)){
			return self::$ticketWeek[$week];
		}else{
			return self::$ticketWeek;
		}
	}

	//获取支付方式
	public static function getTicketPayment($payment = '')
	{
		if($payment){
			return self::$ticketPayment[$payment];
		}else{
			return self::$ticketPayment;
		}
	}

	//获取订单里门票的显示的状态，这里指的是订单里的门票
	public static function getShowStatus($ticketInfo)
	{
		if($ticketInfo){
			if($ticketInfo['status'] == 'used'){
				return '已使用';
			}if($ticketInfo['status'] == 'useless'){
				return '已作废';
			}else{
				if($ticketInfo['refund_apply_status'] == 'reject' || !$ticketInfo['refund_apply_id']){
					return '可使用';
				}elseif($ticketInfo['refund_apply_status'] == 'apply' || $ticketInfo['refund_apply_status'] == 'checked'){
					return '退款中';
				}elseif($ticketInfo['refund_apply_status'] == 'refunded'){
					return '已退款';
				}
			}
		}
	}


	//审核
	public function verify($post)
	{
		if($post) {
			//一级票务id和状态
			if(!$post['id'] || !$post['status']) {
				return $this->_getUserError(-2);
			}

			//状态对应
			if(!array_key_exists($post['status'], self::getTicketStatus())) {
				return $this->_getUserError(-3);
			}

			$ticketTemplatesModel = $this->load->model('ticketTemplates');
			$oldInfo              = $ticketTemplatesModel->getID($post['id'], 'status,name,organization_id,landscape_id');
			if($oldInfo) {
				if($post['status'] == $oldInfo['status'] && $post['status'] == 'normal') {
					return $this->_getUserError(-5);
				}

				if($post['status'] == $oldInfo['status'] && $post['status'] == 'failed') {
					return $this->_getUserError(-6);
				}
			} else {
				return $this->_getUserError(-4);
			}

			$updateArray = array(
				'id'         => $post['id'],
				'status'     => $post['status'],
				'audited_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
			$result       = $ticketTemplatesModel->update($updateArray, array('id' => $post['id']));
			$affectedRows = $ticketTemplatesModel->affectedRows();
			if($result && $affectedRows >= 1) {
				//TODO::门票审核后发送消息：
				$msg = '[系统公告]:'.$oldInfo['name'].'完成审核，结果为'.self::getTicketStatus($post['status']);
				$messageCommon = $this->load->common('message');
       			$resultSend    = $messageCommon->send($oldInfo['organization_id'], $msg);

       			//TODO::二级票务审核后，更新一级票务的更新时间
       			$landscapeModel = $this->load->model('landscapes');
       			$landscapeModel->update(array('updated_at'=> date('Y-m-d H:i:s')), array('id'=>$oldInfo['landscape_id']));
       			if ($resultSend) {
       				return $resultSend;
       			} else {
       				return json_encode(array('data' => array($updateArray)));
       			}
			} else {
				return $this->_getUserError(-7);
			}
		} else {
			return $this->_getUserError(-1);
		}
	}
}