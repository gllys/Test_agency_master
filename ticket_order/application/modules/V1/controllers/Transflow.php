<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/11/20
 * Time: 14:31
 */

class TransflowController extends  Base_Controller_Api
{
    /**
     * 列表
     * author : yinjian
     */
    public function listAction()
    {
//        !Validate::isUnsignedId($this->body['supplier_id']) && Lang_Msg::error('供应商id缺失');
        $where = array();
        $where['deleted_at'] = 0;
        if(isset($this->body['agency_id'])) $where['agency_id'] = intval($this->body['agency_id']);
        if(isset($this->body['supplier_id'])) $where['supplier_id'] = intval($this->body['supplier_id']);
        if(isset($this->body['mode']) && $this->body['mode']) $where['mode'] = trim($this->body['mode']);
        if(isset($this->body['type']) && $this->body['type']) $where['type|in'] = explode(',',trim($this->body['type']));
        if(isset($this->body['time']) && $this->body['time']){
            $time = explode(' - ',$this->body['time']);
            $start_time = strtotime(reset($time).' 00:00:00');
            $end_time = strtotime(end($time).' 23:59:59');
            if(strpos($this->body['time'],' - ')===0){
                $start_time = 0;
            }elseif(strlen($this->body['time']) === 13 && strpos($this->body['time'],' - ')===10){
                $end_time = strtotime(date('Y-m-d').' 23:59:59');
            }
            $where['created_at|between'] = array($start_time,$end_time);
        }
        if(isset($this->body['id']) && $this->body['id']) $where['id'] = trim($this->body['id']);
        $this->count = TransactionFlowModel::model()->countResult($where);
        $this->pagenation();
        $data['data'] = TransactionFlowModel::model()->search($where,'*',$this->getSortRule('created_at desc,id'),$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 添加流水
     * author : yinjian
     */
    public function addAction()
    {
        !in_array($this->body['mode'],array(
            'cash','offline','credit','pos',
            'alipay','advance','union',
            'kuaiqian','taobao')) && Lang_Msg::error('交易方式不正确');
        !in_array($this->body['type'],array(1,2,3,4)) && Lang_Msg::error('交易类型不正确');
        !Validate::isFloat($this->body['amount']) && Lang_Msg::error('支付金额不正确');
        !Validate::isUnsignedId(intval($this->body['supplier_id'])) && Lang_Msg::error('供应商id缺失');
        !Validate::isUnsignedId(intval($this->body['agency_id'])) && Lang_Msg::error('分销商id缺失');
        !Validate::isUnsignedId($this->body['op_id']) && Lang_Msg::error('用户id缺失');
        $res = TransactionFlowModel::model()->add(array(
            'id'=>Util_Common::payid(),
            'mode'=>trim($this->body['mode']),
            'type'=>intval($this->body['type']),
            'amount'=>floatval($this->body['amount']),
            'supplier_id'=>intval($this->body['supplier_id']),
            'agency_id'=>intval($this->body['agency_id']),
            'ip'=>Tools::getIp(),
            'op_id'=>intval($this->body['op_id']),
            'created_at'=>time(),
            'order_id'=>trim($this->body['order_id'])?trim($this->body['order_id']):0,
            'user_name'=>isset($this->body['user_name'])?$this->body['user_name']:'',
            'balance'=>floatval($this->body['balance']),
            'remark'=>isset($this->body['remark'])?$this->body['remark']:'',
        ));
        !$res && Lang_Msg::error('添加失败');
        Lang_Msg::output();
    }
}