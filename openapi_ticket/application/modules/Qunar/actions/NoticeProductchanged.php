<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ProductchangedAction extends Base_Action_Abstract{


    /**
     * 产品变化通知
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();

        !$ctrl->body['code'] && $this->errorLog('缺少产品编码：code');
        !$ctrl->body['agency_id'] && $this->errorLog('缺少分销商id：agency_id');

        $data = array(
            'resourceId'=> $ctrl->body['code'],
        );

        Util_Logger::getLogger('qunar')->info(__METHOD__, $ctrl->body, '', '产品变化通知数据', $ctrl->body['code']);

        $service = new Qunar_Service();
        $service->setIdentity($ctrl->body['agency_id'], 'organization_id');
//        $service->setIdentity('ZFZJY');
        $service->qunar_url = $ctrl->config['qunar']['qunar_url'];
        $service->request('NoticeProductChangedRequest.xml', 'noticeProductChanged', $data);

        Util_Logger::getLogger('qunar')->info(__METHOD__,
            array('header' => $service->response_header, 'body' => $service->response_body), '', '产品变化通知回传数据',
            $ctrl->body['code']
        );

        if($service->response_header->code == 1000){
            //todo 去哪儿核销成功
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '产品变化通知成功',
                'ota_msg' => $service->response_body->message,
            ));
        }else{

        }

        Lang_Msg::error(json_encode($service->response_header->code.':'.$service->response_header->describe));
    }
}