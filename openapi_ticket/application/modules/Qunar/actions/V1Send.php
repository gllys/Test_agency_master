<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 */
class SendAction extends Yaf_Action_Abstract{
    /**
     */
    public function execute(){
        $ctrl = $this->getController();
        try {
            $data = array(
                'distributor_id' => $ctrl->body['distributor_id'],
                'partnerorderId' =>  $ctrl->body['id'],
                'eticketNo' =>  $ctrl->body['id'],
                'eticketSended' => 'TRUE',
            );
            Process_Async::send(array('ApiQunarModel', 'sendCodeNotice'), array($data));
            Lang_Msg::output(array(
                'code' => 200,
            ));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
