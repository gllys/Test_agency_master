<?php

class SaleController extends Controller
{
	public function actionIndex()     
	{
            $param = array();
            $paramlan = array();
            $param = $_GET ;
            $param['is_fit']= 1;
            $param['current'] = isset($param['page'])?$param['page']:0 ;
            $param['expire_end'] = time();
            //获取省份id 门票名称  是否电子票 任务单  条件选择
            if(!empty($_GET)){
                if(!empty($_GET['jqname'])){
                    $param['name'] = $_GET['jqname'];
                }else{
                    unset($param['jqname']);
                }
                if(isset($_GET['province_id'])&&$_GET['province_id']!=''){
                    $param['province_id'] = $_GET['province_id'];
                }else{
                    unset($param['province_id']);
                }
                if(!empty($_GET['type'])){
                    $param['type'] = $_GET['type'];
                }
                if(!empty($_GET['scenic_id'])){
                    $param['scenic_id'] = $_GET['scenic_id'];
                }
                if(!empty($_GET['kind'])){
                	$param['kind'] = $_GET['kind'];
                }
            }
            
            //print_r($param);exit;
            
            //景区列表
            //判断是否搜索了省份 如果有就展示该省份的景区
            if(isset($param['province_id'])){
               $paramlan['province_ids'] = $param['province_id'];//省份id 
                $landscape = Landscape::api()->lists($paramlan);
                $landscapes = ApiModel::getLists($landscape); //景区array
            }
           
            $param['state'] = 1;
            $param['agency_id'] = Yii::app()->user->org_id;
            //不选择的情况下 全部的景区票  继续分页
            //TicketTemplate::api()->debug=true;
            $reserve_list = TicketTemplate::api()->reserve_list($param);
            $lists = ApiModel::getLists($reserve_list);// 票array
          
                //分页
            $pagination = ApiModel::getPagination($reserve_list) ;
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            
          //  print_r($param);
            if(isset($landscapes)){
                 //print_r($landscapes);exit;
                 $this->render('index',  compact('landscapes','lists','pages','param'));
            }else{
                 $this->render('index',  compact('lists','pages','param'));
            }
        
         }

}
