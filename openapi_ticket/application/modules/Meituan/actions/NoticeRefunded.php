<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class RefundedAction extends Yaf_Action_Abstract{


    /**
     * 退款通知
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        Util_Logger::getLogger('meituan')->info(__METHOD__, $ctrl->body, '', '退款通知' , $ctrl->body['order_id']);

        $service = Meituan_Service::create(true);
        try{
            if(!$ctrl->body['source_id']){
                throw new Exception('缺少source_id：ota订单id');
            }
            if(!$ctrl->body['order_id']){
                throw new Exception('缺少order_id：内部订单id');
            }
            if(!$ctrl->body['status']){
                throw new Exception('缺少status：退单状态');
            }

            $body = array(
                'bookOrderId'    => $ctrl->body['source_id'],
                'partnerOrderId' => $ctrl->body['order_id'],
                'autoRefund'     => 1,
                'refundType'     => 1,
                'refundDes'      => $ctrl->body['remark'],
                'orderStatus'    => $ctrl->body['status'] == 'success' ? 6 : 7, //退款状态 6：成功 7：退款失败
                'refundPrice'    => $ctrl->body['refund_money'],
                'refundRefactorage' => $ctrl->body['refund_fee'],
            );
            $params['body'] = $body;
            $res = $service->request($params, '/rhone/lv/order/refund/notice');

            Util_Logger::getLogger('meituan')->info(__METHOD__, $res, '', '退款通知回传数据' , $ctrl->body['order_id']);

            if($res['code'] == 200){
                Lang_Msg::output(array(
                    'code' => 200,
                    'code_msg' => '退款成功',
                    'ota_msg'   => $res['describe'],
                ));
            }else{
                throw new Exception(json_encode($res));
            }
        }catch (Exception $ee){
            Util_Logger::getLogger('meituan')->error(__METHOD__, $ee->getMessage(), '', '退款通知失败' , $ctrl->body['order_id']);
            Lang_Msg::error($ee->getMessage());
        }
    }




}