<?php

class DetailController extends Controller {

    public function actionIndex() {
        //查看是否是景区身份
        $criteria = new CDbCriteria();
        $criteria->order = 'status DESC,id DESC'; 
        $criteria->select = "sell_role";
        $criteria->compare('id', Yii::app()->user->uid);
        $lists = Users::model()->find($criteria);
        if($lists->sell_role=="scenic"){
            $data['status_labels'] = array("unaudited"=>"待确认","reject"=>"已驳回",'unpaid'=>'未支付','cancel' => '已取消','paid' => '已付款','finish' => '已完成','billed' => '已结款');
       	}else{
            $data['status_labels'] = array('unpaid'=>'未支付','cancel' => '已取消','paid' => '已付款','finish' => '已完成','billed' => '已结款');
		}
        $data['paid_type'] = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
        $detail = Order::api()->detail(array('id' => $_GET['id'], 'supplier_id' => Yii::app()->user->org_id,'show_order_items'=>1));
        if ($detail['code'] == 'succ') {
            $data['detail'] = $detail['body'];
            $data['ticket'] = $detail['body']['order_items'];

            //备注内容ubb to html
            if(isset($data['detail']['remark'])){
                $data['detail']['remark'] = UbbToHtml::Entry($data['detail']['remark'], time());
            }
            // //获取分销商操作人
            // $suid = $data['detail']['created_by'];
            // $rs = AgencyUser::api()->search(array('id' => $suid));
            // //print_r($rs);exit;
            // //$data['detail']['distributor_user'] = $suser['name'];

            // //获取供应商操作人
            // $tid = $data['ticket'][0]['ticket_template_id'];
            // $ticketInfo = Tickettemplate::api()->ticketinfo(array('ticket_id' => $tid));
            // $duid = $ticketInfo['body']['created_by'];
            // $duser = Users::model()->findByPk($duid);
            // $data['detail']['supplier_user'] = $duser['name'];            
        }
        
        $infos = Order::api()->infos(array('id' => $_GET['id'], 'type' => 1), 0);
        if($infos['code'] == 'succ') { $data['infos'] = $infos['body']; }
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

    /**
     *hefeng
     *2015-01-29
     *新增订单确认方法
     **/
     public function actionConfirm(){

          $result = Order::api()->checkOrder(array('id' => $_GET['id'], 'supplier_id' => Yii::app()->user->org_id,"allow"=>1));

         if($result['code'] == 'succ'){
             //回传订单消息
             $this->actionOrderMessage(array('order_id'=>$_GET['id'],'receiver_organization'=>$_GET['receiver_organization'],'type'=>'1'));
             echo json_encode(array("code"=>"succ","url"=>"/order/history/"));die;
         }elseif($result["code"]=="fail"){
             echo json_encode(array("code"=>"fail","url"=>"/order/detail/index/id/{$_GET['id']}"));die;
         }
      }
    /**
     *hefeng
     *2015-01-30
     *新增订单驳回方法
     **/
    public function actionRejected(){
        if(Yii::app()->request->isPostRequest){
            $orderId = Yii::app()->request->getParam('orderId');
            $rejectedContent = Yii::app()->request->getParam('rejectedContent');
            $receiver_organization = Yii::app()->request->getParam('receiver_organization');
            $result = Order::api()->checkOrder(array('id' => $orderId, 'supplier_id' => Yii::app()->user->org_id,"allow"=>0,"reason"=>$rejectedContent));
            if($result['code'] == 'succ'){
                //回传订单消息
                $this->actionOrderMessage(array('order_id'=>$orderId,'receiver_organization'=>$receiver_organization,'type'=>'0'));
                echo json_encode(array("code"=>"succ","url"=>"/order/history/"));die;
            }elseif($result["code"]=="fail"){
                echo json_encode(array("code"=>"fail","url"=>"/order/detail/index/id/{$orderId}"));die;
            }
        }
    }
    /**
     *查看当前订单状态
     */
    public function actionCheckStatus(){
        $detail = Order::api()->detail(array('id' => $_GET['id'], 'supplier_id' => Yii::app()->user->org_id,'show_order_items'=>1));
        echo $detail["body"]["status"];die;
    }

    /*
     * 回传订单消息编辑
     * array $params 需要传输的数组
     * type 驳回、确认
     * receiver_organization 分销商id
     * order_id 回传订单id
     */
    public function actionOrderMessage($params = array()) {
        //组织条件
        $order_id = $params['order_id'];
        $type = $params['type'];
        $receiver_organization_id = $params['receiver_organization'];
        $status_type = array('已驳回','已确认');
        $method = array('修改','支付');
        $org_id = Yii::app()->user->org_id;
        $orgRs = Organizations::api()->show(array('id' => $org_id));
        if($orgRs['code'] == 'succ'){
            $org_name = $orgRs['body']['name'];
        }
        $content = '供应商：' . $org_name . ',订单号：' . '<a href="/order/detail/index/id/'. $order_id .'">' . $order_id . '</a>';
        $content .= $status_type[$type] . '请点击订单号去' . $method[$type] . '，今天未支付订单将自动取消';
        $param = array(
            'sys_type' => 6,
            'send_source' => 1,
            'send_status' => 1,
            'sms_type' => 1,
            'content' => $content,    //内容
            'send_organization' => $org_id,
            'receiver_organization' => $receiver_organization_id,  //分销商ID
        );
        Message::api()->add($param);
//        if($result['code'] == 'succ'){
//            $message = 'succ';
//        }else{
//            $message = $result['message'];
//        }
//        return $message;
    }

}
