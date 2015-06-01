<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ProductchangedAction extends Yaf_Action_Abstract{


    /**
     * 产品变化通知
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        Util_Logger::getLogger('meituan')->info(__METHOD__, $ctrl->body, '', '产品变化通知' , $ctrl->body['code']);

        $service = Meituan_Service::create(true);
        try{
            if(!$ctrl->body['code']){
                throw new Exception('缺少code（ota产品编码）参数');
            }

            $body = array(
                'partnerDealId' => $ctrl->body['code'],
                'status'        => !isset($ctrl->body['is_sale']) || $ctrl->body['is_sale'] == '' ? 2 : $ctrl->body['is_sale'],
            );
            $params['body'] = $body;
            $res = $service->request($params, '/rhone/lv/deal/change/notice');

            Util_Logger::getLogger('meituan')->info(__METHOD__, $res, '', '产品变化通知回传数据' , $ctrl->body['code']);

            if($res['code'] == 200){
                Lang_Msg::output(array(
                    'code' => 200,
                    'code_msg' => '通知成功',
                    'ota_msg'  => $res['describe'],
                ));
            }else{
                throw new Exception(json_encode($res));
            }
        }catch (Exception $ee){
            Util_Logger::getLogger('meituan')->error(__METHOD__, $ee->getMessage(), '', '产品变化通知失败' , $ctrl->body['code']);
            Lang_Msg::error($ee->getMessage());
        }
    }




}