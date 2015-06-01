<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ConsumeAction extends Yaf_Action_Abstract{

    /**
     * 用户消费(核销)通知
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        Util_Logger::getLogger('meituan')->info(__METHOD__, $ctrl->body, '', '核销通知' , $ctrl->body['verify_code']);

        $service = Meituan_Service::create(true);
        try{
            if(!$ctrl->body['order_id']){
                throw new Exception('缺少order_id：ota订单id');
            }
            if(!$ctrl->body['verify_code']){
                throw new Exception('缺少verify_code：内部订单id');
            }
            if(!$ctrl->body['num']){
                throw new Exception('缺少num：订单总票数，或者不能为0');
            }
            if(!$ctrl->body['used_num']){
                throw new Exception('缺少used_num：目前总核销数，或者不能为0');
            }

            $body = array(
                'bookOrderId'    => $ctrl->body['order_id'],      //外部订单id
                'partnerOrderId' => $ctrl->body['verify_code'],   //核销码（内部订单id）
                'orderQuantity'  => $ctrl->body['num'],           //订单总票数
                'usedQuantity'   => $ctrl->body['used_num'],      //目前总核销数
                'refundQuantity' => $ctrl->body['refunded_nums'], //退款票数
                'status'         => 4,                            //4：订单成功
            );
            $params['body'] = $body;
            $res = $service->request($params, '/rhone/lv/order/consume/notice');

            Util_Logger::getLogger('meituan')->info(__METHOD__, $res, '', '核销通知回传数据' , $ctrl->body['verify_code']);

            if($res['code'] == 200){
                Lang_Msg::output(array(
                    'code' => 200,
                    'code_msg' => '核销成功',
                    'ota_msg'   => $res['describe'],
                ));
            }else{
                throw new Exception(json_encode($res));
            }
        }catch (Exception $ee){
            Util_Logger::getLogger('meituan')->error(__METHOD__, $ee->getMessage(), '', '核销通知失败' , $ctrl->body['verify_code']);
            Lang_Msg::error($ee->getMessage());
        }

    }




}