<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/11/19
 * Time: 11:28
 */
class MessageModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'message';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|MessageModel|';

    public function getTable() {
        return $this->tblname;
    }
}