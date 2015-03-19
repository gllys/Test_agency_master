<?php
/**
 * 
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class RefundsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'refunds';
	public $pk         = 'id';

	/**
     * 得到唯一的id
     * @return string id
     */
	public function genId()
	{
		$i = rand(0,9999);
		do {
			if(9999 == $i){
				$i = 0;
			}
			$i++;
			$id    = date('YmdHi').str_pad($i, 4, '0', STR_PAD_LEFT);
			$exist = $this->getID($id);
		} while($exist);
		return $id;
	}

	/**
     * 得到唯一的batchNo 这个是给支付宝退款用的  8位日期+refund_id
     * @return string batchNo
     */
	public function genBatchNo($id)
	{
		$batchNo = date('YmdH').$id;
		return $batchNo;
	}

	public function getRefundsList($param)
	{
		$ordersModel        = $this->load->model('orders');
		$organizationsModel = $this->load->model('organizations');
		//$usersModel         = $this->load->model('users');
		$usersModel         = $this->load->model('admin');
		$params             = array(
			'items'  => 15,
			'page'   => $param['page'],
			'fields' => $this->table.'.*,o.id as order_hash,og.name as og_name,u.account as u_account ',
			'join'   => array(
				array(
					'join' => $ordersModel->table.' o ON '.$this->table.'.order_id=o.id ',
				),
				array(
					'join' => $organizationsModel->table.' og ON og.id=o.buyer_organization_id',
				),
				array(
					'join' => $usersModel->table.' u ON '.$this->table.'.op_id=u.id ',
				),
			),
		);

		//只能是购买者和出售者能查看
		// if(isset($param['filter'])) {
		// 	$params['filter'] = $this->parseFilter($param['filter']);
		// 	$params['filter'] .= ' AND (o.buyer_organization_id='.$_SESSION['backend_userinfo']['organization_id'].' OR o.seller_organization_id='.$_SESSION['backend_userinfo']['organization_id'].')';
		// } else {
		// 	$params['filter'] = ' o.buyer_organization_id='.$_SESSION['backend_userinfo']['organization_id'].' OR o.seller_organization_id='.$_SESSION['backend_userinfo']['organization_id'];
		// }

		if (isset($param['filter'])) {
			$params['filter'] = $this->parseFilter($param['filter']);
		}

		$refundsList  = $this->commonGetList($params);
		return $refundsList;
	}
}