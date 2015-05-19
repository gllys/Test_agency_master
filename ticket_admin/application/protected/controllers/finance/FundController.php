<?php
/**
 * 资产列表相关
 * 提现列表相关
 * 2015-03-10
 * @author qiuling
 */
class FundController extends Controller
{
    /**
     * 资产列表
     */
	public function actionIndex(){   
        $get = $_GET;
        $tal_nums = Unionmoney::api()->total(array()); //总资产管理
        $data['total'] = isset($tal_nums['body']) ? $tal_nums['body'] : array(
            "total_union_money" => "0.00",
            "total_frozen_money" => "0.00"
        );
        $data['get'] = $get;

        // 公共条件
        $params['current'] = empty($get['page']) ? 1 : $get['page'];
        $params['items'] = 15;
        //搜索条件
        if (!empty($get['org_name'])) {
            $params['org_name'] = $get['org_name'];
        }
        if (!empty($get['op_account'])) {
            $params['op_account'] = $get['op_account'];
        }
        //时间
        if (isset($get['start_time']) && !empty($get['start_time'])) {
            $params['start_date'] = $get['start_time'];
        }
        if (isset($get['end_time']) && !empty($get['end_time'])) {
            $params['end_date'] = $get['end_time'];
        }
        //交易类型
        if (isset($get['trade_type']) && !empty($get['trade_type'])) {
            $params['trade_type'] = $get['trade_type'];
        }
                 
        $is_export = isset($get["is_export"]) && $get["is_export"]>0;
         
        $data = $this->getApiLists($params,$is_export,$data);
        if ($data['lists']["result"]['code'] == 'succ') {
            //20150215 拷贝supply订单导出代码
            if ($is_export==false) {
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
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
        
        if($is_export)
        {
            $this->renderPartial("excelTop",$data);
            $params['show_verify_items'] = 1;
            $params['items'] = 1000;
        }
        
        do{
            if($result)
            {
                unset($result);
            }
            $result = Unionmoneylog::api()->lists($params);
            
            //print_r($result);
            
            //exit;
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
    
    
    /**
     * 充值优惠记录
     */
    public function actionIndex2(){      
        $get = $_GET;

        $tal_nums = Unionmoney::api()->total(array()); //总资产管理
        $data['total'] = isset($tal_nums['body']) ? $tal_nums['body'] : array(
            "total_union_money" => "0.00",
            "total_frozen_money" => "0.00"
        );
        $data['get'] = $get;

        // 公共条件
        $params['current'] = empty($get['page']) ? 1 : $get['page'];
        $params['items'] = 15;
        //搜索条件        
        if (!empty($get['org_name'])) { //分销商名字
            $params['org_name'] = $get['org_name'];
        }
        if (!empty($get['apply_account'])) { //申请者账号
            $params['apply_account'] = $get['apply_account'];
        }
        if (!empty($get['apply_username'])) { //申请者名称
            $params['apply_username'] = $get['apply_username'];
        }
        
        //时间
        if (isset($get['start_time']) && !empty($get['start_time'])) {
            $params['start_date'] = $get['start_time'];
        }
        if (isset($get['end_time']) && !empty($get['end_time'])) {
            $params['end_date'] = $get['end_time'];
        }

        //提现状态
        if (isset($get['status']) ) {
            $params['status'] = $get['status'];
        }
        //机构角色
        if (isset($get['org_role']) ) {
            $params['org_role'] = $get['org_role'];
        }

        //明细列表
        $datas = Unionmoneyencash::api()->lists($params);
        $lists = ApiModel::getLists($datas);

        //分页
        $pagination = ApiModel::getPagination($datas);

        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15;
        $this->render('index2', compact('lists','data', 'pages'));
	}
    /**
     * 获取详情
     */
    public function actionDetail(){
        $id = isset($_GET['bid'])?$_GET['bid']:0;
        $view = isset($_GET['btype'])?$_GET['btype']:0;
        $data = Unionmoneyencash::api()->detail(array('id' => $id,'with_org_info'=>1));
        if (!ApiModel::isSucc($data)) {            
            $this->_end(1, $data['message']);
        }
        $billInfo = isset($data['body']) ? $data['body'] : array();
        
        $this->renderPartial('detail', compact('billInfo','view'));
    }
    
    /**
     * 确认打款及驳回
     */
    public function actionUploadProve() {
        $post = $_POST;
        
        if (isset($post['type']) && $post['type'] == 'bohui') {
            //驳回
            $param['id'] = $post['id'];
            $param['check_uid'] = Yii::app()->user->uid;
            $param['status'] = 2;
            $param['remark'] = $post['remark'];
            $param['paid_at'] = time();
        } else {
            //打款                
            $param['paid_img'] = $post['proof'];
            $param['id'] = $post['id'];
            $param['check_uid'] = Yii::app()->user->uid;
            $param['status'] = 1;
            $param['paid_at'] = time();
        }
        $data = Unionmoneyencash::api()->check($param);
        if (ApiModel::isSucc($data)) {
            echo '{"msg":"succ"}';
        } else {
            if(!isset($data['message'])){
                $data['message'] = '';
            }
            echo '{"msg":"'.$data['message'].'"}';
        }
    }
}
