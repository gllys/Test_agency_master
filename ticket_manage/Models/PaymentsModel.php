<?php
/**
 * 
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class PaymentsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'payments';
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

	public function getPaymentsList($param)
	{
		$ordersModel        = $this->load->model('orders');
		$organizationsModel = $this->load->model('organizations');
		$usersModel         = $this->load->model('users');
		$params             = array(
			'items'  => 15,
			'page'   => $param['page'],
			'fields' => $this->table.'.*,o.hash as order_hash,og.name as og_name,u.account as u_account ',
			'join'   => array(
				array(
					'join' => $ordersModel->table.' o ON '.$this->table.'.order_id=o.id ',
				),
				array(
					'join' => $organizationsModel->table.' og ON og.id=o.seller_organization_id ',
				),
				array(
					'join' => $usersModel->table.' u ON '.$this->table.'.op_id=u.id ',
				),
			),
			
		);

		//只能是购买者和出售者能查看
		if(isset($param['filter'])) {
			$params['filter'] = $this->parseFilter($param['filter']);
			$params['filter'] .= ' AND (o.buyer_organization_id='.$_SESSION['userInfo']['organization_id'].' OR o.seller_organization_id='.$_SESSION['userInfo']['organization_id'].')';
		} else {
			$params['filter'] = ' o.buyer_organization_id='.$_SESSION['userInfo']['organization_id'].' OR o.seller_organization_id='.$_SESSION['userInfo']['organization_id'];
		}

		$paymentsList  = $this->commonGetList($params);
		return $paymentsList;
	}
}