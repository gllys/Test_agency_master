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
        if(isset($this->body['notify_url'])) $params['notify_url'] = trim($this->body['notify_url']);
        $params['remark'] = trim($this->body['remark']);
        $res = RefundApplyModel::model()->applyRefund($params);
        $order = reset(OrderModel::model()->search(array('id'=>$params['order_id'])));
        MessageModel::model()->addMessage($params,$res,$order);
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
        // 订单号搜索
        if(isset($this->body['order_id']) && Validate::isString($this->body['order_id'])) $where['order_id|in'] = explode(',',$this->body['order_id']);
        // 门票名称
        if(isset($this->body['name']) && Validate::isString($this->body['name'])) $where['name|like'] = array('%'.$this->body['name'].'%');
        // 支付方式
        if(Validate::isString($this->body['pay_app_id'])) $where['pay_app_id'] = $this->body['pay_app_id'];
        // 审核状态
        $allow_status = trim(Tools::safeOutput($this->body['allow_status'])); //多个逗号分割
        $allow_status = Util_Common::intersectExplode($allow_status,array(0,1,2,3));
        if($allow_status){
            $where['allow_status|in'] = $allow_status;
        }
        // 退款状态
        $status = trim(Tools::safeOutput($this->body['status'])); //多个逗号分割
        $status = Util_Common::intersectExplode($status,array(0,1,2));
        if($status){
            $where['status|in'] = $status;
        }
        // 订单号，ids
        if(Validate::isString($this->body['order_id'])) $where['order_id|in'] = explode(',',$this->body['order_id']);
        // 分销商
        if(isset($this->body['distributor_id']) && Validate::isString($this->body['distributor_id'])) $where['distributor_id|in'] = explode(',',$this->body['distributor_id']);
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
     * 根据订单属性筛选退款单
     * author : yinjian
     */
    public function orderAction()
    {
        $from = 'FROM refund_apply a ';
        $join = 'JOIN orders b ON a.`order_id`=b.id ';
        $where = 'WHERE a.allow_status<3 ';
        // 供应商
        if (isset($this->body['supplier_id']) && $this->body['supplier_id']) {
            $where .= ' AND b.supplier_id in (' . $this->body['supplier_id'] . ') ';
        }
        // 分销
        if (isset($this->body['distributor_id']) && $this->body['distributor_id']) {
            $where .= ' AND b.distributor_id in (' . $this->body['distributor_id'] . ') ';
        }
        // 只能单景区
        if (isset($this->body['landscape_ids']) && $this->body['landscape_ids']) {
            $where .= ' AND find_in_set(' . intval($this->body['landscape_ids']) . ',b.landscape_ids)';
        }
        // 产品名称
        if (isset($this->body['name']) && $this->body['name']) {
            $where .= ' AND a.name like \'%' . $this->body['name'] . '%\' ';
        }
        // 取票人
        if (isset($this->body['owner_name']) && $this->body['owner_name']) {
            $where .= ' AND b.owner_name = \'' . trim($this->body['owner_name']) . '\' ';
        }
        // 取票电话
        if (isset($this->body['owner_mobile']) && $this->body['owner_mobile']) {
            $where .= ' AND b.owner_mobile = \'' . trim($this->body['owner_mobile']) . '\' ';
        }
        // 来源
        if (isset($this->body['source'])) {
            $where .= ' AND b.source = ' . intval($this->body['source']) . ' ';
        }
        // 冗余时间筛选，代码统一
        if(isset($this->body['time_type']) && $this->body['time_type']==0){//预定
            $created_at_start = isset($this->body['start_date'])?strtotime(trim($this->body['start_date'])):0;
            $created_at_end = isset($this->body['end_date'])?strtotime(trim($this->body['end_date']) . ' 23:59:59'):time();
            $where .= ' AND b.created_at between ' . $created_at_start . ' AND ' . $created_at_end . ' ';
        }
        if(isset($this->body['time_type']) && $this->body['time_type']==1){//游玩
            $use_day_start = isset($this->body['start_date'])?trim($this->body['start_date']):"1970-01-01";
            $use_day_end = isset($this->body['end_date'])?trim($this->body['end_date']):date("Y-m-d");
            $where .= ' AND b.use_day between \'' . $use_day_start . '\' AND \'' . $use_day_end . '\' ';
        }
        if(isset($this->body['time_type']) && $this->body['time_type']==2){//入园
            $use_time_start = isset($this->body['start_date'])?strtotime(trim($this->body['start_date'])):0;
            $use_time_end = isset($this->body['end_date'])?strtotime(trim($this->body['end_date']) . ' 23:59:59'):time();
            $where .= ' AND b.use_time between ' . $use_time_start . ' AND ' . $use_time_end . ' ';
        }
        // 时间段 旧有时间段筛选 @tips 防止前端部分地方调用
        if (isset($this->body['created_at']) && $this->body['created_at']) {
            $created_at = explode(' - ', $this->body['created_at']);
            $created_at_start = strtotime(reset($created_at));
            $created_at_end = strtotime(end($created_at) . ' 23:59:59');
            $where .= ' AND b.created_at between ' . $created_at_start . ' AND ' . $created_at_end . ' ';
        }
        if (isset($this->body['use_day']) && $this->body['use_day']) {
            $use_day = explode(' - ', $this->body['use_day']);
            $use_day_start = reset($use_day);
            $use_day_end = end($use_day);
            $where .= ' AND b.use_day between \'' . $use_day_start . '\' AND \'' . $use_day_end . '\' ';
        }
        if (isset($this->body['use_time']) && $this->body['use_time']) {
            $use_time = explode(' - ', $this->body['use_time']);
            $use_time_start = strtotime(reset($use_time));
            $use_time_end = strtotime(end($use_time) . ' 23:59:59');
            $where .= ' AND b.use_time between ' . $use_time_start . ' AND ' . $use_time_end . ' ';
        }
        // 审核状态
        if (isset($this->body['allow_status'])) {
            $where .= ' AND a.allow_status in (' . $this->body['allow_status'] . ') ';
        }
        // 使用状态 used_nums
        if (isset($this->body['use_status'])) {
            if ($this->body['use_status'] == 0) {
                // 未使用
                $where .= ' AND b.use_status = 0 ';
            } elseif ($this->body['use_status'] == 1) {
                // 已使用
                $where .= ' AND b.use_status = 1 ';
            }
        }
        $group_by = ' GROUP BY a.order_id,a.allow_status ';
        $select = 'SELECT count(*) count ';
        $count = reset(RefundApplyModel::model()->db->selectBySql('select count(*) count from ('.$select .$from . $join . $where . $group_by.') c'));
        $this->count = $count['count'];
        $this->pagenation();
        $select = 'SELECT b.*,a.allow_status,a.op_id,a.created_name ';
        $order_by = ' ORDER BY b.'.$this->getSortRule();
//        Tools::dump($select . $from . $join . $where . $group_by .$order_by. ' LIMIT ' .$this->limit);
        $data['data'] = RefundApplyModel::model()->db->selectBySql($select . $from . $join . $where . $group_by .$order_by. ' LIMIT ' .$this->limit);
        $data['statics'] = $this->statOrder();
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }

    /**
     * 统计退款单
     * author : yinjian
     */
    private function statOrder()
    {
        $where['deleted_at'] = 0;
        // 供应商
        if (isset($this->body['supplier_id']) && $this->body['supplier_id']) {
            $where['supplier_id|in'] = explode(',',$this->body['supplier_id']);
        }
        // 分销
        if (isset($this->body['distributor_id']) && $this->body['distributor_id']) {
            $where['distributor_id|in'] = explode(',',$this->body['distributor_id']);
        }
        // 只能单景区
        if (isset($this->body['landscape_ids']) && $this->body['landscape_ids']) {
            $where['find_in_set|EXP']='('.$this->body['landscape_ids'].',landscape_ids)';
        }
        // 产品名称
        if (isset($this->body['name']) && $this->body['name']) {
            $where['name|like'] = array("%".trim($this->body['name'])."%");
        }
        // 取票人
        if (isset($this->body['owner_name']) && $this->body['owner_name']) {
            $where['owner_name'] = trim($this->body['owner_name']);
        }
        // 取票电话
        if (isset($this->body['owner_mobile']) && $this->body['owner_mobile']) {
            $where['owner_mobile'] = trim($this->body['owner_mobile']);
        }
        // 来源
        if (isset($this->body['source'])) {
            $where['source'] = intval($this->body['source']);
        }
        // 冗余时间筛选，代码统一
        if(isset($this->body['time_type']) && $this->body['time_type']==0){//预定
            $created_at_start = isset($this->body['start_date'])?strtotime(trim($this->body['start_date'])):0;
            $created_at_end = isset($this->body['end_date'])?strtotime(trim($this->body['end_date']) . ' 23:59:59'):time();
            $where['created_at|BETWEEN'] = array($created_at_start,$created_at_end);
        }
        if(isset($this->body['time_type']) && $this->body['time_type']==1){//游玩
            $use_day_start = isset($this->body['start_date'])?trim($this->body['start_date']):"1970-01-01";
            $use_day_end = isset($this->body['end_date'])?trim($this->body['end_date']):date("Y-m-d");
            $where['use_day|BETWEEN'] = array($use_day_start,$use_day_end);
        }
        if(isset($this->body['time_type']) && $this->body['time_type']==2){//入园
            $use_time_start = isset($this->body['start_date'])?strtotime(trim($this->body['start_date'])):0;
            $use_time_end = isset($this->body['end_date'])?strtotime(trim($this->body['end_date']) . ' 23:59:59'):time();
            $where['use_time|BETWEEN'] = array($use_time_start,$use_time_end);
        }
        // 时间段
        if (isset($this->body['created_at']) && $this->body['created_at']) {
            $created_at = explode(' - ', $this->body['created_at']);
            $created_at_start = strtotime(reset($created_at));
            $created_at_end = strtotime(end($created_at) . ' 23:59:59');
            $where['created_at|BETWEEN'] = array($created_at_start,$created_at_end);
        }
        if (isset($this->body['use_day']) && $this->body['use_day']) {
            $use_day = explode(' - ', $this->body['use_day']);
            $use_day_start = reset($use_day);
            $use_day_end = end($use_day);
            $where['use_day|BETWEEN'] = array($use_day_start,$use_day_end);
        }
        if (isset($this->body['use_time']) && $this->body['use_time']) {
            $use_time = explode(' - ', $this->body['use_time']);
            $use_time_start = strtotime(reset($use_time));
            $use_time_end = strtotime(end($use_time) . ' 23:59:59');
            $where['use_time|BETWEEN'] = array($use_time_start,$use_time_end);
        }
        $where['or'] = array('refunding_nums|>'=>0,'refunded_nums|>'=>0);
        // 审核状态
        if (isset($this->body['allow_status'])) {
            unset($where['or']);
            if($this->body['allow_status']==0){
                // 退款中
                $where['refunding_nums|>'] = 0;
            }elseif($this->body['allow_status']==1){
                // 已审核
                $where['refunded_nums|>'] = 0;
            }
        }
        // 状态 used_nums
        if (isset($this->body['use_status'])) {
            if ($this->body['use_status'] == 0) {
                // 未使用
                $where['use_status'] = 0;
            } elseif ($this->body['use_status'] == 1) {
                // 已使用
                $where['use_status'] = 1;
            }
        }
        $fields = 'count(*) as order_nums,
                            sum(nums) as total_nums,
                            sum(used_nums) as total_used_nums,
                            sum(refunding_nums) as total_refunding_nums,
                            sum(refunded_nums) as total_refunded_nums,
                            sum(refunded) as total_refunded,
                            sum(payed) as total_payed,
                            sum(amount) as total_amount ';
        $statics = reset(OrderModel::model()->search($where,$fields));
//        Tools::dump($where);
        return $statics;
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

    /**
     * 申请并审核退款
     * author : yinjian
     */
    public function applycheckAction()
    {
        try {
            $params = array();
            $params['order_id'] = trim(Tools::safeOutput($this->body['order_id']));
            $params['nums'] = intval($this->body['nums']);
            $params['user_id'] = intval($this->body['user_id']);
            $params['user_account'] = trim(Tools::safeOutput($this->body['user_account']));
            $params['user_name'] = trim(Tools::safeOutput($this->body['user_name']));
            !$params['user_name'] && $params['user_name'] = $params['user_account'];
            $params['remark'] = trim($this->body['remark']);
            $params['source_id'] = trim(Tools::safeOutput($this->body['source_id']));
            $res = RefundApplyModel::model()->applycheck($params);
            Tools::lsJson(true, 'ok', array('id' => $res));
        } catch(Exception $e) {
            Lang_Msg::error( 'ERROR_GLOBAL_3' );
        }
    }

    /**
     * ota订单传订单号进行处理
     * author : yinjian
     */
    public function checkotaApplyAction()
    {
        $params = array();
        $params['order_id'] = trim(Tools::safeOutput($this->body['order_id']));
        $params['allow_status'] = intval($this->body['allow_status']);
        $params['user_id'] = intval($this->body['user_id']);
        $params['user_account'] = trim(Tools::safeOutput($this->body['user_account']));
        $params['user_name'] = trim(Tools::safeOutput($this->body['user_name']));
        !$params['user_name'] && $params['user_name'] = $params['user_account'];
        $params['reject_reason'] = $this->body['reject_reason'];
        $refundApplyModel = new RefundApplyModel();
        $res = $refundApplyModel->checkotaApply($params);
        Tools::lsJson($params['order_id']);
    }

    /** 检查订单是否可退，返回可退未使用张数
     * @author : zqf
     * */
    public function getnumsAction() {
        $params = array();
        $params['order_id'] = trim(Tools::safeOutput($this->body['order_id']));
        !$params['order_id'] && Tools::lsJson(false,'缺少订单ID');
        // 订单是否存在
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $ticketModel = new TicketModel();
        $ticketModel->begin();
        $order = $orderModel->get(array('id' => $params['order_id'], 'deleted_at' => 0));
        !$order && Tools::lsJson(false,'该订单不存在');
        // 票模板为可退属性
        $order['refund'] == 0 && Tools::lsJson(false,'该订单的产品不是可退产品，不能退票');

        // 是否使用了优惠券
        if($order['activity_paid']>0 && $order['used_nums']>0){
            Tools::lsJson(false,'该订单使用了优惠券，且已验过票，不能退票');
        }
        // 未支付单不能退款
        !in_array($order['status'], array('paid', 'finish','billed')) && Tools::lsJson(false,'该订单未支付，不能退票');
        $order['status']=='billed' && Tools::lsJson(false,'该订单已结算，不能退票');
        // 可退票数=总票数-已使用票数-退款中票数-已退款张数
        $remain_ticket = $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'];
        if($remain_ticket<=0) {
            Tools::lsJson(false,'该订单已无剩余可退的票');
        }

        Tools::lsJson(true,'该订单有'.$remain_ticket.'张票可退',
            array(
                'order_id'=>$params['order_id'],
                'nums'=>$order['nums'],
                'used_nums'=>$order['used_nums'],
                'refunding_nums'=>$order['refunding_nums'],
                'refunded_nums'=>$order['refunded_nums'],
                'unused_nums'=>$remain_ticket
            )
        );
    }
}