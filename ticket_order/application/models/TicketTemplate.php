<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-24
 * Time: 上午9:55
 */

class TicketTemplateModel extends Base_Model_Api{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/TicketTemplate/ticketinfo';
    protected $method = 'POST';

    /**
     * 获取产品详情，包含日价格、库存、销售策略信息
     * @param $openMsg 是否弹出错误信息
     * @param $notOrder int 1不是下单操作，0是下单操作
     * */
    public function getInfo($product_id,$price_type=0,$distributor_id=0,$use_day='',$nums=1,$openMsg=1,$notOrder=0){
        //$tktInfoParams = array('or_id'=>$params['supplier_id'],'ticket_id'=>$params['product_id']);
        if(!$product_id)
            return false;
        $this->method = 'POST';
        $this->params = array('ticket_id'=>$product_id,'distributor_id'=>$distributor_id,'type'=>$price_type,'use_day'=>$use_day,'not_order'=>$notOrder);
        $productInfo = json_decode($this->request(null,10),true);
        if(!$productInfo || empty($productInfo['body']))
            return $productInfo;
        $productInfo = $productInfo['body'];
        $now = time();
        if(($productInfo['sale_start_time'] && $now < $productInfo['sale_start_time']) || ($productInfo[ 'sale_end_time'] && $now > $productInfo['sale_end_time'])) {
            $openMsg && Tools::lsJson(false,'门票［'.$productInfo['name'].'］已下架');
        }

        if(!in_array($productInfo['ota_type'],array("weixin"))){
            $openMsg && !$productInfo['can_play'] && Lang_Msg::error('ERROR_TK_USE_DAY',array('use_day'=>$use_day,'ticket_name'=>$productInfo['name']));
        }
        $openMsg && !$productInfo['can_buy'] && Lang_Msg::error('ERROR_NO_BUY_RIGHT',array('ticket_name'=>$productInfo['name']));

        //是否有游玩日期的日价格，判断游玩日期日库存是否足够
        if(!empty($productInfo['day_reserve']) && $nums>$productInfo['remain_reserve']){
            $openMsg && Tools::lsJson(false,"订单预订的票数[{$nums}]超过了门票【{$productInfo['name']}】在[{$use_day}]的剩余日库存[{$productInfo['remain_reserve']}]");
        }

        $productInfo['price_type'] = $price_type;
        $productInfo['use_day'] = $use_day;
        $productInfo['nums'] = $nums;
        return $productInfo;
    }
	
    public function getTicketInfo( $id ,$is_del=0)
    {
    	 $this->params = array('ticket_id'=>$id,'is_del'=>$is_del);
         $this->method = 'GET';
       	 $productInfo = json_decode($this->request(),true);
       	 return $productInfo[ 'body'];
    }

    //更改票日库存已用数
    public function updateTicketDayUsedReserve($ticket_id,$rule_id,$date,$nums,$is_refund=0){
        if(!$rule_id && !$date && !$nums)
            return false;
        $this->method = 'POST';
        $this->params = array('ticket_id'=>$ticket_id,'rule_id'=>$rule_id,'date'=>$date,'nums'=>$nums,'is_refund'=>$is_refund);
        $this->url = '/v1/Ticketrule/chgusedreserve';
        $productInfo = json_decode($this->request(),true);
        if(!$productInfo || empty($productInfo['body']))
            return false;
        else
            return true;
    }

    //按订单号批量更改票日库存已用数
    public function batUpTktDayUsedReserve($order_ids=array(),$prodInfo=array()) {
        if(!$order_ids) return true;
        $ticketNums = array();
        $orders = OrderModel::model()->setTable($order_ids[0])->search(array('id|IN'=>$order_ids));
        if(!$orders) return false;
        foreach($orders as $v){
            $distributor_id = $v['distributor_id'];
            $key = $v['product_id']."_".$v['use_day'];
            !isset($ticketNums[$key]) && $ticketNums[$key] = array(
                    'product_id'=>$v['product_id'],'price_type'=>$v['price_type'],'use_day'=>$v['use_day'],'nums'=>0,'ticket_name'=>$v['name'],
            );
            $ticketNums[$key]['nums'] += $v['nums'];
        }
        if(!$distributor_id ) return false;
        foreach ($ticketNums as $v) {
            if($prodInfo && $prodInfo['id']==$v['product_id']){
                $productInfo = $prodInfo;
            }
            else{
                $productInfo = TicketTemplateModel::model()->getInfo($v['product_id'],$v['price_type'],$distributor_id,$v['use_day'],$v['nums'],1,1);
            }
            !$productInfo  && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['product_id']:$v['ticket_name']));
            isset($productInfo['code']) && $productInfo['code']=='fail' && Lang_Msg::error($productInfo['message']);

            if(isset($productInfo['day_reserve']) && $productInfo['day_reserve']>0){
                $r=$this->updateTicketDayUsedReserve($v['product_id'],$productInfo['rule_id'],$v['use_day'],$v['nums']);
                if(!$r){
                    return false;
                }
            }
        }

        return true;
    }

    public function chkIsAblePay($order_ids=array(),$prodInfo=array()){
        if(!$order_ids) return true;
        $ticketNums = array();
        $orders = OrderModel::model()->setTable($order_ids[0])->search(array('id|IN'=>$order_ids));
        if(!$orders) return false;
        foreach($orders as $v){
            $distributor_id = $v['distributor_id'];
            $key = $v['product_id']."_".$v['use_day'];
            !isset($ticketNums[$key]) && $ticketNums[$key] = array(
                'product_id'=>$v['product_id'],'price_type'=>$v['price_type'],'use_day'=>$v['use_day'],'nums'=>0,'ticket_name'=>$v['name'],
            );
            $ticketNums[$key]['nums'] += $v['nums'];
        }
        if(!$distributor_id ) return false;
        foreach ($ticketNums as $v) {
            if($prodInfo && $prodInfo['id']==$v['product_id']){
                $productInfo = $prodInfo;
            }
            else{
                $productInfo = TicketTemplateModel::model()->getInfo($v['product_id'],$v['price_type'],$distributor_id,$v['use_day'],$v['nums'],1,1);
            }
            !$productInfo  && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['product_id']:$v['ticket_name']));
            isset($productInfo['code']) && $productInfo['code']=='fail' && Lang_Msg::error($productInfo['message']);
        }
        return true;
    }

}