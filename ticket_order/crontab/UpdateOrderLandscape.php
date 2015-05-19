<?php
//生成1千万张票
require dirname(__FILE__) . '/Base.php';

class Crontab_UpdateOrderLandscape extends Process_Base
{
    public function run() {
       $now = time();
       $OrderModel = OrderModel::model();
       $OrderModel->share($now);
       $OrderItemModel = OrderItemModel::model();
       $OrderItemModel->share($now);
       $TicketTemplateModel = TicketTemplateModel::model();
       $orderItems = $OrderItemModel->search(array('kind'=>2));
       foreach($orderItems as $orderItem) {
       		echo "deal ".$orderItem['order_id']."...";
       		$tid = $orderItem['ticket_template_id'];
       		$tinfo = $TicketTemplateModel->getTicketInfo($tid);
       		$scenic_id = $tinfo['scenic_id'];
       		//更新ORDER
       		$OrderModel->updateById($orderItem['order_id'],array('landscape_ids'=>$scenic_id));
       		//更新ORDERITEM
       		$OrderItemModel->updateById($orderItem['id'],array('landscape_ids'=>$scenic_id));
       		echo "ok\n";
       }
    }
}

$test = new Crontab_UpdateOrderLandscape;
