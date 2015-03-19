<?php
/**
 *
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class BillsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'bills';
	public $pk         = 'id';

	//可关联的,对应value为model前缀
	public $relateAble = array(
		'distributor_info' => 'Organizations',
		'supplier_info'    => 'Organizations',
		'orderInfo'        => 'Orders',
		'payed_img_info'   => 'Attachments',
	);
	//关联的字段,对应value为表字段
	public $relateField = array(
		'distributor_info' => 'distributor',
		'supplier_info'    => 'supplier',
		'payed_img_info'   => 'payed_img_id',
	);

	//1对多的关联  model,主表id，关联表id
	public $relateAbleBelongsToMany = array(
		'orderInfo' => array('BillsItems', 'bill_id', 'order_id'),
	);

	//获取账款单明细
	public function getBillDetail($id)
	{
		$info = $this->getOne('id='.$id);
		if($info){
			$info = $this->getOneRelate($info, 'distributor_info,orderInfo,payed_img_info');
			if($info['orderInfo']){
				$ordersModel       = $this->load->model('orders');
				$info['orderInfo'] = $ordersModel->getListRelate($info['orderInfo'], 'landscape,order_item');
			}
			$data['billInfo'] = $info;
			return $data;
		}else{
			return false;
		}
	}
}