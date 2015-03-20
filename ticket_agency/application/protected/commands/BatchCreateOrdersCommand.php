<?php

class BatchCreateOrdersCommand extends CConsoleCommand {

    public function run($args) {
        $this->batchCreate();
    }

    public function batchCreate(){
        require_once(dirname(__FILE__).'/../api/Order.php');
        $order = array(
            'ticket_template_id' => 523,
            'price_type' => 0,
            'distributor_id' => 330,
            'use_day' => '2015-03-04',
            'nums' => 1,
            'payment' => 'credit',
            'owner_name' => "电视台活动",
            'owner_mobile' => "18001751175",
            'owner_card' => "",
            'remark' => '',
            'user_id' => "492",
            'user_name' => "电视台活动",
            'user_account' => "dianshitaihuodong",
        );
        try{
            for($i=0;$i<1;$i++){
                print_r($order);
                $return = Order::api()->addPay($order);
                var_dump($return);
                if($return['code']!='succ'){
                    echo $return['message']."\n";
                    throw new Exception($return['message']);
                }
            }
        }catch (Exception $e){
            Yii::log($e->getMessage());
        }

    }

}