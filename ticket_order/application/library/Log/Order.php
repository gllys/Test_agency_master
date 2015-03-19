<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-14
 * Time: 下午12:13
 */

class Log_Order extends Log_Base
{
    protected $tblname = 'log_order';
    public static $type = array('CREATE'=>1,'UPDATE'=>2,'DEL'=>3);

    public function getTable() {
        return $this->tblname;
    }

    public function add($data) {
        $item = array();
        $item['type'] = intval($data['type']);
        $item['num'] = is_numeric($data['num']) ? $data['num'] : intval($data['num']);
        $item['order_ids'] = $data['order_ids'];
        $item['content'] = $data['content'];
        $item['distributor_id'] = isset($data['distributor_id'])?intval($data['distributor_id']):0;
        $this->write($item);
    }
}
