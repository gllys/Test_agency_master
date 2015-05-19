<?php

/**
 * 短信记录模型
 *
 * @Package controller
 * @Date 2015-3-10
 * @Author Joe
 */
class SmsLogModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'sms_log';
    protected $basename = 'sms_log';
    protected $pkKey = 'id';
    protected $preCacheKey = '';

    public function getTable() {
        return $this->tblname;
    }

}