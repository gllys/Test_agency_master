<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ConsumeAction extends Yaf_Action_Abstract{

    const CONSUME_URL = 'http://agent.beta.qunar.com/api/external/supplierServiceV2.qunar';

    /**
     * 用户消费(核销)通知（去哪儿）
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();

        $data = array(
            'partnerorderId'=> $ctrl->body['verify_code'],
            'orderQuantity'=> $ctrl->body['num'],
            'useQuantity'=> $ctrl->body['used_num'],
            'consumeInfo'=>'核销机具：'.$ctrl->body['posid'].'，内部核销编号：'.$ctrl->body['serial_num'],
        );

//        $ctrl->echoLog('data', var_export($data, true), 'qunar_noticeOrderConsumed.log');
        Util_Logger::getLogger('qunar')->info(__METHOD__, $data, '', '核销请求数据', $ctrl->body['verify_code']);

        $service = new Qunar_Service();
        $service->setIdentity($ctrl->body['distributor_id'], 'organization_id');
        $service->qunar_url = $ctrl->config['qunar']['consume_url'];
        $service->request('NoticeOrderConsumedRequest.xml', 'noticeOrderConsumed', $data);

//        $ctrl->echoLog('response_header', var_export($service->response_header, true), 'qunar_noticeOrderConsumed.log');
//        $ctrl->echoLog('response_body', var_export($service->response_body, true), 'qunar_noticeOrderConsumed.log');
        Util_Logger::getLogger('qunar')->info(__METHOD__,
            array('header' => $service->response_header, 'body' => $service->response_body), '', '核销回传数据',
            $ctrl->body['verify_code']
        );

        if($service->response_header->code == 1000){
            //todo 去哪儿核销成功
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '核销成功',
                'qunar_msg' => $service->response_body->message,
            ));
        }else{

        }

        Lang_Msg::error(json_encode($service->response_header->code.':'.$service->response_header->describe));
    }




}