<?php

/**
 * 退票管理控制器
 * 2014-1-7
 * @package controller
 * @author cuiyulei
 * */
class RefundController extends BaseController {

    /**
     * 退票审核
     *
     * @return void
     * @author cuiyulei
     * */
    public function verify() {
        //加载模块
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $params['page'] = $page;
        $get = $this->getGet();
        $refundApplyModel = $this->load->model('refundApply');
        $refundsModel = $this->load->model('refunds');
        $ordersModel = $this->load->model('orders');
        $orderItemsModel = $this->load->model('orderItems');
        $organizationModel = $this->load->model('organizations');
        $landscapesModel = $this->load->model('landscapes');

        //组织查询条件
        $params = array(
            'order' => 'id DESC',
            'join' => array(
                array(//连接退款表
                    'left_join' => $refundsModel->table . ' re ON re.refund_apply_id=' . $refundApplyModel->table . '.id '
                ),
                array(//连接订单表
                    'left_join' => $ordersModel->table . ' od ON ' . $refundApplyModel->table . '.order_id=od.id '
                ),
                array(//连接订单详情表
                    'left_join' => $orderItemsModel->table . ' oi ON oi.order_id=od.id '
                ),
                array(//连接一级票务表表
                    'left_join' => $landscapesModel->table . ' ld ON ld.id=oi.landscape_id '
                ),
                array(//连接机构表(申请机构)
                    'left_join' => $organizationModel->table . ' og ON og.id=od.buyer_organization_id '
                ),
                array(//连接机构表（供应商）
                    'left_join' => $organizationModel->table . ' supply ON supply.id=od.seller_organization_id '
                ),
            ),
            'page' => $page,
            'items' => 15,
            'order' => $refundApplyModel->table.'.created_at DESC',
            'fields' => $refundApplyModel->table . '.*,re.status as refund_status,re.created_at as refund_time,od.id as order_id,od.payment as order_payment,od.status as order_status,ld.name as landscape_name,oi.name as ticket_type,oi.allow_back,oi.price,og.name as apply_name,supply.name as supply_name'
        );

        //退款申请日期
        if (!empty($get['apply_time']) && $get['apply_time']) {
            $apply_time = explode(' - ', $get['apply_time']);
            $params['filter'][$refundApplyModel->table . '.created_at|between'] = array($apply_time[0], $apply_time[1]);
        }

        //审核状态
        if (!empty($get['apply_status']) && $get['apply_status']) {
            $params['filter'][$refundApplyModel->table . '.status'] = $get['apply_status'];
        }

        //退款状态
        if (!empty($get['refund_status']) && $get['refund_status']) {
            $params['filter']['re.status'] = $get['refund_status'];
        }

        //退款申请机构
        if (!empty($get['apply_name']) && $get['apply_name']) {
            $params['filter']['og.name|like'] = trim($get['apply_name']);
        }

        //一级票务名称
        if (!empty($get['landscape_name']) && $get['landscape_name']) {
            $params['filter']['ld.name|like'] = trim($get['landscape_name']);
        }

        //退款申请单号
        if (!empty($get['refund_apply_id']) && $get['refund_apply_id']) {
            $params['filter'][$refundApplyModel->table . '.id'] = trim($get['refund_apply_id']);
        }
//
//        //电子编码（订单）
//        if (!empty($get['order_hash']) && $get['order_hash']) {
//            $params['filter']['od.hash'] = $get['order_hash'];
//        }
        //订单号
        if (!empty($get['order_id']) && $get['order_id']) {
            $params['filter'][$refundApplyModel->table . '.order_id'] = $get['order_id'];
        }

        //组织数据
        $data['get'] = $get;
        $refundApply = $refundApplyModel->commonGetList($params);
        $data['pagination'] = $this->getPagination($refundApply['pagination']);
        $data['refundApplys'] = $refundApply['data'];
        $data['apply_status'] = RefundApplyCommon::getRefundApplyStatus();
        $data['refund_status'] = RefundsCommon::getRefundsStatus();

        // print_r($refundApply);
        // exit();
        //加载视图
        $this->load->view('refund/verify', $data);
    }

    /**
     * 退票记录
     *
     * @return void
     * @author cuiyulei
     * */
    public function record() {
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $params = array();
        $params['page'] = $page;
        $get = $this->getGet();
        $refundsModel = $this->load->model('refunds');

        if (isset($get['id']) && $get['id']) {
            $params['filter'][$refundsModel->table . '.refund_apply_id'] = $get['id'];
            $get['refund_apply_id'] = $get['id'];
        } else {
            //搜索条件转换
            //默认是今天
            if (empty($get['refunds_time'])) {
                $params['filter'][$refundsModel->table . '.created_at|between'] = array(strtotime(date('Y-m-d')), date('Y-m-d', strtotime("+1 day")));
                $get['refunds_time'] = date('Y-m-d') . ' - ' . date('Y-m-d');
            } else {
                $timeArr = explode(' - ', $get['refunds_time']);
                $params['filter'][$refundsModel->table . '.created_at|between'] = array($timeArr[0], date('Y-m-d', strtotime($timeArr[1]) + 86400));
            }

            //支付方式
            if (!empty($get['pay_app_id']) && $get['pay_app_id']) {
                $params['filter'][$refundsModel->table . '.pay_app_id'] = $get['pay_app_id'];
            }

            //退款申请单号
            if (!empty($get['refund_apply_id']) && $get['refund_apply_id']) {
                $params['filter'][$refundsModel->table . '.refund_apply_id'] = $get['refund_apply_id'];
            }

            //退款单号
            if (!empty($get['refund_id']) && $get['refund_id']) {
                $params['filter'][$refundsModel->table . '.id'] = $get['refund_id'];
            }

            //TODO::申请机构
            if (!empty($get['apply_name']) && $get['apply_name']) {
                $params['filter']['o.buyer_organization_id|IN'] = "SELECT `id` FROM og WHERE `name` LIKE '%" . $get['apply_name'] . "%'";
            }

            //供应商
            if (!empty($get['organization_name']) && $get['organization_name']) {
                $params['filter']['o.seller_organization_id|IN'] = "SELECT `id` FROM og WHERE `name` LIKE '%" . $get['organization_name'] . "%'";
            }
        }

        $data = $refundsModel->getRefundsList($params);
        $paymentsCommon = $this->load->common('payments');
        $data['get'] = $get;
        $data['pagination'] = $this->getPagination($data['pagination']);

        $this->load->view('refund/record', $data);
    }

    /**
     * 退款页面
     *
     * @return void
     * @author cuiyulei
     * */
    public function prefund() {
        //获取退款申请单的 id （int | array）
        if ($this->getMethod() == 'POST') {
            $idArr = $this->getPost('apply_ids');
        } else {
            $idArr = array($this->getGet('id'));
        }
        if (empty($idArr)) {
            $data['refundList'] = array();
        } else {
            $refundsModel = $this->load->model('refunds');
            $refundApplyModel = $this->load->model('refundApply');
            $ordersModel = $this->load->model('orders');
            $orderItemsModel = $this->load->model('orderItems');
            $organizationModel = $this->load->model('organizations');
            $landscapesModel = $this->load->model('landscapes');
            $refundsCommon = $this->load->common('refunds');

            //生成退款单
            foreach ($idArr as $key => $value) {
                $msg = '';
                $refundApplyId = $value;
                $exist = $refundsModel->getOne('refund_apply_id=\'' . $refundApplyId.'\'');
                if (!$exist) {
                    $refundApplyInfo = $refundApplyModel->getID($refundApplyId);
                    $refundsCommon->addRefund($refundApplyInfo, $msg);
                }
            }

            $params = array(
                'join' => array(
                    array(
                        'left_join' => $refundApplyModel->table . ' ra ON ra.id=' . $refundsModel->table . '.refund_apply_id'
                    ),
                    array(
                        'left_join' => $ordersModel->table . ' od ON od.id=ra.order_id '
                    ),
                    array(//连接订单详情表
                        'left_join' => $orderItemsModel->table . ' oi ON oi.order_id=od.id '
                    ),
                    array(//连接一级票务表表
                        'left_join' => $landscapesModel->table . ' ld ON ld.id=oi.landscape_id '
                    ),
                    array(
                        'left_join' => $organizationModel->table . ' og ON og.id=od.seller_organization_id '
                    ),
                    array(
                        'left_join' => $organizationModel->table . ' ogb ON ogb.id=od.buyer_organization_id '
                    )
                ),
                'filter' => array(
                    $refundsModel->table . '.refund_apply_id|in' => $idArr
                ),
                'fields' => $refundsModel->table . '.*,ra.ticket_nums,ra.money as refund_price,oi.name as ticket_name,ld.name as landscape_name,og.name as supply_name,ogb.name as apply_name'
            );

            $refundList = $refundsModel->commonGetList($params);

            $data['refundList'] = $refundList['data'];
            $data['pagination'] = $this->getPagination($refundList['pagination']);
        }

        $data['get'] = $this->getGet();

        //加载视图
        $this->load->view('refund/prefund', $data);
    }

    /**
     * 确认退款
     *
     * @return void
     * @author cuiyulei
     * */
    public function doRefund() {
        $refund_apply_id = $this->getGet('id');
        $refundsModel = $this->load->model('refunds');
        $paymentsCommon = $this->load->common('payments');
        $info = $refundsModel->getOne('refund_apply_id=\'' . $refund_apply_id.'\'');
        $paymentInfo = $paymentsCommon->getPaymentInfo($info['pay_app_id']);
        $appPaymentObj = new $paymentInfo['app_class'];
        $msg = '';
        $appPaymentObj->doRefund($info, $msg);
        echo '支付准备中……';
    }

    /**
     * 退款审核
     *
     * @return json
     * @author cuiyulei
     * */
    public function refundVerify() {
        echo $this->doAction('RefundApply', 'verify', $this->getPost());
    }

}

// END class 