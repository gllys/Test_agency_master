<?php

class DetailController extends Controller
{
	public function actionIndex()
	{
		$data['status_labels'] = array('unpaid'=>'未支付','cancel' => '已取消','paid' => '已付款','finish' => '已结束','billed' => '已结款');
		$data['paid_type'] = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
		$detail = Order::api()->detail(array('id' => $_GET['id'],'distributor_id' => Yii::app()->user->org_id),0);
		if($detail['code'] == 'succ'){
			$data['detail'] = $detail['body'];
			$data['ticket'] = $detail['body']['order_items'];
			$param['order_id'] = $detail['body']['id'];
			$param['allow_status'] = 3;
			$result = Refund::api()->apply_list($param);
			if($result['code'] == 'succ' && !empty($result['body']['data'])){
				$sum = 0;
				$rs = $result['body']['data'];
				foreach ($rs as $value) {
					$sum += $value['nums'];
				}
				$data['refund_num'] = $sum;
			}else{
				$data['refund_num'] = 0;
			}
		}
		$this->render('index',$data);
	}
        
        public function  actionApply(){
            if(Yii::app()->request->isPostRequest){
                $param = $_POST;
                $param['user_id'] = Yii::app()->user->org_id;
                $data = Refund::api()->apply($param,0);
                
                if (ApiModel::isSucc($data)) {
                    $this->_end(0, $data['message']);
                } else {
                    $this->_end(1, $data['message']);
                }
            }
        }

    public function  actionAgainSms(){
        if(Yii::app()->request->isPostRequest){
            $rs = Order::api()->lists(array(
	            'items' => 1,
	            'distributor_id' => Yii::app()->user->org_id,
	            'ids' => $_POST['id'],
	            'fields' => 'id,nums,used_nums,use_day,refunding_nums,refunded_nums,distributor_id,distributor_name,owner_mobile,owner_name,send_sms_nums',
            ),0);
            
	        if ($rs['code'] == 'succ') {
	        	$orderInfo = $rs['body']['data'][0];
	        	$orderInfo['nums'] = $orderInfo['nums'] - $orderInfo['used_nums'] - $orderInfo['refunding_nums'] - $orderInfo['refunded_nums'];
		        $sms = new SMS();
		        $orderInfo['host'] = Yii::app()->getRequest()->getHostInfo();
		        $content = $sms->_getCreateOrderContent($orderInfo);
		        $result = $sms->sendSMS($_POST['mobile'], $content);
		        if($result){
			        echo json_encode(array('errors'=>'短信发送成功！！！'));
			        exit;
		        }
	        }
	        echo json_encode(array('errors'=>'短信发送失败！'));
        }
    }

    public function actionCancel(){
		$param['id'] = $_POST['id'];
		$param['distributor_id'] = Yii::app()->user->org_id;
		$param['user_id'] = Yii::app()->user->uid;
		$param['user_name'] = Yii::app()->user->account;
		$param['status'] = 'cancel';
		$data = Order::api()->update($param);
		if($data['code'] == 'succ'){
            $this->_end(0,$data['message'] );
        }else{
            $this->_end(1,$data['message'] );
        }
    }

}
