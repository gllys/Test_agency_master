<?php
/**
 * 订单生成规则 5位的景区ID，1位的平台ID，10位的自增ID
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class TicketsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'tickets';
	public $pk         = 'id';

	const STATUS_DISABLE = '0';//无效
	const STATUS_ENABLE = '1';//有效

	/**
	 * 生成唯一的订单HASH值
	 *
	 * @param int $landscapeId 分页及其他条件
	 * @param int $orderId     订单ID
	 * @return array
	 */ 
	public function genHash($landscapeId, $orderId, $nums)
	{
		return str_pad($landscapeId , 5, '0', STR_PAD_LEFT).PI_PLATFORM_ID.str_pad($orderId , 10, '0', STR_PAD_LEFT).str_pad($nums , 3, '0', STR_PAD_LEFT);
	}

	//生成票
	public function addTickets($ticketsData)
	{
		if($ticketsData){
			$insertSql = 'INSERT INTO '.$this->table.' (`id`,`order_id`)';
			$addItmes  = array();
			foreach($ticketsData as $key => $value){
				$addItmes[] = "('{$value['id']}','{$value['order_id']}')";
			}
			$insertSql .= 'VALUES '.implode(',', $addItmes);
			$this->query($insertSql);
			return $this->getAddID();
		}else{
			return false;
		}
	}

	/**
	 * 获取订单中票的使用情况
	 *
	 * @param int $orderId     订单id,即订单表主键
	 * @param string $type 获取类型 used、unused 跟数据库字段对应
	 * @return array
	 */ 
	public function getOrderTicketUsedCount($orderId, $type)
	{
		if($type == 'used'){
			$count = $this->getCount(array('order_id' => $orderId, 'status' => $type));
		}else{
			$sql = 'SELECT count(t.id) as count FROM '.$this->table.' as t 
					LEFT JOIN refund_apply_items rai ON t.id=rai.ticket_id
					LEFT JOIN refund_apply ra ON rai.refund_apply_id=ra.id
					WHERE t.order_id=\''.$orderId.'\' AND t.status=\''.self::STATUS_ENABLE.'\' AND (ra.`status`=\'reject\' OR ra.id is NULL)';
			$result = $this->getOneBySql($sql);
			$count  = $result['count'];
		}
		return $count;
	}

	/**
	 * 获取订单中票的状态情况
	 *
	 * @param int $orderId     订单id,即订单表主键
	 * @param string $type 获取类型 'refunding','refunded' 跟数据库字段对应
	 * @return array
	 */ 
	public function getOrderTicketStatusCount($orderId, $type)
	{
	    $statusMap = array(
                'unused'=>self::STATUS_ENABLE,
                'refunding'=>self::STATUS_DISABLE,
                'apply'=>self::STATUS_DISABLE,
                'refunding'=>self::STATUS_DISABLE,
                'checked'=>self::STATUS_DISABLE,
                'refunded'=>self::STATUS_DISABLE,
	    );

		if($type == 'refunding') {
			$where = "where t.order_id='".$orderId."' AND t.status='{$statusMap[$type]}' and ra.status in('apply','checked')";
		}else{
			$where = "where t.order_id='".$orderId."' AND t.status='{$statusMap[$type]}' and ra.status='{$type}'";
		}

		$sql = 'select count(t.id) as count from '.$this->table.' as t 
				LEFT JOIN refund_apply_items rai ON t.id=rai.ticket_id
				LEFT JOIN refund_apply ra ON rai.refund_apply_id=ra.id
				'.$where;
		$result = $this->getOneBySql($sql);
		$count  = $result['count'];
		return $count;
	}

	//获取订单中可以申请退款的票
	public function getRefundAbleTickets($orderId, $nums = '')
	{
		$ordersModel = $this->load->model('orders');
		$param = array(
			'join'   => array(
				array(
					'left_join'=>' refund_apply_items rai ON '.$this->table.'.id=rai.ticket_id'
				),
				array(
					'left_join'=>' refund_apply ra ON rai.refund_apply_id=ra.id'
				),
				array(
					'left_join'=>$ordersModel->table.' ON '.$this->table.'.order_id='.$ordersModel->table.'.id'
				),
			),
			'filter' => $ordersModel->table.'.pay_status=\'paid\' AND '.$ordersModel->table.'.status=\'active\' AND '.$this->table.".order_id='{$orderId}' AND ".$this->table.".status='".self::STATUS_ENABLE."' and (ra.`status`='reject' OR ra.id is NULL)"
		);
		$result = $this->getListExtension($param['filter'], $nums, 'id ASC', $this->table.'.id', '', $param['join']);
		return $result;
	}

	/**
	 * 可使用的票,1.未超过有效期 2.未使用且（未退款或者申请退款拒绝）
	 * 
	 */
	public function getUseAbleTickets($orderId)
	{
		$filter = array(
			'order_id' => $orderId,
			'status'   => self::STATUS_ENABLE,
		);
		return $this->getList($filter, '', '', 'id');
	}

	//票是否可用
	public function checkUseAble($ticketId)
	{

	}
}