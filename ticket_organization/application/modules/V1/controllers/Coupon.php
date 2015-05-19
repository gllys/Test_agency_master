<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/19
 * Time: 17:29
 */
class CouponController extends Base_Controller_Api
{
    /**
     *
     * author : yinjian
     */
    public function addAction()
    {
        !Validate::isString($this->body['title']) && Lang_Msg::error('主题不能为空');
        !Validate::isTimestamp($this->body['start_time']) && Lang_Msg::error('有效期开始时间不正确');
        !Validate::isTimestamp($this->body['end_time']) && Lang_Msg::error('有效期结束时间不正确');
        !Validate::isUnsignedFloat($this->body['num']) && Lang_Msg::error('充值额度不正确');
        !Validate::isUnsignedFloat($this->body['coupon']) && Lang_Msg::error('赠送礼券不正确');
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户不能为空');
        !in_array($this->body['status'],array(0,1)) && Lang_Msg::error('是否开启未设置');
        $res = ActivityChargeModel::model()->add(array(
            'title' => trim($this->body['title']),
            'num' => floatval($this->body['num']),
            'coupon' => floatval($this->body['coupon']),
            'start_time' => intval($this->body['start_time']),
            'end_time' => intval($this->body['end_time']),
            'status' => intval($this->body['status']),
            'created_by' => intval($this->body['uid']),
            'created_at' => time(),
            'updated_at' => time(),
        ));
        !$res && Lang_Msg::error('添加失败');
        Lang_Msg::output();
    }

    /**
     *
     * author : yinjian
     */
    public function listsAction()
    {
        $where = array('deleted_at'=>0);
        if(isset($this->body['id']) && $this->body['id']) $where['id|in'] = explode(',',$this->body['id']);
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $where['status'] = intval($this->body['status']);
        if(isset($this->body['can_use']) && $this->body['can_use']) {
            $where['start_time|<='] = time();
            $where['end_time|>='] = time();
        }
        // 分页
        $count = reset(ActivityChargeModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        $data['data'] = ActivityChargeModel::model()->search($where, '*', 'status desc,created_at desc', $this->limit);
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     *
     * author : yinjian
     */
    public function updateAction()
    {
        !Validate::isUnsignedId($this->body['id']) && Lang_Msg::error('id不存在');
        $active_charge =  reset(ActivityChargeModel::model()->search(array('id'=>intval($this->body['id']),'deleted_at'=>0)));
        !$active_charge && Lang_Msg::error('优惠方案不存在');
        if(isset($this->body['title']) && Validate::isString($this->body['title'])) $data['title'] = trim($this->body['title']);
        if(isset($this->body['num']) && Validate::isUnsignedFloat($this->body['num'])) $data['num'] = trim($this->body['num']);
        if(isset($this->body['coupon']) && Validate::isUnsignedFloat($this->body['coupon'])) $data['coupon'] = trim($this->body['coupon']);
        if(isset($this->body['start_time']) && Validate::isTimestamp($this->body['start_time'])) $data['start_time'] = trim($this->body['start_time']);
        if(isset($this->body['end_time']) && Validate::isTimestamp($this->body['end_time'])) $data['end_time'] = trim($this->body['end_time']);
        if(isset($this->body['deleted_at']) && $this->body['deleted_at']) $data['deleted_at'] = time();
        $data['updated_at'] = time();
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $data['status'] = intval($this->body['status']);
        if(isset($this->body['deleted_at'])) $data['deleted_at'] = $data['updated_at'];
        $res = ActivityChargeModel::model()->updateByAttr($data,array('id'=>$active_charge['id']));
        !$res && Lang_Msg::error('更新失败');
        Lang_Msg::output();
    }

    /**
     * 充值记录
     * author : yinjian
     */
    public function historyAction()
    {
        $where = array('paid_at|>'=>0);
        if(isset($this->body['organization_id'])) $where['organization_id|in'] = explode(',',$this->body['organization_id']);
        if(isset($this->body['paid_at'])) $where['paid_at|between'] = array(strtotime(reset(explode(' - ',$this->body['paid_at']))." 00:00:00"),strtotime(end(explode(' - ',$this->body['paid_at']))." 23:59:59"));
        if(isset($this->body['organization_name']) && $this->body['organization_name']) $where['organization_name|like'] = array('%'.trim($this->body['organization_name']).'%');
        // 分页
        $count = reset(ActivityChargeLogModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        $data['data'] = ActivityChargeLogModel::model()->search($where, '*', 'created_at desc', $this->limit);
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true, 'ok', $data);
    }
}