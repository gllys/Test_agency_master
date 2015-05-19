<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-25
 * Time: 上午11:30
 */


class Log_Landscape extends Log_Base
{
    protected $tblname = 'log_landscape';
    public static $type = array('CREATE'=>1,'UPDATE'=>2,'DEL'=>3);

    public function getTable() {
        return $this->tblname;
    }

    public function add($data) {
        $item = array();
        $item['type'] = intval($data['type']);
        $item['num'] = is_numeric($data['num']) ? $data['num'] : intval($data['num']);
        $item['poi_ids'] = $data['poi_ids']?$data['poi_ids']:'';
        $item['content'] = $data['content'];
        $this->write($item);
    }
}
