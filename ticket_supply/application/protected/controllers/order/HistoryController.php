<?php

use common\huilian\utils\Header;
use common\huilian\models\Pay;

class HistoryController extends Controller {

    public function actionView($is_export = false) {
      $this->actionIndex($is_export);
    }
   
    public function actionIndex($is_export = false) {
        $params = PublicFunHelper::filter($_GET);
        //查看是否是景区身份
        $criteria = new CDbCriteria();
        $criteria->order = 'status DESC,id DESC'; 
        $criteria->select = "sell_role";
        $criteria->compare('id', Yii::app()->user->uid);
        $lists = Users::model()->find($criteria);
        
         $data['sell_role'] = $lists->sell_role ;
        //景区供应商和非景区共应商显示不同
        if ($lists->sell_role == "scenic") {
            //二级菜单配置
            $data['menus'] = array('all' => array('title' => '全部订单'),
                'verify' => array('title' => '审核订单'),
                'paid' => array('title' => '未使用订单'),
                'refund' => array('title' => '退款订单'),
                'bill' => array('title' => '已使用订单'),
            );
        } else {
            //二级菜单配置
            $data['menus'] = array('all' => array('title' => '全部订单', 'stauts' => ''),
                'paid' => array('title' => '未使用订单'),
                'refund' => array('title' => '退款订单'),
                'bill' => array('title' => '已使用订单'),
            );
        }

        //状态配置
        $data['status_labels'] = array("unaudited"=>"待确认","reject"=>"已驳回",'unpaid'=>'未支付','cancel' => '已取消','paid' => '已支付','finish' => '已完成','billed' => '已结款');
        $data['status_class'] = array("unaudited"=>"info","reject"=>"danger",'unpaid' => 'danger', 'cancel' => 'warning', 'paid' => 'success', 'finish' => 'info', 'billed' => 'error');
        $data['status'] = array_keys($data['status_labels']);
        
        $org_id = Yii::app()->user->org_id;

        // 获取景区id和名字的数组
        $landscapeDatas = Landscape::api()->supplyLan();
        $landscapes = array();
        foreach ($landscapeDatas as $v) {
            $landscapes[$v['id']] = $v['name'];
        }
        $data['landscape_labels'] = $landscapes;
        
        //获取支付类型配置
        $data['payTypes'] = Pay::types();
        
        //获取所有游玩类型配置
        $data['timeTypes'] = array(
          '预订日期',
          '游玩日期',
          '入园日期',
        );
        $data['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
        
        
        // 获取分销商 id=>name
        //Credit::api()->debug = true;
        $rs = Credit::api()->lists(array('supplier_id' => $org_id, 'fields' => 'distributor_id,distributor_name', 'items' => 1000),true);
        $distributorDatas = ApiModel::getLists($rs);
        $distributors = array();
        foreach ($distributorDatas as $v) {
            $distributors[$v['distributor_id']] = $v['distributor_name'];
        }
        $data['distributors_labels'] = $distributors;
        
        if (!empty($params)) {
            if (isset($params['status']) && !in_array($params['status'], $data['status'])) {
                unset($params['status']);
            }
        }

        $data['get'] = $params;
    
        if (intval($org_id) > 0) {
            //得到查询数据
            if(!empty($params['menu'])&&$params['menu']=='verify'){
                $params['statuses'] = 'unaudited,reject' ;
            }elseif(!empty($params['menu'])&&$params['menu']=='paid'){
                $params['status'] = 'unused' ;
            }elseif (!empty($params['menu']) && $params['menu'] == 'bill') {
                $params['status'] = 'used' ;
            }
            
            $params['current'] = isset($params['page']) ? $params['page'] : 1;
            
            $params['items'] = $is_export==true?1000:20;
            
            $params['type'] = 0;
            $params['supplier_id'] = $org_id;
            $params['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
            $data = $this->getApiLists($params,$is_export,$data);
            
            if ($data['lists']["result"]['code'] == 'succ') {
                //$data['lists'] = $result['body'];
                if($is_export==false)
                {
                    $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                    $data['pages']->pageSize = $params['items'];
                }
            }
        }

        $this->render('index', $data);
    }
    
    private function getApiLists($params,$is_export,$data)
    {
        $d = array();
        $pagination =null;
        $result = null;
        $num = 0;
        $params['show_verify_items'] = 1;
        if($is_export)
        {
            $this->renderPartial("excelTop",$data);
        }
        
        do{
            if($result)
            {
                unset($result);
            }
            
            if (!empty($params['menu']) && $params['menu'] == 'refund') {
                $result = Refund::api()->order($params);
            } else{
                $result = Order::api()->lists($params);
            }
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            if($result['code'] == 'succ')
            {
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data"=>$result['body']["data"],"statics"=>$result['body']["statics"],"pagination"=>$pagination,"result"=>$result);
                
                // 获取景区id和名字的数组
                $lans = $data['lists']['data'];
                $lanIds = PublicFunHelper::arrayIds($lans, 'landscape_ids');
                $landscapeDatas =  Landscape::api()->getSimpleByIds($lanIds, true);
                $landscapes = array();
                foreach ($landscapeDatas as $v) {
                    $landscapes[$v['id']] = $v['name'];
                }
                $data['landscape_lists'] = $landscapes;
                
                if($is_export)
                {
                    $this->renderPartial("excelBody",$data);
                }
                $num += count($data['lists']["data"]);
            }
         }while($params["current"]<1000 && $is_export==true && $result['code'] == 'succ' && $pagination['current']<$pagination['total']);
         if($is_export==true)
         {
             $data["num"] = $num;
            $this->renderPartial("excelBottom",$data);
            exit;
          }
         return $data;
    }
            
    
    private function _getExcel2($expList){
      // var_dump($expList);die;
      set_time_limit(180000) ;
      ini_set('memory_limit', '1024M');
      $path = YiiBase::getPathOfAlias('webroot') .'/assets';
      if (!is_dir($path)) {
          mkdir($path,0755,true);
      }
      $objPHPExcel = PHPExcel_IOFactory::load($path .'/export-template.xls');
      $objPHPExcel->setActiveSheetIndex(0);
      $sheet = $objPHPExcel->getActiveSheet();
      $payTypes = Pay::types();
      $last = "   总人次： ".($expList['body']['statics']['total_nums']?($expList['body']['statics']['total_nums']-$expList['body']['statics']['total_refunded_nums']):"0")."   使用人次： ".($expList['body']['statics']['total_used_nums']?$expList['body']['statics']['total_used_nums']:"0")."    总金额：￥ ".($expList['body']['statics']['total_amount']?$expList['body']['statics']['total_amount']-$expList['body']['statics']['total_refunded']:"0");
      //print_r($expList);
      //exit;
      $row = 4;
      $setColor = 0;
      $allRow = count($expList['body']['data']);
      $last_row = $allRow + 2;
      $refunded = $payeds = $payed = $refun_nums = $used_nums = $nums = 0;
      $source = array(array("","","","","","","","","","","","","","","","",""));
      $margeArray = array();
      
      if(is_array($expList['body']['data'])){
        foreach ($expList['body']['data'] as $ks => $vs) {
            
          ++$row;          
          if($setColor<$last_row){
            if(1 == $setColor%2){
              $is_set = 1;
              $sheet->duplicateStyle($sheet->getStyle('A3:P3'),'A'.$row.':P'.$row);
            }else{
              $is_set = 0;              
              $sheet->duplicateStyle($sheet->getStyle('A2:P2'),'A'.$row.':P'.$row);
            }
          }
          $verify_items = isset($vs['verify_items']) ? $vs['verify_items'] : array();
          // $verify_items = array(
          //   array('use_time'=>'14440000091','num'=>1),
          //   array('use_time'=>'14420003451','num'=>3),
          //   array('use_time'=>'14440010091','num'=>5),
          //   // array('use_time'=>'14440000091','num'=>1),
          //   // array('use_time'=>'14440000091','num'=>1),
          //   );
          // $verify_items = rand(0,1) ? array(): $verify_items ;
          // $verify_items = rand(0,1) ? $verify_items: array(array('use_time'=>'14440000091','num'=>1)) ;
          $mergeCells = count($this->array_column($verify_items,'use_time'));
          $mergeNo[$vs['id']] = TRUE;
          $refunded += $vs['refunded'];
          
          if($mergeCells > 1){
            $verify_num = $verify_use_time = '';
            foreach ($verify_items as $verify_item) {
                $verify_use_time .= date('Y-m-d',$verify_item['use_time'])."\r\n";
                $verify_num .= $verify_item['num']."\r\n";
            }
            
            $source[] = array(
              ' '.$vs['id'], // 订单号
              $vs['name'], //=>'门票名称',
              
//              
//              $verify_use_time,// $vs['verify_data'],//=>'验证日期',
//              $verify_num,// $vs['verify_nums'],//=>'验证数量',
//              empty($vs['refunding_nums']) ? $vs['refunding_nums'] : 0,//=>'退票中的数量',
//              empty($vs['refunded_nums']) ? $vs['refunded_nums']: 0,//=>'退票数量',
//              
//              //$vs['amount'],//=>'支付金额',
//                           
//              //$payment[$vs['payment']],
//              
//              //  "xxx",pay_type
//              
//              
//              $vs['valid'],//=>'门票有效期(天)',
                
                $vs['owner_name'],//=>'取票人',
                $vs['owner_mobile'],//=>'取票人手机号',
                date('Y-m-d',$vs['created_at']),//=>'预定日期',
                $vs['use_day'],//=>'游玩日期',
                empty($vs['used_nums'])?"":date('Y-m-d',$vs['updated_at']),//入园日期
                $vs['nums'],//=>'预定数量',
                ($vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums'])==0?"0":($vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums']),//=>'未使用门票',
                empty($vs['used_nums']) ?"0": $vs['used_nums'],////=>'已使用数量',
                //$vs['amount']-$vs['refunded'],//=>'结算金额' 
                (empty($payTypes[$vs['pay_type']]) ? '' : $payTypes[$vs['pay_type']]),//=>'支付方式',
                $vs['amount'],//=>'支付金额',
                
                $expList['status_labels'][$vs['status']],//=>'订单状态',
                !empty($expList['landscape_labels'])?$expList['landscape_labels'][$vs['landscape_ids']]:"",// 景区
                
              $vs['distributor_name'],// 分销商
                $vs['remark'], //=> '备注',
              );

            // $margeArray[] = array(
            //   'start' => $row,
            //   'end'   => $row+$mergeCells-1
            //   );
            // $first = 1;
            // foreach ($verify_items as $verify_item) {
            //   if($first){
            //     $source[] = array(
            //       ' '.$vs['id'], // 订单号
            //       $vs['distributor_name'],// 分销商
            //       $vs['landscape_ids'], // 景区
            //       $vs['name'], //=>'门票名称',
            //       $vs['nums'],//=>'预定数量',
            //       empty($vs['used_nums']) ? $vs['used_nums'] : 0,////=>'已使用数量',
            //       date('Y-m-d',$verify_item['use_time']),// $vs['verify_data'],//=>'验证日期',
            //       $verify_item['num'],// $vs['verify_nums'],//=>'验证数量',
            //       empty($vs['refunding_nums']) ? $vs['refunding_nums'] : 0,//=>'退票中的数量',
            //       empty($vs['refunded_nums']) ? $vs['refunded_nums']: 0,//=>'退票数量',
            //       $vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums'],//=>'未使用门票',
            //       $vs['amount'],//=>'支付金额',
            //       $vs['amount']-$vs['refunded'],//=>'结算金额'              
            //       $payment[$vs['payment']],//=>'支付方式',
            //       $expList['status_labels'][$vs['status']],//=>'订单状态',
            //       $vs['use_day'],//=>'游玩日期',
            //       $vs['valid'],//=>'门票有效期(天)',
            //       $vs['owner_name'],//=>'取票人',
            //       $vs['owner_mobile'],//=>'取票人手机号',
            //       $vs['owner_card'],//=>'取票人身份证号码',
            //       date('Y-m-d',$vs['created_at']),//=>'预定日期',
            //       $vs['remark'],//=> '备注',
            //     );
            //     $first = 0;
            //   }else{
            //     ++$row;
            //     $source[] = array(
            //       ' ', // 订单号
            //       ' ',// 分销商
            //       ' ', // 景区
            //       ' ', //=>'门票名称',
            //       ' ',//=>'预定数量',
            //       ' ',////=>'已使用数量',
            //       date('Y-m-d',$verify_item['use_time']),// $vs['verify_data'],//=>'验证日期',
            //       $verify_item['num'],// $vs['verify_nums'],//=>'验证数量',
            //       ' ',//=>'退票中的数量',
            //       ' ',//=>'退票数量',
            //       ' ',//=>'未使用门票',
            //       ' ',//=>'支付金额',
            //       ' ',//=>'结算金额'              
            //       ' ',//=>'支付方式',
            //       ' ',//=>'订单状态',
            //       ' ',//=>'游玩日期',
            //       ' ',//=>'门票有效期(天)',
            //       ' ',//=>'取票人',
            //       ' ',//=>'取票人手机号',
            //       ' ',//=>'取票人身份证号码',
            //       ' ',//=>'预定日期',
            //       ' ',//=> '备注',
            //     );
            //     if($is_set){
            //       $sheet->duplicateStyle($sheet->getStyle('A3:V3'),'A'.$row.':V'.$row);
            //     }else{
            //       $sheet->duplicateStyle($sheet->getStyle('A2:V2'),'A'.$row.':V'.$row);
            //     }
            //   }
            // }
          }elseif($mergeCells == 1){
              
            $source[] = array(
              ' '.$vs['id'], // 订单号
              
              $vs['name'], //=>'门票名称',
              
//              
//              date('Y-m-d',$verify_items['0']['use_time']),// $vs['verify_data'],//=>'验证日期',
//              $verify_items[0]['num'],// $vs['verify_nums'],//=>'验证数量',
//              empty($vs['refunding_nums']) ? $vs['refunding_nums'] : 0,//=>'退票中的数量',
//              empty($vs['refunded_nums']) ? $vs['refunded_nums']: 0,//=>'退票数量',
//              
//              
//                          
//              //$payment[$vs['payment']],
//              
//              //  "xxx",
//              
//              
//              $vs['valid'],//=>'门票有效期(天)',
               $vs['owner_name'],//=>'取票人',
              $vs['owner_mobile'],//=>'取票人手机号', 
              date('Y-m-d',$vs['created_at']),//=>'预定日期',
                $vs['use_day'],//=>'游玩日期',
                empty($vs['used_nums'])?"":date('Y-m-d',$vs['updated_at']),//入园日期
                $vs['nums'],//=>'预定数量',
                ($vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums'])==0?"0":($vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums']),
                empty($vs['used_nums']) ?"0": $vs['used_nums'],////=>'已使用数量',
                (empty($payTypes[$vs['pay_type']]) ? '' : $payTypes[$vs['pay_type']]),//=>'支付方式',
               // $vs['amount']-$vs['refunded'],//=>'结算金额' 
                $vs['amount'],//=>'支付金额',
                $expList['status_labels'][$vs['status']],//=>'订单状态',
                !empty($expList['landscape_labels'])?$expList['landscape_labels'][$vs['landscape_ids']]:"",// 景区
                $vs['distributor_name'],// 分销商
                $vs['remark'], //=> '备注',
            );
          }else{
              
            $source[] = array(
              ' '.$vs['id'], // 订单号
              
              
              $vs['name'], //=>'门票名称',
              
//              
//              ' ',// $vs['verify_data'],//=>'验证日期',
//              ' ',// $vs['verify_nums'],//=>'验证数量',
//              empty($vs['refunding_nums']) ? $vs['refunding_nums'] : 0,//=>'退票中的数量',
//              empty($vs['refunded_nums']) ? $vs['refunded_nums']: 0,//=>'退票数量',
//              
//              
//                           
//              //$payment[$vs['payment']],//=>'支付方式',
//              
//              //  "xxx",
//              
//              
//              $vs['valid'],//=>'门票有效期(天)',
              
                
              $vs['owner_name'],//=>'取票人',
              $vs['owner_mobile'],//=>'取票人手机号',  
              date('Y-m-d',$vs['created_at']),//=>'预定日期',
                $vs['use_day'],//=>'游玩日期'
                empty($vs['used_nums'])?"":date('Y-m-d',$vs['updated_at']),//入园日期
                $vs['nums'],//=>'预定数量',
                ($vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums'])==0?"0":($vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums']),//=>'未使用门票',
                empty($vs['used_nums']) ?"0": $vs['used_nums'],////=>'已使用数量',
                (empty($payTypes[$vs['pay_type']]) ? '' : $payTypes[$vs['pay_type']]),//=>'支付方式',
                $vs['amount'],//=>'支付金额',
               // $vs['amount']-$vs['refunded'],//=>'结算金额' 
                $expList['status_labels'][$vs['status']],//=>'订单状态',
              !empty($expList['landscape_labels'])?$expList['landscape_labels'][$vs['landscape_ids']]:"",// 景区
              $vs['distributor_name'],// 分销商
                $vs['remark'], //=> '备注',
            );
          }
          unset($expList['body']['data'][$ks]);
          $sheet->getRowDimension($row)->setRowHeight(20);          
          ++$setColor;
        }
      }
      
      $sheet->fromArray($source, null, 'A4');
      unset($source);      
      // foreach ($margeArray as $key => $marge) {
      //   $sheet->mergeCells('A'.$marge['start'].':'.'A'.$marge['end']);
      //   $sheet->mergeCells('B'.$marge['start'].':'.'B'.$marge['end']);
      //   $sheet->mergeCells('C'.$marge['start'].':'.'C'.$marge['end']);
      //   $sheet->mergeCells('D'.$marge['start'].':'.'D'.$marge['end']);
      //   $sheet->mergeCells('E'.$marge['start'].':'.'E'.$marge['end']);
      //   $sheet->mergeCells('F'.$marge['start'].':'.'F'.$marge['end']);
      //   $sheet->mergeCells('I'.$marge['start'].':'.'I'.$marge['end']);
      //   $sheet->mergeCells('J'.$marge['start'].':'.'J'.$marge['end']);
      //   $sheet->mergeCells('K'.$marge['start'].':'.'K'.$marge['end']);
      //   $sheet->mergeCells('L'.$marge['start'].':'.'L'.$marge['end']);
      //   $sheet->mergeCells('M'.$marge['start'].':'.'M'.$marge['end']);
      //   $sheet->mergeCells('N'.$marge['start'].':'.'N'.$marge['end']);
      //   $sheet->mergeCells('O'.$marge['start'].':'.'O'.$marge['end']);
      //   $sheet->mergeCells('P'.$marge['start'].':'.'P'.$marge['end']);
      //   $sheet->mergeCells('Q'.$marge['start'].':'.'Q'.$marge['end']);
      //   $sheet->mergeCells('R'.$marge['start'].':'.'R'.$marge['end']);
      //   $sheet->mergeCells('S'.$marge['start'].':'.'S'.$marge['end']);
      //   $sheet->mergeCells('T'.$marge['start'].':'.'T'.$marge['end']);
      //   $sheet->mergeCells('U'.$marge['start'].':'.'U'.$marge['end']);
      //   $sheet->mergeCells('V'.$marge['start'].':'.'V'.$marge['end']);
      // }
      $sheet->removeRow(2,3);
      
      
      
       $allRow = $row;
       $last = "订单数： ".($expList['body']['statics']['order_nums']).$last;
       
       
       $sheet->mergeCells('A'.$allRow.':U'.$allRow);
       $sheet->setCellValue('A'.$allRow,$last);
      unset($expList);
      // $sheet->getStyle('G2:G'.$row)->getAlignment()->setWrapText(true);
      $sheet->getStyle('H2:H'.$row)->getAlignment()->setWrapText(true);

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
      $filename = '订单导出'.date('YmdHis').'.xls';
      $filename = mb_convert_encoding($filename,'gbk','utf-8');
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
      header("Content-Type:application/force-download");
      header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
      header("Content-Type:application/octet-stream");
      header("Content-Type:application/download");
      header("Content-Disposition:attachment;filename=".$filename);
      header("Content-Transfer-Encoding:binary");
      $objWriter->save("php://output"); 
      exit();
    }
    private function _getExcel($expList){
      // var_dump($expList);die;
      set_time_limit(180000) ;
      ini_set('memory_limit', '1024M');
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->setActiveSheetIndex(0);
      //设置表头        
      $headArr = array(
          'id'=>'订单号',
          'distributor_name'=>'分销商',
          'landscape_ids'=>'景区',
          'name'=>'门票名称',
          'nums'=>'预定数量',
          'used_nums'=>'已使用数量',
          'verify_data'=>'验证日期',
          'verify_nums'=>'验证数量',
          'refunding_nums'=>'退票中的数量',
          'refunded_nums'=>'退票数量',
          'nums-used_nums-refunding_nums-refunded_nums'=>'未使用门票',
          'amount'=>'支付金额',
          'amount-refunded'=>'结算金额',
          'payment'=>'支付方式',
          'status'=>'订单状态',
          'use_day'=>'游玩日期',
          'valid'=>'门票有效期(天)',
          'owner_name'=>'取票人',
          'owner_mobile'=>'取票人手机号',
          'owner_card'=>'取票人身份证号码',
          'created_at'=>'预定日期',
          'remark'=> '备注',
        );
        $temp = array();
        $headerStyle = array(
          'font' => array(
            'bold' => true,
            'size'=>11,
            'color'=>array(
              'argb' => '00FFFFFF',
            ),
          )
        );
      $key = ord("A");
      //表头
      foreach($headArr as $k => $v){
        $colum = chr($key);
        $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);          
        
        $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->getFill()->getStartColor()->setARGB('FF31869B');
        $objPHPExcel->getActiveSheet()->setCellValue($colum.'1', $v);
        $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle($colum.'1')->getFont()->setName('宋体');
        $temp[$k] = $colum;
        ++$key;
      }
      $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(24);
      $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      
      unset($headArr);
      $styleThinBlackBorderOutline = array(
          'borders' => array (
             'outline' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                //'style' => PHPExcel_Style_Border::BORDER_THICK, 另一种样式
                'color' => array ('argb' => 'FF000000'),     //设置border颜色
            ),
         ),
      );
      $row = 2;
      $setColor = 2;
      $allRow = count($expList['body']['data']);
      $refunded = $payeds = $payed = $refun_nums = $used_nums = $nums = 0;
      foreach ($expList['body']['data'] as $ks => $vs) {
        $verify_items = isset($vs['verify_items']) ? $vs['verify_items'] : array();
        $mergeCells = count($this->array_column($verify_items,'use_time'));
        $refunded += $vs['refunded'];
        foreach($temp as  $k => $v){            
            $columValue = '';            
            switch ($k) {
              case 'nums': // 门票预定数量
                    $columValue = $vs['nums'];
                    // $nums += $columValue;
                break;
              case 'verify_data': // 验证日期
                    if($mergeCells == 0){
                      $columValue = '';
                    }elseif($mergeCells == 1){
                      $columValue = date('Y-m-d',$verify_items['0']['use_time']);
                    }else{
                      $columValue = $this->array_column($verify_items,'use_time');//$vs['verify_items'];
                    }
                break;
              case 'verify_nums': // 验证数量
                    if($mergeCells == 0){
                      $columValue = '';
                    }elseif($mergeCells == 1){
                      $columValue = $verify_items[0]['num'];
                    }else{
                      $columValue = $this->array_column($verify_items,'num');//$vs['verify_items'];
                    }
                break;
              case 'used_nums': // 已使用门票数
                    $columValue = $vs['used_nums'];
                    // $used_nums += $columValue;
                break;
              case 'amount-refunded': // 结算金额
                    $columValue = $vs['amount']-$vs['refunded'];
                    // $payed += (float)$vs['payed'];
                break;
              // case 'payed': // 支付金额
              //       $columValue = $vs['payed'];
                    // $payed += (float)$vs['payed'];
                break;
              case 'nums-used_nums-refunding_nums-refunded_nums': //未使用门票
                    $columValue = $vs['nums']-$vs['used_nums']-$vs['refunding_nums']-$vs['refunded_nums'];
                break;
              // case 'refunding_nums+refunded_nums': // 退票数
              //       $columValue = $vs['refunding_nums']+$vs['refunded_nums'];
              //       $refun_nums += (int)$columValue;
              //   break;
              // case 'amount': //结算金额
              //       $columValue = $vs['amount'];
              //       $payeds += (float)$columValue;
              //   break;
              case 'created_at': //预定日期
                    $columValue = date('Y-m-d',$vs[$k]);
                break;
              case 'landscape_ids': //景区
                    $landscapeArr = explode(',', $vs[$k]);
                    $comma = 0;
                    foreach($landscapeArr as $landscapeId) {
                      if(isset($expList['landscape_labels'][$landscapeId])){
                        $columValue .= $expList['landscape_labels'][$landscapeId];
                        $columValue .= $comma ? ',' : '';
                        $comma++;
                      }
                    }
                break;
                // cash,online,credit,pos
              case 'payment'://支付方式 cash,online,credit,pos
                    switch ($vs[$k]){
                        case 'cash':
                              $columValue = '现金';
                          break;
                        case 'offline':
                              $columValue = '线下';
                          break;
                        case 'credit':
                              $columValue = '信用支付';
                          break;
                        case 'pos':
                              $columValue = 'pos机';
                          break;
                        case 'alipay':
                              $columValue = '支付宝';
                          break;
                        case 'advance':
                              $columValue = '储值支付';
                          break;
                        case 'union':
                              $columValue = '平台支付';
                          break;
                        case 'kuaiqian':
                              $columValue = '快钱';
                          break;
                        case 'taobao':
                              $columValue = '淘宝支付';
                          break;
                    }
                    // if($vs[$k] == 'online'){
                    //   $columValue = "在线支付";
                    // }elseif ($vs[$k] == 'credit') {
                    //   $columValue = "信用支付";
                    // }elseif ($vs[$k] == 'advance') {
                    //   $columValue = "储值支付";
                    // }elseif($vs[$k] == 'union'){
                    //   $columValue = "平台支付";
                    // }
                break;
              case 'status': //订单状态
                    $columValue = $expList['status_labels'][$vs[$k]];
                break;
              default:
                    $columValue = $vs[$k];
                break;
            }
            // 验证日期与验证数量大于1条
            if($mergeCells>1){
              if($k == 'verify_data' || $k == 'verify_nums'){//验证日期或验证数量
                foreach ($columValue as $kk => $vv) {
                  $tempRow = $row+$kk;
                  if(0 !== $setColor%2){
                    //设置表格颜色 C0C0C0
                    $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->getFill()->getStartColor()->setARGB('FFD9D9D9');
                  }
                  $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                  $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                  $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->getFont()->setName('宋体');
                  $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->getFont()->setSize('11');
                  
                  $objPHPExcel->getActiveSheet()->getStyle($v.$tempRow)->applyFromArray($styleThinBlackBorderOutline);
                  // $objPHPExcel->getActiveSheet()->setCellValue($v.$row, $columValue); 
                  $setValue = $k == 'verify_data' ? date('Y-m-d',$vv) : $vv;
                  $objPHPExcel->getActiveSheet()->setCellValueExplicit($v.$tempRow,$setValue,PHPExcel_Cell_DataType::TYPE_STRING);        
          
                }
              }else{
                $mergeRow = $row+$mergeCells-1;
                $objPHPExcel->getActiveSheet()->mergeCells($v.$row.':'.$v.$mergeRow);
                if(0 !== $setColor%2){
                  //设置表格颜色 C0C0C0
                  $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                  $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFill()->getStartColor()->setARGB('FFD9D9D9');
                }
                $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFont()->setName('宋体');
                $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFont()->setSize('11');
                
                $objPHPExcel->getActiveSheet()->getStyle($v.$row.':'.$v.$mergeRow)->applyFromArray($styleThinBlackBorderOutline);
                // $objPHPExcel->getActiveSheet()->setCellValue($v.$row, $columValue); 
                
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($v.$row,$columValue,PHPExcel_Cell_DataType::TYPE_STRING);        
        
              }
            }else{
              if(0 !== $setColor%2){
                //设置表格颜色 C0C0C0
                $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFill()->getStartColor()->setARGB('FFD9D9D9');
              }
              //设置居中
              $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
              $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
              $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFont()->setName('宋体');
              $objPHPExcel->getActiveSheet()->getStyle($v.$row)->getFont()->setSize('11');
              
              $objPHPExcel->getActiveSheet()->getStyle($v.$row)->applyFromArray($styleThinBlackBorderOutline);
              // $objPHPExcel->getActiveSheet()->setCellValue($v.$row, $columValue); 
              
              $objPHPExcel->getActiveSheet()->setCellValueExplicit($v.$row,$columValue,PHPExcel_Cell_DataType::TYPE_STRING);
            }
            
            
          }
        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
        unset($expList['body']['data'][$ks]);
        $row += $mergeCells > 1 ? $mergeCells : 1;
        ++$setColor;
      }
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
      

      $objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
      $objPHPExcel->getActiveSheet()->getStyle('C2:C'.$allRow)->getAlignment()->setWrapText(true);

      $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      
      $objPHPExcel->getActiveSheet()->freezePane('A2');

      $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
      $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(17);
      $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(22);
      $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(12);


      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      // 从浏览器直接输出$filename
      $filename = '订单导出'.date('YmdHis').'.xls';
      $filename = mb_convert_encoding($filename,'gbk','utf-8');
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
      header("Content-Type:application/force-download");
      header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
      header("Content-Type:application/octet-stream");
      header("Content-Type:application/download");
      header("Content-Disposition:attachment;filename=".$filename);
      header("Content-Transfer-Encoding:binary");
      $objWriter->save("php://output"); 
      exit();
    }
  protected function array_column($input, $columnKey, $indexKey=null){
    $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
    $indexKeyIsNull            = (is_null($indexKey))?true :false; 
    $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
    $result                         = array(); 
    foreach((array)$input as $key=>$row){ 
        if($columnKeyIsNumber){ 
            $tmp= array_slice($row, $columnKey, 1); 
            $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
        }else{ 
            $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
        } 
        if(!$indexKeyIsNull){ 
            if($indexKeyIsNumber){ 
              $key = array_slice($row, $indexKey, 1); 
              $key = (is_array($key) && !empty($key))?current($key):null; 
              $key = is_null($key)?0:$key; 
            }else{ 
              $key = isset($row[$indexKey])?$row[$indexKey]:0; 
            } 
        } 
        $result[$key] = $tmp; 
    } 
    return $result;   
  }
    // Uncomment the following methods and override them if needed
    /*
      public function filters()
      {
      // return the filter configuration for this controller, e.g.:
      return array(
      'inlineFilterName',
      array(
      'class'=>'path.to.FilterClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }

      public function actions()
      {
      // return external action classes, e.g.:
      return array(
      'action1'=>'path.to.ActionClass',
      'action2'=>array(
      'class'=>'path.to.AnotherActionClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }
     */
}
