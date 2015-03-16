<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-19
 * Time: 上午11:59
 */

class TicketTypeModel extends Base_Model_Abstract
{
    protected $dbname = '';
    protected $tblname = '';
    protected $pkKey = '';
    protected $preCacheKey = '';

    protected $types = array(
        1=>'成人票',
        2=>'儿童票',
        3=>'老人票',
        4=>'团队票',
        5=>'学生票',
    );

    public function getAll() {
        return $this->types;
    }

    public function getById($id){
        return empty($this->types[$id])?'':$this->types[$id];
    }

}
