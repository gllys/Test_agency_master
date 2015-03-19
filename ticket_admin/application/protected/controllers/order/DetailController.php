<?php

class DetailController extends Controller {

    public function actionIndex() {
       	$data['status_labels'] = array('unpaid'=>'未支付','cancel' => '已取消','paid' => '已付款','finish' => '已完成','billed' => '已结款');
		$data['paid_type'] = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
        $detail = Order::api()->detail(array('id' => $_GET['id'], 'supplier_id' => Yii::app()->user->org_id,'show_order_items'=>1));
        if ($detail['code'] == 'succ') {
            $data['detail'] = $detail['body'];
            $data['ticket'] = $detail['body']['order_items'];

            // //获取分销商操作人
            // $suid = $data['detail']['created_by'];
            // $rs = AgencyUser::api()->search(array('id' => $suid));
            // //print_r($rs);exit;
            // //$data['detail']['distributor_user'] = $suser['name'];

            // //获取供应商操作人
            // $tid = $data['ticket'][0]['ticket_template_id'];
            // $ticketInfo = TicketTemplate::api()->ticketinfo(array('ticket_id' => $tid));
            // $duid = $ticketInfo['body']['created_by'];
            // $duser = Users::model()->findByPk($duid);
            // $data['detail']['supplier_user'] = $duser['name'];            
        }
        $this->render('index', $data);
    }

    public function actionApply() {
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['user_id'] = Yii::app()->user->org_id;
            $data = Refund::api()->apply($param);
            if ($data['code'] != 'fail') {
                $this->redirect(array('/order/refund/'));
            } else {
                $this->redirect(array('/order/detail/', 'id' => $param['order_id']));
            }
        }
    }

}
