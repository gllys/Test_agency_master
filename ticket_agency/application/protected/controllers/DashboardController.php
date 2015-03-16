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
            
            //未支付订单
            $par['status'] = 'unpaid';
            $par['type'] = 0;
            $par['distributor_id'] = yii::app()->user->org_id;
            $lists = Order::api()->lists($par);
            //print_r($par);exit;
            //$data['num1']= $lists['body']['pagination']['count'];
            
            
            //未退款订单
            $fie['distributor_id'] = yii::app()->user->org_id;
            $fie['status'] = 0;
            $list0 =  Refund::api()->apply_list($fie);
            $data['num2']=$list0['body']['pagination']['count'];
            
            //购物车
             $rs = Cart::api()->lists(array('user_id'=>Yii::app()->user->uid));
             $data['num3'] = count($rs['body']);
            
            //我的收藏
            $all = Favorites::api()->count(array('organization_id'=>Yii::app()->user->org_id));
             $data['num4'] = $all['body']['count'];
             
             
             //柱状图
             //ym 	否 	string 	年月，格式xxxx-xx，留空默认本月
            //year      否 	string 	年份，格式xxxx，留空默认今年
             //月订单信息
             $field['distributor_id'] = Yii::app()->user->org_id;
            // $field['ym'] = date("Y-m"); 
             $field['year'] = 0; 
             $months = Order::api()->agencystat($field);
             //print_r();
             $data['m'] = $months['body'];
          //  print_r($months);
             
             //当月的每天订单信息
             $field1['distributor_id'] = Yii::app()->user->org_id;
             $field1['ym'] = 0;
            // $field1['year'] =date("Y");
             $days = Order::api()->agencystat($field1);
             $data['d'] = $days['body'];
           //  print_r($days);
             

		$this->render('index',$data);
	}
} 
