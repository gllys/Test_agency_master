<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-20
 * Time: 下午7:44
 */

class TicketDayPriceModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_tpl_day_price';
    protected $pkKey = '';
    protected $preCacheKey = 'cache|TicketDayPriceModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addList($data,$days){
        $values = array();
        foreach($days as $v){
            $values[] = "({$data['ticket_template_id']},'{$v}',{$data['fat_price']},{$data['group_price']},{$data['setting_at']},{$data['setting_by']})";
        }
        $values = implode(',',$values);
        $sql = "REPLACE INTO ".$this->getTable()." (ticket_template_id,date,fat_price,group_price,setting_at,setting_by) VALUES {$values}";
        return $this->exec($sql);
    }
}
