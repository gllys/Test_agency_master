<?php

class PlatformController extends Controller
{
	public function actionIndex()
	{
		$para = array();
		$params = $_REQUEST;
		$org_id = Yii::app()->user->org_id; 
		if(!empty($org_id) && intval($org_id) <= 0){
			$this->redirect('/finance/flatform/index');
		}
		if(isset($params['tab'])) $data['tab'] = $params['tab'];
		/*平台金额*/
		$money = Unionmoney::api()->total(array('org_ids'=>$org_id),0); 
		if(isset($money['code'])&&$money['code']=='succ') {	
				$data['total'] = $money['body'];
			}
		$data['trade_type'] = array('1' => '支付', '2' => '退款', '3' => '充值', '4' => '提现', '5' => '应收账款');
		/*提现*/
		$bank = Bank::api()->list(array('fields'=>'id,name'));
		$data['bank'] = $bank['body'];
		$param['organization_id'] = $org_id;
		$bank = Bank::api()->list_own($param,0);
		$data['bank_own'] = empty($bank['body']) ? array() : $bank['body']['data'];
		$data['org_id'] = $org_id;
		$org = Yii::app()->user;
		$data['user_name'] = $org->display_name;
        $data['user_account'] = $org->account;
		$user = Users::model()->find('id=:id',array('id' => $org->uid));
		$data['user_mobile'] = $user->mobile;	
		/*提现记录*/
		$data['status_labels'] = array('0' => '未打款', '1' => '已打款', '2' => '驳回');
		$data['status_class'] = array( '0' => 'danger', '1' => 'success','2' => 'info');
        $data['mode_type'] = array('credit'=>'信用支付','advance' =>'储值支付','union' =>'平台支付','kuaiqian'=>'快钱','alipay'=>'支付宝');
        	 if (isset($params['status']) && (!in_array($params['status'], array_keys($data['status_labels']))||$params['status']==='')) {
                unset($params['status']);
            }
          	$data['get'] = $params;

        	if(!isset($params['time']) || empty($params['time']) ){
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
            $params['org_role'] = '1';//机构角色：0分销售，1供应商
            $params['current'] = isset($params['page']) ? $params['page'] : 1;
            $params['items'] = 20; //var_dump($params);
            $result = Unionmoneyencash::api()->lists($params); 
            

            if ($result['code'] == 'succ') {
                $data['lists'] = $result['body'];
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items']; 
            } 
        }

        $this->render('index', $data);
	}

	function actionFetchCashExport()
    {
        Yii::import('application.extensions.PHPExcel');
        require_once "PHPExcel.php";
        require_once "PHPExcel/Autoloader.php";
        Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Load'), true);
        $params = $_REQUEST;
        $provider = array();
        $org_id = Yii::app()->user->org_id;
        if (intval($org_id) > 0) {
            set_time_limit(180000);
            ini_set('memory_limit', '256M');
            $path = YiiBase::getPathOfAlias('webroot') . '/assets';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $objExcel = PHPExcel_IOFactory::load($path . '/export-platform-template.xls');
            $objExcel->setActiveSheetIndex(0);
            $sheet = $objExcel->getActiveSheet();
            // Yii::import('ext.CSVExport');
            $org = Yii::app()->user;
            $data['status_labels'] = array('0' => '未打款', '1' => '已打款', '2' => '驳回');
            if (isset($params['status']) && !in_array($params['status'], array_keys($data['status_labels']))) {
                unset($params['status']);
            }
            if (!isset($params['time']) || empty($params['time'])) {
                $params['time'] = date('Y-m');
            }
            $month = explode('-', $params['time']);
            $days = cal_days_in_month(CAL_GREGORIAN, $month[1], $month[0]);
            $params['start_date'] = $params['time'] . '-01';
            $params['end_date'] = $params['time'] . '-' . $days;

            $params['org_ids'] = $org_id; //机构ID
            //$params['org_name'] = $org->display_name; //分销商名字
            $params['op_account'] = $org->account; //操作者账号	
            $params['trade_type'] = '4'; //交易类型:1支付,2退款,3充值,4提现,5应收账款
            $params['org_role'] = '1'; //机构角色：0分销售，1供应商
            $params['current'] = isset($params['page']) ? $params['page'] : 1;
            $params['items'] = 20;
            $result = Unionmoneyencash::api()->lists($params);
            if ($result['body']['pagination']['count'] == '0') {
                $flag['msg'] = '对不起,没有找到相关记录,无法导出!';
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "<script>alert('" . $flag['msg'] . "');history.go(-1);</script>";
                exit; //json_encode($flag);
            }
            $provider_header[0] = array('id' => '序列', 'created_at' => '提现时间', 'apply_username' => '操作人', 'apply_account' => '用户账号', 'money' => '金额', 'type' => '交易类型', 'status' => '交易状态', 'union_money' => '账户总余额');
            if ($result['code'] == 'succ') {
                $last_row = count($result['body']['data']) + 2;
                $i = 4;
                foreach ($result['body']['data'] as $key => $value) {
                    $value['union_money'] = intval(100 * $value['union_money'] - 100 * $value['money']) / 100;
                    if ($i < $last_row) {
                        if (0 != $i % 2) {
                            $sheet->duplicateStyle($sheet->getStyle('A3:H3'), 'A' . $i . ':H' . $i);
                        } else {
                            $sheet->duplicateStyle($sheet->getStyle('A2:H2'), 'A' . $i . ':H' . $i);
                        }
                    }
                    foreach ($provider_header[0] as $k => $val) {
                        if (in_array($k, array_keys($value))) {
                            if ($k == 'created_at')
                                $provider[$key][$k] = date('Y年m月d日', $value[$k]);
                            elseif ($k == 'money' || $k == 'union_money')
                                $provider[$key][$k] = number_format($value[$k], 2);
                            elseif ($k == 'status' && $val == '1')
                                $provider[][$k] = '-' . $data['status_labels'][$value[$k]];
                            elseif ($k == 'status')
                                $provider[$key][$k] = $data['status_labels'][$value[$k]];
                            else
                                $provider[$key][$k] = $value[$k];
                        }
                        if ($k == 'type' && isset($provider[$key]))
                            $provider[$key][$k] = '提现';
                    }
                    $i++;
                }
            }
            // if(!empty($provider)) $provider = array_merge($provider_header,$provider);
            $objExcel->getActiveSheet()->fromArray($provider, null, 'A2');
            $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
            // $csv = new ECSVExport($provider,true,false); 
            // $content = $csv->toCSV();
            if (isset($params['status']) && isset($data['status_labels'][$params['status']])) {
                $filename = $params['time'] . $data['status_labels'][$params['status']] . '提现记录.xls';
            } else {
                $filename = $params['time'] . '提现记录.xls';
            }
            $filename = iconv("UTF-8", "GBK", $filename);
            // Yii::app()->getRequest()->sendFile($filename, $content . "\r\n", "text/csv", false);
            ob_end_clean();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type: application/vnd.ms-excel;");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            header("Content-Disposition:attachment;filename=" . $filename);
            header("Content-Transfer-Encoding:binary");
            $objWriter->save("php://output");
            exit();
        }
        return '';
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
 			$flag['msg'] = '请选择银行卡';
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
        } else{
            $flag['status'] = false;
            $flag['msg'] = $result['message'];
        }
        
        echo json_encode($flag);
        exit;
    }


/**添加银行卡
*http://192.168.1.105:8090/pages/viewpage.action?pageId=4391045
*~/v1/bank/add_own
*type 	是 	string 	‘bank’,‘alipay’
*organization_id 	是 	int 	机构id
*account 	是 	string 	账号
*account_name 	是 	string 	账户名
*bank_id 	是 	int 	银行id
*open_bank 	否 	string 	开户行
**/
	protected function actionAddCard($params=array())	{
		//$params = $_REQUEST;
        $param = array(
        	'type'=>'bank',
        	'organization_id'=>$params['organization_id'],
        	'account'=>$params['bank_account'],
        	'account_name'=>$params['account_name'],
        	'open_bank'=>$params['bank_open'],
        	'bank_id'=>$params['bank_id'],
        	); 
         return  Bank::api()->add_own($param,0);
	}

public function actionPre() {
        $data = array();
        $data['error'] = 0;
        $val = Yii::app()->request->getParam('code');
        $val = trim($val);
       $code_arr = explode(',', Yii::app()->redis->get('code_for_fetchcash:' . Yii::app()->getSession()->getSessionId()));
        if (strlen($val) != 6 || $val != $code_arr['0']) {
            $data['msg'] = '短信验证码输入错误';
            $data['error'] = 1;
        } 
        echo json_encode($data);  exit;
    }



}
