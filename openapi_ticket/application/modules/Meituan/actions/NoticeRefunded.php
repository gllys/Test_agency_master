<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class RefundedAction extends Base_Action_Abstract{


    /**
     * 退款通知
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        Util_Logger::getLogger('meituan')->info(__METHOD__, $ctrl->body, '', '退款通知');

        if(!Util_Common::checkParams($ctrl->body, array('source_id', 'order_id', 'product_id', 'distributor_id', 'status'))){
            $this->errorLog('缺少必要参数！');
        }

        $logKey = $ctrl->body['order_id'];
        $service = Meituan_Service::create(true);

        if($ctrl->body['source_id'] == ''){
            $this->errorLog('source_id：ota订单id，不能为空', $logKey);
        }
        if($ctrl->body['order_id'] == ''){
            $this->errorLog('order_id：内部订单id，不能为空', $logKey);
        }
        if($ctrl->body['status'] == ''){
            $this->errorLog('status：退单状态，不能为空', $logKey);
        }

        $params = array(
            'product_id' => $ctrl->body['product_id'],
            'agency_id' => $ctrl->body['distributor_id'],
        );
        $product = ApiOtaModel::model()->productDetail($params);
        if($product['code'] != 'succ'){
            $this->errorLog('分销商产品获取失败：'.json_encode($params), $logKey);
        }
        $body = array(
            'bookOrderId'    => $ctrl->body['source_id'],
            'partnerOrderId' => $ctrl->body['order_id'],
            'autoRefund'     => 1,
            'refundType'     => 1,
            'refundDes'      => isset($ctrl->body['remark']) ? $ctrl->body['remark'] : '',
            'orderStatus'    => $ctrl->body['status'] == 'success' ? 6 : 7, //退款状态 6：成功 7：退款失败
            'refundPrice'    => $ctrl->body['nums'] * floatval($product['body']['price']),
            'refundRefactorage' => isset($ctrl->body['refund_fee']) ? $ctrl->body['refund_fee'] : '',
        );
        $params['body'] = $body;
        $res = $service->request($params, '/rhone/lv/order/refund/notice');
        if($res['code'] != 200){
            $this->errorLog(json_encode($res), $logKey);
        }

        Util_Logger::getLogger('meituan')->info(__METHOD__, $res, '', '退款通知回传数据' , $logKey);

        Lang_Msg::output(array(
            'code' => 200,
            'code_msg' => '退款成功',
            'ota_msg'   => $res['describe'],
        ));
    }




}