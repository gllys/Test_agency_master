<?php
use common\huilian\models\Pay;
use common\huilian\utils\Format;
class ReportController extends Controller {
    public function actionView($is_export = false) {
      $this->actionIndex($is_export);
    }
    public function actionIndex($is_export = false)
    {
        $params = $_REQUEST;
        $oldEndDate = isset($params["end_date"])==true?$params["end_date"]:null;
        
        $data['payTypes'] = Pay::types();
        
        if(isset($params["report_type"]) && $params["report_type"]>0)
        {
           switch($params["report_type"])
           {
             case 1:
                 //addCondition
                $params["income"] = 'true';
                 break;
             case 2:
                 $params["refunded"] = 'true';
                 break;
           }
        }
        
        if(isset($oldEndDate)==false || trim($oldEndDate)==Format::date(time()))
        {
            $params["end_date"] = Format::date(time()-24*3600);
        }
        
//       $rs =  Payrate::api()->lists(array());
//       $payRateDatas = ApiModel::getLists($rs);
//       $data["payRateDatas"] = (empty($payRateDatas) || $payRateDatas["code"]!="succ")?array():$payRateDatas["body"];
       
//        //查看是否是景区身份
//        $criteria = new CDbCriteria();
//        $criteria->order = 'status DESC,id DESC'; 
//        $criteria->select = "sell_role";
//        $criteria->compare('id', Yii::app()->user->uid);
//        $lists = Users::model()->find($criteria);
//        
//      if($lists->sell_role=="scenic"){
        $data['status_labels'] = array("auditing"=>"待确认","reject"=>"已驳回",'unpaid'=>'未支付','canceled' => '已取消','paid' => '已付款','finish' => '已完成','billed' => '已结款');
        $data['status_class'] = array("unaudited"=>"info","reject"=>"danger",'unpaid' => 'danger', 'canceled' => 'warning', 'paid' => 'success', 'finish' => 'info', 'billed' => 'error');
        $data['status'] = array_keys($data['status_labels']);
        
        $data['order_types'] = array("电子票","任务票");
        $data['order_kind_types'] = array("","单票","联票","套票");
        
        $params['type'] = isset($params['type']) ? $params['type'] :'whole';
        
         // 获取景区id和名字的数组
//        $rs = Landscape::api()->lists(array());
//        $landscapeDatas = ApiModel::getLists($rs);
//        $landscapes = array();
//        foreach ($landscapeDatas as $v) {
//            $landscapes[$v['id']] = $v['name'];
//        }
//        $data['landscape_labels'] = $landscapes;
        
        if(isset($params["landscape_name"])) $params["scenic_name"] = $params["landscape_name"];
        if(isset($params["distributor_name"])) $params["agency_name"] = $params["distributor_name"];
        if(isset($params["supplier_name"])) $params["supply_name"] = $params["supplier_name"];
        
       // print_r($landscapes);
        //exit;
        
        //Credit::api()->debug = true;
        $rs = Credit::api()->lists(array('items' => 1000));
        $distributorDatas = ApiModel::getLists($rs);
        $distributors = array();
        foreach ($distributorDatas as $v) {
            $distributors[$v['distributor_id']] = $v['distributor_name'];
        }
        $data['distributors_labels'] = $distributors;

        $data['get'] = $params;
    
        
        $params['current'] = isset($params['page']) ? $params['page'] : 1;
        
        $params['items'] = 20;
        
        //$params['type'] = 0;
        //此处多个状态值请不要拆分，保持现状
        $params['status'] = "paid";
        $params['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
        //var_dump($params);exit;
       //  Order::api()->d

        $data = $this->getApiLists($params,$is_export,$data);
       if ($data['lists']["result"]['code'] == 'succ') {
            //20150215 拷贝supply订单导出代码
            if ($is_export==false) {
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }
       }
       
        $data['get']["end_date"] = $oldEndDate;
       
       
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
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            
            if($result['code'] == 'succ')
            {
                
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data"=>$result['body']["data"],"statics"=>$result['body']["statics"],"pagination"=>$pagination,"result"=>$result);
               
                $id = "";
                 //20150215 拷贝supply订单导出代码
                foreach($data["lists"]["data"] as $k=>$v){
                    $id .= $v["landscape_ids"].","; 
                }
                $id=trim($id,",");
                
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

