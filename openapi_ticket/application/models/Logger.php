<?php

/**
 * Description of LoggerModel
 *
 * @author wfdx1_000
 */
class LoggerModel extends Base_Model_Abstract {
    
    protected $dbname = 'log';
    protected $tblname = 'log_common';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LoggerModel|';
    
    public static function write2Db($params) {
        $module = $params['module'];

        unset($params['module']);
        
        $params['created_date'] = date('Y-m-d H:i:s',$params['created_date']);
//        $params['comment'] = $params['comment'];
//        $params['params'] = $params['params'];

        $model = self::model();
        $model->setAutoShare(false);
        
        $model->setTable("log_{$module}");
        $model->add($params);
        
        print_r($params);
    }
    
    public function setAutoShare($val = 1) {
        parent::setAutoShare($val);
    }
    
    public function setTable($table = 0) {
        $this->tblname = $table;
        return parent::setTable(0);
    }
    
}
