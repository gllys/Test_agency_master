<?php
use common\huilian\models\Pay;
class HistoryController extends Controller {

    public function actionView($is_export = false) {
        $this->actionIndex($is_export);
    }

    public function actionIndex($is_export = false) {
        $params = $_REQUEST;
        //$data['source_labels'] = array(0=>'默认',1=>'淘宝', 2=>'八爪鱼', 3=>'同程', 4=>'途牛', 5=>'驴妈妈', 6=>'携程', 7=>'景点通', 8=>'度周末', 9=>'途家');
        $data['source_labels'] = array(0=>'票台',1=>'淘宝',);
        $data['status_labels'] = array('unaudited' => '待确认', 'reject' => '驳回', 'unpaid' => '未支付', 'cancel' => '已取消', 'paid' => '已付款', 'finish' => '已结束', 'billed' => '已结款');
        $data['status_class'] = array('unaudited' => 'warning', 'reject' => 'danger', 'unpaid' => 'danger', 'cancel' => 'warning', 'paid' => 'success', 'finish' => 'info', 'billed' => 'error');
        $data['status'] = array_keys($data['status_labels']);
        if (!empty($params)) {
            if (isset($params['status']) && !in_array($params['status'], $data['status'])) {
                unset($params['status']);
            }
        }
        if(!(isset($params['source']) && is_numeric($params['source']))) {
            unset($params['source']);
        }

        $org_id = Yii::app()->user->org_id;
        // 获取景区id和名字的数组
        $rs = TicketTemplate::api()->reserve_list(
            array('agency_id' => $org_id, 'show_scenic_name' => 1, 'fields' => 'scenic_id', 'items' => 1000)
        );
        $landscapeDatas = ApiModel::getLists($rs);
        $landscapes = array();
        foreach ($landscapeDatas as $v) {
            $landscapes[$v['scenic_id']] = $v['scenic_name'];
        }
        $data['landscape_labels'] = $landscapes;
        $data['payTypes'] = Pay::types();
        $data['get'] = $params;
        //var_dump($data['get']);exit;
        $params['distributor_id'] = $org_id;
        $params['type'] = 0;
        $params['current'] = isset($params['page']) ? $params['page'] : 1;
        $params['items'] = 20;
        $params['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
        if (isset($params['ticket_name'])) {
            $params['product_name'] = $params['ticket_name'];
        }
        //print_r($params);exit;
        //$result = Order::api()->lists($params, 0);
        //print_r($result);exit;
       $data = $this->getApiLists($params,$is_export,$data);
       if ($data['lists']["result"]['code'] == 'succ') {
            //20150215 拷贝supply订单导出代码
            if ($is_export==false) {
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
//                $pages = ceil($data['lists']['pagination']['count'] / 3000);
//                $expList = array();
//                $time = 0;
//                while (true) {
//                    $params['items'] = 3000;
//                    $params['show_verify_items'] = 1;
//                    $temp = Order::api()->lists($params);
//                    if (!$time) {
//                        $expList = $temp;
//                        $time++;
//                    } else {
//                        foreach ($temp['body']['data'] as $key => $value) {
//                            $expList['body']['data'][] = $value;
//                            unset($temp['body']['data'][$key]);
//                        }
//                    }
//                    if ($pages == $params['current']) {
//                        break;
//                    }
//                    $params['current'] = $params['current'] + 1;
//                }
//                $expList['landscape_labels'] = $data['landscape_labels'];
//                $expList['status_labels'] = $data['status_labels'];
//                unset($data);
//                $this->_getExcel2($expList);
            }
        }
        
        $data['timeTypes'] = array(
          '预订日期',
          '游玩日期',
          '入园日期',
        );
        $data['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
        
        $this->render('index', $data);
    }
    
    private function getApiLists($params,$is_export,$data)
    {
        $d = array();
        $pagination =null;
        $result = null;
        $num = 0;
        
        if($is_export)
        {
            $this->renderPartial("excelTop",$data);
            $params['show_verify_items'] = 1;
            $params["items"] = 1000;
        }
        
        do{
            if($result)
            {
                unset($result);
            }
            $result = Order::api()->lists($params);
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            
            if($result['code'] == 'succ')
            {
                
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data"=>$result['body']["data"],"statics"=>$result['body']["statics"],"pagination"=>$pagination,"result"=>$result);
               
                if($is_export)
                {
                    $this->renderPartial("excelBody",$data);
                }
                
                $num += count($data['lists']["data"]);
            }
         }while($params["current"]<1000 && $is_export==true && $result['code'] == 'succ' && empty($pagination)==false && $pagination['current']<$pagination['total']);
         if($is_export==true)
         {
             $data["num"] = $num;
            $this->renderPartial("excelBottom",$data);
            exit;
          }
         return $data;
    }
    

    /**
     * 导出excel函数
     * @param array $expList 需要导出的数据
     */
    private function _getExcel2($expList) {
        // var_dump($expList);die;
        set_time_limit(180000);
        ini_set('memory_limit', '10240M');
        $path = YiiBase::getPathOfAlias('webroot') . '/assets';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $objPHPExcel = PHPExcel_IOFactory::load($path . '/export-template.xls');
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $payment = array(
            'cash' => '现金支付',
            'offline' => '线下支付',
            'credit' => '信用支付',
            'pos' => 'pos机支付',
            'alipay' => '支付宝支付',
            'advance' => '储值支付',
            'union' => '平台支付',
            'kuaiqian' => '快钱支付',
            'taobao' => '淘宝支付',
        );
        $row = 3;
        $setColor = 0;
        $allRow = count($expList['body']['data']);
        $last_row = $allRow + 2;
        $refunded = $payeds = $payed = $refun_nums = $used_nums = $nums = 0;
        $source = array();
        $margeArray = array();
        if (is_array($expList['body']['data'])) {
            foreach ($expList['body']['data'] as $ks => $vs) {
                ++$row;
                if ($setColor < $last_row) {
                    if (0 == $setColor % 2) {
                        $is_set = 1;
                        $sheet->duplicateStyle($sheet->getStyle('A3:V3'), 'A' . $row . ':V' . $row);
                    } else {
                        $is_set = 0;
                        $sheet->duplicateStyle($sheet->getStyle('A2:V2'), 'A' . $row . ':V' . $row);
                    }
                }
                $verify_items = isset($vs['verify_items']) ? $vs['verify_items'] : array();
                // $verify_items = array(
                //   array('use_time'=>'14440000091','num'=>1),
                //   array('use_time'=>'14440000091','num'=>1),
                //   array('use_time'=>'14440000091','num'=>1),
                //   // array('use_time'=>'14440000091','num'=>1),
                //   // array('use_time'=>'14440000091','num'=>1),
                //   );
                // $verify_items = rand(0,1) ? array(): $verify_items ;
                // $verify_items = rand(0,1) ? $verify_items: array(array('use_time'=>'14440000091','num'=>1)) ;
                $mergeCells = count($this->array_column($verify_items, 'use_time'));
                $mergeNo[$vs['id']] = TRUE;
                $refunded += $vs['refunded'];
                if ($mergeCells > 1) {
                    $margeArray[] = array(
                        'start' => $row,
                        'end' => $row + $mergeCells - 1
                    );
                    $first = 1;
                    foreach ($verify_items as $verify_item) {
                        if ($first) {
                            $source[] = array(
                                ' ' . $vs['id'], // 订单号
                                $vs['distributor_name'], // 分销商
                                $this->_getLandscape($vs['landscape_ids'], $expList['landscape_labels']), // 景区
                                $vs['name'], //=>'门票名称',
                                $vs['nums'], //=>'预定数量',
                                $vs['used_nums'], ////=>'已使用数量',
                                date('Y-m-d', $verify_item['use_time']), // $vs['verify_data'],//=>'验证日期',
                                $verify_item['num'], // $vs['verify_nums'],//=>'验证数量',
                                $vs['refunding_nums'], //=>'退票中的数量',
                                $vs['refunded_nums'], //=>'退票数量',
                                (string) ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']), //=>'未使用门票',
                                $vs['amount'], //=>'支付金额',
                                (string) ($vs['amount'] - $vs['refunded']), //=>'结算金额'              
                                !empty($vs['payment']) ? $payment[$vs['payment']] : ' ', //=>'支付方式',
                                $expList['status_labels'][$vs['status']], //=>'订单状态',
                                $vs['use_day'], //=>'游玩日期',
                                $vs['valid'], //=>'门票有效期(天)',
                                $vs['owner_name'], //=>'取票人',
                                $vs['owner_mobile'], //=>'取票人手机号',
                                $vs['owner_card'], //=>'取票人身份证号码',
                                date('Y-m-d', $vs['created_at']), //=>'预定日期',
                                $vs['remark'], //=> '备注',
                            );
                            $first = 0;
                        } else {
                            ++$row;
                            $source[] = array(
                                ' ', // 订单号
                                ' ', // 分销商
                                ' ', // 景区
                                ' ', //=>'门票名称',
                                ' ', //=>'预定数量',
                                ' ', ////=>'已使用数量',
                                date('Y-m-d', $verify_item['use_time']), // $vs['verify_data'],//=>'验证日期',
                                $verify_item['num'], // $vs['verify_nums'],//=>'验证数量',
                                ' ', //=>'退票中的数量',
                                ' ', //=>'退票数量',
                                ' ', //=>'未使用门票',
                                ' ', //=>'支付金额',
                                ' ', //=>'结算金额'              
                                ' ', //=>'支付方式',
                                ' ', //=>'订单状态',
                                ' ', //=>'游玩日期',
                                ' ', //=>'门票有效期(天)',
                                ' ', //=>'取票人',
                                ' ', //=>'取票人手机号',
                                ' ', //=>'取票人身份证号码',
                                ' ', //=>'预定日期',
                                ' ', //=> '备注',
                            );
                            if ($is_set) {
                                $sheet->duplicateStyle($sheet->getStyle('A3:V3'), 'A' . $row . ':V' . $row);
                            } else {
                                $sheet->duplicateStyle($sheet->getStyle('A2:V2'), 'A' . $row . ':V' . $row);
                            }
                        }
                    }
                } elseif ($mergeCells == 1) {
                    $source[] = array(
                        ' ' . $vs['id'], // 订单号
                        $vs['distributor_name'], // 分销商
                        $this->_getLandscape($vs['landscape_ids'], $expList['landscape_labels']), // 景区
                        $vs['name'], //=>'门票名称',
                        $vs['nums'], //=>'预定数量',
                        $vs['used_nums'], ////=>'已使用数量',
                        date('Y-m-d', $verify_items['0']['use_time']), // $vs['verify_data'],//=>'验证日期',
                        $verify_items[0]['num'], // $vs['verify_nums'],//=>'验证数量',
                        $vs['refunding_nums'], //=>'退票中的数量',
                        $vs['refunded_nums'], //=>'退票数量',
                        (string) ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']), //=>'未使用门票',
                        $vs['amount'], //=>'支付金额',
                        (string) ($vs['amount'] - $vs['refunded']), //=>'结算金额'              
                        !empty($vs['payment']) ? $payment[$vs['payment']] : ' ', //=>'支付方式',
                        $expList['status_labels'][$vs['status']], //=>'订单状态',
                        $vs['use_day'], //=>'游玩日期',
                        $vs['valid'], //=>'门票有效期(天)',
                        $vs['owner_name'], //=>'取票人',
                        $vs['owner_mobile'], //=>'取票人手机号',
                        $vs['owner_card'], //=>'取票人身份证号码',
                        date('Y-m-d', $vs['created_at']), //=>'预定日期',
                        $vs['remark'], //=> '备注',
                    );
                } else {
                    $source[] = array(
                        ' ' . $vs['id'], // 订单号
                        $vs['distributor_name'], // 分销商
                        $this->_getLandscape($vs['landscape_ids'], $expList['landscape_labels']), // 景区
                        $vs['name'], //=>'门票名称',
                        $vs['nums'], //=>'预定数量',
                        $vs['used_nums'], ////=>'已使用数量',
                        ' ', // $vs['verify_data'],//=>'验证日期',
                        ' ', // $vs['verify_nums'],//=>'验证数量',
                        $vs['refunding_nums'], //=>'退票中的数量',
                        $vs['refunded_nums'], //=>'退票数量',
                        (string) ($vs['nums'] - $vs['used_nums'] - $vs['refunding_nums'] - $vs['refunded_nums']), //=>'未使用门票',
                        $vs['amount'], //=>'支付金额',
                        (string) ($vs['amount'] - $vs['refunded']), //=>'结算金额'              
                        !empty($vs['payment']) ? $payment[$vs['payment']] : ' ', //=>'支付方式',
                        $expList['status_labels'][$vs['status']], //=>'订单状态',
                        $vs['use_day'], //=>'游玩日期',
                        $vs['valid'], //=>'门票有效期(天)',
                        $vs['owner_name'], //=>'取票人',
                        $vs['owner_mobile'], //=>'取票人手机号',
                        $vs['owner_card'], //=>'取票人身份证号码',
                        date('Y-m-d', $vs['created_at']), //=>'预定日期',
                        $vs['remark'], //=> '备注',
                    );
                }
                unset($expList['body']['data'][$ks]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                ++$setColor;
            }
        }
        unset($expList);
        $sheet->fromArray($source, null, 'A4');
        unset($source);
        foreach ($margeArray as $key => $marge) {
            $sheet->mergeCells('A' . $marge['start'] . ':' . 'A' . $marge['end']);
            $sheet->mergeCells('B' . $marge['start'] . ':' . 'B' . $marge['end']);
            $sheet->mergeCells('C' . $marge['start'] . ':' . 'C' . $marge['end']);
            $sheet->mergeCells('D' . $marge['start'] . ':' . 'D' . $marge['end']);
            $sheet->mergeCells('E' . $marge['start'] . ':' . 'E' . $marge['end']);
            $sheet->mergeCells('F' . $marge['start'] . ':' . 'F' . $marge['end']);
            $sheet->mergeCells('I' . $marge['start'] . ':' . 'I' . $marge['end']);
            $sheet->mergeCells('J' . $marge['start'] . ':' . 'J' . $marge['end']);
            $sheet->mergeCells('K' . $marge['start'] . ':' . 'K' . $marge['end']);
            $sheet->mergeCells('L' . $marge['start'] . ':' . 'L' . $marge['end']);
            $sheet->mergeCells('M' . $marge['start'] . ':' . 'M' . $marge['end']);
            $sheet->mergeCells('N' . $marge['start'] . ':' . 'N' . $marge['end']);
            $sheet->mergeCells('O' . $marge['start'] . ':' . 'O' . $marge['end']);
            $sheet->mergeCells('P' . $marge['start'] . ':' . 'P' . $marge['end']);
            $sheet->mergeCells('Q' . $marge['start'] . ':' . 'Q' . $marge['end']);
            $sheet->mergeCells('R' . $marge['start'] . ':' . 'R' . $marge['end']);
            $sheet->mergeCells('S' . $marge['start'] . ':' . 'S' . $marge['end']);
            $sheet->mergeCells('T' . $marge['start'] . ':' . 'T' . $marge['end']);
            $sheet->mergeCells('U' . $marge['start'] . ':' . 'U' . $marge['end']);
            $sheet->mergeCells('V' . $marge['start'] . ':' . 'V' . $marge['end']);
        }
        $sheet->removeRow(2, 2);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        // $last = '合计：共';
        // $last .= $allRow;
        // $last .= '条订单      ';
        // $last .= '门票预定数量:';
        // $last .= $nums;
        // $last .= '      已使用门票数量:';
        // $last .= $used_nums;
        // $last .= '      退票数量:';
        // $last .= $refun_nums;
        // $last .= '      支付金额:';
        // $last .= '￥'.$payed;
        // $last .= '      退款金额:';
        // $last .= '￥'.$refunded;
        // $last .= '      结算金额:';
        // $last .= '￥'.$payeds;
        // $allRow = $row;
        //底部信息
        // $objPHPExcel->getActiveSheet()->mergeCells('A'.$allRow.':U'.$allRow);
        // $objPHPExcel->getActiveSheet()->setCellValue('A'.$allRow,$last);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$allRow,$last)->applyFromArray($headerStyle);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$allRow.':U'.$allRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$allRow.':U'.$allRow)->getFill()->getStartColor()->setARGB('FF31869B');
        // $objPHPExcel->getActiveSheet()->getRowDimension($allRow)->setRowHeight(24);   
        // $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(24);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$allRow.':U'.$allRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$allRow.':U'.$allRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // $objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
        // $objPHPExcel->getActiveSheet()->getStyle('C2:C'.$allRow)->getAlignment()->setWrapText(true);
        // $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // $objPHPExcel->getActiveSheet()->freezePane('A2');
        // $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(17);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(22);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(12);
        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // 从浏览器直接输出$filename
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

    private function _getLandscape($landscape_ids, $landscape_labels) {
        $landscapeArr = explode(',', $landscape_ids);
        $comma = 0;
        $columValue = '';
        foreach ($landscapeArr as $landscapeId) {
            if (isset($landscape_labels[$landscapeId])) {
                $columValue .= $landscape_labels[$landscapeId];
                $columValue .= $comma ? ',' : '';
                $comma++;
            }
        }
        return $columValue;
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
