<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ConsumeAction extends Yaf_Action_Abstract{
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
            'consumeInfo'=>'核销机具：'.$this->body['posid'].'，内部核销编号：'.$this->body['serial_num'],
        );

//        $service = new Qunar_RequestService();
        $service = new Qunar_Service();
        $service->request('NoticeOrderConsumedRequest.xml', 'noticeOrderConsumed', $data);

        if($service->response_header->code == 1000){
            //todo 去哪儿核销成功
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '核销成功',
                'qunar_msg' => $service->response_body->message,
            ));
        }else{

        }

        $ctrl->echoLog('response_header', var_export($service->response_header, true), 'qunar_noticeOrderConsumed.log');
        $ctrl->echoLog('response_body', var_export($service->response_body, true), 'qunar_noticeOrderConsumed.log');
    }




}