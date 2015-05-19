<?php

class ConfigController extends Controller
{
    /**
     * 结算配置
     * @return void
     * @author xuhongbin
     * */
    public function actionIndex()
    {
        $rs = Bill::api()->getconf(array());
        $data['config'] = $rs['body'];
        $data['weekArray'] = Bill::getWeekDay();

        //得到供应商列表
        $param = array();
        $param['items'] = 100000;
        $param['type'] = 'supply';
        $rs = Organizations::api()->list($param);

        $data['orgList'] = ApiModel::getLists($rs);


        $param = array();
        $param['items'] = 100000;
        $param['balance_type'] = '1,2';
        $param['sortby'] = 'updated_at:desc';
        $rs = Unionmoney::api()->lists($param);

        $data['list'] = ApiModel::getLists($rs);
        
        $this->render('index', $data);
    }
    
    /**
     * 保存系统结算周期配置
     *
     * @return void
     * @author xuhongbin
     * */
    public function actionSavesetting()
    {
        // echo 'what the fuck';exit();
        $post = $_REQUEST;
        $param['conf_bill_type'] = $post['account_cycle'] == "month" ? 0 : 1;
        $param['conf_bill_value'] = $post['account_cycle_day'];
        $rs = Bill::api()->setconf($param);
        if (Bill::isSucc($rs)) {
            echo json_encode(array('data' => array($rs['body'])));
        } else {
            echo json_encode(array('errors' => array($rs['message'])));
        }
    }
    
    /**
     * 设置供应商平台结算周期
     *
     * @return void
     * @author xuhongbin
     * */
    public function actionSavesupplyconfig()
    {
        $post = $_REQUEST;
        $param['org_id'] = $post['supply_id'];
        $param['user_id'] = Yii::app()->user->uid;
        $param['user_name'] = Yii::app()->user->display_name;
        if ($post['account_cycle']) {
            $param['balance_type'] = $post['account_cycle'] == "month" ? 2 : 1;
        } else {
            $param['balance_type'] = $post['account_cycle'];
        }
        $param['balance_cycle'] = $post['account_cycle_day'];
        $rs = Unionmoney::api()->set($param);
        if (Bill::isSucc($rs)) {
            echo json_encode(array('data' => array($rs['body'])));
        } else {
            echo json_encode(array('errors' => array($rs['message'])));
        }
    }
}