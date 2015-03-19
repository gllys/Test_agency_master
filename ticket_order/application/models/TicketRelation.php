<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-24
 * Time: 下午6:15
 */


class TicketRelationModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_relations';
    protected $basename = 'ticket_relations';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketRelationModel|';
    protected $autoShare = 1;

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        if (!$id) $this->tblname = $this->basename . date('Ym');
        else  $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        if (!$ts) $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //生成景点关联记录，对单个票的订单
    public function addNew($orderInfo,$ticketTemplateInfo,$ticket_ids){
        $this->setTable($orderInfo['id']);
        $values = array();
        foreach($ticket_ids as $ticket_id){
            $view_point = explode(',',$ticketTemplateInfo['view_point']);
            foreach($view_point as $poi_id){
                $id = Util_Common::uniqid(2);
                $values[] = "('".$id."','{$ticket_id}','{$orderInfo['id']}','{$ticketTemplateInfo['id']}','{$poi_id}','{$ticketTemplateInfo['scenic_id']}','{$orderInfo['created_at']}','{$orderInfo['updated_at']}')";
            }
        }
        $values = implode(',',$values);
        $sql = "INSERT INTO ".$this->getTable()." (id,ticket_id,order_id,ticket_template_id,poi_id,landscape_id,created_at,updated_at) VALUES {$values}";
        return $this->exec($sql);
    }
}
