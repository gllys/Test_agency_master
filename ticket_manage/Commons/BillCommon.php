<?php
/**
 *  
 * 
 * 2013-09-09
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class BillCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
		'-2'  => '{"errors":{"msg":["您只能还在线支付的账单"]}}',
		'-3'  => '{"errors":{"msg":["您只能还自己的账单"]}}',
		'-4'  => '{"errors":{"msg":["不存在的账单"]}}',
		'-5'  => '{"errors":{"msg":["保存至数据库失败"]}}',
	);

	//账单支付状态对应 'unpaid','paid'
	static public $billPayStatus = array(
		'unpaid'    => '未结款',
		'paid'      => '已结款',
	);

	//账单类型
	static public $billType = array(
		'credit' => '信用支付',
		'online' => '在线支付',
	);

	//每次传送的最大数据量
	private $_pageSize = 100;

	//每次脚本最多循环次数,按道理走不到
	private $_max = 6000;

	//上传凭证
	public function uploadProve($post)
	{
		$id                = $post['bill_id'];
		$billsModel        = $this->load->model('bills');
		$billInfo          = $billsModel->getID($id);

		if(!$billInfo) {
			return $this->_getUserError(-4);
		}

		//只能还在线支付的账单
		if($billInfo['bill_type'] != 'online') {
			return $this->_getUserError(-2);
		}

		$uid               = $_SESSION['backend_userinfo']['id'];
		$attachmentsCommon = $this->load->common('attachments');
		$jsonData          = $attachmentsCommon->saveAttachment($uid);
		$result            = json_decode($jsonData, 1);
		if($result['errors']) {
			return $jsonData;
		} else {
			//更新账单的状态为已付款
			$updateData = array(
				'updated_at'   => date('Y-m-d H:i:s'),
				'pay_status'   => 'paid',
				'payed_img_id' => $result['data'][0]['id'],
			);

			$updateResult = $billsModel->update($updateData, array('id'=>$id));
			$affectedRows = $billsModel->affectedRows();
			if($updateResult && $affectedRows >= 1) {
				return json_encode(array('data' => array($billInfo)));
			} else {
				return $this->_getUserError(-5);
			}
		}
	}

	/**
	 * 获取账款单的类型
	 * @param string $type 账款单类型
	 * @return bool
	 */
	static public function getBillType($type = '')
	{
		if($type) {
			return self::$billType[$type];
		} else {
			return self::$billType;
		}
	}

	/**
	 * 获取账款单的支付状态
	 * @param string $payStatus 账款单支付状态
	 * @return bool
	 */
	static public function getBillPayStatus($payStatus = '')
	{
		if($payStatus) {
			return self::$billPayStatus[$payStatus];
		} else {
			return self::$billPayStatus;
		}
	}


	/**
	 * 强制生成结算账单
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function create()
	{
		// $today    = time();
		$startMsg = date('Y-m-d H:i:s', time())."	log start ".get_class($this).":createBill\r\n";
		$this->setLog('createBill', $startMsg);
		// echo $startMsg;

		$page     = 1;
		while( true ){
			// echo " now page is {$page} \n";

			$buyerSellerList = $this->getOrganizationList('online', $page);
			if(!$buyerSellerList['data']){
				break;
			}

			if($buyerSellerList['data']){
				$count       = count($buyerSellerList['data']);
				foreach($buyerSellerList['data'] as $value){
					$this->createBill('online', $value);
				}

				$str = '当前是此次更新的第'.$page.'页';
				$msg = date('Y-m-d H:i:s', time())."	".$str."\r\n";
				$this->setLog('createBill', $msg);
				// echo $msg."\r\n";
				$page++;
				if($count < $this->_pageSize){
					break;
				}
			}else{
				if($page > $this->_max){
					break;
				}
				$page++;
				continue;
			}
		}

		$endMsg = date('Y-m-d H:i:s', time())."	log end ".get_class($this).":createBill\r\n\r\n";
		$this->setLog('createBill', $endMsg);	
		// echo $endMsg;
	}

	/**
	 *  首先获取有已结束订单的机构
	 *
	 * @param string $paymentType 支付方式类型 online credit
	 * @param int $page 当前页数
	 * 2013-12-16
	 *
	 * @author  liuhe(liuhe009@gmail.com)
	 * @version 1.0
	 */
	public function getOrganizationList($paymentType, $page)
	{
		$ordersModel        = $this->load->model('orders');
		$params             = array(
			'fields' => 'buyer_organization_id,seller_organization_id',
			'page'   => $page,
			'items'  => $this->_pageSize,
			'filter' => array(
				'pay_status' => 'paid',
				'status'     => 'finish',
			),
			'group'  => 'buyer_organization_id,seller_organization_id',
		);

		//信用支付和在线支付
		if($paymentType == 'credit') {
			$params['filter']['pay_type'] = 'credit';
		} elseif($paymentType == 'online') {
			$params['filter']['pay_type'] = 'online';
		} else {
			return false;
		}

		$buyerSellerList = $ordersModel->commonGetList($params);

		return $buyerSellerList;
	}

	/**
	 *  创建账款单 
	 * 前提条件 1.订单已支付 2.订单已结束 3.订单的分销商和供应商 4.支付方式类型跟传的对应 5.支付单或退款单的支付类型跟传的对应 6.支付单或退款单成功
	 *
	 * @param mixed $value 包含buyer_organization_id,seller_organization_id 
	 * @param string $paymentType 支付方式类型 online credit  
	 * @param string $operatType 操作类型 system|supply
	 * 2013-12-16
	 *
	 * @author  liuhe(liuhe009@gmail.com)
	 * @version 1.0
	 */
	public function createBill($paymentType, $value, $operatType = 'supply')
	{
		if(!$value['buyer_organization_id'] || !$value['seller_organization_id']){
			return false;
		}

		if(!in_array($paymentType, array('online', 'credit'))){
			return false;
		}

		$paymentsModel = $this->load->model('payments');
		$refundsModel  = $this->load->model('refunds');
		$ordersModel   = $this->load->model('orders');

		//订单已支付，订单已结束，订单的分销商和供应商
		$params        = array(
			'filter' => array(
				$ordersModel->table.'.pay_status'             => 'paid',
				$ordersModel->table.'.status'                 => 'finish',
				$ordersModel->table.'.buyer_organization_id'  => $value['buyer_organization_id'],
				$ordersModel->table.'.seller_organization_id' => $value['seller_organization_id'],
			),
		);

		//订单支付方式
		if($paymentType == 'credit') {
			$params['filter'][$ordersModel->table.'.pay_type'] = 'credit';
		} elseif($paymentType == 'online') {
			$params['filter'][$ordersModel->table.'.pay_type'] = 'online';
		}

		//这里不直接用mysql计算总和，太慢，以后量大了考虑redis队列
		//支付单
		$paymentsParams = $this->_getBillParams($paymentsModel, $params, $paymentType);
		$paymentsList   = $paymentsModel->commonGetList($paymentsParams);

		//退款单
		$refundsParams  = $this->_getBillParams($refundsModel, $params, $paymentType);
		$refundsList    = $refundsModel->commonGetList($refundsParams);

		//这里只记录支付单的订单号，因为完成的订单有了支付单才可能有退款单
		if($paymentsList['data']) {
			$increaseMoney = $decreaseMoney = 0;
			$orderIds      = array();

			//支付单
			foreach($paymentsList['data'] as $payment) {
				$increaseMoney += $payment['money'];
				$orderIds[]     = $payment['order_id'];
			}

			//退款单
			if($refundsList['data']) {
				foreach($refundsList['data'] as $refund){
					$decreaseMoney += $refund['money'];
				}
			}

			//账单总额
			$totalAmount   = $increaseMoney - $decreaseMoney;
			$billsModel    = $this->load->model('bills');
			$billData      = $this->_formatBillData($value['buyer_organization_id'], $value['seller_organization_id'], $totalAmount, $paymentType);
			$billsModel->begin();
			$billAddResult = $billsModel->add($billData);
			$addId         = $billsModel->getAddID();
			if(!$billAddResult || !$addId){
				$billsModel->rollback();
				return false;
			}

			$saveBillItemsResult = $this->saveBillItems($addId, $orderIds);
			if(!$saveBillItemsResult){
				$billsModel->rollback();
				return false;
			}

			//更新订单表
			$ordersModel->update(array('status' => 'billed'), 'id in(\''.implode('\',\'', $orderIds).'\')');
			$orderUpdateAffectedRows = $ordersModel->affectedRows();
			if($orderUpdateAffectedRows < 1){
				$billsModel->rollback();
				return false;
			}

			//添加日志
			$orderLog = array();
			$operator = $operatType == 'system' ? '系统' : '系统强制生成账款单';
			foreach($orderIds as $order){
				$orderLog[] = "('".$order."', '', '', '".date('Y-m-d H:i:s')."', 'update', '".$operator."将订单状态改为已结款，订单号为：{$order}', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
			}

			$orderLogModel   = $this->load->model('orderLog');
			$orderLogField   = '(order_id,op_id,op_name,action_time,action,memo,created_at,updated_at)';
			$orderLogSql     = 'INSERT INTO '.$orderLogModel->table.$orderLogField.' VALUES'.implode(',', $orderLog);;
			$orderLogResult  = $orderLogModel->query($orderLogSql);

			$billsModel->commit();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取支付单或者退款单的参数
	 * @param object $model model 
	 * @param mixed $params 已有参数
	 *
	 * @return array
	 */
	private function _getBillParams($model, $params, $paymentType)
	{
		$ordersModel   = $this->load->model('orders');
		$params['filter'][$model->table.'.pay_type'] = $paymentType;
		$params['filter'][$model->table.'.status']   = 'succ';
		$params['join'][]                            = array(
			'join' => $ordersModel->table.' ON '.$model->table.'.order_id = '.$ordersModel->table.'.id',
		);
		$params['fields'] = $model->table.'.money,'.$model->table.'.order_id';
		return $params;
	}

	/**
	 * 账款单的结构
	 * @param int $distributor 分销商 
	 * @param int $supplier 供应商
	 * @param float $totalAmount 账单金额
	 * @param string $billType 单据类型：online|credit,在线付款|信用支付
	 */
	private function _formatBillData($distributor, $supplier, $totalAmount, $billType)
	{
		$data = array(
			'distributor'  => $distributor,
			'supplier'     => $supplier,
			'total_amount' => $totalAmount,
			'bill_type'    => $billType,
			'pay_status'   => 'unpaid',
			'created_at'   => date('Y-m-d H:i:s'),
		);
		return $data;
	}

	//保存账单明细
	public function saveBillItems($addId, $orderids)
	{
		$billsItemsModel = $this->load->model('billsItems');
		$tmpSqlCount     = 200;
		$field           = '(bill_id,order_id,created_at,updated_at)';
		$third           = array();
		for($firstCircle = 1; $firstCircle <= ceil(count($orderids)/$tmpSqlCount); $firstCircle ++){
			for($seCondCircle = ($firstCircle-1)*$tmpSqlCount; $seCondCircle < min($firstCircle*$tmpSqlCount, count($orderids));$seCondCircle++){
				$newInsertData['bill_id']      = $addId;
				$newInsertData['order_id']     = $orderids[$seCondCircle];
				$third[$firstCircle][$seCondCircle] = '('.$newInsertData['bill_id'].',"'.$newInsertData['order_id'].'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'")';
			}
			$insertSql    = 'INSERT INTO '.$billsItemsModel->table.' '.$field.' VALUES'.implode(',', $third[$firstCircle]);
			$result       = $billsItemsModel->query($insertSql);
			$affectedRows = $billsItemsModel->affectedRows();
			if(!$result || $affectedRows < 1){
				return false;
			}
		}
		return true;
	}
}