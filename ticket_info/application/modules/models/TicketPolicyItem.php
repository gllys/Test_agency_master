<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-30
 * Time: 下午3:59
 */

class TicketPolicyItemModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_policy_items';
    protected $pkKey = '';
    protected $preCacheKey = 'cache|TicketPolicyItemModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addList($policy_id,$items){
        $this->delete(array('policy_id'=>$policy_id));
        array_unshift($items,array_keys($items[0]));
        return $this->add($items);
    }

}
