<?php

/**
 * Class OrderController
 */
class RefundController extends Base_Controller_Api {
    /**
     * 退款申请
     * author : yinjian
     */
    public function applyAction()
    {
        $params = array();
        $params['order_id'] = trim(Tools::safeOutput($this->body['order_id']));
        $params['nums'] = intval($this->body['nums']);
        $params['user_id'] = intval($this->body['user_id']);
        $params['user_account'] = trim(Tools::safeOutput($this->body['user_account']));
        $params['user_name'] = trim(Tools::safeOutput($this->body['user_name']));
        !$params['user_name'] && $params['user_name'] = $params['user_account'];
        $params['remark'] = trim($this->body['remark']);
        $res = RefundApplyModel::model()->applyRefund($params);
        Tools::lsJson(true,'ok',array('id'=>$res));
    }

    /**
     * 退款申请列表
     * author : yinjian
     */
    public function apply_listAction()
    {
        $where = array('is_del'=>0);
        // 交易日期
        if(Validate::isString($this->body['order_at'])){
            $order_at = explode(' - ',$this->body['order_at']);
            $start_at = intval(strtotime(reset($order_at).' 00:00:00'));
            $end_at = intval(strtotime(end($order_at).'  23:59:59'));
            ($end_at<$start_at || !Validate::isUnsignedInt($start_at) || !Validate::isUnsignedInt($end_at)) && Lang_Msg::error("ERROR_ApplyList_1");
            $where['order_at|between'] = array($start_at,$end_at);
        }
        if(Validate::isString($this->body['updated_at'])){
            $updated_at = explode(' - ',$this->body['updated_at']);
            $start_at = intval(strtotime(reset($updated_at).' 00:00:00'));
            $end_at = intval(strtotime(end($updated_at).'  23:59:59'));
            ($end_at<$start_at || !Validate::isUnsignedInt($start_at) || !Validate::isUnsignedInt($end_at)) && Lang_Msg::error("ERROR_ApplyList_1");
            $where['updated_at|between'] = array($start_at,$end_at);
        }
        if(Validate::isString($this->body['created_at'])){
            $created_at = explode(' - ',$this->body['created_at']);
            $start_at = intval(strtotime(reset($created_at).' 00:00:00'));
            $end_at = intval(strtotime(end($created_at).'  23:59:59'));
            ($end_at<$start_at || !Validate::isUnsignedInt($start_at) || !Validate::isUnsignedInt($end_at)) && Lang_Msg::error("ERROR_ApplyList_1");
            $where['created_at|between'] = array($start_at,$end_at);
        }
        // id搜索
        if(isset($this->body['id']) && Validate::isString($this->body['id'])) $where['id|in'] = explode(',',$this->body['id']);
        // 支付方式
        if(Validate::isString($this->body['pay_app_id'])) $where['pay_app_id'] = $this->body['pay_app_id'];
        // 审核状态
        if(isset($this->body['allow_status']) && in_array(intval($this->body['allow_status']),array(0,1,2,3))) $where['allow_status'] = intval($this->body['allow_status']);
        // 退款状态
        if(isset($this->body['status']) && in_array(intval($this->body['status']),array(0,1,2))) $where['status'] = intval($this->body['status']);
        // 订单号，ids
        if(Validate::isString($this->body['order_id'])) $where['order_id|in'] = explode(',',$this->body['order_id']);
        // 分销商
        if(isset($this->body['distributor_id']) && Validate::isString($this->body['distributor_id'])) $where['distributor_id'] = intval($this->body['distributor_id']);
        // 供应商
        if(isset($this->body['supplier_id']) && Validate::isString($this->body['supplier_id'])) $where['supplier_id'] = intval($this->body['supplier_id']);
        // 景区
        if(isset($this->body['landscape_id']) && Validate::isString($this->body['landscape_id'])) $where['landscape_id'] = intval($this->body['landscape_id']);
        $refundApplyModel = new RefundApplyModel();
        $this->count = $refundApplyModel->countResult($where);
        $this->pagenation();
        $data['data'] = $refundApplyModel->search($where,'*',$this->getSortRule(),$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 审核退款
     * author : yinjian
     */
    public function check_refundAction()
    {
        $params = array();
        $params['id'] = trim(Tools::safeOutput($this->body['id']));
        $params['allow_status'] = intval($this->body['allow_status']);
        $params['user_id'] = intval($this->body['user_id']);
        $params['user_account'] = trim(Tools::safeOutput($this->body['user_account']));
        $params['user_name'] = trim(Tools::safeOutput($this->body['user_name']));
        !$params['user_name'] && $params['user_name'] = $params['user_account'];
        $params['reject_reason'] = $this->body['reject_reason'];
        $refundApplyModel = new RefundApplyModel();
        $res = $refundApplyModel->checkApply($params);
        Tools::lsJson($res);
    }
}