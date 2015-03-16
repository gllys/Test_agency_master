<?php

class GroupController extends Controller
{
	public function actionIndex()
	{
            $param = array();
            $paramlan = array();
            $param = $_GET ;
            $param['is_full']= 1;
            $param['current'] = isset($param['page'])?$param['page']:0 ;
            $param['expire_end'] = time();
            //获取省份id 门票名称  是否电子票 任务单  条件选择
            if(!empty($_GET)){//Array ( [province_id] => 110000 [type] => 0 [name] => sds ) 
                if(!empty($_GET['jqname'])){
                    $param['name'] = $_GET['jqname'];
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
            }
            $param['state'] = 1;
            $param['agency_id'] = Yii::app()->user->org_id;
            
            //景区列表
            //判断是否搜索了省份 如果有就展示该省份的景区
            if(isset($param['province_id'])){
               $paramlan['province_ids'] = $param['province_id'];//省份id 
                $landscape = Landscape::api()->lists($paramlan);
                $landscapes = ApiModel::getLists($landscape); //景区array
            }
           
     
            //不选择的情况下 全部的景区票  继续分页
          //TicketTemplate::api()->debug=true;
           // $param['fat_price'] = $param['fat_price']>0;
            $reserve_list = TicketTemplate::api()->reserve_list($param);
            //$relist = ApiModel::getData($reserve_list);
            $lists = ApiModel::getLists($reserve_list);// 票array
          
                //分页
            $pagination = ApiModel::getPagination($reserve_list) ;
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            
            //print_r($param);
            if(isset($landscapes)){
                 //print_r($landscapes);exit;
                 $this->render('index',  compact('landscapes','lists','pages','param'));
            }else{
                 $this->render('index',  compact('lists','pages','param'));
            }
	}

	
}
