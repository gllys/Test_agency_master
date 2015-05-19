<?php

class SalesController extends Controller {

    public function actionView($is_export = false) {
      $this->actionIndex($is_export);
    }
   
    public function actionIndex($is_export = false){
    	//接收搜索条件
    	$params = $_REQUEST;
    	//默认搜索条件中的日期为当前月
    	if(!isset($params["start_date"]) && !isset($params["end_date"]) ){
    		$params["start_date"] = gmdate("Y-m-01",time()+3600*8);
    		$params["end_date"] = gmdate("Y-m-d",time()+3600*8);
    		$params["date"] =  $params["start_date"]." - ".$params["end_date"];
    	}else{
    		if(empty($params["start_date"]) || empty($params["end_date"])){
    			$params["start_date"] = gmdate("Y-m-01",time()+3600*8);
    			$params["end_date"] = gmdate("Y-m-d",time()+3600*8);
    			$params["date"] =  $params["start_date"]." - ".$params["end_date"];
    		}else{
    			$params["date"] =  $params["start_date"]." - ".$params["end_date"];
    		}
    	}
    	$params["date_type"] = isset($params["date_type"])?$params["date_type"]:1;
    	//默认页面为供应商
    	$params['type'] = isset($params['type']) ? $params['type'] :'whole';			    	
    	$data=array();
    	$data['get'] = $params;
        
        $data['timeTypes'] = array(
          1=>'预订日期'
        );
        $data['supplyType'] = array(
        	'whole'=>'供应商名称',
        	'scenic'=>'景区名称'
        );
        //　全角空格
        $data['type'] = isset($params['type']) ? $params['type'] :'whole';
        $data['date_type'] = isset($params['date_type']) ? $params['date_type'] :1;
        
    	/**
    	 *搜索条件
    	 *通过供应商名称 
    	 *景区名称
    	 *查询供应商id，景区id
    	 *
    	 **/
    	if($params['type'] == "whole"){
    		if(isset($params["name"]) && !empty($params["name"])){
    				$supplier_id = "";
    				$arr["type"] = "supply";
    				$arr["name"] = $params["name"];
    				$arr["fields"] = "id";
    				$arr["items"] = 1000; 
    				$result = Organizations::api()->list($arr);
	    			if($result["code"]=="succ"){
	    				if(isset($result["body"]["data"]) && !empty($result["body"]["data"])){
	    					foreach($result["body"]["data"] as $v){
	    						$supplier_id .= $v["id"].","; 
	    					}
	    					$params["supplier_id"] = trim($supplier_id,",");
	    				}else{
	    					$params["supplier_id"] = 99999999999999; //如果没有搜索到供应商的id默认一个ID
	    				}
    				}
    		}
    	}else{
    		if(isset($params["name"]) && !empty($params["name"])){
    				$landscape_id = "";
    				$arr["keyword"] = $params["name"];
    				$arr["fields"]	= "id";
    				$arr["items"] = 1000; 
    				$result = Landscape::api()->lists($arr);
    				if($result["code"]=="succ"){
	    				if(isset($result["body"]["data"]) && !empty($result["body"]["data"])){
	    					foreach($result["body"]["data"] as $v){
	    						$landscape_id .= $v["id"].","; 
	    					}
	    					$params["landscape_ids"] = trim($landscape_id,",");
	    				}else{
	    					$params["landscape_ids"] = 99999999999999; //如果没有搜索到景区的id默认一个ID
	    				}
    				}
    		}	
    	}
    	
        // 获取机构id和名字的数组
//        $res = Organizations::api()->list(array('items' => 1000,"fields"=>"name"));
//        $supplyDatas = ApiModel::getLists($res);
//        $supplys = array();
//        foreach ($supplyDatas as $v) {
//            $supplys[$v['id']] = $v['name'];
//        }
//        $data['supply_labels'] = $supplys;
    	$params['current'] = isset($params['page']) ? $params['page'] : 1;
        
        $params['items'] = 20;
        
         $data = $this->getApiLists($params,$is_export,$data);
         if(empty($data['lists']))
         {
             exit;
         }
       if ($data['lists']["result"]['code'] == 'succ') 
       {
            if ($is_export==false) 
            {
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }
       }
        
        
        //获取数据列表
//	$result = Stat::api()->plateform_list($params);
//	if($result["code"] == "succ")
//        {
//            $id = "";
//            $data["lists"] = $result["body"];
//            foreach($data["lists"]["data"] as $k=>$v){
//                $id .= $k.","; 
//            }
//            $id=trim($id,",");
//            //echo $id;die;
//            if($params["type"]=="scenic")
//            {
//                // 获取景区id和名字
//                $rs = Landscape::api()->lists(array('ids' => $id,"fields"=>"name","items"=>500));
//                $landscapeDatas = ApiModel::getLists($rs);
//                $landscapes = array();
//                foreach ($landscapeDatas as $v) {
//                    $landscapes[$v['id']] = $v['name'];
//                }
//                $data['landscape_labels'] = $landscapes;
//            }
//		//分页			
//		$data['pages'] = new CPagination($data['lists']['pagination']['count']);
//	        $data['pages']->pageSize = $params['items'];
//	        //导出数据cho 
//	        if($is_export)
//                {
//                    $this->renderPartial("outputExcel", $data);
//                    exit;
//	        }
//	}
        
        $this->render('index',$data);
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
            $result = Stat::api()->plateform_list($params);
            
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            if($result['code'] == 'succ')
            {
             
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data"=>$result['body']["data"],"pagination"=>$pagination,"result"=>$result);
               
                $id = "";
                 //20150215 拷贝supply订单导出代码
                foreach($data["lists"]["data"] as $k=>$v){
                    $id .= $k.","; 
                }
                $id=trim($id,",");
                
                
                if($params["type"]=="scenic")
                {
                    // 获取景区id和名字
                    $rs = Landscape::api()->lists(array('ids' => $id,"fields"=>"name","items"=>1000));
                    $landscapeDatas = ApiModel::getLists($rs);
                    if(isset($data['landscape_labels'])==false)
                    {
                        $data['landscape_labels'] = array();
                    }
                    foreach ($landscapeDatas as $v) 
                    {
                        $data['landscape_labels'][$v['id']] = $v['name'];
                    }
                }else
                {
                    
                     // 获取机构id和名字的数组
                    $res = Organizations::api()->list(array('id' => $id,"fields"=>"name","items"=>1000));
                    $supplyDatas = ApiModel::getLists($res);
                    $supplys = array();
                    foreach ($supplyDatas as $v) {
                        $supplys[$v['id']] = $v['name'];
                    }
                    $data['supply_labels'] = $supplys;  
                }
                
                
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
    
}


