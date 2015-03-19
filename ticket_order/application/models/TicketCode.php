<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-28
 * Time: 上午11:38
 */



class TicketCodeModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_code';
    protected $basename = 'ticket_code';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketCodeModel|';
    protected $autoShare = 0;

    public function getTable() {
        return $this->tblname;
    }

    //创建组合票
    public function addNew($params){
        $nowTime = time();
        $data = array();
        $data['distributor_id'] = $params['distributor_id'];
        $data['ticket_template_ids'] = $params['ticket_template_ids'];
        $data['op_id'] = $params['user_id'];
        $data['created_at'] = $nowTime;
        $data['updated_at'] = $nowTime;
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return  $r ? $data :false;
    }

    public function decodeTicketCode($t_code){
        $isGroup = substr($t_code,0,1)=="G"?1:0;
        $ticket_group_id = $isGroup ? intval(substr($t_code,1)) : intval($t_code);
        $info = $this->getById($ticket_group_id);
        return $info;
    }

    public function listByIds($ids){
        if(!$ids) return false;
        !is_array($ids) && $ids = explode(',',$ids);
        foreach($ids as $k=>$id){
            if(substr($id,0,1)=="G")
                $ids[$k] = intval(substr($id,1));
            else
                $ids[$k] = intval($id);
        }
        return $this->getByIds($ids);
    }


}