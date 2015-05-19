<?php
/**
 * Created by PhpStorm.
 * User: liuyong
 * Date: 14-10-21
 * Time: 上午11:11
 */


class TicketDayReserveModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_tpl_day_reserve';
    protected $pkKey = '';
    protected $preCacheKey = 'cache|TicketDayReserveModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addList($data,$days){
        $values = array();
        foreach($days as $v){
            $values[] = "({$data['ticket_template_id']},'{$v}',{$data['reserve']},{$data['setting_by']},{$data['setting_at']})";
        }
        $values = implode(',',$values);
        $sql = "REPLACE INTO ".$this->getTable()." (ticket_template_id,date,reserve,setting_by,setting_at) VALUES {$values}";
        return $this->exec($sql);
    }
}
