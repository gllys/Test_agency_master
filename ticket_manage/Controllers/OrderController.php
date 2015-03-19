<?php

/**
 * 订单管理控制器
 * 2014-1-7
 * @package controller
 * @author cuiyulei
 * */
class OrderController extends BaseController {

    /**
     * 订单查询功能
     *
     * @return void
     * @author cuiyulei
     * */
    public function search() {
        //加载数据模型
        $ordersModel = $this->load->model('orders');
        $organizationPModel = $this->load->model('organizationPartner');
        //组织查询条件
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'relate' => 'landscape,buyer_organization,seller_organization,order_item',
            'order' => $ordersModel->table . '.updated_at DESC',
            'fields' => $ordersModel->table . ".*,(CASE WHEN op.id is null THEN 0 ELSE 1 END) as is_partner",
            'join' => array(
                array(
                    'left_join' => $organizationPModel->table . ' op ON ' . $ordersModel->table . '.buyer_organization_id=op.organization_partner_id AND ' . $ordersModel->table . '.seller_organization_id=op.organization_main_id'
                )
            ),
            'filter' => array(
                'op.status' => 'normal'
            )
        );

        $get = $this->getGet();

        //下单时间
        if (!empty($get['created_at']) && isset($get['created_at'])) {
            $timeFilter = explode(' - ', $get['created_at']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$ordersModel->table . '.created_at|between'] = $timeFilter;
        }

        //游玩时间
        if (!empty($get['useday']) && isset($get['useday'])) {
            $timeFilter = explode(' - ', $get['useday']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$ordersModel->table . '.useday|between'] = $timeFilter;
        }

        //取票人电话
        if (!empty($get['owner_mobile']) && isset($get['owner_mobile'])) {
            $param['filter'][$ordersModel->table . '.owner_mobile'] = $get['owner_mobile'];
        }

        //一级票务名称
        if (!empty($get['landscape_name']) && isset($get['landscape_name'])) {
            $landscapesModel = $this->load->model('landscapes');
            $param['join'][] = array(
                'left_join' => $landscapesModel->table . ' ON ' . $ordersModel->table . '.landscape_id=' . $landscapesModel->table . '.id',
            );
            $param['filter'][$landscapesModel->table . '.name|like'] = $get['landscape_name'];
        }

        //支付方式
        if (!empty($get['payment']) && isset($get['payment'])) {
            $param['filter'][$ordersModel->table . '.payment'] = $get['payment'];
        }

        //订单状态
        if (!empty($get['status']) && isset($get['status'])) {
            //未付款
            if ($get['status'] == 'unpaid') {
                $params['filter'][$ordersModel->table . '.status'] = 'active';
                $params['filter'][$ordersModel->table . '.pay_status'] = 'unpaid';
            } elseif ($get['status'] == 'paid') {
                $params['filter'][$ordersModel->table . '.status'] = 'active';
                $params['filter'][$ordersModel->table . '.pay_status'] = 'paid';
            } else {
                $params['filter'][$ordersModel->table . '.status'] = $get['status'];
            }
            //var_dump($params['filter']);
        }

        //电子编码
        if (!empty($get['hash']) && isset($get['hash'])) {
            $param['filter'][$ordersModel->table . '.id'] = $get['hash'];
        }

        $allStatus = array_merge(OrderCommon::getOrderPayStatus(), OrderCommon::getOrderStatus());
        unset($allStatus['active']);
        $data['allStatus'] = $allStatus;
        $data['get'] = $get;
        $orderList = $ordersModel->commonGetList($param);
        $data['pagination'] = $this->getPagination($orderList['pagination']);
        $data['orderList'] = $orderList['data'];
        //加载视图
        $this->load->view('order/search', $data);
    }

    /**
     * 退票视图
     *
     * @return void
     * @author cuiyulei
     * */
    public function getRefund() {
        header("Content-type: text/html; charset=utf-8");
        $hash = $this->getGet('hash');
        $data = $this->load->common('order')->getOrderInfo($hash, 'landscape,order_item', 'more');
        // if($data['orderInfo']){
        // 	$refundApplyModel      = $this->load->model('refundApply');
        // 	$refundApplyItemsModel = $this->load->model('refundApplyItems');
        // 	$refundApplyInfo       = $refundApplyModel->getList('order_id='.$data['orderInfo']['id']);
        // 	if($refundApplyInfo){
        // 		foreach($refundApplyInfo as $key => $value){
        // 			$refundApplyInfo[$key]['refundApplyItems'] = $refundApplyItemsModel->getList('refund_apply_id='.$value['id']);
        // 		}
        // 		$data['refundApplyInfo'] = $refundApplyInfo;
        // 	}
        // }
        $refundValidNum = $data['orderInfo']['nums'] - $data['orderInfo']['used_nums'] - $data['orderInfo']['refunded_nums'] - $data['orderInfo']['apply_nums'] - $data['orderInfo']['checked_nums'];

        $data['refundValidNum'] = $refundValidNum;
        $data['refund_illegal'] = '';
        if (!$refundValidNum) {
            $data['refund_illegal'] = '不存在可退的票';
        }

        $this->load->view('order/refund', $data);
    }

    /**
     * 申请退票操作
     *
     * @return void
     * @author cuiyulei
     * */
    public function doRefundApply() {
        $this->doAction('refundApply', 'addRefundApply', $this->getPost());
    }

    /**
     * 改期视图
     *
     * @return void
     * @author cuiyulei
     * */
    public function getUseday() {
        $id = $this->getGet('order_id');
        $ordersModel = $this->load->model('orders');
        $orderInfo = $ordersModel->getOrderDetailById($id, '', 'more');
        $msg = '';

        //是否能改期
        $orderCommon = $this->load->common('order');
        $changeAble = $orderCommon->checkChangeUseDayAble($orderInfo, $msg);
        if (!$changeAble) {
            $data['error_msg'] = $msg;
        }

        $data['orderInfo'] = $orderInfo;

        $this->load->view('order/change_use_day', $data);
    }

    /**
     * 确认改期
     *
     * @return json
     * @author cuiyulei
     * */
    public function doChangeUseDay() {
        $post = $this->getPost();
        $this->doAction('order', 'changeUseDay', $post);
    }

    /**
     * 检票记录
     *
     * @return void
     * @author fangshixiang
     * */
    public function getRecord() {
        $order_id = $this->getGet('order_id');
        $where = "code='" . $order_id . "'";
        $data['list'] = $this->load->model('ticketRecord')->getList($where, '', 'created_at DESC');
        $this->load->view('order/record',$data);
    }

    /**
     * 发送短信
     *
     * @return json
     * @author cuiyulei
     * */
    public function doSMS() {
        return $this->doAction('order', 'doSMS', $this->getPost());
    }

    /**
     * 门票使用查询
     *
     * @return void
     * @author cuiyulei
     * */
    public function detail() {
        $get = $this->getGet();
        $ticketsModel = $this->load->model('tickets');
        $ordersModel = $this->load->model('orders');
        $orderItemsModel = $this->load->model('orderItems');
        $refundApplyModel = $this->load->model('refundApply');
        $refundApplyItemsModel = $this->load->model('refundApplyItems');
        $landscapesModel = $this->load->model('landscapes');
        //电子编码
        if (!empty($get['order_hash']) && $get['order_hash']) {
            $filter[$ordersModel->table . '.id'] = $get['order_hash'];
        }

        //票号
        if (!empty($get['ticket_hash']) && $get['ticket_hash']) {
            $filter[$ticketsModel->table . '.id'] = $get['ticket_hash'];
        }

        //游玩时间 
        if (!empty($get['useday_time']) && $get['useday_time']) {
            $useday_time = explode(' - ', $get['useday_time']);
            $filter[$orderItemsModel->table . '.useday|gethan'] = $useday_time[0];
            $filter[$orderItemsModel->table . '.useday|lethan'] = $useday_time[1];
        }

        //使用时间
        if (!empty($get['used_time']) && $get['used_time']) {
            $used_time = explode(' - ', $get['used_time']);
            $filter[$ticketsModel->table . '.used_time|gethan'] = strtotime($used_time[0]);
            $filter[$ticketsModel->table . '.used_time|lethan'] = strtotime($used_time[1]);
        }

        //门票名称
        if (!empty($get['landscape_name']) && $get['landscape_name']) {
            $filter[$landscapesModel->table . '.name|like'] = trim($get['landscape_name']);
        }

        //前提条件,票是自己机构发的票
        // $filter[$ordersModel->table.'.seller_organization_id'] = $_SESSION['backend_userinfo']['organization_id'];
        //门票状态
        if (!empty($get['ticket_status']) && $get['ticket_status']) {
            if ($get['ticket_status'] == 'used') {
                $filter[$ticketsModel->table . '.status'] = $get['ticket_status'];
            } elseif ($get['ticket_status'] == 'unused') {
                $tmpFilter = $ticketsModel->parseFilter($filter);
                $tmpFilter .= ' AND ' . $ticketsModel->table . '.status=\'unused\' AND (refund_apply_id is NULL OR ' . $refundApplyModel->table . '.status=\'reject\')';
                $filter = $tmpFilter;
            } elseif ($get['ticket_status'] == 'refunding') {
                $filter[$refundApplyModel->table . '.status|in'] = array('apply', 'checked');
            } elseif ($get['ticket_status'] == 'refunded') {
                $filter[$refundApplyModel->table . '.status'] = 'refunded';
            }
        }

        //退票状态
        // if (!empty($get['refund_status']) && $get['refund_status']) {
        // 	$filter[$refundApplyModel->table.'.status'] = $get['refund_status'];
        // }

        $page = $get['p'] ? intval($get['p']) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'filter' => $filter,
            'order' => '`id` DESC',
            'join' => array(
                array(
                    'left_join' => $ordersModel->table . ' ON ' . $ticketsModel->table . '.order_id=' . $ordersModel->table . '.id',
                ),
                array(
                    'left_join' => $orderItemsModel->table . ' ON ' . $orderItemsModel->table . '.order_id=' . $ticketsModel->table . '.order_id',
                ),
                array(
                    'left_join' => $landscapesModel->table . ' ON ' . $orderItemsModel->table . '.landscape_id=' . $landscapesModel->table . '.id',
                ),
                array(
                    'left_join' => $refundApplyItemsModel->table . ' ON ' . $ticketsModel->table . '.id=' . $refundApplyItemsModel->table . '.ticket_id',
                ),
                array(
                    'left_join' => $refundApplyModel->table . ' ON ' . $refundApplyItemsModel->table . '.refund_apply_id=' . $refundApplyModel->table . '.id',
                ),
            ),
            'fields' => $ticketsModel->table . '.*,' . $landscapesModel->table . '.name as landscape_name,'
            . $refundApplyModel->table . '.id as refund_apply_id,'
            . $orderItemsModel->table . '.name as ticket_tmp_name,'
            . $orderItemsModel->table . '.useday as order_useday,'
            . $ordersModel->table . '.payment as order_payment,'
            . $refundApplyModel->table . '.status as refund_apply_status',
        );

        $ticketsList = $ticketsModel->commonGetList($param);
        $data['get'] = $get;
        $data['ticketsList'] = $ticketsList['data'];
        //print_r($ticketsList );
        //得到使用模型
        $data['ticketUsedModel'] = $this->load->model('TicketUsed');
        //分页信息  
        $data['pagination'] = $this->getPagination($ticketsList['pagination']);

        $this->load->view('order/detail', $data);
    }

    /**
     * 验票
     *
     * @return void
     * @author cuiyulei
     * */
    public function check() {
        $data['data'] = $this->_check();
        $data['get'] = $this->getGet();
        $this->load->view('order/check', $data);
    }

    /**
     * 得到所查询的hash 订单号，和订单号下的票号
     * @return array
     */
    public function _check() {
        #加载模块
        $orderModel = $this->load->model('orders');  //加载订单模型
        $orderItemModel = $this->load->model('orderItems');  //加载订单模型
        $ticketModel = $this->load->model('tickets'); //加载票模型
        $ticketUsedModel = $this->load->model('ticketUsed');  //加载票务使用状况模型
        $get = $this->getGet();
        $codeNo = trim($get['hash']);
        $codeLength = strlen($codeNo); //号码的长度
        //检查编码code是否正确,
        if ($codeLength != 16 && $codeLength != 19) {
            return array('status' => 0, 'msg' => '票号不存在');
        }

        //需传参数定义
        $orderId = null;
        $ticketId = null;

        //如果是订单号
        if ($codeLength == 16) {
            $orderId = $orderCode = $codeNo;
        }

        //如果是票号
        if ($codeLength == 19) {
            $useNum = 1;
            $rs = $ticketModel->getOne(array('id' => $codeNo), '', 'order_id,id'); #得到订单号
            if (!$rs) {
                return array('status' => 0, 'msg' => '票号不正确');
            }
            $orderId = $rs['order_id'];
            $ticketId = $rs['id'];
            $orderCode = $orderId;
        }
        //得到票的订单情况信息
        $orderItem = $orderItemModel->getOne(array('order_id' => $orderId));
        if (!$orderItem) {
            return array('status' => 0, 'msg' => '订单号不正确');
        }

        //得到订单下所有的票
        $param = array(
            'order_id' => $orderItem['order_id'],
        );
        if ($ticketId) {
            $param['id'] = $ticketId;
        }
        $tickets = $ticketModel->getList($param);
        return array('status' => 1, 'msg' => array('order' => $orderItem, 'tickets' => $tickets));
    }

    public function useTicket() {
        $this->doAction('order', 'useTicket', $this->getPost());
    }

}

// END class 