<?php

class PayableController extends Controller
{
    /**
     * 应付账款
     * @return void
     * @author xuhongbin
     * */
    public function actionIndex()
    {
        $param = $_REQUEST;
        $param['agency_id'] = 0;
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $bill = Bill::api()->lists($param);
        $data['bill'] = $bill['body']['data'];
        $data['pages'] = new CPagination($bill['body']['pagination']['count']);
        $data['pages']->pageSize = 15;
        
        $this->render('index',$data);
    }
    
    /**
     * 应付账款明细
     */
    public function actionDetail() {
        $param['id'] = $_GET['id'];
        $bill = Bill::api()->detail($param);
        $data['detail'] = $bill['body'];
        
        $this->layout = false;
        $this->render('detail', $data);
    }
    
    /**
     * 打款确认
     */
    public function actionUploadshow()
    {
        $param['id'] = $_GET['id'];
        $bill = Bill::api()->detail($param);
        $data['billInfo'] = $bill['body'];
        
        $this->layout = false;
        $this->render('uploadshow', $data);
    }
    
    public function actionSetprove()
    {
        $post = $_REQUEST;
        
        $param['id'] = $post['bill_id'];
        $param['type'] = '0';
        $param['user_id'] = Yii::app()->user->uid;
        $param['user_account'] = Yii::app()->user->account;
        $param['user_name'] = Yii::app()->user->display_name;
        
        $res = Bill::api()->finish($param);
        if (Bill::isSucc($res)) {
            echo json_encode($res);
        } else {
            //echo '{"errors":{"msg":["订单状态设置失败"]}}';
            echo json_encode(array('errors' => array($res['message'])));
        }
    }
}