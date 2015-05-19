<?php

class ReceivableController extends Controller
{
    /**
     * 应收账款
     * @return void
     * @author xuhongbin
     * */
    public function actionIndex()
    {
        $param = $_REQUEST;
        $param['supply_id'] = 0;
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $rs = Bill::api()->lists($param);
        $billData = Bill::api()->getData($rs);
        
        $data['bill'] = $billData['data'];
        $data['pages'] = new CPagination($billData['pagination']['count']);
        $data['pages']->pageSize = 15;
        
		$this->render('index',$data);
    }
    
    /**
     * 应收账款明细
     */
    public function actionDetail() {
        $param['id'] = $_GET['id'];
        $bill = Bill::api()->detail($param);
        $data['detail'] = $bill['body'];
        
        $this->layout = false;
        $this->render('detail', $data);
    }
}