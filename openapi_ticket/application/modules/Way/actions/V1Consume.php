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
     * 用户消费(核销)通知（淘在路上）
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        
        $config = Yaf_Registry::get('config');
        $resp = $message = '';
        
        //@todo 临时解决一个订购，核销的时候，不知道属于哪个appid的问题
        foreach ($config['way']['account'] as $account) {
            try {
                $resp = ApiWayModel::consume($ctrl->body, ApiWayModel::CONSUME_UPDATE, $account);
                $message = '';
                break;
            } catch (Exception $ex) {
                $message = $ex->getMessage();
            }
        }
        
        if ($message) {
            Util_Logger::getLogger('way')->error(__METHOD__, $ctrl->body, $message, '订单核销', $ctrl->body['order_id']);
             
            Lang_Msg::error($message);
        } else {
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '核销成功',
                'way_msg' => json_encode($resp),
            ));
<<<<<<< HEAD
        } catch (Exception $ex) {
            Lang_Msg::error($ex->getMessage());
<<<<<<< HEAD

=======
            
>>>>>>> 90f42504b60bff00f11952fbd557ac17a2520d2a
=======
>>>>>>> 81dcc72e90aae190d036164819d74ccaa19fd891
        }
    }




}