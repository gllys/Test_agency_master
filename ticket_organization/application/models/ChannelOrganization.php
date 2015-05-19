<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/19
 * Time: 15:10
 */
class ChannelOrganizationModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'channel_organization';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|ChannelOrganizationModel|';

    public function getTable()
    {
        return $this->tblname;
    }
}