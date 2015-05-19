<?php

/**
 * 景区统计
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/18/15
 * Time: 1:56 PM
 * File: StatsController.php
 */
class StatsController extends Controller
{
    public function actionIndex()
    {
        $param = $_GET;
        $range = isset($param['range']) ? $param['range'] : 'day';
        if ($range != 'day') {
            //只传入年月时，转成年月日
            if (isset($param['first_date']) && substr_count($param['first_date'], '-') == 1) {
                $param['first_date'] .= '-01';
            }
            if (isset($param['last_date']) && substr_count($param['last_date'], '-') == 1) {
                $param['last_date'] .= '-' . date('t', strtotime($param['last_date'] . '-01'));
            }
            $param['first_time'] = isset($param['first_date']) ? strtotime($param['first_date'] . '00:00:00') : (isset($param['last_date']) ? strtotime('midnight first day of jan', strtotime($param['last_date'])) : strtotime('midnight first day of jan'));
            $param['last_time'] = isset($param['last_date']) ? strtotime($param['last_date'] . '00:00:00'): (isset($param['first_date']) ? strtotime('midnight last day of dec', strtotime($param['first_date'])) : strtotime('midnight last day of dec'));
        } else {
            $param['first_time'] = isset($param['first_date']) ? strtotime($param['first_date'] . '00:00:00') : (isset($param['last_date']) ? strtotime('midnight first day of this month', strtotime($param['last_date'])) : strtotime('midnight first day of this month'));
            $param['last_time'] = isset($param['last_date']) ? strtotime($param['last_date'] . '00:00:00'): (isset($param['first_date']) ? strtotime('midnight last day of this month', strtotime($param['first_date'])) : strtotime('midnight last day of this month'));
        }
        $param['last_time'] += 86399;

        $param['page'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 20;
        $param["current"] = $param['page'];
        if(isset($param["is_export"]) && $param["is_export"]==1)
        {
            $is_export = true;
            $this->getIndexApiLists($param,$is_export,array());
            exit;
        }
        
        

        $rs = TicketStats::api()->lists($param);

        $param['date_range'] = $range;

        $format = $range != 'day' ? 'Y-m' : 'Y-m-d';
        $param['first_date'] = date($format, $param['first_time']);
        $param['last_date'] = date($format, $param['last_time']);

        $data['param'] = $param;
        if ($rs['code'] == 'ok' && isset($rs['data'])) {
            $data['lists'] = $rs['data']['lists'];
            $data['pages'] = new CPagination($rs['data']['count']);
            $data['pages']->pageSize = $param['items'];
        }

        $rs = Landscape::api()->usedList(array(
            'show_all' => 1
        ));
        if ($rs['code'] == 'succ' && isset($rs['body'])) {
            $data['landscapes'] = $rs['body']['data'];
        }

        $this->render('index', $data);
    }
    
    
    
    
    

    public function actionDetail()
    {
        $param = $_GET;
        $param['type'] = 'detail';
        $range = isset($param['range']) ? $param['range'] : 'day';
        $param['page'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 15;
        $param["current"] = $param['page'];
        if ($range != 'day') {
            //只传入年月时，转成年月日
            if (isset($param['first_date']) && substr_count($param['first_date'], '-') == 1) {
                $param['first_date'] .= '-01';
            }
            if (isset($param['last_date']) && substr_count($param['last_date'], '-') == 1) {
                $param['last_date'] .= '-' . date('t', strtotime($param['last_date'] . '-01'));
            }
        }
        $param['first_time'] = strtotime($param['first_date'] . ' 00:00:00');
        $param['last_time'] = strtotime($param['last_date'] . ' 23:59:59');

        if(isset($param["is_export"]))
        {
            $is_export = true;
            $this->getApiLists($param,$is_export,array());
            exit;
        }
            
        
        $rs = TicketStats::api()->detail($param, 0);
        $data['param'] = $param;
        if ($rs['code'] == 'ok' && isset($rs['data'])) {
            $data['lists'] = $rs['data']['lists'];
            $data['pages'] = new CPagination($rs['data']['count']);
            $data['pages']->pageSize = $param['items'];
            $data['landscape_name'] = $data['lists'][0]['landscape_name'];
        }

        $this->render('detail', $data);
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
            
            $params["items"] = 1000;
        }
        
        do{
            if($result)
            {
                unset($result);
            }
            $result = TicketStats::api()->detail($params);
           // print_r($result);
            //exit;
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            
            if($result['code'] == 'ok')
            {
                
                $data['lists'] = array("data"=>$result['data']["lists"],"result"=>$result);
               
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

    public function actionGraph()
    {
        $param = $_GET;
        $param['type'] = 'graph';
        $range = isset($param['range']) ? $param['range'] : 'day';
        $param['page'] = isset($param['page']) ? $param['page'] : 1;

        if ($range != 'day') {
            //只传入年月时，转成年月日
            if (isset($param['first_date']) && substr_count($param['first_date'], '-') == 1) {
                $param['first_date'] .= '-01';
            }
            if (isset($param['last_date']) && substr_count($param['last_date'], '-') == 1) {
                $param['last_date'] .= '-' . date('t', strtotime($param['last_date'] . '-01'));
            }
        }
        $param['first_time'] = strtotime($param['first_date'] . ' 00:00:00');
        $param['last_time'] = strtotime($param['last_date'] . ' 23:59:59');
        $param['items'] = ceil(abs($param['last_time'] - $param['first_time']) / 86400);

        $rs = TicketStats::api()->detail($param, 0);
        $data['param'] = $param;
        if ($rs['code'] == 'ok' && isset($rs['data'])) {
            foreach ($rs['data']['lists'] as $item) {
                $data['lists']['day'][] = $item['created_day'];
                $data['lists']['tickets_total'][] = $item['tickets_total'];
                $data['lists']['sale_money'][] = $item['sale_money'];
                $data['lists']['used_total'][] = $item['used_total'];
                $data['lists']['refunded_total'][] = $item['refunded_total'];
                $data['lists']['refund_money'][] = $item['refund_money'];
            }

            $data['landscape_name'] = $rs['data']['lists'][0]['landscape_name'];
        }

        $this->render('graph', $data);
    }
    
    
    private function getIndexApiLists($params,$is_export,$data)
    {
        $d = array();
        $pagination =null;
        $result = null;
        $num = 0;
        
        if($is_export)
        {
            $this->renderPartial("indexExcelTop",$data);
            $params['show_verify_items'] = 1;
            $params["items"] = 1000;
        }
        
        do{
            if($result)
            {
                unset($result);
            }
            $result = TicketStats::api()->lists($params);
            
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            
            if($result['code'] == 'ok')
            {
                
                $data['lists'] = array("data"=>$result['data']["lists"],"result"=>$result);
               
                if($is_export)
                {
                    $this->renderPartial("indexExcelBody",$data);
                }
                
                $num += count($data['lists']["data"]);
            }
         }while($params["current"]<1000 && $is_export==true && $result['code'] == 'succ' && empty($pagination)==false && $pagination['current']<$pagination['total']);
         if($is_export==true)
         {
             $data["num"] = $num;
            $this->renderPartial("indexExcelBottom",$data);
            exit;
          }
         return $data;
    }

}


