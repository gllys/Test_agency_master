<?php

/**
 * 财务统计控制器
 * 2014-1-7
 * @package controller
 * @author cuiyulei
 * */
class BillController extends BaseController {

    /**
     * 系统配置
     *
     * @return void
     * @author cuiyulei
     * */
    public function config() {
        $rs = Bill::api()->getconf(array());
        $data['config'] = $rs['body'];
        $data['weekArray'] = Bill::getWeekDay();

        //得到供应商列表
        $param = array();
        $param['items'] = 100000;
        $param['type'] = 'supply';
        $rs = Organizations::api()->list($param);

        $data['orgList'] = ApiModel::getLists($rs);


        $param = array();
        $param['items'] = 100000;
        $param['balance_type'] = '1,2';
        $param['sortby'] = 'updated_at:desc';
        $rs = Unionmoney::api()->lists($param);

        $data['list'] = ApiModel::getLists($rs);
        //加载视图
        $this->load->view('bill/config', $data);
    }

    /**
     * 设置供应商平台结算周期
     *
     * @return void
     * @author fangshixiang
     * */
    public function saveSupplyConfig() {
        $post = $this->getPost();
        $param['org_id'] = $post['supply_id'];
        $param['user_id'] = $_SESSION['backend_userinfo']['id'];
        $param['user_name'] = $_SESSION['backend_userinfo']['name'];
        if ($post['account_cycle']) {
            $param['balance_type'] = $post['account_cycle'] == "month" ? 2 : 1;
        } else {
            $param['balance_type'] = $post['account_cycle'];
        }
        $param['balance_cycle'] = $post['account_cycle_day'];
        $rs = Unionmoney::api()->set($param);
        if (Bill::isSucc($rs)) {
            echo json_encode(array('data' => array($rs['body'])));
        } else {
            echo json_encode(array('errors' => array($rs['message'])));
        }
    }

    public function fund2() {
        $get = $this->getGet();

        $tal_nums = Unionmoney::api()->total(array()); //总资产管理
        $data['total'] = isset($tal_nums['body']) ? $tal_nums['body'] : array(
            "total_union_money" => "0.00",
            "total_frozen_money" => "0.00"
        );

        // 公共条件
        $params['current'] = empty($get['p']) ? 1 : $get['p'];
        $params['items'] = 15;
        //搜索条件
        if (isset($get['op_org'])) {
            if ($get['sel_name'] == '0') { //分销商名字
                $params['org_name'] = $get['op_org'];
            }
            if ($get['sel_name'] == '1') { //申请者账号
                $params['apply_account'] = $get['op_org'];
            }
            if ($get['sel_name'] == '2') { //申请者名称
                $params['apply_username'] = $get['op_org'];
            }
        }
        //时间
        if (isset($get['created_at']) && !empty($get['created_at'])) {
            $times = explode(" - ", $get['created_at']);
            $params['start_date'] = $times[0];
            $params['end_date'] = $times[1];
        }

        //提现状态
        if (isset($get['status']) ) {
            $params['status'] = $get['status'];
        }
        //机构角色
        if (isset($get['org_role']) ) {
            $params['org_role'] = $get['org_role'];
        }

        //明细列表
        $rs = Unionmoneyencash::api()->lists($params);
        if ($rs['code'] == 'succ') {
            $data['list'] = $rs['body']['data'];
            $data['pagination'] = $this->getPagination($rs['body']['pagination']);
        }

        $data['get'] = $get;

        $this->load->view('bill/fund2', $data);
    }

    /**
     * 资产管理
     *
     * @return void
     * @author cuiyulei
     * */
    public function fund() {
        $get = $this->getGet();

        $tal_nums = Unionmoney::api()->total(array()); //总资产管理
        $data['total'] = isset($tal_nums['body']) ? $tal_nums['body'] : array(
            "total_union_money" => "0.00",
            "total_frozen_money" => "0.00"
        );

        // 公共条件
        $params['current'] = empty($get['p']) ? 1 : $get['p'];
        $params['items'] = 15;
        //搜索条件
        if (isset($get['op_org']) && !empty($get['op_org'])) {
            if ($get['sel_name'] == '0') {
                $params['op_uid'] = $get['op_org'];
            }
            if ($get['sel_name'] == '1') {
                $params['op_account'] = $get['op_org'];
            }
            if ($get['sel_name'] == '2') {
                $params['org_name'] = $get['op_org'];
            }
        }
        //时间
        if (isset($get['created_at']) && !empty($get['created_at'])) {
            $times = explode(" - ", $get['created_at']);
            $params['start_date'] = $times[0];
            $params['end_date'] = $times[1];
        }
        //交易类型
        if (isset($get['trade_type']) && !empty($get['trade_type'])) {
            $params['trade_type'] = $get['trade_type'];
        }

        //列表资产
        $result = Unionmoneylog::api()->lists($params);
        if ($result['code'] == 'succ') {
            $data['lists'] = $result['body']['data'];
            $data['pagination'] = $this->getPagination($result['body']['pagination']);
        }


        $data['get'] = $get;
        //加载视图
        $this->load->view('bill/fund', $data);
    }

    //交易报表
    public function report() {
        $get = $this->getGet();
        $params['current'] = empty($get['p']) ? 1 : $get['p'];

        $params['status'] = 'billed';
        $params['start_date'] = $get['date'][0] = isset($get['date'][0]) ? $get['date'][0] : date('Y-m-01');
        $params['end_date'] = $get['date'][1] = isset($get['date'][1]) ? $get['date'][1] : date('Y-m-d');
        if (isset($get['val'])) {
            $params[$get['field']] = $get['val'];
        }
        if (isset($get['method'])) {
            $params['payment'] = $get['method'];
        }
        $data['get'] = $get;

        $params['items'] = 10;
        $result = Order::api()->lists($params);
        if ($result['code'] == 'succ') {
            $data['lists'] = $result['body']['data'];
            $data['pagination'] = $this->getPagination($result['body']['pagination']);
        }

        $this->load->view('bill/report', $data);
    }

    /**
     * 保存系统结算周期配置
     *
     * @return void
     * @author cuiyulei
     * */
    public function saveSetting() {
        // echo 'what the fuck';exit();
        $post = $this->getPost();
        $param['conf_bill_type'] = $post['account_cycle'] == "month" ? 0 : 1;
        $param['conf_bill_value'] = $post['account_cycle_day'];
        $rs = Bill::api()->setconf($param);
        if (Bill::isSucc($rs)) {
            echo json_encode(array('data' => array($rs['body'])));
        } else {
            echo json_encode(array('errors' => array($rs['message'])));
        }
    }

    /**
     * 应付账款
     * @return void
     * @author cuiyulei
     * */
    public function payable() {
        $param = array('agency_id' => 0);
        $get = $this->getGet();
        if (isset($get['settlement_time']) && !empty($get['settlement_time'])) {
            $times = explode(" - ", $get['settlement_time']);
            $param['bill_sd'] = $times[0];
            $param['bill_ed'] = $times[1];
        }
        if (isset($get['pay_status']) && !empty($get['pay_status'])) {
            $param['pay_state'] = $get['pay_status'] == 'paid' ? 1 : 0;
        }
        if (isset($get['organization_name']) && !empty($get['organization_name'])) {
            $param['supply_name'] = $get['organization_name'];
        }
        $param['current'] = isset($get['p']) ? $get['p'] : 1;
        $rs = Bill::api()->lists($param);
        $billData = Bill::api()->getData($rs);
        $data['billsList'] = $billData['data'];
        $data['pageType'] = __FUNCTION__;
        //支付状态
        $data['allBillPayStatus'] = BillCommon::getBillPayStatus();
        $pagination = Bill::getPagination($rs);
        $data['pagination'] = $this->getPagination($pagination);
        $data['get'] = $get;
        $this->load->view('bill/payable', $data);
    }

    /**
     * 应收账款
     *
     * @return void
     * @author cuiyulei
     * */
    public function receivable() {
        $param = array('supply_id' => 0);
        $get = $this->getGet();
        if (isset($get['update_time']) && !empty($get['update_time'])) {
            $times = explode(" - ", $get['update_time']);
            $param['bill_sd'] = $times[0];
            $param['bill_ed'] = $times[1];
        }
        if (isset($get['pay_state'])) {
            $param['pay_state'] = $get['pay_state'];
        }
        if (isset($get['agency_name']) && !empty($get['agency_name'])) {
            $param['agency_name'] = $get['agency_name'];
        }
        if (isset($get['supply_name']) && !empty($get['supply_name'])) {
            $param['supply_name'] = $get['supply_name'];
        }
        $param['current'] = isset($get['p']) ? $get['p'] : 1;
        $rs = Bill::api()->lists($param);
        $billData = Bill::api()->getData($rs);
        $data['bill'] = $billData['data'];
        $data['pageType'] = __FUNCTION__;
        //支付状态
        $data['allBillPayStatus'] = BillCommon::getBillPayStatus();
        $pagination = Bill::getPagination($rs);
        $data['pagination'] = $this->getPagination($pagination);
        $data['get'] = $get;
        $this->load->view('bill/receivable', $data);
    }

    //账单详情
    public function detail() {
        $id = $this->getGet('id');
        $data['supply'] = $this->getGet('s');
        $rs = Bill::api()->detail(array('id' => $id));
        $data['billInfo'] = Bill::api()->getData($rs);
        $this->load->view('bill/detail', $data);
    }

    //弹出框
    public function uploadShow() {
        $id = $this->getGet('id');
        $rs = Bill::api()->detail(array('id' => $id));
        $data['billInfo'] = Bill::api()->getData($rs);
        $this->load->view('bill/upload_show', $data);
    }

    //资产管理
    public function uploadShow1() {
        $id = $this->getGet('id');
        $view = $this->getGet('view');
        $rs = Unionmoneyencash::api()->detail(array('id' => $id,'with_org_info'=>1));
        $data['billInfo'] = isset($rs['body']) ? $rs['body'] : array();
        if ($view) {
            //查看
            $data['view'] = 1;
            $this->load->view('bill/upload_show_1', $data);
        } else {
            //打款
            $data['view'] = 0;
            $this->load->view('bill/upload_show_1', $data);
        }
    }

    //平台打钱支付
    public function setProve() {
        $post = $this->getPost();
        $rs = Bill::api()->detail(array('id' => $post['bill_id']));
        $data['billInfo'] = Bill::api()->getData($rs);

        $param['org_id'] = $data['billInfo']['order_list'][0]['supply_id'];
        $param['distributor_id'] = $data['billInfo']['order_list'][0]['agency_id'];
        $param['user_id'] = $_SESSION['backend_userinfo']['id'];
        $param['user_account'] = $_SESSION['backend_userinfo']['account'];
        $param['user_name'] = $_SESSION['backend_userinfo']['name'];
        $param['money'] = $data['billInfo']['bill_amount'];
        $param['trade_type'] = 5;
        $rs = Unionmoney::api()->inout($param);
        if (Unionmoney::isSucc($rs)) {
            $res = Bill::api()->finish(array('id' => $post['bill_id'], 'pay_status' => '1'));
            if (Bill::isSucc($rs)) {
                echo json_encode(array('data' => array($data['message'])));
            } else {
                echo '{"errors":{"msg":["订单状态设置失败"]}}';
            }
        } else {
            echo '{"errors":{"msg":["打款失败"]}}';
        }
    }

    //上传凭证
    public function uploadProve() {
        $post = $this->getPost();

        //驳回
        if (isset($post['type']) && $post['type'] == 'bohui') {
            $param['id'] = $post['id'];
            $param['check_uid'] = $_SESSION['backend_userinfo']['id'];
            $param['status'] = 2;
            $param['remark'] = $post['remark'];
            $param['paid_at'] = time();
        } else {
            //打款
            $uid = $_SESSION['backend_userinfo']['id'];
            $attachmentsCommon = $this->load->common('attachments');
            $jsonData = $attachmentsCommon->saveAttachment($uid);
            $result = @json_decode($jsonData, true);

            $param['paid_img'] = $result['data'][0]['url'];
            $param['id'] = $post['id'];
            $param['check_uid'] = $_SESSION['backend_userinfo']['id'];
            $param['status'] = 1;
            $param['paid_at'] = time();
        }

        $rs = Unionmoneyencash::api()->check($param);
        if (Unionmoneyencash::isSucc($rs)) {
            echo json_encode(array('data' => array($data['message'])));
        } else {
            echo '{"errors":{"msg":["操作失败"]}}';
        }
    }

    public function file() {
        $post = $this->getPost();

        $params['status'] = 'billed';
        $params['start_date'] = $post['date'][0] = isset($post['date'][0]) ? $post['date'][0] : date('Y-m-01');
        $params['end_date'] = $post['date'][1] = isset($post['date'][1]) ? $post['date'][1] : date('Y-m-d');
        if (isset($post['val'])) {
            $params[$post['field']] = $post['val'];
        }
        if (isset($post['method'])) {
            $params['payment'] = $post['method'];
        }
        $data['get'] = $post;

        $params['items'] = 10;

        $type_names = array('电子票', '任务单');
        $payment_types = array('cash' => '现金', 'offline' => '线下', 'credit' => '信用支付', 'advance' => '储值支付', 'union' => '平台支付', 'alipay' => '支付宝', 'kuaiqian' => '快钱');
        $status_labels = array('unpaid' => '未支付', 'cancel' => '已取消', 'paid' => '已付款', 'finish' => '已结束', 'billed' => '已结款');



        $path = PI_APP_ROOT . 'Views/files/reports/' . $_SESSION['backend_userinfo']['id'];
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file = $path . '/' . $params['start_date'] . '-' . $params['end_date'] . '.xlsx';
        if (is_file($file)) {
            unlink($file);
        }
        require_once PI_APP_ROOT . "Libraries/PHPExcel/PHPExcel.php";
        require_once PI_APP_ROOT . "Libraries/PHPExcel/PHPExcel/Autoloader.php";
        $objExcel = new PHPExcel();
//		$excel_properties = $objExcel->getProperties();
//		$excel_properties->setCreator("中航工业集团 汇联皆景信息有限公司 监管平台");
//		$excel_properties->setLastModifiedBy("中航工业集团 汇联皆景信息有限公司 监管平台");
//		$excel_properties->setTitle($pTitle);
//		$excel_properties->setSubject($pTitle);
//		$excel_properties->setDescription("");
//		$excel_properties->setKeywords("");
//		$excel_properties->setCategory("报表");
//		$excel_properties->setCompany("中航工业集团 汇联皆景信息有限公司");
//		$excel_properties->setCreated();
//		$excel_properties->setModified();

        $sheet = $objExcel->setActiveSheetIndex(0);
        //$sheet->setCellValue('A1', $pTitle);

        $sheet->fromArray(array(
            '序号',
            '订单号',
            '用户名称',
            '门票名称',
            '供应商名称',
            '预订时间',
            '支付时间',
            '游玩时间',
            '张数',
            '单价',
            '结算金额',
            '订单类型',
            '支付方式',
            '支付金额',
            '退款金额',
            '结款金额',
            '订单状态'
            ), null, 'A1');

        $page = 1;
        $idx = 1;
        $data = true;
        while ($data) {
            $params['current'] = $page;

            $result = Order::api()->lists($params);
            if ($result['code'] == 'succ') {
                $data = $result['body']['data'];
            } else {
                break;
            }
            //$pNumRows = count($data);
            $pRow = $idx + 1;
            //$sheet->insertNewRowBefore($pRow, $pNumRows);
            //$sheet->duplicateStyle($objExcel->getActiveSheet()->getStyle('A'.$pRow),'A'.$pRow.':A'.($pRow+$pNumRows-1));
            $source = array();
            foreach ($data as $key => $record) {
                $record['created_at'] = $record['created_at'] > 0 ? date('Y-m-d H:i', $record['created_at']) : '';
                $record['pay_at'] = $record['pay_at'] > 0 ? date('Y-m-d H:i', $record['pay_at']) : '';
                $source[] = array(
                    $idx,
                    ' ' . $record['id'],
                    $record['distributor_name'],
                    $record['name'],
                    $record['supplier_name'],
                    $record['created_at'],
                    $record['pay_at'],
                    $record['use_day'],
                    $record['nums'],
                    number_format($record['amount'] / $record['nums'], 2),
                    $record['amount'],
                    $type_names[$record['type']],
                    $payment_types[$record['payment']],
                    $record['payed'],
                    $record['refunded'],
                    $record['payed'] - $record['refunded'],
                    $status_labels[$record['status']]
                );
                $idx += 1;
            }
            unset($key, $record);
            $sheet->fromArray($source, null, 'A' . $pRow);

            $page += 1;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objWriter->save($file);
        echo json_encode(array(
            'link' => '/Views/files/reports/' . $_SESSION['backend_userinfo']['id'] . '/' . $params['start_date'] . '-' . $params['end_date'] . '.xlsx'
        ));
        exit;
    }

}

// END class 
