<?php

class NewdetailController extends Controller
{
	public function actionIndex()
	{
		$data['status_labels'] = array('unpaid'=>'未确认','cancel' => '已取消','paid' => '已确认','finish' => '已结束');
		$detail = Order::api()->detail(array('id' => $_GET['id'],'distributor_id' => Yii::app()->user->org_id,'show_order_items'=>1));
		if($detail['code'] == 'succ'){
			$data['detail'] = $detail['body'];
			$data['ticket'] = $detail['body']['order_items'];
		}
		$this->render('index',$data);
	}


	//取消任务单
	public function actionCancel(){
		$param['id'] = $_POST['id'];
		$param['distributor_id'] = Yii::app()->user->org_id;
		$param['user_id'] = Yii::app()->user->id;
		$param['user_name'] = Yii::app()->user->name;
		$param['status'] = 'cancel';
		$data = Order::api()->update($param,0);
		if($data['code'] == 'succ'){
            $this->_end(0,$data['message'] );
        }else{
            $this->_end(1,$data['message'] );
        }
    }

    //确认任务单
    public function actionPaid(){
		$param['id'] = $_POST['id'];
		$param['distributor_id'] = Yii::app()->user->org_id;
		$param['user_id'] = Yii::app()->user->id;
		$param['user_name'] = Yii::app()->user->name;
		$param['status'] = 'paid';
		$data = Order::api()->update($param,0);
		if($data['code'] == 'succ'){
            $this->_end(0,$data['message'] );
        }else{
            $this->_end(1,$data['message'] );
        }
    }

}