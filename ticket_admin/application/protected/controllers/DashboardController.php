<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/17/14
 * Time: 3:16 PM
 */

class DashboardController extends Controller
{

	public function actionIndex() {
            $data = array('mun1'=>0,'mun2'=>0,'mun3'=>0,'mun4'=>0,'mun5'=>0);
            //待处理退款申请单
            $param['supplier_id'] = 0;
            $param['allow_status'] = 0;
            $lists = Refund::api()->apply_list($param);
            if($lists['code'] == 'succ'){
                $data['mun1'] = !empty($lists['body']['pagination']['count']) ? $lists['body']['pagination']['count'] : '0';
            }
            
         
            
            //待查看消息
            
            $par['read_time'] = 0;
            $par['receiver_organization'] = 0;
            $list = Message::api()->list($par);
            if($list['code'] == 'succ'){
                $data['mun2'] = !empty($list['body']['pagination']['count']) ?  $list['body']['pagination']['count'] : '0';
            }
            
            
            //即将到期产品提醒
            $par['sys_type'] = 3;
            $par['sms_type'] = 0;
            $list1 = Message::api()->list($par);
            if($list1['code'] == 'succ'){
                $data['mun3'] = !empty($list1['body']['pagination']['count']) ? $list1['body']['pagination']['count'] : '0';
            }
            
            
            //今日订单统计
            $filed['supplier_id'] = 0;
            $rs = Order::api()->supplierstat($filed);
            $mun4 = 0; //当天票总数目
            $mun5 = 0; //当天已付款总金额
            if($rs['code'] == 'succ'){
                if(count($rs['body']) > 0){
                    foreach ($rs['body'] as $item){
                        $mun4 += $item['ticket_nums'];
                        $mun5 += $item['money_amount'];
                    }
                    $data['list'] = $rs['body'];
                    $data['total'] = count($rs['body']);
                }
            }

            $data['mun4'] = $mun4;
            $data['mun5'] = $mun5;
            
            
            $this->render('index',$data);
	}
} 
