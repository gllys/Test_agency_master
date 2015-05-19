<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-17
 * Time: 上午9:41
 */

class LandscapeImageModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'landscape_images';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LandscapeImageModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data){
        $data['created_at'] = date("Y-m-d H:i:s");
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return $r ? $data : false ;
    }

}
