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

    public function getInfo($ticket_template_id,$price_type=0,$distributor_id=0,$use_day='',$nums=1){
        //$tktInfoParams = array('or_id'=>$params['supplier_id'],'ticket_id'=>$params['ticket_template_id']);
        if(!$ticket_template_id)
            return false;
        $this->method = 'GET';
        $this->params = array('ticket_id'=>$ticket_template_id,'distributor_id'=>$distributor_id,'type'=>$price_type,'use_day'=>$use_day);
        $ticketTemplateInfo = json_decode($this->request(),true);
        if(!$ticketTemplateInfo || empty($ticketTemplateInfo['body']))
            return $ticketTemplateInfo;
        $ticketTemplateInfo = $ticketTemplateInfo['body'];
        $price = $this->getPrice($ticketTemplateInfo,$price_type, $use_day,$nums);
        $ticketTemplateInfo['price_type'] = $price['price_type'];
        $ticketTemplateInfo['price'] = $price['price'];
        $ticketTemplateInfo['use_day'] = $use_day;
        $ticketTemplateInfo['nums'] = $nums;
        return $ticketTemplateInfo;
    }
	
    
    //
    private function getPrice($ticketTemplateInfo,$price_type=0,$use_day='',$nums=1) {
        if(!in_array($ticketTemplateInfo['ota_type'],array("weixin"))){
            !$ticketTemplateInfo['can_play'] && Lang_Msg::error('ERROR_TK_USE_DAY',array('use_day'=>$use_day,'ticket_name'=>$ticketTemplateInfo['name']));
        }
        !$ticketTemplateInfo['can_buy'] && Lang_Msg::error('ERROR_NO_BUY_RIGHT',array('ticket_name'=>$ticketTemplateInfo['name']));

        $price = array(); //价格类型：0散客1团客
        $price['price_type'] = $price_type;
        $price['price'] =  $ticketTemplateInfo[($price_type==0?'fat_price':'group_price')];
        //是否有游玩日期的日价格，判断游玩日期日库存是否足够
        if(!empty($ticketTemplateInfo['day_reserve']) && $nums>$ticketTemplateInfo['remain_reserve']){
            Tools::lsJson(false,"订单预订的票数[{$nums}]超过了门票【{$ticketTemplateInfo['name']}】在[{$use_day}]的剩余日库存[{$ticketTemplateInfo['remain_reserve']}]");
        }
        if($price_type==0 && isset($ticketTemplateInfo['fat_discount']) && 0!=$ticketTemplateInfo['fat_discount']){
            $price['price'] += $ticketTemplateInfo['fat_discount'];
        }
        else if($price_type==1 && isset($ticketTemplateInfo['group_discount']) && 0!=$ticketTemplateInfo['group_discount']){
            $price['price'] += $ticketTemplateInfo['group_discount'];
        }

        if($price_type==0 && isset($ticketTemplateInfo['day_fat_price']) && 0!=$ticketTemplateInfo['day_fat_price']){
            $price['price'] += $ticketTemplateInfo['day_fat_price'];
        }
        else if($price_type==1 && isset($ticketTemplateInfo['day_group_price']) && 0!=$ticketTemplateInfo['day_group_price']){
            $price['price'] += $ticketTemplateInfo['day_group_price'];
        }
        $price['price']<0 && $price['price']=0;
        return $price;
    }
	
    public function getTicketInfo( $id )
    {
    	 $this->params = array('ticket_id'=>$id);
         $this->method = 'GET';
       	 $ticketTemplateInfo = json_decode($this->request(),true);
       	 return $ticketTemplateInfo[ 'body'];
    }

    //更改票日库存已用数
    public function updateTicketDayUsedReserve($ticket_id,$rule_id,$date,$nums,$is_refund=0){
        if(!$rule_id && !$date && !$nums)
            return false;
        $this->method = 'POST';
        $this->params = array('ticket_id'=>$ticket_id,'rule_id'=>$rule_id,'date'=>$date,'nums'=>$nums,'is_refund'=>$is_refund);
        $this->url = '/v1/Ticketrule/chgusedreserve';
        $ticketTemplateInfo = json_decode($this->request(),true);
        if(!$ticketTemplateInfo || empty($ticketTemplateInfo['body']))
            return false;
        else
            return true;
    }

    //按订单号批量更改票日库存已用数
    public function batUpTktDayUsedReserve($order_ids=array()) {
        if(!$order_ids) return true;
        $ticketNums = array();
        $orderItems = OrderItemModel::model()->setTable($order_ids[0])->search(array('order_id|IN'=>$order_ids));
        if(!$orderItems) return false;
        foreach($orderItems as $v){
            $distributor_id = $v['distributor_id'];
            $key = $v['ticket_template_id']."_".$v['use_day'];
            !isset($ticketNums[$key]) && $ticketNums[$key] = array(
                    'ticket_template_id'=>$v['ticket_template_id'],'price_type'=>$v['price_type'],'use_day'=>$v['use_day'],'nums'=>0,'ticket_name'=>$v['name'],
            );
            $ticketNums[$key]['nums'] += $v['nums'];
        }
        if(!$distributor_id ) return false;
        foreach ($ticketNums as $v) {
            $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($v['ticket_template_id'],$v['price_type'],$distributor_id,$v['use_day'],$v['nums']);
            !$ticketTemplateInfo  && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['ticket_template_id']:$v['ticket_name']));
            isset($ticketTemplateInfo['code']) && $ticketTemplateInfo['code']=='fail' && Lang_Msg::error($ticketTemplateInfo['message']);

            if(isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve']>0){
                $r=$this->updateTicketDayUsedReserve($v['ticket_template_id'],$ticketTemplateInfo['rule_id'],$v['use_day'],$v['nums']);
                if(!$r){
                    return false;
                }
            }
        }

        return true;
    }

    public function chkIsAblePay($order_ids=array()){
        if(!$order_ids) return true;
        $ticketNums = array();
        $orderItems = OrderItemModel::model()->setTable($order_ids[0])->search(array('order_id|IN'=>$order_ids));
        if(!$orderItems) return false;
        foreach($orderItems as $v){
            $distributor_id = $v['distributor_id'];
            $key = $v['ticket_template_id']."_".$v['use_day'];
            !isset($ticketNums[$key]) && $ticketNums[$key] = array(
                'ticket_template_id'=>$v['ticket_template_id'],'price_type'=>$v['price_type'],'use_day'=>$v['use_day'],'nums'=>0,'ticket_name'=>$v['name'],
            );
            $ticketNums[$key]['nums'] += $v['nums'];
        }
        if(!$distributor_id ) return false;
        foreach ($ticketNums as $v) {
            $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($v['ticket_template_id'],$v['price_type'],$distributor_id,$v['use_day'],$v['nums']);
            !$ticketTemplateInfo  && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['ticket_template_id']:$v['ticket_name']));
            isset($ticketTemplateInfo['code']) && $ticketTemplateInfo['code']=='fail' && Lang_Msg::error($ticketTemplateInfo['message']);
        }
        return true;
    }

}