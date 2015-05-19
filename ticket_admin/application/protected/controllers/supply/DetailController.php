 <?php

class DetailController extends Controller {

    public function actionView($is_export = false) {
      $this->actionIndex($is_export);
    }
   	public function actionView2($is_export = false) {
      $this->actionIndex2($is_export);
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
    	//拼一个url参数
    	$url="";
    	foreach($params as $k=>$v){
    		if($k!="page"){
    			$url .= $k."=".$v."&";
    		}
    	}
    	$data=array();
    	$data['get'] = $params;
    	$data["url"] = !empty($url)?"?".trim($url,"&"):"";
        
        $data['timeTypes'] = array(
          1=>'预订日期'
        );
        $data['supplyType'] = array(
        	'whole'=>'供应商名称',
        	'scenic'=>'景区名称'
        );
        $data['date_type'] = isset($params['date_type']) ? $params['date_type'] : 1;
        $data['type'] = isset($params['type']) ? $params['type'] :'whole';
        
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
    	/***
    	 *### ~/v1/stat/plateform_detail 
    	 *day|date|日期
		 *order_num|int|订单数量
		 *person_num|int|订购人数
		 *used_person_num|int|已使用人数
		 *unused_person_num|int|未使用人数
		 *refunded_person_num|int|退款人数
		 *amount|float|订单金额
		 *receive_amount|float|收入金额
		 *refunded|float|退款金额 
    	 ***/
    	$params['current'] = isset($params['page']) ? $params['page'] : 1;
        $params['items'] = 20;
        
         $data = $this->getApiLists($params,$is_export,$data);
       if ($data['lists']["result"]['code'] == 'succ') {
            //20150215 拷贝supply订单导出代码
            if ($is_export==false) {
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }
       }
        
//        $result = Stat::api()->plateform_detail($params);
//        if ($result['code'] == 'succ') {
//            $data['lists'] = $result['body'];
//            $data['pages'] = new CPagination($data['lists']['pagination']['count']);
//            $data['pages']->pageSize = $params['items'];
//            if($is_export){
//            	$this->renderPartial("outputExcel", $data);
//                exit;
//            }
//        }
        
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
            $result = Stat::api()->plateform_detail($params);
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            
            if($result['code'] == 'succ')
            {
                
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data"=>$result['body']["data"],"pagination"=>$pagination,"result"=>$result);
               
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
    

    public function actionIndex2($is_export = false){
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
    	$params['date_type'] = isset($params['date_type']) ? $params['date_type'] : 1;
    	//默认页面为供应商
    	$params['type'] = isset($params['type']) ? $params['type'] :'whole';			    	
    	$data=array();
    	$data['get'] = $params;

    	$data["url"] = !empty($_SERVER["QUERY_STRING"])?"?".$_SERVER["QUERY_STRING"]:"";
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
    				//$this->pre($result);die;
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
    	
    	$params["items"] = 1;
    	$result = Stat::api()->plateform_detail($params);
        $params["items"] = !empty($result["body"]["pagination"]["count"])?$result["body"]["pagination"]["count"]:1;
        $result = Stat::api()->plateform_detail($params);
        if ($result['code'] == 'succ') {
            $data['lists'] = $result['body'];
            if($is_export){
            	$expList = $result;
            	$expList['type'] = $params['type']; 
            	unset($data);
            	$this->_getExcel($expList);exit;
            }
        }
        //$this->pre($result);die;
        $data['timeTypes'] = array(
          1=>'预订日期',
        );
        $data['supplyType'] = array(
        	'whole'=>'供应商名称',
        	'scenic'=>'景区名称'
        );
        $data['date_type'] = isset($params['date_type']) ? $params['date_type'] : 1;
        $data['type'] = isset($params['type']) ? $params['type'] :'whole';
        $this->render('index2',$data);
    }
    public function pre($arr){
    	if(is_array($arr)){
    		echo "<pre>";
    		print_r($arr);
    		echo "</pre>";
    	}else{
    		echo "<pre>";
    		var_dump($arr);
    		echo "</pre>";
    	}
    }
    
}
