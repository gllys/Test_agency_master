<?php

use common\huilian\utils\Header;
use common\huilian\models\Pay;

/**
 * @desc 验票账号的订单管理
 * @author xuejian
 */
class OrderController extends Controller {

    /**
     * @desc ajax查询
     */
    public function actionView($is_export = false) {
        $this->actionIndex($is_export);
    }

    /**
     * @desc 获取此账号对应景区票列表
     */
    public function actionIndex($is_export = false) {
        $params = $_REQUEST;

        $is_export = empty($params["is_export"]) == false;

        if (isset($params['landscape_id']) && $params['landscape_id'] == null) {
            unset($params['landscape_id']);
        }
        if (isset($params['distributor_id']) && $params['distributor_id'] == null) {
            unset($params['distributor_id']);
        }
        $data['status_labels'] = array('unpaid' => '未支付', 'cancel' => '已取消', 'paid' => '已付款', 'finish' => '已完成', 'billed' => '已结款');
        $data['status_class'] = array('unpaid' => 'danger', 'cancel' => 'warning', 'paid' => 'success', 'finish' => 'info', 'billed' => 'error');
        $data['status'] = array_keys($data['status_labels']);

        $lan_id = Yii::app()->user->lan_id;

        // 获取分销商 id=>name
        //Credit::api()->debug = true;
//        $rs = Credit::api()->lists(array('fields' => 'distributor_id,distributor_name', 'items' => 1000));
//        $distributorDatas = ApiModel::getLists($rs);
        $distributors = array();
//        foreach ($distributorDatas as $v) {
//            $distributors[$v['distributor_id']] = $v['distributor_name'];
//        }
        $data['distributors_labels'] = $distributors;

        if (!empty($params)) {
            if (isset($params['status']) && !in_array($params['status'], $data['status'])) {
                unset($params['status']);
            }
        }

        $data['get'] = $params;


        $params['current'] = isset($params['page']) ? $params['page'] : 1;
        $params['items'] = $is_export ? 1000 : 20;
        $params['type'] = 0;
        $params['landscape_id'] = $lan_id;
        $params['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
        //var_dump($params);exit;
        //  Order::api()->d
        $data['payTypes'] = Pay::types();
        $data['timeTypes'] = array(
            '预订日期',
            '游玩日期',
            '入园日期',
        );
        $data['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;

        $data = $this->getApiLists($params, $is_export, $data);

        if ($data['lists']["result"]['code'] == 'succ') {
            //$data['lists'] = $result['body'];
            if ($is_export == false) {
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }
        }


//            $result = Order::api()->lists($params);
//            if ($result['code'] == 'succ') {
//                $data['lists'] = $result['body'];
//                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
//                $data['pages']->pageSize = $params['items'];
//            }
//         Header::utf8();
//         var_dump($data['lists']);
//         exit;

        $this->render('index', $data);
    }

    private function getApiLists($params, $is_export, $data) {
        $d = array();
        $pagination = null;
        $result = null;
        $num = 0;


        // print_r(Order::api()->lists($params));
        // exit;
        if ($is_export) {
            $this->renderPartial("excelTop", $data);
            $params['show_verify_items'] = 1;
        }

        do {
            if ($result) {
                unset($result);
            }
            if (isset($params['status'])) {
                foreach (Order::$status as $status_type => $status_lists) {
                    foreach ($status_lists as $status_item => $status_value) {
                        if ($params['status'] == $status_item) {
                            $params[$status_item] = $status_value;
                            break 2;
                        }
                    }
                    unset($status_item, $status_value);
                }
                unset($status_type, $status_lists, $params['status']);
            }
            $result = Order::api()->lists($params);
            $params["current"] = ((int) trim($params["current"])) + 1;
            $params["page"] = $params["current"];
            if ($result['code'] == 'succ') {
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data" => $result['body']["data"], "statics" => $result['body']["statics"], "pagination" => $pagination, "result" => $result);
                if ($is_export) {
                    $this->renderPartial("excelBody", $data);
                }
                $num += count($data['lists']["data"]);
            }
        } while ($params["current"] < 1000 && $is_export == true && $result['code'] == 'succ' && $pagination['current'] < $pagination['total']);
        if ($is_export == true) {
            $data["num"] = $num;
            $this->renderPartial("excelBottom", $data);
            exit;
        }
        return $data;
    }

    /**
     * @desc 查看订单详情页
     */
    public function actionDetail() {
        $data['status_labels'] = array('unpaid' => '未支付', 'cancel' => '已取消', 'paid' => '已付款', 'finish' => '已完成', 'billed' => '已结款');
        $data['paid_type'] = array('cash' => '现金', 'offline' => '线下', 'credit' => '信用支付', 'advance' => '储值支付', 'union' => '平台支付', 'alipay' => '支付宝', 'kuaiqian' => '快钱');
        $detail = Order::api()->detail(array('id' => $_GET['id'], 'show_order_items' => 1));
        if ($detail['code'] == 'succ') {
            if(isset($detail['body']['remark'])){
                $detail['body']['remark'] = UbbToHtml::Entry($detail['body']['remark'], time());
            }
            $data['detail'] = $detail['body'];
            $data['ticket'] = $detail['body']['order_items'];
        }
        
        $infos = Order::api()->infos(array('id' => $_GET['id'], 'type' => 1), 0);
        if($infos['code'] == 'succ') { $data['infos'] = $infos['body']; }
        
        $this->render('detail', $data);
    }

    private function _getExcel2($expList) {
        // var_dump($expList);die;
        set_time_limit(180000);
        ini_set('memory_limit', '1024M');
        $path = YiiBase::getPathOfAlias('webroot') . '/assets';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $objPHPExcel = PHPExcel_IOFactory::load($path . '/export-template-check.xls');
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $payTypes = Pay::types();
        $last = "   总人次： " . ($expList['body']['statics']['total_nums'] ? ($expList['body']['statics']['total_nums'] - $expList['body']['statics']['total_refunded_nums']) : "0") . "   使用人次： " . ($expList['body']['statics']['total_used_nums'] ? $expList['body']['statics']['total_used_nums'] : "0") . "    总金额：￥ " . ($expList['body']['statics']['total_amount'] ? $expList['body']['statics']['total_amount'] - $expList['body']['statics']['total_refunded'] : "0");
        //print_r($expList);
        //exit;
        $row = 4;
        $setColor = 0;
        $allRow = count($expList['body']['data']);
        $last_row = $allRow + 2;
        $refunded = $payeds = $payed = $refun_nums = $used_nums = $nums = 0;
        $source = array(array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""));
        $margeArray = array();

        if (is_array($expList['body']['data'])) {
            foreach ($expList['body']['data'] as $ks => $vs) {

                ++$row;
                if ($setColor < $last_row) {
                    if (1 == $setColor % 2) {
                        $is_set = 1;
                        $sheet->duplicateStyle($sheet->getStyle('A3:O3'), 'A' . $row . ':O' . $row);
                    } else {
                        $is_set = 0;
                        $sheet->duplicateStyle($sheet->getStyle('A2:O2'), 'A' . $row . ':O' . $row);
                    }
                }
                $verify_items = isset($vs['verify_items']) ? $vs['verify_items'] : array();
                $mergeCells = count($this->array_column($verify_items, 'use_time'));
                $mergeNo[$vs['id']] = TRUE;
                $refunded += $vs['refunded'];

                if ($mergeCells > 1) {
                    $verify_num = $verify_use_time = '';
                    foreach ($verify_items as $verify_item) {
                        $verify_use_time .= date('Y-m-d', $verify_item['use_time']) . "\r\n";
                        $verify_num .= $verify_item['num'] . "\r\n";
                    }

                    $source[] = array(
                        ' ' . substr_replace($vs['id'], '********', 3, 8), // 订单号
                        $vs['name'], //=>'门票名称',
                        $vs['owner_name'], //=>'取票人',
                        substr_replace($vs['owner_mobile'], '****', 3, 4), //=>'取票人手机号',
                        date('Y-m-d', $vs['created_at']), //=>'预定日期',
                        $vs['use_day'], //=>'游玩日期',
                        empty($vs['used_nums']) ? "" : date('Y-m-d', $vs['updated_at']), //入园日期
                        $vs['nums'], //=>'预定数量',
                        ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']) == 0 ? "0" : ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']), //=>'未使用门票',
                        empty($vs['used_nums']) ? "0" : $vs['used_nums'], ////=>'已使用数量',
                        //$vs['amount']-$vs['refunded'],//=>'结算金额'
                        (empty($payTypes[$vs['pay_type']]) ? '' : $payTypes[$vs['pay_type']]), //=>'支付方式',
                        $vs['amount'], //=>'支付金额',
                        $expList['status_labels'][$vs['status']], //=>'订单状态',
                        //!empty($expList['landscape_labels'])?$expList['landscape_labels'][$vs['landscape_ids']]:"",// 景区
                        $vs['distributor_name'], // 分销商
                        $vs['remark'], //备注
                    );
                } elseif ($mergeCells == 1) {

                    $source[] = array(
                        ' ' . substr_replace($vs['id'], '********', 3, 8), // 订单号
                        $vs['name'], //=>'门票名称',
                        $vs['owner_name'], //=>'取票人',
                        substr_replace($vs['owner_mobile'], '****', 3, 4), //=>'取票人手机号',
                        date('Y-m-d', $vs['created_at']), //=>'预定日期',
                        $vs['use_day'], //=>'游玩日期',
                        empty($vs['used_nums']) ? "" : date('Y-m-d', $vs['updated_at']), //入园日期
                        $vs['nums'], //=>'预定数量',
                        ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']) == 0 ? "0" : ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']),
                        empty($vs['used_nums']) ? "0" : $vs['used_nums'], ////=>'已使用数量',
                        (empty($payTypes[$vs['pay_type']]) ? '' : $payTypes[$vs['pay_type']]), //=>'支付方式',
                        $vs['amount'], //=>'支付金额',
                        $expList['status_labels'][$vs['status']], //=>'订单状态',
                        //!empty($expList['landscape_labels'])?$expList['landscape_labels'][$vs['landscape_ids']]:"",// 景区
                        $vs['distributor_name'], // 分销商
                        $vs['remark'], //备注
                    );
                } else {

                    $source[] = array(
                        ' ' . substr_replace($vs['id'], '********', 3, 8), // 订单号
                        $vs['name'], //=>'门票名称',
                        $vs['owner_name'], //=>'取票人',
                        substr_replace($vs['owner_mobile'], '****', 3, 4), //=>'取票人手机号',
                        date('Y-m-d', $vs['created_at']), //=>'预定日期',
                        $vs['use_day'], //=>'游玩日期'
                        empty($vs['used_nums']) ? "" : date('Y-m-d', $vs['updated_at']), //入园日期
                        $vs['nums'], //=>'预定数量',
                        ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']) == 0 ? "0" : ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']), //=>'未使用门票',
                        empty($vs['used_nums']) ? "0" : $vs['used_nums'], ////=>'已使用数量',
                        (empty($payTypes[$vs['pay_type']]) ? '' : $payTypes[$vs['pay_type']]), //=>'支付方式',
                        $vs['amount'], //=>'支付金额',
                        // $vs['amount']-$vs['refunded'],//=>'结算金额'
                        $expList['status_labels'][$vs['status']], //=>'订单状态',
                        //!empty($expList['landscape_labels'])?$expList['landscape_labels'][$vs['landscape_ids']]:"",// 景区
                        $vs['distributor_name'], // 分销商
                        $vs['remark']//备注
                    );
                }
                unset($expList['body']['data'][$ks]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                if (!empty($vs['remark']) && strlen($vs['remark']) > 25) {
                    $sheet->getRowDimension($row)->setRowHeight(60);
                }
                ++$setColor;
            }
        }

        $sheet->fromArray($source, null, 'A4');
        unset($source);
        $sheet->removeRow(2, 3);



        $allRow = $row;
        $last = "订单数： " . ($expList['body']['statics']['order_nums']) . $last;


        $sheet->mergeCells('A' . $allRow . ':U' . $allRow);
        $sheet->setCellValue('A' . $allRow, $last);
        unset($expList);
        // $sheet->getStyle('G2:G'.$row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('H2:H' . $row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('O2:O' . $row)->getAlignment()->setWrapText(true);


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $filename = '订单导出' . date('YmdHis') . '.xls';
        $filename = mb_convert_encoding($filename, 'gbk', 'utf-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename=" . $filename);
        header("Content-Transfer-Encoding:binary");
        $objWriter->save("php://output");
        exit();
    }

    protected function array_column($input, $columnKey, $indexKey = null) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
        $indexKeyIsNull = (is_null($indexKey)) ? true : false;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
        $result = array();
        foreach ((array) $input as $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
            }
            if (!$indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key)) ? current($key) : null;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }

}
