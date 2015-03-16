<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-3
 * Time: 下午2:37
 */

class TicketRuleModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_rule';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketRuleModel|';

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
