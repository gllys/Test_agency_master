<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-24
 * Time: 下午18:06
 */

class Log_Payment extends Log_Base
{
    protected $tblname = 'log_payment';
    public static $type = array('CREATE'=>1,'UPDATE'=>2,'DEL'=>3);

    public function getTable() {
        return $this->tblname;
    }

    public function add($data) {
        $item = array();
        $item['type'] = intval($data['type']);
        $item['num'] = is_numeric($data['num']) ? $data['num'] : intval($data['num']);
        $item['payment_id'] = intval($data['payment_id']);
        $item['order_ids'] = $data['order_ids'];
        $item['content'] = $data['content'];
        $item['distributor_id'] = intval($data['distributor_id']);
        $this->write($item);
    }
}
