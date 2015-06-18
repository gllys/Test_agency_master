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
        Util_Logger::getLogger('meituan')->info(__METHOD__, $ctrl->body, '', '产品变化通知');

        if(!Util_Common::checkParams($ctrl->body, array('code'))){
            $this->errorLog('缺少必要参数！');
        }

        $logKey = $ctrl->body['code'];
        $service = Meituan_Service::create(true);

        if($ctrl->body['code'] == ''){
            $this->errorLog('code：ota产品编码，不能为空', $logKey);
        }

        $body[] = array(
            'partnerDealId' => $ctrl->body['code'],
            'status'        => !isset($ctrl->body['is_sale']) || $ctrl->body['is_sale'] == '' ? 2 : $ctrl->body['is_sale'],
        );
        $params['body'] = $body;
        $res = $service->request($params, '/rhone/lv/deal/change/notice');
        if($res['code'] != 200){
            $this->errorLog(json_encode($res), $logKey);
        }

        Util_Logger::getLogger('meituan')->info(__METHOD__, $res, '', '产品变化通知回传数据' , $logKey);

        Lang_Msg::output(array(
            'code' => 200,
            'code_msg' => '通知成功',
            'ota_msg'  => $res['describe'],
        ));
    }




}