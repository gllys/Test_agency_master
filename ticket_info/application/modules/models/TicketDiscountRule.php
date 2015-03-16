<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-10
 * Time: 下午4:56
 * 优惠规则
 */


class TicketDiscountRuleModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_discount_rule';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketDiscountRuleModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data){
        $nowTime = time();
        $data['created_at'] = $nowTime;
        $data['updated_at'] = $nowTime;
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return $r ? $data : false ;
    }

}