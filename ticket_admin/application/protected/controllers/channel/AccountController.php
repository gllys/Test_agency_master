<?php
/**
 * create：hongbin.hsu <hongbin.hsu@bincent.com>
 * date：2015-03-13
 */

class AccountController extends Controller {

    // 淘宝账号列表
    public function actionIndex() 
    {
        $param = $_REQUEST;
        $data = array();
        
        $data['status'] = null;
        $param['source'] = 1;  
        if(isset($param['status']) && is_numeric($param['status'])){
            $data['status'] = intval($param['status']);
        }else{
            unset($param['status']);
        }
        
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $taobao = Organizations::api()->orgList($param);
        $data['taobao'] = $taobao['body']['data'];
        
        $data['pages'] = new CPagination($taobao['body']['pagination']['count']);
        $data['pages']->pageSize = 15;
        $data['pages']->params = array_filter($param);
        
        $this->render('taobao', $data);
    }
    
    // 去哪儿账号列表
    public function actionQunar()
    {
        $param = $_REQUEST;
        $data = array();
        $param['source'] = 10;    
        $data['status'] = $data['account'] =  $data['organization_id'] =  null ;
        if(isset($param['status']) && is_numeric($param['status'])){
            $param['status'] = intval($param['status']);
            $data['status'] = $param['status'];
        }else{
            unset($param['status']);
        }
        
        if(isset($param['account']) && !empty($param['account'])){
            $param['account'] = trim($param['account']);
            $data['account'] = $param['account'];
        }else{
            unset($param['account']);
        }
        
        if(isset($param['organization_id']) && !empty($param['organization_id'])){
            $param['organization_id'] = trim($param['organization_id']);
            $data['organization_id'] = $param['organization_id'];
        }else{
            unset($param['organization_id']);
        }
        
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $qunar = Organizations::api()->orgList($param);
                      
        $data['qunar'] = $qunar['body']['data'];
        
        $data['organizations'] = $qunar['body']['organizations'];
                              
        $data['pages'] = new CPagination($qunar['body']['pagination']['count']);
        $data['pages']->pageSize = 15;
        $data['pages']->params = array_filter($param);
                
        $this->render('qunar', $data);
    }
    
    // 账号审核
    public function actionAccountaudit()
    {
        $error = 1; $msg = null;
        $param = $_REQUEST;
        $param['uid'] = Yii::app()->user->uid;
      
        if(is_numeric($param['id'])){
            $data = Organizations::api()->orgUpdate($param);
            if('succ' == $data['code']){ 
                $error = 0; $msg = '状态修改成功';
            }else{
                $msg = $data['message']; 
            }
        }else{
            $msg = 'ID必须是数字类型';
        }
        
        $this->_end($error, $msg);
    }
}
