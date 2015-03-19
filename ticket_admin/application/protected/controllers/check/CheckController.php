<?php

use common\huilian\utils\Header;

class CheckController extends Controller {

    public function actionOrderCheck(){
        $this->childNav = '/check/check/orderCheck/';
        $this->actionIndex();
    }
    
    public function actionIndex() {
        //景点查询
        //Landscape::api()->debug = true ;
        $param = array();
        foreach ($_GET as $k => $v) {
            if (!empty($v))
                $param[$k] = $v;
        }
        $param['current'] = isset($param['page']) ? $param['page'] : 0;

       // Verification::api()->debug = true;
        $data = Verification::api()->record($param);
        $lists = ApiModel::getLists($data);
        $totalNums = $data['body']['total_nums'] ;
        $orderNums = $data['body']['order_nums'] ;
        //分页
        $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
		$param['status'] = 1;
		$param['items'] = 1000;
		$param['organization_id'] = YII::app()->user->org_id;
		// 此处不能为景区列表引入分页值，否则造成第二分页无法获取景区列表
		$data = Landscape::api()->lists(array_merge($param, array('current' => 0, )));	
		$landscapes = ApiModel::getLists($data);
		
		$pois = array();
		if(isset($_GET['landscape_id'])) {
			$lanid = $_GET['landscape_id'];
			$param['status'] = 1;
			$param['items'] = 1000;
			$param['landscape_ids'] = $lanid;
			//$param['organization_ids'] = YII::app()->user->org_id;
			$data = Poi::api()->lists($param);
			$pois = ApiModel::getLists($data);
		}
// 		Header::utf8();
// 		var_dump($lists);
// 		var_dump($landscapes);
// 		exit;
		$this->render('index', compact('lists', 'pages', 'landscapes', 'pois', 'totalNums', 'orderNums'));
    }
	
    /**
     * 备注：
     * - 需返回所有景点，不对用户进行organization_id筛选，即$param['organization_ids'] = YII::app()->user->org_id;
     */
	public function actionGetPoi() {
		$id = $_POST['id'];
		$param['status'] = 1;
		$param['items'] = 1000;
		$param['landscape_ids'] = $id;
		$data = Poi::api()->lists($param);
		$data['result'] = ApiModel::getLists($data);
		echo json_encode($data);
	}
	
	// 撤销
	public function actionCancel() {
		if(Yii::app()->request->isPostRequest) {
			$param['id'] = $_POST['id'];
			$param['supplier_id'] = YII::app()->user->org_id;
			$rs = Verification::api()->cancel($param);
			// print_r($rs);
			if($rs['code'] != 'fail') {
				$this->_end(0, $rs['message']);
			} else {
				$this->_end(1, $rs['message']);
			}
		}
	}

	public function actionReprint() {
		if(Yii::app()->request->isPostRequest) {
			// todo optimize
			//Verification::api()->debug = true;
			//得到历史记录
			$data = Verification::api()->recordDetail(array(
				'id' => $_POST['id']
			));
			$history = ApiModel::getData($data);
			if(empty($history))
				$this->_end(1, '不存在该历史记录');
			
			$landscape = Landscape::api()->detail(array('id' => $history['landscape_id'], ));
			if(empty($landscape)) {
				$this->_end(1, '无法获取景区信息');
			} else if($landscape['code'] == 'fail') {
				$this->_end(1, $landscape['message']);
			}
			
			isset($landscape['body']['name']) ? $landscapeName = $landscape['body']['name']  : $this->_end(1, '无法获取景点的名称');
			
			//得到订单信息 
			$orderId=$history['record_code'] ;
			$_rs = Order::api()->detail(array(
				'id' => $orderId,
				'show_order_items' => 1
			));
			if(empty($_rs)) {
				$this->_end(1, '请求接口失败');
			}else if($_rs['code'] == 'fail') {
				$this->_end(1, '无法得到订单信息');
			}

			$detail = ApiModel::getData($_rs);
			$orderItem = current($detail['order_items']);			
			//var_export($detail);

			$printInfo['order_id'] = $orderId;
			$printInfo['ticket_name'] = $orderItem['name'];
			$printInfo['num'] = $history['num'];
			$printInfo['date'] = date('Y/m/d H:i:s', $history['created_at']);
			$printInfo['op_name'] = Yii::app()->user->display_name;
			$printInfo['owner_name'] = $detail['owner_name'];
			$printInfo['owner_mobile'] = $detail['owner_mobile'];
			$printInfo['remark'] = $detail['remark'];
			$printInfo['lan_name'] = $landscapeName;
			$printInfo['operator'] = Yii::app()->user->id; // 操作员，执行打印行为的后台人员
			// LogCollect::add('验证小票成功');
			$this->_end(0, '重打小票成功', $printInfo);
		}
	}
}
