<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-6
 * Time: 上午10:03
 */

class TicketUsedModel  extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_used';
    protected $basename = 'ticket_used';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|ticketUsedModel|';
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
}