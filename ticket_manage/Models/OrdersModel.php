<?php
/**
 * 订单生成规则 5位的景区ID，1位的平台ID，10位的自增ID
 *
 * 2014-1-10
 *
 * @author  cyl
 * @version 1.0
 */
class OrdersModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'orders';
	public $pk         = 'id';


	//可关联的,对应value为model前缀
	public $relateAble = array(
		'landscape'           => 'Landscapes',
		'buyer_organization'  => 'Organizations',
		'seller_organization' => 'Organizations',
		'created_user_info'   => 'Users',
		'order_item'          => 'OrderItems',
		'refund_applys'       => 'RefundApply',
	);

	//关联的字段,对应value为表字段
	public $relateField = array(
		'landscape'           => 'landscape_id',
		'buyer_organization'  => 'buyer_organization_id',
		'seller_organization' => 'seller_organization_id',
		'created_user_info'   => 'created_by',
	);

	//1对多的关联  model,主表id，关联表id
	public $relateAbleBelongsToMany = array(
		'order_item'    => array('OrderItems', 'order_id', 'id'),
		'refund_applys' => array('RefundApply', 'order_id', 'id')
	);

	/**
	 * 生成唯一的订单HASH值
	 *
	 * @param int $landscapeId 分页及其他条件
	 * @param int $orderId     订单ID
	 * @return array
	 */ 
	public function genHash($landscapeId, $orderId)
	{
		return str_pad($landscapeId , 5, '0', STR_PAD_LEFT).PI_PLATFORM_ID.str_pad($orderId , 10, '0', STR_PAD_LEFT);
	}

	/**
	 * 获取订单详情
	 *
	 * @param int $hash     订单hash
	 * @param string $relate 订单关联 relateAble
	 * @param string $type 订单详情 simple:简单的详情，只包括订单、景区、票部分信息，more ，基本包括所有信息，用于详情页
	 * @return array
	 */ 
	public function getOrderDetail($hash, $relate, $type = 'simple')
	{
		$params            = array();
		$params['filter']  = array(
			$this->table.'.id' => $hash,
		);

		$orderList         = $this->commonGetList($params);

		$info              = $orderList['data'][0];
		if($info){
			$info              = $this->_getOrderDetail($info, $type);
		}
		return $info;
	}

	public function _getOrderDetail($info, $type)
	{
		$orderItems        = $this->load->model('orderItems')->getOne("order_id='{$info['id']}'");
		$info['price']     = $orderItems['price'];
                $info['useday']     = $orderItems['useday'];
		$info['ticket']    = $orderItems;
		if($type == 'more'){
			$ticketsModel           = $this->load->model('tickets');
			$info['used_nums']      = $this->load->model('TicketUsed')->getCount(array('order_id'=>$info['id']),'COUNT(distinct ticket_id)');;
			$info['unused_nums']    = $ticketsModel->getOrderTicketUsedCount($info['id'], 'unused');
			$info['refunding_nums'] = $ticketsModel->getOrderTicketStatusCount($info['id'], 'refunding');
			$info['apply_nums']     = $ticketsModel->getOrderTicketStatusCount($info['id'], 'apply');
			$info['checked_nums']   = $ticketsModel->getOrderTicketStatusCount($info['id'], 'checked');
			$info['refunded_nums']  = $ticketsModel->getOrderTicketStatusCount($info['id'], 'refunded');
		}
		return $info;
	}

	//获取详情时候的params
	private function _detailParams($relate)
	{
		$orderExtensionModel = $this->load->model('orderExtension');
		$params              = array(
			'relate' => $relate,
			'join' => array(
				array(
					'left_join' => $orderExtensionModel->table.' ON '.$this->table.'.id='.$orderExtensionModel->table.'.order_id',
				),
			),
			'fields' => $this->table.'.*,'.$orderExtensionModel->table.'.changed_useday_times',
		);
		return $params;
	}

	/**
	 * 获取订单详情
	 *
	 * @param int $id     订单hash
	 * @param string $relate 订单关联 relateAble
	 * @param string $type 订单详情 simple:简单的详情，只包括订单、景区、票部分信息，more ，基本包括所有信息，用于详情页
	 * @return array
	 */ 
	public function getOrderDetailById($id, $relate, $type = 'simple')
	{
		$params            = $this->_detailParams($relate);
		$params['filter']  = array(
			$this->table.'.id' => $id,
		);
		$orderList         = $this->commonGetList($params);
		$info              = $orderList['data'][0];
		if($info){
			$info              = $this->_getOrderDetail($info, $type);
		}
		return $info;
	}

	/**
	 * 获取订单列表
	 *
	 * @param array $param   过滤条件
	 * @return array
	 */ 
	public function getOrderList($param)
	{
		$list = $this->commonGetList($param);
		if($list['data']){
			foreach($list['data'] as $key => $value){
				$list['data'][$key] = $this->_getOrderDetail($value, 'more');
			}
		}
		return $list;
	}

	/**
	 * 获取报表
	 *
	 * @param array $param   过滤条件
	 * @return array
	 */ 
	public function getOrderStatement($param = '')
	{
		//offset
		if(isset($param['items'])) {
			$items = $param['items'];
			$limit = ' LIMIT '.($param['page']-1)*$param['items'].','.$param['items'].' ';
		}

		$listFields = 'SELECT oi.name,oi.landscape_id,l.name as l_name,
			SUM(CASE WHEN o.payment="credit" THEN o.amount ELSE 0 END) AS credit_amount,
			SUM(CASE WHEN o.payment="offline" THEN o.amount ELSE 0 END) AS offline_amount,
			SUM(CASE WHEN o.payment!="credit" AND o.payment!="offline" THEN o.amount ELSE 0 END) AS online_amount,
			SUM(o.amount) AS total,  
			SUM(o.nums) AS tickets_total_nums,
			COUNT(o.id) AS order_total_nums ';

		$countField = 'SELECT count(distinct oi.name,oi.landscape_id) as count ';
		$sql        = ' FROM orders o 
						JOIN order_items oi ON o.id=oi.order_id 
						LEFT JOIN landscapes l ON oi.landscape_id=l.id ';

		$where      = ' WHERE '.$this->parseFilter($param['filter']);

		$groupBy    = ' GROUP BY oi.name,oi.landscape_id ';
		$orderBy    = ' ORDER BY total DESC ';
		$data       = $this->getListBySQL($listFields.$sql.$where.$groupBy.$orderBy.$limit);
		$count      = $this->getOneBysql($countField.$sql.$where);
		return array(
			'data' => $data,
			'pagination' => array(
				'items' => $param['items'],
				'count' => $count['count'],
			)
		);
	}
}