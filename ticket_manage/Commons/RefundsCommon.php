<?php
/**
 *  
 * 
 * 2013-12-09
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class RefundsCommon extends BaseCommon
{
	public function __construct()
	{
		parent::__construct();
		require_once(PI_APP_ROOT.'Configs/PaymentsMapping.php');
	}

	static public $refundsStatus = array(
		'succ'     => '支付成功',
		'fail'     => '支付失败',
		'cancel'   => '未支付',
		'error'    => '处理异常',
		'invalid'  => '非法参数',
		'progress' => '已付款至担保方',
		'timeout'  => '超时',
		'ready'    => '准备中',
	);

	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
		'-2'  => '{"errors":{"msg":["您的信用账户余额不足"]}}',
		'-3'  => '{"errors":{"msg":["支付失败"]}}',
	);


	//获取支付单状态
	static public function getRefundsStatus($status = '')
	{
		if($status){
			return self::$refundsStatus[$status];
		}else{
			return self::$refundsStatus;
		}
	}

	//生成退款单
	public function addRefund($refundApplyInfo, &$msg)
	{
		$refundData     = $this->_formatRefund($refundApplyInfo);
		$refundsModel   = $this->load->model('refunds');

		//信用支付直接成功
		if(in_array($refundApplyInfo['payment'], array('credit', 'advance'))) {
			$refundData['status'] = 'succ';
		} else {
			if ($refundApplyInfo['status'] != 'checked') { //不是信用支付，并且审核未通过不生成退款单
				return ;
			}
		}


		$refundData['id'] = $addId = $refundsModel->genId();
        unset($refundData['create_time']);
		$result           = $refundsModel->add($refundData);
		$affectedRows     = $refundsModel->affectedRows();
		if($result && $affectedRows >= 1){
			return true;
		}else{
			$msg = $this->_getUserError(-35);
			return false;
		}
	}

	private function _formatRefund($refundApplyInfo)
	{
		$paymentsCommon  = $this->load->common('payments');
		$paymentInfo     = $paymentsCommon->getPaymentInfo($refundApplyInfo['payment']);

		$paymentsModel   = $this->load->model('payments');
		$paymentBn       = $paymentsModel->getOne('order_id=\''.$refundApplyInfo['order_id'].'\' AND status=\'succ\' AND pay_app_id=\''.$refundApplyInfo['payment'].'\'', '', 'payment_bn');
		$ordersModel     = $this->load->model('orders');
		$organizationId  = $ordersModel->getOne(array('id' => $refundApplyInfo['order_id']), '', 'seller_organization_id');
		$syncId          = $ordersModel->generateSyncID($organizationId['seller_organization_id']);
		$postData = array(
			'money'           => $refundApplyInfo['money'],
			'u_id'            => $_SESSION['backend_userinfo']['id'],
			'op_id'           => $_SESSION['backend_userinfo']['id'],
			'account'         => '',
			'bank'            => '',
			'pay_account'     => '',
			'remark'          => '',
			'payment_bn'      => $paymentBn['payment_bn'],
			'pay_type'        => $paymentInfo['app_pay_type'],
			'ip'              => getIp(),
			'order_id'        => $refundApplyInfo['order_id'],
			'status'          => 'ready',
			'pay_app_id'      => $paymentInfo['app_id'],
			'refund_apply_id' => $refundApplyInfo['id'],
			'sync_id'         => $syncId,
		);
		return $postData;
	}
}