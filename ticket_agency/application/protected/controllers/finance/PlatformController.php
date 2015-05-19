<?php

class PlatformController extends Controller {

    public $payPlatform = array(
        1 => 'kuaiqian',
        2 => 'alipay',
    );

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('api'),
                'users' => array('*'),
            ),
        ); 
    }

    public function filters() {
        return array('accessControl',);
    }
    
    public function actionIndex() {
        $para = array();
        $params = $_REQUEST;
        $org_id = Yii::app()->user->org_id;
        if(isset($params['tab'])) $data['tab'] = $params['tab'];
        if (!empty($org_id) && intval($org_id) > 0) {
            /* 平台金额 */
            $money = Unionmoney::api()->total(['org_ids' => $org_id], 0); //var_dump($money);
            if (isset($money['code']) && $money['code'] == 'succ') {
                $data['total'] = $money['body'];
            }
            /* 支付 */
            $data['trade_type'] = array('1' => '支付', '2' => '退款', '3' => '充值', '4' => '提现', '5' => '应收账款');
            $data['pay_type'] = array('1' => '快钱', '2' => '支付宝');

            //$e_order_ids = array();
            unset($_COOKIE['order_id']);
            
            
            /* 提现 */
            $bank = Bank::api()->list();
            $data['bank'] = $bank['body'];
            $param['organization_id'] = $org_id;
            $bank = Bank::api()->list_own($param, 0);
            $data['bank_own'] = empty($bank['body']) ? array() : $bank['body']['data'];
            $data['org_id'] = $org_id;
            $org = Yii::app()->user;
            $data['user_name'] = $org->display_name;
            $data['user_account'] = $org->account;
            $user = Users::model()->find('id=:id', ['id' => $org->uid]);
            $data['user_mobile'] = $user->mobile;
            /* 提现记录 */
            $data['status_labels'] = array('0' => '未打款', '1' => '已打款', '2' => '驳回');
            $data['status_class'] = array( '0' => 'danger', '1' => 'success','2' => 'info');
            $data['mode_type'] = array('credit' => '信用支付', 'advance' =>'储值支付','union' =>'平台支付', 'kuaiqian' => '快钱', 'alipay' => '支付宝');
            if (isset($params['status']) && !in_array($params['status'], array_keys($data['status_labels'])) ) {
                unset($params['status']);
            }
            $data['get'] = $params;

            if (!isset($params['time']) || empty($params['time'])) {
                $params['time'] = date('Y-m');
            }
            $month = explode('-', $params['time']);
            $days = cal_days_in_month(CAL_GREGORIAN, $month[1], $month[0]);
            $params['start_date'] = $params['time'] . '-01';
            $params['end_date'] = $params['time'] . '-'.$days;
            if (intval($org_id) > 0) {
                $params['org_ids'] = $org_id; //机构ID
                //$params['org_name'] = $org->display_name; //分销商名字
                $params['op_account'] = $org->account; //操作者账号	
                $params['trade_type'] = '4'; //交易类型:1支付,2退款,3充值,4提现,5应收账款
                $params['org_role'] = '0'; //机构角色：0分销售，1供应商
                $params['current'] = isset($params['page']) ? $params['page'] : 1;
                $params['items'] = 20;  //var_dump($params);exit;
                $result = Unionmoneyencash::api()->lists($params); 
                if ($result['code'] == 'succ') {
                    $data['lists'] = $result['body']; 
                    $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                    $data['pages']->pageSize = $params['items'];
                } //var_dump($data['lists']['pagination']['count']);exit;
            }//var_dump($data);exit;
        }
        $this->render('index', $data);
    }

	function actionFetchCashExport()
	{
		$params = $_REQUEST;
		$provider = array();
		$org_id = Yii::app()->user->org_id;
		if (intval($org_id) > 0) {
			Yii::import('ext.CSVExport');
			$org = Yii::app()->user;
			$data['status_labels'] = array('0' => '未打款', '1' => '已打款', '2' => '驳回');
			if (isset($params['status']) && !in_array($params['status'], array_keys($data['status_labels'])) ) {
                unset($params['status']); 
            }
			if (!isset($params['time']) || empty($params['time'])) {
                $params['time'] = date('Y-m');
            }
            $month = explode('-', $params['time']);
            $days = cal_days_in_month(CAL_GREGORIAN, $month[1], $month[0]);
            $params['start_date'] = $params['time'] . '-01';
            $params['end_date'] = $params['time'] . '-'.$days;
            
                $params['org_ids'] = $org_id; //机构ID
                //$params['org_name'] = $org->display_name; //分销商名字
                $params['op_account'] = $org->account; //操作者账号	
                $params['trade_type'] = '4'; //交易类型:1支付,2退款,3充值,4提现,5应收账款
                $params['org_role'] = '0'; //机构角色：0分销售，1供应商
                $params['current'] = isset($params['page']) ? $params['page'] : 1;
                $params['items'] = 20; 
                $result = Unionmoneyencash::api()->lists($params);
                if($result['body']['pagination']['count']=='0'){
                    $flag['msg'] = '对不起,没有找到相关记录,无法导出!';
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."<script>alert('".$flag['msg']."');history.go(-1);</script>";
                    exit; //json_encode($flag);
                }
                $provider_header[0] = array('id'=>'序列','created_at'=>'提现时间','apply_username'=>'操作人','apply_account'=>'用户账号','money'=>'金额',
                                            'type'=>'交易类型','status'=>'交易状态','union_money'=>'账户总余额');
            if ($result['code'] == 'succ') {
                 foreach ($result['body']['data'] as $key => $value) {
                    foreach ($provider_header[0] as $k => $val) {
                        if(in_array($k, array_keys($value) )){
                            if($k=='created_at') 
                                 $provider[$key][$k] =  date('y年m月d日',$value[$k]);
                            elseif($k=='money' || $k=='union_money') 
                                 $provider[$key][$k] =  number_format($value[$k],2);
                            elseif($k=='status' && $val=='1') 
                                 $provider[][$k] = '-'. $data['status_labels'][$value[$k]];
                            elseif($k=='status') 
                                 $provider[$key][$k] =  $data['status_labels'][$value[$k]];
                            else $provider[$key][$k] =  $value[$k];
                        }
                        if($k=='type' && isset($provider[$key]))
                                 $provider[$key][$k] =  '提现';
                    }
                 }
               }  
               if(!empty($provider)) $provider = array_merge($provider_header,$provider);

                $csv = new ECSVExport($provider,true,false); 
                $content = $csv->toCSV();
                if(isset($params['status'])) $filename = $params['time'].$data['status_labels'][$params['status']].'提现记录.csv'; 
                else
                $filename = $params['time'].'提现记录.csv'; 
                Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
                exit();
        }
        return '';
    }

    /**
     * 跳转到支付方式页面 ,成功生成充值流水单
     */
    public function actionPrePay() {
        Yii::import('application.extensions.Payments.*');
        $params = $_POST;
        $params['org_id'] = Yii::app()->user->org_id;
        $params['user_id'] = Yii::app()->user->uid;
        $params['user_name'] = Yii::app()->user->name;

        //预充值
        //Unionmoneyrecharge::api()->debug = true;
        $rs = Unionmoneyrecharge::api()->add($params);
        if (!ApiModel::isSucc($rs)) {
            $this->fail('充值出错啦！', '/finance/platform/', '下一步：重新跳转到充值页面');
            return false;
        }

        //得到平台资金充值记录
        $data = ApiModel::getData($rs);
        setcookie('order_id',$data['id'],time()+3600,'/','.piaotai.com');
        $info = array('subject' => '平台充值', 'show_url' => 'http://www.xxx.com/myorder.html');
        $method = $this->payPlatform[$_POST['pay_type']];
        $class = 'Payments' . ucfirst($method);
        $payment = new $class();
        $msg = '';
        $params = array();
        $params['order_id'] = $data['id'];
        $params['amount'] = $_POST['money'];
        $re = $payment->doPayUnion($params, $info, $msg);

        if ($re === false) {
            $this->fail('充值出错啦！', '/finance/platform/', '下一步：重新跳转到充值页面');
        }
    }

    public function actionApi() {
        Yii::import('application.extensions.Payments.*');

        $way = Yii::app()->request->getParam('way');
        if ($way == '99bill') {
            $type = Yii::app()->request->getParam('callback');
            $func = $type . 'UnionCallback';
            $payment = new PaymentsKuaiqian();
            $result = $payment->$func();
            if ($result['result'] == 1) {//成功
                $rs = Unionmoneyrecharge::api()->paid(array(
                    'id' => $_REQUEST['orderId'],
                    //'distributor_id' => substr($_REQUEST['ext2'], 7),
                    //'status' => 'succ',
                    //'payment' => 'kuaiqian',
                    //'user_id' => Yii::app()->user->uid,
                    //'user_name' => Yii::app()->user->account
                ));

                if (!ApiModel::isSucc($rs)) {
                    
                } //如果失败扔到异步执行成功
            }
            if ($type == 'async' || Yii::app()->user->isGuest) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . '/finance/platform/completed/id/' . $result['orderId'];
                echo "<result>{$result['result']}</result><redirecturl>$url</redirecturl>";
                exit();
            } else {
                $this->redirect('/finance/platform/completed/id/' . $result['orderId']);
            }
        }
    }

    public function actionCompleted() {
        $pid = Yii::app()->request->getParam('id');
        if ((int) $pid > 0) {
            $result = Unionmoneyrecharge::api()->detail(array(
                'id' => $pid,
                ), 0);
            if ($result['code'] == 'succ') {
                $data = ApiModel::getData($result) ;
                $status = $data['paid_at']?'succ':'fail';
            } else {
                $this->redirect('/finance/platform/');
            }
        } else {
            $this->redirect('/finance/platform/');
        }
        $data['pid'] = $pid;
        $data['status_labels'] = array('succ' => '成功', 'fail' => '失败', 'cancel' => '已取消', 'error' => '出错啦', 'invalid' => '参数不正确', 'progress' => '处理中', 'timeout' => '已超时', 'ready' => '就绪');
        $data['status'] = $status;

        $this->render('completed', $data);
    }

    public function actionState() {
        if(!isset($_COOKIE['order_id'])){
            echo 0;
            exit();
        }
        $result = Unionmoneyrecharge::api()->detail(array(
                'id' => $_COOKIE['order_id'],
                ), 0);
        if ($result['code'] == 'succ' && $result['body']['paid_at']) {
            echo $result['body']['id'];
        } else {
            echo 0;
        }
    }

/**提取现金
org_id 	是 	Int 	机构ID
apply_uid 	是 	Int 	申请者UID
apply_account 	是 	string 	申请者账号
apply_username 	否 	string 	申请者名称
apply_phone 	否 	string 	申请者联系电话
money 	是 	double 	申请提现额度
bank_id 	否 	int 	银行ID
bank_name 	否 	string 	银行名称
open_bank 	是 	string 	开户行
account 	是 	string 	账号/卡号
account_name 	是 	string 	账户名
remark 	否 	string 	备注
**/

    public function actionFetchapply() { 

		$params = $_POST;
		$flag = array();
		$band_id = '';
		//$this->actionPre();
		$params['organization_id'] = Yii::app()->user->org_id; 
        //Unionmoney::api()->debug= true;
 		if(!empty($_POST['bank_open'])) { 
 			$band_option = $_POST['bank_addid'];
 			$bank_op = explode(' _ ', $band_option);
 			$params['bank_id'] = $bank_op[0];
 			$params['bank_name'] = $bank_op[1];
 			$bankcard = $this->actionAddCard($params);
 			$band_id = $bankcard['body']['id'];

 		}elseif(!empty($_POST['bank_own'])){
 			$band_option = $_POST['bank_own'];
 			$bank_op = explode(' _ ', $band_option);
 			// $param['id'] = $band_id;
    //         $param['organization_id'] = $params['organization_id'];
    //         $bank = Bank::api()->list_own($param, 0); $bank['body']['data'][$bank_own]['account']
 			//$params['bank_id'] = $bank_op[0];
 			$params['bank_name'] = $bank_op[1];
            $params['bank_account'] = $bank_op[2];
 			$params['bank_open'] = $bank_op[3];
            $params['account_name'] = $bank_op[4];
 		}
 		if(empty($params['bank_name']))	{
 			$flag['status'] = false;
 			$flag['msg'] = '提取银行卡出错';
        	echo json_encode($flag);exit;
 		} 
        $para = array(
            'org_id' => Yii::app()->user->org_id,
            'money' => $_POST['amount'],
            'apply_uid' => Yii::app()->user->uid,
            'apply_account' => Yii::app()->user->account,
            'apply_username' => Yii::app()->user->display_name,
            //'apply_phone' => Yii::app()->user->phone,
            //'bank_id' => $band_id,
            'open_bank' => $params['bank_open'],
            'bank_name' => $params['bank_name'],
            'account' => $params['bank_account'],
            'account_name' => $params['account_name'],
        );
       
        $result = Unionmoneyencash::api()->apply($para, 0); 

        if ($result['code'] == 'succ') {
            $flag['status'] = true;
            $flag['msg'] = '申请提现成功!'; //.$result['body']['id']
            Yii::app()->redis->delete('code_for_fetchcash:' . Yii::app()->getSession()->getSessionId());
        } else{
            $flag['status'] = false;
            $flag['msg'] = $result['message'];
        }
        
        echo json_encode($flag);
        exit;
    }

    /*     * 添加银行卡
     * http://192.168.1.105:8090/pages/viewpage.action?pageId=4391045
     * ~/v1/bank/add_own
     * type 	是 	string 	‘bank’,‘alipay’
     * organization_id 	是 	int 	机构id
     * account 	是 	string 	账号
     * account_name 	是 	string 	账户名
     * bank_id 	是 	int 	银行id
     * open_bank 	否 	string 	开户行
     * */

    protected function actionAddCard($params = array()) {
        //$params = $_REQUEST;
        $param = array(
            'type' => 'bank',
            'organization_id' => $params['organization_id'],
            'account' => $params['bank_account'],
            'account_name' => $params['account_name'],
            'open_bank' => $params['bank_open'],
            'bank_id' => $params['bank_id'],
        ); 
        return Bank::api()->add_own($param, 0);
    }

    public function actionPre() {
        $data = array();
        $data['error'] = 0;
        $val = Yii::app()->request->getParam('code');
        $val = trim($val); 

        if (strlen($val) != 6 || $val != Yii::app()->redis->get('code_for_fetchcash:' . Yii::app()->getSession()->getSessionId())) {
            $data['msg'] = '短信验证码输入错误';
            $data['error'] = 1;
        } 
        echo json_encode($data);  exit;
    }

   
}
