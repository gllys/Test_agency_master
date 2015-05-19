<?php
set_time_limit(10);
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 15-02-04
 * Time: 上午9:54
 * 批量生成订单
 */

require dirname(__FILE__) . '/Base.php';

class Crontab_BatGenOrder extends Process_Base
{
    private $orderNum = 1; //生成数量
    private $use_day;
    private $payments = array('union','credit','advance');
    private $tasks = array(
        //'dengyunhua11' => array('product_id'=>798,'payment'=>'credit','supply_id'=>286, 'agency_id'=>288, 'total'=>105, 'added'=>0), //dengyunhua11
        //'family2' => array('product_id'=>798,'payment'=>'credit','supply_id'=>286, 'agency_id'=>387, 'total'=>180, 'added'=>0), //family2
        //'family3' => array('product_id'=>798,'payment'=>'credit','supply_id'=>286, 'agency_id'=>388, 'total'=>205, 'added'=>0), //family3
        //'family4' => array('product_id'=>798,'payment'=>'credit','supply_id'=>286, 'agency_id'=>389, 'total'=>50,  'added'=>0), //family4 50
        //'family5' => array('product_id'=>798,'payment'=>'credit','supply_id'=>286, 'agency_id'=>390, 'total'=>99,  'added'=>0), //family5
        //'family6' => array('product_id'=>798,'payment'=>'credit','supply_id'=>286, 'agency_id'=>391, 'total'=>408, 'added'=>0), //family6
        'zqf0' => array('product_id'=>903,'payment'=>'credit','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        'zqf1' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        'zqf2' => array('product_id'=>903,'payment'=>'credit','supply_id'=>18, 'agency_id'=>188, 'total'=>20, 'added'=>0),
        'zqf3' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>188, 'total'=>30, 'added'=>0),
        //'zqf4' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>30, 'added'=>0),
        //'zqf5' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>30, 'added'=>0),
        //'zqf6' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf7' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf8' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf9' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf10' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf11' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>188, 'total'=>20, 'added'=>0),
        //'zqf12' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>188, 'total'=>20, 'added'=>0),
        //'zqf13' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf14' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf15' => array('product_id'=>903,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>20, 'added'=>0),
        //'zqf-1' => array('product_id'=>786,'payment'=>'union','supply_id'=>18, 'agency_id'=>167, 'total'=>2, 'added'=>0), //零元票
    );

    public function run() {
        try {
            ApiOrder::model()->genOrder($this->tasks);
            ApiOrder::model()->useOrder();

            //$this->addOrder();
            //$this->verifyOrder();
        } catch(Exception $e){
            print_r($e);
        }
    }

    //批量核销订单
    private function verifyOrder() {
        $OrderModel = new OrderModel();
        $orders = $OrderModel->search(array('used_nums'=>0),"*",'use_day asc',28);
        if(!$orders){
            exit("No order!");
        }
        try {
            foreach($orders as $order){
                $params = array(
                    'data'=>json_encode(array($order['id']=>$order['nums'])),
                    'landscape_id'=>28,
                    'view_point'=>528,
                    'uid'=>379,
                    'or_id'=>18,
                );
                ApiOrder::model()->verify($params);
            }
            exit("\nDone!\n");
        } catch (Lang_Exception $e) {
            $OrderModel->rollback();
            Lang_Msg::error($e->getMessage());
        } catch ( Exception  $e) {
            print_r($e);
            $OrderModel->rollback();
            Lang_Msg::error('操作失败');
        }
    }

    //批量生成订单
    private function addOrder(){
        $this->use_day = date("Y-m-");
        $start = time();
        for ($i=0;$i<$this->orderNum;$i++) {
            $r = $this->genOrder();
            if(!$r){
                echo "\nHad Gen Order: ".$i."\n";
                exit;
            }
            if(0==($i+1)%100) echo "\nGen Order: ".($i+1)." , Use Time:".(time()-$start)." sec\n";
            //usleep(20);
            //$this->sleep($this->interval);
        }
        echo "\nGen Order: ".$this->orderNum."\n";
        exit;
    }


    private function genOrder(){
        $params = array(
            'sign'=>'debug',
            'product_id'=>903,
            'price_type'=>0,
            'distributor_id'=>167,
            'use_day'=>$this->use_day.'27', //随机
            'nums'=>rand(1,5),  //随机
            'owner_card'=>'320222198808181888',
            'owner_name'=>'family',
            'owner_mobile'=>'12345678901',
            'user_id'=>1026,
            'user_account'=>'family',
            'user_name'=>'family',
            'payment'=>'credit',
        );
        $r = $this->addPay($params);
        return $r;
    }

    private function addPay($params){
        $OrderModel = new OrderModel();
        try{
            $OrderModel->begin();
            $order = $OrderModel->addOrder($params);
            if($order){
                if($params[ 'payment' ]=='offline'){
                    if(!PaymentModel::model()->chgOrderStatusOnSucc($order['id'],array('status'=>'paid','pay_type'=>'offline','payment'=>'offline'))){
                        $OrderModel->rollback();
                        return false;
                    }
                } else {
                    $payInfo = PaymentModel::model()->addPayment(array(
                        'distributor_id'=>$order['distributor_id'],
                        'order_ids'=>$order['id'],
                        'payment'=>$params['payment'],
                        'status'=>'succ',
                        'remark'=>$order['remark'],
                        'user_id'=>$this->body['user_id']?$this->body['user_id']:$order['user_id'],
                        'activity_paid'=>$params['activity_paid'],//抵用券金额
                    ));
                    if(!$payInfo['order_ids']){
                        $OrderModel->rollback();
                        return false;
                    }

                    $pay_money = $params['activity_paid']>=$order['amount']?0:($order['amount']-$params['activity_paid']);
                    if($params[ 'payment' ]=='union') {
                        $unionParams = array(
                            'org_id'=> $order['distributor_id'],
                            'user_id'=> $this->body['user_id']?$this->body['user_id']:$order['user_id'],
                            'user_account'=> $this->body['user_account']?$this->body['user_account']:$order['user_account'],
                            'user_name'=> $this->body['user_name']?$this->body['user_name']:$order['user_name'],
                            'money'=> $pay_money,
                            'in_out'=> 0,
                            'trade_type'=> 1,
                            'pay_type'=> 0,
                            'remark'=> $order['id'],
                        );
                        $dopay = ApiUnionMoneyModel::model()->unionInout($unionParams);
                    }
                    else {
                        $dopay = OrganizationModel::model()->creditPay(array(
                            'distributor_id'=>$order['distributor_id'],
                            'supplier_id' => $order['supplier_id'],
                            'money'=>$pay_money,
                            'type'=>$params[ 'payment' ]=='advance'?1:0,
                            'serial_id'=>$order['id'],
                        ));
                    }
                    if(!$dopay || $dopay['code']=='fail'){
                        $OrderModel->rollBack();
                        return false;
                    }
                }
                $OrderModel->commit();
                $order = $OrderModel->getById($order['id']);

                Log_Order::model()->add(array('type'=>Log_Order::$type['CREATE'],'num'=>1,'order_ids'=>$order['id'],'content'=>Lang_Msg::getLang('INFO_ORDER_1'),'distributor_id'=>$params['distributor_id']));
                return true;
            }
            else{
                $OrderModel->rollback();
                return false;
            }
        } catch(Exception $e) {
            Log_Base::save('Order', 'error:'.$e->getMessage());
            Log_Base::save('Order', var_export($this->body,true));
            $OrderModel->rollback();
            return false;
        }
    }

}

class ApiOrder extends Base_Model_Api
{
    protected $srvKey = 'ticket_order';
    protected $url = '/v1/Verification/update';
    protected $method = 'POST';
    protected $filepath = "../log/batGenOrder.tmp";
    protected $taskFile = "../log/batGenOrderTask.php";

    //核销
    public function verify($params){
        $this::$srvUrls[$this->srvKey]='http://ticket-order.com';
        $this->params = $params;
        $this->url='/v1/Verification/update';
        $r = $this->request();
        $r = json_decode($r,true);
        return $r;
    }

    //下单并支付
    public function addPay($params){
        $this::$srvUrls[$this->srvKey]='http://ticket-order.com';
        $this->url = '/v1/order/addPay';
        //print_r($this->getSrvUrl().$this->url);
        $this->params = $params;
        $r = $this->request();
        $r = json_decode($r,true);
        return $r;
    }

    public function genOrder($tasks=array()) {
        $start = time();
        if(!$tasks) exit("Please Set Task!");

        if(file_exists($this->taskFile)) {
            $taskTmp = include($this->taskFile);
            $tasks = $taskTmp ? array_merge($tasks,$taskTmp) : $tasks;
        }

        $orderNum=0;
        foreach($tasks as $tk=>$task) {
            for ($i = $task['added']; $i < $task['total']; $i++) {
                $params = array(
                    'sign' => 'debug',
                    'product_id' => $task['product_id'],
                    'price_type' => 0,
                    'distributor_id' => $task['agency_id'],
                    'use_day' => date("Y-m-d"), //. rand(20, 31), //随机
                    'nums' => 1,  //随机
                    'owner_card' => '320222198808181888',
                    'owner_name' => 'family',
                    'owner_mobile' => '12345678901',
                    'user_id' => 1026,
                    'user_account' => 'family',
                    'user_name' => 'family',
                    'payment' => $task['payment'],
                );
                $r = $this->addPay($params);
                if (!$r || $r['code'] != 'succ') {
                    echo "\nAdding Order failed! Had Gen Order: " . ($i + 1) . "\n";
                    print_r($r);
                    exit;
                }
                $orderNum++;
                $task['added']++;

                $order = $r['body'];
                echo "\nOrder Created: {$order['id']} ,Nums: {$order['nums']}  ";
                $vparam = array(
                    'data' => '{"' . $order['id'] . '":' . $order['nums'] . '}',
                    'landscape_id' => $order['landscape_ids'],
                    'uid' => 1026,
                    'or_id' => $order['supplier_id'],
                    'order_id' => $order['id'],
                    'use_day'=> $order['use_day'],
                );
                file_put_contents($this->filepath, json_encode($vparam) . "\n", FILE_APPEND);


                if (0 == ($i + 1) % 100) {
                    echo "\nGen Order: " . ($i + 1) . " , Use Time:" . (time() - $start) . " sec\n";
                }
                usleep(20);
                //if($orderNum>0) break;
            }
            $tasks[$tk]= $task;

            //if($orderNum>0) break;
        }
        echo "\nGen Order: ".$orderNum."\n\n";
        file_put_contents($this->taskFile,"<?php return ".var_export($tasks,true).";?>");
    }

    public function useOrder(){
        if(!file_exists($this->filepath)) {
            echo "Order FIle [ {$this->filepath} ] not found!";
        }
        $vOrders = file($this->filepath);
        $failedOrders = array();
        $i=0;
        foreach($vOrders as $v) {
            $v = trim($v);
            if($v){
                $vo = json_decode($v,true);
                $r = $this->verify($vo);
                if(!$r || ($r['code']!='succ' && $r['message']!='没有可核销的门票')){
                    echo "\nUsing Order [{$vo['order_id']}] failed! Had Used Order: ".($i+1)."\n";
                    array_push($failedOrders,$v);
                }
                else {
                    $i++;
                    echo "\nUse [{$vo['order_id']}] successfully!";
                }
            }
        }
        file_put_contents($this->filepath,'');
        if($failedOrders) {
            file_put_contents($this->filepath,implode("\n",$failedOrders));
        }
        echo "\nHad Used Order: ".$i." ".($failedOrders? "Have ".count($failedOrders)." Orders Using failed":"")."\n\n";
    }

}

$test = new Crontab_BatGenOrder;
