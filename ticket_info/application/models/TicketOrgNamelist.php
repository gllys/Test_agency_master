<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-10
 * Time: 下午2:54
 */


class TicketOrgNamelistModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_org_namelist';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketOrgNamelistModel|';

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