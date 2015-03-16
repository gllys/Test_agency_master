<?php

class RefundController extends Controller
{
    //退款列表
	public function actionIndex() {
		$param = $getval = array_filter($_GET);
		if (!empty($param) && isset($param['createdat'])) {
			$param['created_at'] = implode(' - ', $param['createdat']);
			if ($param['created_at'] == ' - ') {
				unset($param['created_at']);
			}
			unset($param['createdat']);
		}

		if (isset($_GET['status']) && trim($_GET['status']) !== '') {
			$param['status'] = intval($_GET['status']);
		}

		if(isset($_GET['pay_app_id']) && trim($_GET['pay_app_id']) !== ''){
			$param['pay_app_id'] = strval($_GET['pay_app_id']);
		}

		$param['current']        = isset($param['page']) ? $param['page'] : 0;
		$param['distributor_id'] = YII::app()->user->org_id;
		//print_r($param);
		$lists = Refund::api()->apply_list($param, 0);
		//print_r($lists);
		$list  = ApiModel::getLists($lists);
		// print_r($list);
		//分页
		$pagination      = ApiModel::getPagination($lists);
		$pages           = new CPagination($pagination['count']);
		$pages->pageSize = 15; #每页显示的数目

		//print_r($list);
		$this->render('index', compact('list', 'pages', 'getval'));
	}
        
        //单个订单信息
        public function actionPoint(){
            $param = $_GET;
            $infos = Refund::api()->apply_list($param);
            $infoid =  ApiModel::getLists($infos);
            $info = $infoid[$_GET['id']] ;
            $this->renderPartial('point',compact('info'));
        }
}
