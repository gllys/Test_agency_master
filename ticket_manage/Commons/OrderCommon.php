<?php
/**
 *  订单 
 * 
 * 2013-1-10
 *
 * @author  cyl
 * @version 1.0
 */
class OrderCommon extends BaseCommon
{
	//订单支付状态对应 'unpaid','paid'
	static public $orderPayStatus = array(
		'unpaid'    => '未付款',
		'paid'      => '已付款',
	);

	//订单状态
	static public $orderStatus = array(
		'active' => '活动订单',
		'cancel' => '已取消',
		'finish' => '已结束',
		'billed' => '已结款'
	);

	//支付方式
	static public $payments = array(
        'cash'=>'现金支付',
        'alipay' => '支付宝',
        'kuaiqian' => '快钱',
        'credit' => '信用支付',
        'advance' => '储值支付',
        'offline'=> '线下支付'
	);

	protected $_code = array(
		'-1'  => '{"errors":{"post":["post data is null"]}}',
		'-2'  => '{"errors":{"method":["unknown api method"]}}',
		'-3'  => '{"errors":{"msg":["游玩时间必选"]}}',
		'-4'  => '{"errors":{"msg":["门票数量必填"]}}',
		'-5'  => '{"errors":{"msg":["门票数量须为数字"]}}',
		'-6'  => '{"errors":{"msg":["取票人姓名必填"]}}',
		'-7'  => '{"errors":{"msg":["取票人手机号码必填"]}}',
		'-8'  => '{"errors":{"msg":["创建订单失败"]}}',
		'-9'  => '{"errors":{"msg":["游玩时间不合法"]}}',
		'-10' => '{"errors":{"msg":["取票人手机号码不合法"]}}',
		'-11' => '{"errors":{"msg":["错误的门票"]}}',
		'-12' => '{"errors":{"msg":["未选择门票"]}}',
		'-13' => '{"errors":{"msg":["最少订票数为{min_order}"]}}',
		'-14' => '{"errors":{"msg":["最多订票数为{max_order}"]}}',
		'-15' => '{"errors":{"msg":["购买者信息获取失败"]}}',
		'-16' => '{"errors":{"msg":["出售者信息获取失败"]}}',
		'-17' => '{"errors":{"msg":["游玩时间不在票有效期内"]}}',
		'-18' => '{"errors":{"msg":["订单数据不能为空"]}}',
		'-19' => '{"errors":{"msg":["订单数据更新失败"]}}',
		'-20' => '{"errors":{"msg":["创建订单失败"]}}',
		'-21' => '{"errors":{"msg":["错误的数据"]}}',
		'-22' => '{"errors":{"msg":["订单明细保存失败"]}}',
		'-23' => '{"errors":{"msg":["数据错误"]}}',
		'-24' => '{"errors":{"msg":["票生成失败"]}}',
		'-25' => '{"errors":{"msg":["票生成失败,单张订单最多999张票"]}}',
		'-26' => '{"errors":{"msg":["错误的电子编码"]}}',
		'-27' => '{"errors":{"msg":["错误的用户信息"]}}',
		'-28' => '{"errors":{"msg":["不存在的订单"]}}',
		'-29' => '{"errors":{"msg":["订单支付状态不是未支付状态，不能取消"]}}',
		'-30' => '{"errors":{"msg":["订单状态不是活动订单，不能取消"]}}',
		'-31' => '{"errors":{"msg":["取消订单失败"]}}',
		'-32' => '{"errors":{"msg":["保存订单信息失败"]}}',
		'-33' => '{"errors":{"msg":["支付方式错误"]}}',
		'-34' => '{"errors":{"msg":["您的信用额度不足，请选择其他支付方式"]}}',
		'-35' => '{"errors":{"msg":["保存订单时发生错误"]}}',
		'-36' => '{"errors":{"msg":["未选择支付方式"]}}',
		'-37' => '{"errors":{"msg":["获取订单信息失败"]}}',
		'-38' => '{"errors":{"msg":["支付信息出错"]}}',
		'-39' => '{"errors":{"msg":["更新订单支付状态失败"]}}',
		'-40' => '{"errors":{"msg":["保存订单交易记录时发生错误"]}}',
		'-41' => '{"errors":{"msg":["改期日期不能为空"]}}',
		'-42' => '{"errors":{"msg":["该订单不属于你"]}}',
		'-43' => '{"errors":{"msg":["该订单的票不可改期"]}}',
		'-44' => '{"errors":{"msg":["该订单允许改期{allow_change_times}次，您已改期{changed_useday_times}次"]}}',
		'-45' => '{"errors":{"msg":["订单状态不是活动订单"]}}',
		'-46' => '{"errors":{"msg":["订单已支付"]}}',
		'-47' => '{"errors":{"msg":["至少提前一天改期"]}}',
		'-48' => '{"errors":{"msg":["所选游玩日期不在票的有效期内或适用天数"]}}',
		'-49' => '{"errors":{"msg":["有票已经使用，不能改期"]}}',
	);

	//创建订单
	public function createOrder($post)
	{
		if($post){
			$msg = '';
			if(!$this->_checkOrder($post, $msg)){
				return $msg;
			}

			$postData        = $this->_formatOrder($post);
			$ordersModel     = $this->load->model('orders');
			$ordersModel->begin();

			//1.保存订单信息
			$saveOrderResult = $this->saveOrder($postData, $msg);
			if(!$saveOrderResult){
				$ordersModel->rollback();
				return $msg;
			}

			//2.保存订单明细信息
			$saveOrderItemResult = $this->saveOrderItems($post, $postData['id'], $msg);
			if(!$saveOrderItemResult){
				$ordersModel->rollback();
				return $msg;
			}

			// 3.生成票号
			$saveOrderTicketsResult = $this->saveOrderTickets($post, $postData['id'], $msg);
			if(!$saveOrderTicketsResult){
				$ordersModel->rollback();
				return $msg;
			}

			// 4. 假如不是线下支付，则生成支付单
			if($post['payment'] != 'offline'){
				$savePaymentResult = $this->addPayment($post, $postData['id'], $msg);
				if(!$savePaymentResult){
					$ordersModel->rollback();
					return $msg;
				}
			}

			$ordersModel->commit();

			//5.保存订单操作信息
			$usersModel = $this->load->model('users');
			$account    = $usersModel->getID($_SESSION['backend_userinfo']['id'], 'account');
			$memo       = '`'.$account['account'].'`创建了订单，订单号为`'.$postData['id'].'`';
			$this->saveOrderLog($postData['id'], $_SESSION['backend_userinfo']['id'], 'create', $memo, $msg);

			return json_encode(array('data' => array($postData)));
		}else{
			return $this->_getUserError(-1);
		}
	}

	//检查订单信息是否合法
	private function _checkOrder($order, &$msg)
	{
		$ticketTemplatesModel = $this->load->model('ticketTemplates');
		$ticketInfo           = $ticketTemplatesModel->getID($order['ticket_id']);

		//门票id
		if($order['ticket_id']){
			$useAble                 = $ticketTemplatesModel->checkUseAble($order['ticket_id']);
			if(!$useAble){
				$msg = $this->_getUserError(-11);
				return false;
			}

			//出售者信息
			if(!$ticketInfo['organization_id'] || !$ticketInfo['landscape_id']){
				$msg = $this->_getUserError(-16);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-12);
			return false;
		}

		//游玩时间
		if($order['useday']){
			if(!preg_match('/^(([1-2][0-9]{3}-)((([1-9])|(0[1-9])|(1[0-2]))-)((([1-9])|(0[1-9])|([1-2][0-9])|(3[0-1]))))$/', $order['useday'])){
				$msg = $this->_getUserError(-9);
				return false;
			}

			//须在票有效期内，且是可游玩的星期
			$useday    = strtotime($order['useday']);
			$startTime = strtotime($ticketInfo['expire_start_at']);
			$endTime   = strtotime($ticketInfo['expire_end_at']);
			if($useday < $startTime || $useday > $endTime || !in_array(date('w', $useday), explode(',', $ticketInfo['weekly']))){
				$msg = $this->_getUserError(-17);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-3);
			return false;
		}

		//支付方式
		$paymentsCommon  = $this->load->common('payments');
		$useAblePayments = $paymentsCommon->getUseAblePayments($order['ticket_id'], $_SESSION['backend_userinfo']['organization_id']);
		if(!array_key_exists($order['payment'], $useAblePayments)){
			$msg = $this->_getUserError(-33);
			return false;
		}else{
			if($order['payment'] == 'credit'){
				//假如是信用支付的话，信用额度不足要提示用户选择其他支付方式
				$salePrice     = $this->getSalePrice($order['ticket_id'], $_SESSION['backend_userinfo']['organization_id']);
				$orderAmount   = $order['nums']*$salePrice;
				$creditUseAble = $paymentsCommon->checkCreditAndOrderAmount($ticketInfo['organization_id'], $_SESSION['backend_userinfo']['organization_id'], $orderAmount);
				if(!$creditUseAble){
					$msg = $this->_getUserError(-34);
					return false;
				}
			}
		}

		//取票人姓名
		if(!$order['owner_name']){
			$msg = $this->_getUserError(-6);
			return false;
		}

		//取票人手机
		if($order['owner_mobile']){
			if(!preg_match('/^1\d{10}$/', $order['owner_mobile'])){
				$msg = $this->_getUserError(-10);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-7);
			return false;
		}

		//订票数量
		if($order['nums'] && is_numeric($order['nums'])){
			if($order['nums'] < $ticketInfo['min_order']){
				$msg = str_replace('{min_order}', $ticketInfo['min_order'], $this->_getUserError(-13));
				return false;
			}elseif($order['nums'] > $ticketInfo['max_order']){
				$msg = str_replace('{max_order}', $ticketInfo['max_order'], $this->_getUserError(-14));
				return false;
			}
		}else{
			$msg = $this->_getUserError(-5);
			return false;
		}

		//购买的用户
		if($order['buyer_user_info']){
			if(!$order['buyer_user_info']['id'] || !$order['buyer_user_info']['organization_id']){
				$msg = $this->_getUserError(-15);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-15);
			return false;
		}

		return true;
	}

	//将表单数据转换为订单结构数据
	private function _formatOrder($post)
	{
		$ticketTemplatesModel = $this->load->model('ticketTemplates');
		$ticketInfo           = $ticketTemplatesModel->getID($post['ticket_id']);

		//价格
		$salePrice            = $this->getSalePrice($post['ticket_id'], $_SESSION['backend_userinfo']['organization_id']);

		$now                     = time();
		$postData = array(
			'hash'                    => 0, //先给0，得到ID后须再更新hash
			'payment'                 => $post['payment'],
			'amount'                  => $post['nums']*$salePrice,
			'useday'                  => $post['useday'],
			'owner_name'              => $post['owner_name'],
			'owner_mobile'            => $post['owner_mobile'],
			'buyer_organization_id'   => $post['buyer_user_info']['organization_id'],
			'seller_organization_id'  => $ticketInfo['organization_id'],
			'landscape_id'            => $ticketInfo['landscape_id'],
			'remarks'                 => $post['remarks'],
			'created_by'              => $post['buyer_user_info']['id'],
			'created_at'              => date('Y-m-d H:i:s', $now),
			'nums'                    => $post['nums'],
		);
		return $postData;
	}

	//获取票的结算价格
	public function getSalePrice($ticketId, $organizationId, &$isPartnerPrice = false, &$allowCredit = 'no')
	{
		//合作价格
		$ticketTemplatesModel = $this->load->model('ticketTemplates');
		$ticketInfo           = $ticketTemplatesModel->getID($ticketId);
		$partnerPrice = $ticketTemplatesModel->getTicketPartnerPriceDetail($ticketId, $organizationId);

		if($partnerPrice['ticket_templates_id']){
			$salePrice      = $partnerPrice['pti_partner_price'];
			$isPartnerPrice = true;
			$allowCredit    = $partnerPrice['allow_credit'];
		}else{
			$salePrice      = $ticketInfo['sale_price'];
			$isPartnerPrice = false;
		}
		return $salePrice;
	}

	//保存订单
	public function saveOrder(&$order, &$msg)
	{
		if($order){
			$ordersModel = $this->load->model('orders');
			$result = $ordersModel->add($order);
			$addId  = $ordersModel->getAddID();
			if($addId){
				$order['hash'] = $ordersModel->genHash($order['landscape_id'], $addId);
				$result        = $ordersModel->update($order, array('id' => $addId));
				$order['id']   = $addId;
				if($result){
					return true;
				}else{
					$msg = $this->_getUserError(-20);
					return false;
				}
			}else{
				$msg = $this->_getUserError(-20);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-18);
			return false;
		}
	}

	//保存订单明细
	public function saveOrderItems($post, $orderId, &$msg)
	{
		$itemsData = $this->_formatOrderItems($post, $orderId);

		if($itemsData){
			$orderItemsModel = $this->load->model('orderItems');
			$result          = $orderItemsModel->add($itemsData);
			$addId           = $orderItemsModel->getAddID();
			if($addId){
				return true;
			}else{
				$msg = $this->_getUserError(-22);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-21);
			return false;
		}
	}

	//将表单数据转换为订单明细
	private function _formatOrderItems($post, $orderId)
	{
		$ticketTemplatesModel = $this->load->model('ticketTemplates');
		$ticketInfo           = $ticketTemplatesModel->getID($post['ticket_id']);

		//实际购买价格
		$salePrice            = $this->getSalePrice($post['ticket_id'], $_SESSION['backend_userinfo']['organization_id'], $isPartnerPrice);
		//假如是合作价格，在明细里标示下
		if($isPartnerPrice){
			$ticketInfo['isPartnerPrice'] = true;
			$ticketInfo['partner_price']  = $salePrice;
		}else{
			$ticketInfo['isPartnerPrice'] = false;
		}

		$postData = array(
			'order_id'           => $orderId,
			'ticket_id'          => $post['ticket_id'],
			'price'              => $salePrice,
			'isPartnerPrice'     => $ticketInfo['isPartnerPrice'],
			'landscape_id'       => $ticketInfo['landscape_id'],
			'organization_id'    => $ticketInfo['organization_id'],
			'name'               => $ticketInfo['name'],
			'payment'            => $ticketInfo['payment'],
			'expire_start_at'    => $ticketInfo['expire_start_at'],
			'expire_end_at'      => $ticketInfo['expire_end_at'],
			'use_expire'         => $ticketInfo['use_expire'],
			'weekly'             => $ticketInfo['weekly'],
			'allow_back'         => $ticketInfo['allow_back'],
			'max_order'          => $ticketInfo['max_order'],
			'min_order'          => $ticketInfo['min_order'],
			'brand_price'        => $ticketInfo['brand_price'],
			'market_price'       => $ticketInfo['market_price'],
			'sale_price'         => $ticketInfo['sale_price'],
			'description'        => $ticketInfo['description'],
			'reserve'            => $ticketInfo['reserve'],
			'allow_change_times' => $ticketInfo['allow_change_times'],
		);
		return $postData;
	}

	/**
	 * 订单操作记录
	 * @param int $orderId 订单id
	 * @param int $uid 操作用户
	 * @param string $type 订单操作类型
	 * @param string $msg 错误信息
	 * @return json
	 */
	public function saveOrderLog($orderId, $uid, $type, $memo, &$msg)
	{
		$orderLogData    = $this->_formatOrderLog($orderId, $uid, $type, $memo);
		$orderLogModel   = $this->load->model('orderLog');
		$result          = $orderLogModel->add($orderLogData);
		$addId           = $orderLogModel->getAddID();
		if($addId){
			return true;
		}else{
			$msg = $this->_getUserError(-32);
			return false;
		}
	}

	/**
	 * 将表单数据转换为订单操作日志结构
	 * @param int $orderId 订单id
	 * @param int $uid 操作用户
	 * @param string $type 订单操作类型
	 * @param string $memo 日志
	 * @return mixed
	 */
	private function _formatOrderLog($orderId, $uid, $type, $memo)
	{
		$usersModel = $this->load->model('users');
		$account    = $usersModel->getID($uid, 'account');
		$postData = array(
			'order_id'    => $orderId,
			'op_id'       => $uid,
			'op_name'     => $account['account'],
			'action_time' => time(),
			'action'      => $type,
			'memo'        => $memo,
		);
		return $postData;
	}

	//保存订单票号信息
	public function saveOrderTickets($post, $orderId, &$msg)
	{
		if($post['nums'] > 999){
			$msg = $this->_getUserError(-25);
			return false;
		}
		$ticketsData = $this->_formatOrderTickets($post, $orderId);
		if($ticketsData){
			$ticketsModel = $this->load->model('tickets');
			$result       = $ticketsModel->addTickets($ticketsData);
			if($result){
				return true;
			}else{
				$msg = $this->_getUserError(-24);
				return false;
			}
		}else{
			$msg = $this->_getUserError(-23);
			return false;
		}
	}

	//将表单数据转换为订单票明细
	private function _formatOrderTickets($post, $orderId)
	{
		$ticketTemplatesModel = $this->load->model('ticketTemplates');
		$ticketInfo   = $ticketTemplatesModel->getID($post['ticket_id']);

		$ticketsModel = $this->load->model('Tickets');
		$postData     = array();
		for($i=1; $i<=$post['nums']; $i++){
			$postData[] = array(
				'hash'        => $ticketsModel->genHash($ticketInfo['landscape_id'], $orderId, $i),
				'order_id'    => $orderId,
			);
		}

		return $postData;
	}

	//线下支付无支付单，生成支付单
	public function addPayment($post, $orderId, &$msg)
	{
		$paymentData     = $this->_formatPayment($post, $orderId);
		$paymentsModel   = $this->load->model('payments');
		$result          = $paymentsModel->add($paymentData);
		$addId           = $paymentsModel->getAddID();
		if($addId){
			$addOrderReceiptResult = $this->addOrderReceipt($orderId, $addId, 'payments', $paymentData['money'], $msg);
			if($addOrderReceiptResult){
				return true;
			}else{
				return false;
			}
		}else{
			$msg = $this->_getUserError(-35);
			return false;
		}
	}

	private function _formatPayment($post, $orderId)
	{
		$paymentsCommon  = $this->load->common('payments');
		$paymentInfo     = $paymentsCommon->getPaymentInfo($post['payment']);

		if($post['payment'] == 'credit' || $post['payment'] == 'offline'){
			$bank = $account = $paymentInfo['app_display_name'];
		}

		$salePrice            = $this->getSalePrice($post['ticket_id'], $_SESSION['backend_userinfo']['organization_id']);
		$amount               = $post['nums']*$salePrice;
		$postData = array(
			'money'           => $amount,
			'u_id'            => $_SESSION['backend_userinfo']['id'],
			'op_id'           => $_SESSION['backend_userinfo']['id'],
			'account'         => '',
			'bank'            => '',
			'pay_account'     => '',
			'remark'          => '',
			'payment_bn'      => '',
			'pay_type'        => $paymentInfo['app_pay_type'],
			'ip'              => getIp(),
			'order_id'        => $orderId,
			'status'          => 'ready',
			'pay_app_id'      => $paymentInfo['app_id'],
			'create_time'     => time(),
		);
		return $postData;
	}

	/**
	 * 将表单数据转换为订单操作日志结构
	 * @param int $orderId 订单id
	 * @param int $receiptId 交易单
	 * @param string $receiptType 交易单类型
	 * @param float $money 金额
	 * @param string $msg 错误信息
	 * @return mixed
	 */
	public function addOrderReceipt($orderId, $receiptId, $receiptType, $money, &$msg = '')
	{
		$orderReceiptData = array(
			'order_id'     => $orderId,
			'receipt_type' => $receiptType,
			'receipt_id'   => $receiptId,
			'money'        => $money,
		);

		$orderReceiptModel = $this->load->model('orderReceipt');
		$result            = $orderReceiptModel->add($orderReceiptData);
		$affectedRows      = $orderReceiptModel->affectedRows();
		if($result && $affectedRows >= 1){
			return true;
		}else{
			$msg = $this->_getUserError(-40);
			return false;
		}
	}

	//这里的id指的是hash值
	public function getOrderInfo($hash, $relate = 'landscape' ,$type = 'more')
	{
		if(empty($hash)){
			$data['error_msg'] = '不存在的订单';
		}else{
			$ordersModel = $this->load->model('orders');
			$orderInfo   = $ordersModel->getOrderDetail($hash, $relate ,$type);
			if($orderInfo){
				$data['orderInfo'] = $orderInfo;
			}else{
				$data['error_msg'] = '不存在的订单';
			}
		}

		return $data;
	}

	//取消订单
	public function cancelOrder($param)
	{
		if(!$param['hash']){
			return $this->_getUserError(-26);
		}

		if(!$param['userInfo']){
			return $this->_getUserError(-27);
		}

		$ordersModel = $this->load->model('orders');
		$exist       = $ordersModel->getOne(array('hash'=> $param['hash'], 'buyer_organization_id'=>$param['userInfo']['organization_id']));

		if(!$exist){
			return $this->_getUserError(-28);
		}else{
			if($exist['deleted_at'] > 0){
				return $this->_getUserError(-29);
			}else{
				if($exist['pay_status'] != 'unpaid'){
					return $this->_getUserError(-29);
				}elseif($exist['status'] != 'active'){
					return $this->_getUserError(-30);
				}else{
					$now                 = date('Y-m-d H:i:s', time());
					$saveData            = array('status' => 'cancel', 'updated_at'=>$now);
					$filter              = array('hash' => $param['hash']);
					$exist['status']     = 'cancel';
					$exist['updated_at'] = $now;
					$exist['deleted_at'] = $now;
					$result              = $ordersModel->update($saveData, $filter);
					$affectedRows        = $ordersModel->affectedRows();
					if($affectedRows >= 1 && $result){
						return json_encode(array('data' => array($exist)));
					}else{
						return  $this->_getUserError(-31);
					}
				}
			}
		}
	}

	/**
	 * 检查订单是否能支付
	 * @param string $orderHash 订单hash
	 * @param string $relate 订单关联的信息
	 * @param string $msg 错误信息
	 * @return bool
	 */
	public function checkPayAble($orderHash, $relate = 'landscape', &$msg = '')
	{
		$ordersModel = $this->load->model('orders');
		$orderInfo   = $ordersModel->getOrderDetail($orderHash, $relate ,'more');
		if($orderInfo){
			//不是该用户的订单
			if($orderInfo['buyer_organization_id'] != $_SESSION['backend_userinfo']['organization_id']){
				$msg = '该订单不属于你';
				return false;
			}

			//支付状态为已支付
			if($orderInfo['pay_status'] == 'paid'){
				$msg = '订单已经支付过，无须重新支付';
				return false;
			}

			//订单状态
			if($orderInfo['status'] != 'active'){
				$msg = '订单不是活动订单，不能支付';
				return false;
			}
			return true;
		}else{
			$msg = '订单信息获取失败';
			return false;
		}
	}

	//支付
	public function dopay($post)
	{
		if($post){
			$check = $this->_checkPay($post, $msg);
			if(!$check){
				return $msg;
			}

			$ordersModel  = $this->load->model('orders');
			$oldOrderInfo = $ordersModel->getOrderDetailById($post['order_id'], 'landscape' ,'more');
			$ordersModel->begin();

			//1.是否修改过支付方式
			if($post['payment'] != $oldOrderInfo['payment']){
				//保存支付方式，生成新的支付单
				$saveOrderResult = $this->updateOrderPayment($post, $oldOrderInfo, $msg);
				if(!$saveOrderResult){
					$ordersModel->rollback();
					return $msg;
				}
			}

			$paymentsCommon = $this->load->common('payments');
			//假如传过来的不是景区支付，检查最新的支付单和传过来的支付方式是否一致
			$payappInfo     = $paymentsCommon->getPaymentInfo($post['payment']);
			$paymentsModel  = $this->load->model('payments');
			$paymentsInfo   = $paymentsModel->getOne('order_id='.$post['order_id'], 'id DESC');
			if($paymentsInfo['pay_app_id'] != $post['payment']){
				$ordersModel->rollback();
				return $this->_getUserError(-38);
			}

			//支付方式是否存在
			$allPayments    = $paymentsCommon->getPaymentsList();
			if(!array_key_exists($post['payment'], $allPayments)){
				$ordersModel->rollback();
				return $this->_getUserError(-33);
			}

			//假如不是景区支付的话，就执行支付
			if($post['payment'] != 'offline'){
				//2.执行支付
				$paymentClassName        = $payappInfo['app_class'];
				$appPaymentObj           = new $paymentClassName;
				$newOrderInfo            = $oldOrderInfo;
				$newOrderInfo['payment'] = $post['payment'];
				$doPayResult             = $appPaymentObj->doPay($paymentsInfo, $newOrderInfo, $msg);
				if(!$doPayResult){
					$ordersModel->rollback();
					return $msg;
				}
			}

			$ordersModel->commit();
			return json_encode(array('data' => array($post)));
		}else{
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 支付时检查订单
	 * @param string $post 订单id和支付方式的数组
	 * @param string $msg 错误信息
	 * @return json
	 */
	private function _checkPay($post, &$msg = '')
	{
		//未传订单号
		if(!$post['order_id']){
			$msg = $this->_getUserError(-18);
			return false;
		}

		//未选择支付方式
		if(!$post['payment']){
			$msg = $this->_getUserError(-36);
			return false;
		}

		//订单信息查询
		$ordersModel = $this->load->model('orders');
		$orderInfo   = $ordersModel->getOrderDetailById($post['order_id'], 'landscape' ,'more');
		if(!$orderInfo){
			$msg = $this->_getUserError(-37);
			return false;
		}

		//能否支付
		if(!$this-> checkPayAble($orderInfo['hash'], 'landscape', $errorMsg)){
			$msg = '{"errors":{"msg":["'.$errorMsg.'"]}}';
			return false;
		}

		//支付方式 
		$useAblePayments = $this->load->common('payments')->getUseAblePayments($orderInfo['ticket']['ticket_id'], $orderInfo['buyer_organization_id']);
		if(!array_key_exists($post['payment'], $useAblePayments)){
			$msg = $this->_getUserError(-33);
			return false;
		}else{
			//信用支付
			if($post['payment'] == 'credit'){
				$curMoney = $this->load->common('partner')->getPartnerCredit($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id']);
				if($curMoney < $orderInfo['amount']){
					$msg = $this->_getUserError(-34);
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 更新支付订单方式，并生成新的支付单
	 * @param string $post 订单id和支付方式的数组
	 * @param string $oldOrderInfo 订单信息
	 * @param string $msg 错误信息
	 * @return bool
	 */
	public function updateOrderPayment($post, $oldOrderInfo, &$msg)
	{
		//未传订单号
		if(!$post['order_id']){
			$msg = $this->_getUserError(-18);
			return false;
		}

		//未选择支付方式
		if(!$post['payment']){
			$msg = $this->_getUserError(-36);
			return false;
		}

		$orderSaveData = array(
			'payment'    => $post['payment'],
			'updated_at' => date('Y-m-d H:i:s', time()),
		);

		//更新订单表
		$ordersModel  = $this->load->model('orders');
		$result       = $ordersModel->update($orderSaveData, array('id'=>$post['order_id']));
		$affectedRows = $ordersModel->affectedRows();
		if(!$result || $affectedRows < 1){
			$msg = $this->_getUserError(-19);
			return false;
		}else{
			//假如不是线下支付，生成新的支付单
			$paymentNeedData   = array(
				'payment'   => $post['payment'],
				'nums'      => $oldOrderInfo['nums'],
				'ticket_id' => $oldOrderInfo['ticket']['ticket_id'],
			);
			$savePaymentResult = $this->addPayment($paymentNeedData, $post['order_id'], $msg);
			if(!$savePaymentResult){
				return false;
			}

			//保存订单操作日志
			$usersModel     = $this->load->model('users');
			$account        = $usersModel->getID($_SESSION['backend_userinfo']['id'], 'account');
			$paymentsCommon = $this->load->common('payments');
			$oldPayment     = $paymentsCommon->getPaymentInfo($oldOrderInfo['payment']);
			$newPayment     = $paymentsCommon->getPaymentInfo($post['payment']);
			$memo           = '`'.$account['account'].'`将订单支付方式从`'.$oldPayment['app_display_name'].'`改为`'.$newPayment['app_display_name'].'`，订单号为`'.$post['order_id'].'`';
			$this->saveOrderLog($post['order_id'], $_SESSION['backend_userinfo']['id'], 'update', $memo, $msg);
			return true;
		}
		
	}

	/**
	 * 支付成功，更新订单支付状态
	 * @param int $orderId 订单id
	 * @param array $paymentInfo 支付单信息
	 * @return bool
	 */
	public function payFinish($orderId, $paymentInfo, &$msg = '')
	{
		$updateArray = array(
			'pay_at'     => date('Y-m-d H:i:s', time()),
			'pay_status' => 'paid',
			'updated_at' => date('Y-m-d H:i:s', time()),
			'payed'      => $paymentInfo['money'],
		);
		$ordersModel  = $this->load->model('orders');
		$result       = $ordersModel->update($updateArray, array('id'=> $orderId));
		$affectedRows = $ordersModel->affectedRows();
		if($result && $affectedRows >= 1){
			//假如是信用支付，需要扣除信用额度
			if($paymentInfo['pay_app_id'] == 'credit'){
				$partnerCommon              = $this->load->common('partner');
				$deductPartnerCreditResult  = $partnerCommon->deductPartnerCredit($paymentInfo, $msg);
				if(!$deductPartnerCreditResult){
					return false;
				}
			}

			//保存支付完成日志
			$usersModel     = $this->load->model('users');
			$account        = $usersModel->getID($_SESSION['backend_userinfo']['id'], 'account');
			$paymentsCommon = $this->load->common('payments');
			$newPayment     = $paymentsCommon->getPaymentInfo($paymentInfo['pay_app_id']);
			$memo           = '`'.$account['account'].'`完成订单支付，支付方式：`'.$newPayment['app_display_name'].'`，支付单：'.$paymentInfo['id'].'`，订单号为`'.$paymentInfo['order_id'].'`';
			$this->saveOrderLog($orderId, $_SESSION['backend_userinfo']['id'], 'payment', $memo, $msg);
			return true;
		}else{
			$msg = $this->_getUserError(-39);
			return false;
		}
	}

	//订单改期
	public function changeUseDay($post)
	{
		if(!$post['order_id']) {
			return $this->_getUserError(-18);
		}

		if(!$post['changeTo']) {
			return $this->_getUserError(-41);
		}

		$ordersModel = $this->load->model('orders');
		$orderInfo   = $ordersModel->getOrderDetailById($post['order_id'], '' ,'more');
		$msg         = '';

		//是否能改期
		$changeAble  = $this->checkChangeUseDayAble($orderInfo, $msg);
		if($changeAble){
			if($this->_changeToAble($post['changeTo'], $orderInfo, $msg)){
				$changeUseDayResult = $this->_changeUseDay($post, $msg);
				if($changeUseDayResult){
					return json_encode(array('data' => array($post)));
				}
			}
		}
		return $msg;
	}

	/**
	 * 检查订单是否可以改期
	 * @param mixed $orderInfo 订单详情
	 * @param string $msg 错误信息
	 * @return bool
	 */
	public function checkChangeUseDayAble($orderInfo, &$msg = '')
	{
		//订单是否存在
		if(!$orderInfo) {
			$msg = $this->_getUserError(-37);
			return false;
		}

		//假如不是自己的订单不能改期（仅限前台）
		// if($orderInfo['buyer_organization_id'] != $_SESSION['backend_userinfo']['organization_id']) {
		// 	$msg = $this->_getUserError(-42);
		// 	return false;
		// }

		//是否允许改期（仅限前台）
		// if($orderInfo['ticket']['allow_change_times'] <= 0) {
		// 	$msg = $this->_getUserError(-43);
		// 	return false;
		// }

		//允许改期次数小于等于已改期次数（仅限前台）
		// if($orderInfo['ticket']['allow_change_times'] <= $orderInfo['changed_useday_times']){
		// 	$tmpMsg = str_replace('{allow_change_times}', $orderInfo['ticket']['allow_change_times'], $this->_getUserError(-44));
		// 	$msg    = str_replace('{changed_useday_times}', $orderInfo['changed_useday_times'], $tmpMsg);
		// 	return false;
		// }

		//至少提前一天改期,订单里的游玩日期
		// if(strtotime($orderInfo['useday']) <= strtotime(date('Y-m-d', time()))) {
		// 	$msg = $this->_getUserError(-47);
		// 	return false;
		// }

		//订单不是活动订单
		if($orderInfo['status'] != 'active') {
			$msg = $this->_getUserError(-45);
			return false;
		}

		//假如有使用过的票，不能改期
		if($orderInfo['used_nums'] > 0){
			$msg = $this->_getUserError(-49);
			return false;
		}
		return true;
	}

	/**
	 * 检查订单要改到的日期
	 * @param mixed $orderInfo 订单详情
	 * @param string $changeTo 要改到的日期
	 * @return bool
	 */
	private function _changeToAble($changeTo, $orderInfo, &$msg)
	{
		//至少提前一天改期,要改到的日期或者订单里的游玩日期
		if(strtotime($changeTo) <= strtotime(date('Y-m-d', time()))) {
			$msg = $this->_getUserError(-47);
			return false;
		}

		//票的适用日期 start-end之间，去除不在weekly中的
		$usableDay  = explode(',', $orderInfo['ticket']['weekly']);
		if(strtotime($changeTo) < strtotime($orderInfo['ticket']['expire_start_at']) || 
			strtotime($changeTo) > strtotime($orderInfo['ticket']['expire_end_at']) || 
			!in_array(date('w',strtotime($changeTo)), $usableDay)) {
			$msg = $this->_getUserError(-48);
			return false;
		}

		return true;
	}

	/**
	 * 订单改期
	 * @param mixed $post 包含订单号和改期日期
	 * @return bool
	 */
	private function _changeUseDay($post, $msg)
	{
		$saveData = array(
			'useday'     => $post['changeTo'],
			'updated_at' => date('Y-m-d H:i:s', time()),
		);

		$ordersModel  = $this->load->model('orders');
		$oldOrderInfo = $ordersModel->getID($post['order_id'], 'useday');
		$updateResult = $ordersModel->update($saveData, array('id' => $post['order_id']));
		$affectedRows = $ordersModel->affectedRows();
		if($updateResult && $affectedRows >= 1){

			//保存订单操作信息
			$usersModel = $this->load->model('users');
			$account    = $usersModel->getID($_SESSION['backend_userinfo']['id'], 'account');
			$memo       = '`'.$account['account'].'`给订单改期，从'.$oldOrderInfo['useday'].'改为'.$post['changeTo'].'，订单号为`'.$post['order_id'].'`';
			$this->saveOrderLog($post['order_id'], $_SESSION['backend_userinfo']['id'], 'update', $memo, $msg);
			return true;
		}else{
			$msg = $this->_getUserError(-19);
			return false;
		}
	}

	/**
	 * 获取订单在页面显示的状态
	 * @param string $status 订单状态
	 * @param string $payStatus 订单支付状态
	 * @return mixed string | array
	 */
	static public function getOrderRealShowStatus($status, $payStatus)
	{
		if($status != 'active') {
			return self::$orderStatus[$status];
		} else {
			return self::$orderPayStatus[$payStatus];
		}
	}

	/**
	 * 获取订单的状态
	 * @param string $status 订单状态
	 * @return mixed string | array
	 */
	static public function getOrderStatus($status = '')
	{
		if($status) {
			return self::$orderStatus[$status];
		} else {
			return self::$orderStatus;
		}
	}

	/**
	 * 获取订单的支付方式
	 * @param string $status 订单状态
	 * @return mixed string | array
	 */
	static public function getPayments($status = '')
	{
		if($status) {
			return self::$payments[$status];
		} else {
			return self::$payments;
		}
	}

	/**
	 * 获取订单的支付状态
	 * @param string $payStatus 订单支付状态
	 * @return bool
	 */
	static public function getOrderPayStatus($payStatus = '')
	{
		if($payStatus) {
			return self::$orderPayStatus[$payStatus];
		} else {
			return self::$orderPayStatus;
		}
	}


	/**
	 * 发送短信
	 *
	 * @return json
	 * @author cuiyulei
	 **/
	public function doSMS($post)
	{
		//TODO::发送短信
		$mobile  = $post['mobile'];
		$content = $post['content'];
		
		return json_encode(array('data' => array('mobile' => $mobile, 'content' => $content, 'succ' => '发送成功')));
	}

	/**
	 * 获取使用的week信息
	 *
	 * @param  string $weekly
	 *
	 * @return string
	 * @author cuiyulei
	 **/
	static public function getWeekly($weekly)
	{
		$weekly = explode(',', $weekly);
		$week   = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
		if (count($weekly) < 7) {
			foreach ($weekly as $key => $value) {
				$useday[] = $week[$value];
			}
		} else {
			$useday[] = '平日';
		}

		return implode(',', $useday);
	}

	//使用门票
	public function useTicket($post)
	{
		if($post['tickets']) {
			if(!$data = $this->_checkTicket($post['tickets'][0], $msg = '')){
				return $msg;
			}
			$ticketsModel = $this->load->model('tickets');
			$updateData   = array(
				'status'    => 'used',
				'used_time' => time(),
			);
			$where        = $ticketsModel->parseFilter(array('hash|in' => $post['tickets']));
			$result       = $ticketsModel->update($updateData, $where);
			$affectedRows = $ticketsModel->affectedRows();
			return json_encode(array('data'=>$post['tickets']));
		} else {
			return $this->_getUserError(-1);
		}
	}
}