<?php
/**
 * 退款申请
 * 2014-1-14
 * @author  cyl
 * @version 1.0
 */
class RefundApplyCommon extends BaseCommon
{
	const REFUND_REMARK = '分销后台管理员申请退款';

	protected $_code = array(
		'-1'  => '{"errors":{"msg":["退票数量必须是一个大于零的整数数字"]}}',
		'-2'  => '{"errors":{"msg":["获取用户信息失败"]}}',
		'-3'  => '{"errors":{"msg":["保存至数据库失败"]}}',
		'-5'  => '{"errors":{"msg":["不存在的订单"]}}',
		'-6'  => '{"errors":{"msg":["票不符合申请条件"]}}',
		'-7'  => '{"errors":{"msg":["订单支付方式为景区支付，只能在景区退票"]}}',
        '-9'  => '{"errors":{"msg":["更新票状态失败"]}}',

        '-10'  => '{"errors":{"msg":["null post"]}}',
		'-11'  => '{"errors":{"msg":["缺少必要的参数"]}}',
		'-12'  => '{"errors":{"msg":["错误的状态"]}}',
		'-13'  => '{"errors":{"msg":["不存在的退票申请单"]}}',
		'-14'  => '{"errors":{"msg":["退票申请已经通过，请勿重复操作"]}}',
		'-15'  => '{"errors":{"msg":["退票申请已经驳回，请勿重复操作"]}}',
		'-16'  => '{"errors":{"msg":["保存至数据库失败"]}}',
	);

	static public $refundApplyStatus = array(
		'apply'     => '审核中',
		'checked'   => '审核通过',
		'reject'    => '拒绝',
		'refunded'  => '退款成功',
	);

	//获取支付单状态
	static public function getRefundApplyStatus($status = '')
	{
		if($status){
			return self::$refundApplyStatus[$status];
		}else{
			return self::$refundApplyStatus;
		}
	}

	//保存退款申请单
	public function addRefundApply($param)
	{
		//确定有申请退票数量
		if(!$param['refund_apply_num'] || !preg_match("/^[1-9]\d*$/", $param['refund_apply_num'])){
			return $this->_getUserError(-1);
		}

		//用户信息
		if(!$_SESSION['backend_userinfo']){
			return $this->_getUserError(-2);
		}

		$param['userInfo'] = $_SESSION['backend_userinfo'];

		//检测票是否能够申请退款
		$refund_apply_num = $param['refund_apply_num'];
		if(!$this->_checkRefundAble($param['order_id'], $refund_apply_num)){
			return $this->_getUserError(-6);
		}

		$tickets = $this->_getRefundTickets($param['order_id'], $refund_apply_num);

		$ordersModel      = $this->load->model('orders');
		$orderInfo        = $ordersModel->getID($param['order_id'], 'id,buyer_organization_id,seller_organization_id,payment');
		if($orderInfo['payment'] == 'offline'){
			return $this->_getUserError(-7);
		}else{
			$param['payment'] = $orderInfo['payment'];
		}

		$msg = '';

		$refundApplyModel      = $this->load->model('refundApply');
		$refundApplyItemsModel = $this->load->model('refundApplyItems');

		//组申请单的数据
		$orderItemsInfo = $this->load->model('orderItems')->getOne("order_id='{$param['order_id']}'");
		$now            = date('Y-m-d H:i:s', time());
		$postData       = array(
	        'id'          => $refundApplyModel->genId(),
			'payment'     => $param['payment'],
			'remark'      => self::REFUND_REMARK,
			'created_by'  => $param['userInfo']['id'],
			'created_at'  => $now,
			'money'       => count($tickets)*$orderItemsInfo['price'],
			'ticket_nums' => count($tickets),
			'order_id'    => $param['order_id'],
		);

		//开启事物
		$refundApplyModel->begin();

		//假如是信用支付状态就直接为退款成功，然后下面再进行增加信用额度的操作
		if(in_array($param['payment'], array('credit', 'advance'))){
			$postData['status'] = 'refunded';
		}

		$refundApplyModel->add($postData);
		$affectedRows = $refundApplyModel->affectedRows();
		if($affectedRows){
			//保存申请单明细数据
			$saveItemsResult = $refundApplyItemsModel->addRefundApplyItems($tickets, $postData['id']);
			if(!$saveItemsResult){
				$refundApplyModel->rollback();
				return $this->_getUserError(-3);
			}

			//将相应的票置为不可使用
			$ticketsModel = $this->load->model('tickets');
			if(!$ticketsModel->update(array('status' => '0'), "id in ('".implode("','", $tickets)."')")) {
			    $refundApplyModel->rollback();
			    return $this->_getUserError(-9);
			}

			//假如是信用支付的话直接生成退款成功的单据
			if(in_array($param['payment'], array('credit', 'advance'))) {
				$refundsCommon = $this->load->common('refunds');

				//退款单
				$refundsResult = $refundsCommon->addRefund($postData, $msg);
				if(!$refundsResult){
					$refundApplyModel->rollback();
					return $msg;
				}

				//增加信用或储值，包含日志
				$partnerCommon        = $this->load->common('partner');
				$increaseMoneyResult = $partnerCommon->increasePartnerMoney($postData, $param['payment'], $msg);
				if(!$increaseMoneyResult){
				    $refundApplyModel->rollback();
				    return $msg;
				}

				//发送退款成功的消息
				$message = "[系统公告]汇联皆景分销后台退款成功(订单编号:{$param['order_id']})";
				$this->_sendMessage($param['order_id'], $message);
			}

			$refundApplyModel->commit();
			$postData['tickets'] = $tickets;
			return json_encode(array('data'=>array($postData)));
		}else{
			$refundApplyModel->rollback();
			return $this->_getUserError(-3);
		}
	}

	/**
	 * 获取需要退的票号
	 *
	 * @return array
	 * @author cuiyulei
	 **/
	private function _getRefundTickets($orderId, $refund_apply_num)
	{
		$ticketsModel      = $this->load->model('tickets');
		$refundAbleTickets = $ticketsModel->getRefundAbleTickets($orderId, $refund_apply_num);
		$hashArr = array();
		foreach ($refundAbleTickets as $key => $value) {
			$hashArr[] = $value['id'];
		}
		return $hashArr;
	}

	//检查订单的票是否满足申请退款的条件
	public function _checkRefundAble($orderId, $refund_apply_num)
	{
		$ticketsModel      = $this->load->model('tickets');
		$refundAbleTickets = $ticketsModel->getRefundAbleTickets($orderId);
		if(!$refundAbleTickets || count($refundAbleTickets) < $refund_apply_num)
		{	
			return false;
		}
		return true;
	}

	/**
	 * 退款成功，更新退款申请单的状态
	 * @param array $refundInfo 支付单信息
	 * @return bool
	 */
	public function refundFinish($refundInfo, &$msg = '')
	{
		$updateArray = array(
			'status'     => 'refunded',
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		$refundApplyModel  = $this->load->model('refundApply');
		$result            = $refundApplyModel->update($updateArray, array('id'=> $refundInfo['refund_apply_id']));
		$affectedRows      = $refundApplyModel->affectedRows();
		if($result && $affectedRows >= 1){
			//保存退款完成日志
			$paymentsCommon = $this->load->common('payments');
			$newPayment     = $paymentsCommon->getPaymentName($refundInfo['pay_app_id']);
			$memo           = '`系统`完成退款，支付方式：`'.$newPayment['app_display_name'].'`，退款单：'.$refundInfo['id'].'`，订单号为`'.$refundInfo['order_id'].'`';
			$orderCommon    = $this->load->common('order');
			$orderCommon->saveOrderLog($refundInfo['order_id'], 0, 'refund', $memo, $msg);

			//TODO::向机构成员发送审核消息
			$msg = "[系统公告]汇联皆景分销后台退款成功(订单编号：{$refundInfo['order_id']})";
			$this->_sendMessage($refundInfo['order_id'], $msg);

			return true;
		}else{
			$msg = $this->_getUserError(-39);
			return false;
		}
	}

	//审核
	public function verify($post)
	{
		if($post) {
			//一级票务id和状态
			if(!$post['id'] || !$post['status']) {
				return $this->_getUserError(-11);
			}

			//状态对应
			if(!array_key_exists($post['status'], self::getRefundApplyStatus())) {
				return $this->_getUserError(-12);
			}

			$refundApplyModel = $this->load->model('refundApply');
			$oldInfo          = $refundApplyModel->getID($post['id'], 'order_id,status');
			if($oldInfo) {
				if($post['status'] == $oldInfo['status'] && $post['status'] == 'checked') {
					return $this->_getUserError(-14);
				}

				if($post['status'] == $oldInfo['status'] && $post['status'] == 'reject') {
					return $this->_getUserError(-15);
				}
			} else {
				return $this->_getUserError(-13);
			}

			$updateArray = array(
				'status'     => $post['status'],
				'audited_by' => $_SESSION['backend_userinfo']['id'],
				'audited_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
			$result       = $refundApplyModel->update($updateArray, array('id' => $post['id']));
			$affectedRows = $refundApplyModel->affectedRows();
			if($result && $affectedRows >= 1) {

				//TODO::向机构成员发送审核消息
				$msg = "[系统公告]汇联皆景分销后台审核退票申请（订单编号：{$oldInfo['order_id']}），结果为".self::getRefundApplyStatus($post['status']).".";
				$result = $this->_sendMessage($oldInfo['order_id'], $msg);
				if ($result) {
					return $result;
				} else {
					return json_encode(array('data' => array($updateArray)));
				}
			} else {
				return $this->_getUserError(-16);
			}
		} else {
			return $this->_getUserError(-10);
		}
	}

	/**
	 * 发送审核消息
	 *
	 * @param int $order_id  订单id
	 * @param string $msg    信息
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	private function _sendMessage($order_id, $msg)
	{
		//加载数据模型
		$ordersModel   = $this->load->model('orders');

		//获取订单信息
		$orderInfo     = $ordersModel->getID($order_id, 'buyer_organization_id');

		//发送审核消息
        $messageCommon = $this->load->common('message');
        return $messageCommon->send($orderInfo['buyer_organization_id'], $msg);
	}
}