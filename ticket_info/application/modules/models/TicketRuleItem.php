<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-3
 * Time: 下午16:55
 */

class TicketRuleItemModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_rule_items';
    protected $pkKey = '';
    protected $preCacheKey = 'cache|TicketRuleItemModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addList($data,$days){
        $values = array();
        foreach($days as $v){
            $values[] = "({$data['rule_id']},'{$v}','{$data['fat_price']}','{$data['group_price']}',{$data['reserve']})";
        }
        $values = implode(',',$values);
        $sql = "REPLACE INTO ".$this->getTable()." (rule_id,date,fat_price,group_price,reserve) VALUES {$values}";
        return $this->exec($sql);
    }

}
