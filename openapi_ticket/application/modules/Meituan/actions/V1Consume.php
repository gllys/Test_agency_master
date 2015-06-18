<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ConsumeAction extends Base_Action_Abstract{

    /**
     * 用户消费(核销)通知
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        Util_Logger::getLogger('meituan')->info(__METHOD__, $ctrl->body, '', '核销通知');

        if(!Util_Common::checkParams($ctrl->body, array('order_id', 'verify_code', 'num', 'used_num', 'refunded_nums'))){
            $this->errorLog('缺少必要参数！');
        }

        $logKey = $ctrl->body['verify_code'];
        $service = Meituan_Service::create(true);

        if($ctrl->body['order_id'] == ''){
            $this->errorLog('order_id：ota订单id，不能为空', $logKey);
        }
        if($ctrl->body['verify_code'] == ''){
            $this->errorLog('verify_code：内部订单id，不能为空', $logKey);
        }
        if($ctrl->body['num'] == 0){
            $this->errorLog('num：订单总票数，不能为0', $logKey);
        }
        if($ctrl->body['used_num'] == 0){
            $this->errorLog('used_num：目前总核销数，不能为0', $logKey);
        }

        $status = ($ctrl->body['used_nums'] + $ctrl->body['refunded_nums']) == $ctrl->body['nums'] ? 8 : 9;  //8:已全部使用 ; 9:未全部使用
        $body = array(
            'bookOrderId'    => $ctrl->body['order_id'],      //外部订单id
            'partnerOrderId' => $ctrl->body['verify_code'],   //核销码（内部订单id）
            'orderQuantity'  => $ctrl->body['num'],           //订单总票数
            'usedQuantity'   => $ctrl->body['used_num'],      //目前总核销数
            'refundQuantity' => $ctrl->body['refunded_nums'], //退款票数
            'orderStatus'    => $status,                       //4：订单成功
        );
        $params['body'] = $body;
        $res = $service->request($params, '/rhone/lv/order/consume/notice');
        if($res['code'] != 200){
            $this->errorLog(json_encode($res), $logKey);
        }

        Util_Logger::getLogger('meituan')->info(__METHOD__, $res, '', '核销通知回传数据' , $logKey);

        Lang_Msg::output(array(
            'code' => 200,
            'code_msg' => '核销成功',
            'ota_msg'   => $res['describe'],
        ));

    }


}