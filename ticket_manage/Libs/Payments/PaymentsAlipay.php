<?php
/* *
 * 功能：即时到账交易接口
 * 支付宝版本：3.3
 *
 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 *
 * 
 *
 */
final class PaymentsAlipay
{
	public $appKey       = 'alipay';
	public $appName      = '支付宝';
	public $displayName  = '支付宝';
	public $payType      = 'online';
	public $version      = '1.0';

	// 服务器异步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数
	private $notify_url;
	//页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数
	private $return_url;
	// 退款服务器异步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数
	private $refund_notify_url;
	//提交到支付宝的地址 
	private $alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';
	// 支付宝支付成功回调页面
	private $sign_type          = 'MD5';
	//提交的方式
	private $submit_method      = 'POST';
	// //页面字符
	private $input_charset      = 'utf-8';
	//partner
	private $_partner           = '2088901261578126';
	//partner
	private $_key               = 'q9l8ulrvomddvg2ggfve2wo0fkh7b75l';
	//卖家账号
	private $seller_email       = 'xiexiao@acelinked.com';

	public function __construct()
	{
		$this->load  = new Load();
	}

	/**
	 * 支付宝支付
	 * @param $payment 支付单的数据
	 * @param $orderInfo 订单数据
	 * @param $msg 
	 * return bool
	 */
	public function dopay($payment, $orderInfo, &$msg)
	{
		header("content-Type: text/html; charset=utf-8");
		$this->notify_url = 'http://'.PI_APP_DOMAIN.'/api/alipay/async_callback.html';
		$this->return_url = 'http://'.PI_APP_DOMAIN.'/api/alipay/sync_callback.html';

		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//服务器异步通知页面路径
		$notify_url = $this->notify_url;
		//需http://格式的完整路径，不能加?id=123这类自定义参数

		//页面跳转同步通知页面路径
		$return_url = $this->return_url;
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

		//卖家支付宝帐户
		$seller_email = $this->seller_email;
		//必填

		//商户订单号 指支付单号
		$out_trade_no = $payment['id'];

		//订单名称
		$subject = $orderInfo['landscape']['name'].'-'.$orderInfo['ticket']['name'];
		//必填

		//付款金额
		$total_fee = $payment['money'];
		//必填

		//订单描述
		$body = '订单id:'.$payment['order_id'];
		//商品展示地址
		$show_url = 'http://'.PI_APP_DOMAIN.'/shopping_reserve_'.$orderInfo['ticket']['ticket_template_id'].'.html';
		//需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

		//防钓鱼时间戳
		$anti_phishing_key = $this->_query_timestamp();
		//若要使用请调用类文件submit中的query_timestamp函数

		//客户端的IP地址
		$exter_invoke_ip = $this->_getip();
		//非局域网的外网IP地址，如：221.0.0.1
		//构造要请求的参数数组
		$parameter = array(
			"service"           => "create_direct_pay_by_user",
			"partner"           => trim($this->_partner),
			"payment_type"      => 1, //支付类型 商品购买
			"notify_url"        => $notify_url,
			"return_url"        => $return_url,
			"seller_email"      => $seller_email, //卖家支付宝帐户
			"out_trade_no"      => $out_trade_no,
			"subject"           => $subject,
			"total_fee"         => $total_fee,
			"body"              => $body,
			"show_url"          => $show_url,
			"anti_phishing_key" => $anti_phishing_key,
			"exter_invoke_ip"   => $exter_invoke_ip,
			"_input_charset"    => trim(strtolower($this->input_charset))
		);

		$logMsg = date('Y-m-d H:i:s')." pay start \n order_id:".$payment['order_id'].",payment_id:".$payment['id']."\n";
		logMsgToFile($this->_getLogPath('pay') , $logMsg);
		$html_text = $this->buildRequestForm($parameter,"get", "");
		echo $html_text;
	}

	//支付接口同步回调-GET 
	public function syncCallback()
	{
		$requestInfo = explode('?', $_SERVER['REQUEST_URI']);
		if(isset($requestInfo[1]) && $requestInfo[1]) {
			$querystring = $requestInfo[1];
			if($querystring) {
				$querystring = substr($querystring, 0);
				$arrStr      = explode("&", $querystring);
				foreach ($arrStr as $str) {
					$arrSplits                              = explode("=", $str);
					$arrQueryStrs[urldecode($arrSplits[0])] = urldecode($arrSplits[1]);
				}

				$logMsg = date('Y-m-d H:i:s')." alipay synccallback \n payment_id:".$arrQueryStrs['out_trade_no']."\n callback params:".$querystring."\n";
				logMsgToFile($this->_getLogPath('pay') , $logMsg);
			}
		}

		$partner = $this->_partner;
		$key     = $this->_key;

		if($this->_getSignVeryfy($arrQueryStrs, $arrQueryStrs['sign'])) {
			$ret = $this->_getPaymentData($arrQueryStrs);
			switch($arrQueryStrs['trade_status']) {
				case 'TRADE_FINISHED':
					$ret['status'] = 'succ';
					break;
				case 'TRADE_SUCCESS':
					$ret['status'] = 'succ';
					break;
			}
			$logMsg = date('Y-m-d H:i:s')." alipay synccallback sign OK \n";
			logMsgToFile($this->_getLogPath('pay') , $logMsg);
		} else {
			$logMsg = date('Y-m-d H:i:s')." alipay synccallback error:error_msg:Invalid Sign \n payment_id:".$arrQueryStrs['out_trade_no']."\n callback params:".$querystring."\n";
			logMsgToFile($this->_getLogPath('pay') , $logMsg);
			$ret['message'] = 'Invalid Sign';
			$ret['status'] = 'error';
		}

		$paymentsModel  = $this->load->model('payments');
		$oldPaymentInfo = $paymentsModel->getOne('id='.$ret['id'], '','order_id,status');
		$ordersModel    = $this->load->model('orders');
		if($ret['status'] == 'succ' && $oldPaymentInfo['status'] != 'succ') {
			$logMsg = date('Y-m-d H:i:s')." alipay callback succ \n payment_id:".$ret['id']."\n money:".$ret['money']." \n";
			logMsgToFile($this->_getLogPath('pay') , $logMsg);
			$this->_alipayPayFinish($ret);
		}

		header("Location:".base_url()."/shopping_succ_".$oldPaymentInfo['order_id'].".html");
	}

	//支付接口异步回调-POST 异步成功须echo success
	public function asyncCallback()
	{
		$arrQueryStrs = PI::get('post');
		$logMsg       = date('Y-m-d H:i:s')." alipay asynccallback \n payment_id:".$arrQueryStrs['out_trade_no']."\n callback params:".$this->_createLinkstring($arrQueryStrs)."\n";
		logMsgToFile($this->_getLogPath('pay') , $logMsg);
		if($this->_getSignVeryfy($arrQueryStrs, $arrQueryStrs['sign'])) {
			$ret = $this->_getPaymentData($arrQueryStrs);
			switch($arrQueryStrs['trade_status']){
				case 'WAIT_BUYER_PAY':
					echo "success";
					$ret['status'] = 'ready';
					break;
				case 'TRADE_FINISHED':
					echo "success";
					$ret['status'] = 'succ';
					break;
				case 'TRADE_SUCCESS':
					echo "success";
					$ret['status'] = 'succ';
					break;
			}
			$logMsg = date('Y-m-d H:i:s')." alipay asynccallback sign OK \n";
			logMsgToFile($this->_getLogPath('pay') , $logMsg);
		}else{
			$logMsg = date('Y-m-d H:i:s')." alipay asynccallback error:error_msg:Invalid Sign \n payment_id:".$arrQueryStrs['out_trade_no']."\n callback params:".$this->_createLinkstring($arrQueryStrs)."\n";
			logMsgToFile($this->_getLogPath('pay') , $logMsg);
			$ret['message'] = 'Invalid Sign';
			$ret['status'] = 'error';
		}

		$paymentsModel  = $this->load->model('payments');
		$oldPaymentInfo = $paymentsModel->getOne('id='.$ret['id'], '','order_id,status');
		if($ret['status'] == 'succ'  && $oldPaymentInfo['status'] != 'succ') {
			$logMsg = date('Y-m-d H:i:s')."alipay callback succ \n payment_id:".$ret['id']."\n money:".$ret['money']." \n";
			logMsgToFile($this->_getLogPath('pay') , $logMsg);
			$this->_alipayPayFinish($ret);
		}
	}

	//获取支付单的数据
	public function _getPaymentData($arrQueryStrs)
	{
		$ret['id']          = $arrQueryStrs['out_trade_no'];
		$ret['money']       = $arrQueryStrs['total_fee'];
		$ret['account']     = '上海汇联皆景信息科技有限公司';
		$ret['bank']        = '支付宝';
		$ret['pay_account'] = $arrQueryStrs['buyer_email'];
		$ret['remark']      = $arrQueryStrs['body'];
		$ret['payment_bn']  = $arrQueryStrs['trade_no'];
		$ret['updated_at']  = $arrQueryStrs['notify_time'] ? $arrQueryStrs['notify_time'] : date('Y-m-d H:i:s');
		return $ret;
	}

	/**
	 * 支付宝支付完成
	 * @param $ret 支付单的数据
	 * return bool
	 */
	private function _alipayPayFinish($ret)
	{
		$paymentsModel  = $this->load->model('payments');
		$updateData     = array(
			'account'     => $ret['account'],
			'bank'        => $ret['bank'],
			'pay_account' => $ret['pay_account'],
			'remark'      => $ret['remark'],
			'payment_bn'  => $ret['payment_bn'],
			'updated_at'  => $ret['updated_at'],
			'status'      => $ret['status'],
		);

		$filter = 'id=\''.$ret['id'].'\' AND status <> \'succ\'';

		$result       = $paymentsModel->update($updateData, $filter);
		$affectedRows = $paymentsModel->affectedRows();
		if($result && $affectedRows >= 1) {
			$newPaymentsInfo = $paymentsModel->getOne('id=\''.$ret['id'].'\'');
			$orderCommon     = $this->load->common('order');
			$orderCommon->payFinish($newPaymentsInfo['order_id'], $newPaymentsInfo, $msg);
			return true;
		} else {
			return false;
		}
	}


	/**
	 * 支付宝 退款接口
	 * @param $refund 支付单的数据
	 * return bool
	 */
	public function doRefund($refund, &$msg)
	{
		header("content-Type: text/html; charset=utf-8");
		$this->refund_notify_url = 'http://'.PI_APP_DOMAIN.'/api/alipay/async_refund_callback.html';

		//服务器异步通知页面路径
		$refund_notify_url = $this->refund_notify_url;
		//需http://格式的完整路径，不能加?id=123这类自定义参数

		//卖家支付宝帐户
		$seller_email = $this->seller_email;
		//必填

		//退款当天日期
		$refund_date = date('Y-m-d H:i:s', time());
		//必填，格式：年[4位]-月[2位]-日[2位] 小时[2位 24小时制]:分[2位]:秒[2位]，如：2007-10-01 13:13:13

		//批次号
		$refundsModel  = $this->load->model('refunds');
		$batch_no      = $refundsModel->genBatchNo($refund['id']);

		//更新该退款单的批次号
		$refundsModel->update(array('batch_no' => $batch_no), array('id' => $refund['id']));
		//必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001

		//退款笔数
		$batch_num = 1;
		//必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）

		//退款详细数据  支付宝支付的流水号^退款金额^refund_id_(退款单id),order_id_(订单id)  便于回来的时候更新
		$detail_data = $refund['payment_bn'].'^'.$refund['money'].'^refund_id_'.$refund['id'].',order_id_'.$refund['order_id'];
		//必填，具体格式请参见接口技术文档

		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service"        => "refund_fastpay_by_platform_pwd",
			"partner"        => trim($this->_partner),
			"notify_url"     => $refund_notify_url,
			"seller_email"   => $seller_email,
			"refund_date"    => $refund_date,
			"batch_no"       => $batch_no,
			"batch_num"      => $batch_num,
			"detail_data"    => $detail_data,
			"_input_charset" => trim(strtolower($this->input_charset))
		);

		$logMsg = date('Y-m-d H:i:s')." refund start \n order_id:".$refund['order_id']."\n";
		logMsgToFile($this->_getLogPath('refund') , $logMsg);
		$html_text = $this->buildRequestForm($parameter,"get", "");
		echo $html_text;
	}

	//退款的异步返回处理
	public function asyncRefundCallback()
	{
		$arrQueryStrs = PI::get('post');
		$ret          = $this->_getRefundData($arrQueryStrs);
		$logMsg       = date('Y-m-d H:i:s')." alipay asyncrefundcallback \n refund_id:".$ret['id'].",batch_no:".$ret['batch_no']."\n callback params:".$this->_createLinkstring($arrQueryStrs)."\n";
		logMsgToFile($this->_getLogPath('refund') , $logMsg);

		//验证
		if($this->_getSignVeryfy($arrQueryStrs, $arrQueryStrs['sign'])) {

			$logMsg = date('Y-m-d H:i:s')." alipay asyncrefundcallback sign OK \n";
			logMsgToFile($this->_getLogPath('refund') , $logMsg);
			if($arrQueryStrs['success_num'] >= 1) {
				echo 'success';
				$this->_alipayRefundFinish($ret);
			}
		}else{
			$logMsg = date('Y-m-d H:i:s')." alipay asyncrefundcallback error:error_msg:Invalid Sign \n refund_id:".$ret['id']."\n callback params:".$this->_createLinkstring($arrQueryStrs)."\n";
			logMsgToFile($this->_getLogPath('refund') , $logMsg);
			$ret['message'] = 'Invalid Sign';
			$ret['status'] = 'error';
		}
	}

	//获取支付单的数据
	private function _getRefundData($arrQueryStrs)
	{
		$ret['batch_no']    = $arrQueryStrs['batch_no'];
		$ret['bank']        = '支付宝';
		$resultDetails      = explode('^', $arrQueryStrs['result_details']);
		$ret['payment_bn']  = $resultDetails[0];
		$ret['money']       = $resultDetails[1];
		$ret['updated_at']  = $arrQueryStrs['notify_time'] ? $arrQueryStrs['notify_time'] : date('Y-m-d H:i:s');
		return $ret;
	}

	/**
	 * 支付宝支付完成
	 * @param $ret 支付单的数据
	 * return bool
	 */
	private function _alipayRefundFinish($ret)
	{
		$refundsModel  = $this->load->model('refunds');
		$updateData     = array(
			'bank'       => $ret['bank'],
			'payment_bn' => $ret['payment_bn'],
			'updated_at' => $ret['updated_at'],
			'money'      => $ret['money'],
			'status'     => 'succ',
		);

		$filter = 'payment_bn=\''.$ret['payment_bn'].'\' AND status <> \'succ\' AND batch_no=\''.$ret['batch_no'].'\'';

		$result       = $refundsModel->update($updateData, $filter);
		$affectedRows = $refundsModel->affectedRows();
		if($result && $affectedRows >= 1) {
			$logMsg = date('Y-m-d H:i:s')." alipay asyncrefundcallback refund update local succ \n";
			logMsgToFile($this->_getLogPath('refund') , $logMsg);
			$newRefundInfo     = $refundsModel->getOne('payment_bn=\''.$ret['payment_bn'].'\' AND batch_no=\''.$ret['batch_no'].'\'');
			$refundApplyCommon = $this->load->common('refundApply');
			$refundApplyCommon->refundFinish($newRefundInfo, $msg);
			return true;
		} else {
			$logMsg = date('Y-m-d H:i:s')." alipay asyncrefundcallback refund update local fail \n";
			logMsgToFile($this->_getLogPath('refund') , $logMsg);
			return false;
		}
	}

	//获取日志的路径， pay 、refund
	private function _getLogPath($type)
	{
		return PI_LOG_BASE_PATH.'alipay/'.$type.'/'.date('Y-m-d').'.log';
	}


	/* ==============以下方法基本是alipay sdk上的内容，经过微小改动 ==================*/
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	public function buildRequestMysign($para_sort) 
	{
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->_createLinkstring($para_sort);
		
		$mysign = "";
		switch (strtoupper(trim($this->sign_type))) {
			case "MD5" :
				$mysign = $this->_md5Sign($prestr, $this->_key);
				break;
			default :
				$mysign = "";
		}
		
		return $mysign;
	}

	/**
	 * 生成要请求给支付宝的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组
	 */
	public function buildRequestPara($para_temp) 
	{
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->_paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = $this->_argSort($para_filter);

		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = strtoupper(trim($this->sign_type));
		
		return $para_sort;
	}

	/**
	 * 建立请求，以表单HTML形式构造（默认）
	 * @param $para_temp 请求参数数组
	 * @param $method 提交方式。两个值可选：post、get
	 * @param $button_name 确认按钮显示文字
	 * @return 提交表单HTML文本
	 */
	public function buildRequestForm($para_temp, $method, $button_name) 
	{
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->alipay_gateway_new."_input_charset=".trim(strtolower($this->input_charset))."' method='".$method."'>";
		while (list ($key, $val) = each ($para)) {
			$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
		}

		//submit按钮控件请不要含有name属性
		$sHtml = $sHtml."<input type='submit' value='".$button_name."' style=\"display:none\"></form>";
		$sHtml .= "正在提交请求，请稍后。。。";
		
		$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
		
		return $sHtml;
	}

	/**
	 * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
	 * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
	 * return 时间戳字符串
	 */
	private function _query_timestamp() 
	{
		$url = $this->alipay_gateway_new."service=query_timestamp&partner=".trim(strtolower($this->_partner))."&_input_charset=".trim(strtolower($this->input_charset));
		$encrypt_key = "";

		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
		
		return $encrypt_key;
	}

	/**
	 * 除去数组中的空值和签名参数
	 * @param $para 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 */
	private function _paraFilter($para) 
	{
		$para_filter = array();
		while (list ($key, $val) = each ($para)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para_filter[$key] = $para[$key];
		}
		return $para_filter;
	}
	/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
	 */
	private function _argSort($para) 
	{
		ksort($para);
		reset($para);
		return $para;
	}

	/**
	 * 签名字符串
	 * @param $prestr 需要签名的字符串
	 * @param $key 私钥
	 * return 签名结果
	 */
	private function _md5Sign($prestr, $key) 
	{
		$prestr = $prestr . $key;
		return md5($prestr);
	}

	/**
	 * 验证签名
	 * @param $prestr 需要签名的字符串
	 * @param $sign 签名结果
	 * @param $key 私钥
	 * return 签名结果
	 */
	private function _md5Verify($prestr, $sign, $key) 
	{
		$prestr = $prestr . $key;
		$mysgin = md5($prestr);

		if($mysgin == $sign) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	private function _createLinkstring($para) 
	{
		$arg  = "";
		while (list ($key, $val) = each ($para)) {
			$arg.=$key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
		
		return $arg;
	}

	/**
	* 获取当前 ip
	* @return string $ip
	*/
	private function _getip()
	{
		$ip = '0.0.0.0';
		if (getenv('HTTP_CLIENT_IP')
		&& strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')
		&& strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('REMOTE_ADDR')
		&& strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif (isset($_SERVER['REMOTE_ADDR'])
		&& $_SERVER['REMOTE_ADDR']
		&& strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * 获取返回时的签名验证结果
	 * @param $para_temp 通知返回来的参数数组
	 * @param $sign 返回的签名结果
	 * @return 签名验证结果
	 */
	private function _getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->_paraFilter($para_temp);
		
		//对待签名参数数组排序
		$para_sort = $this->_argSort($para_filter);

		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->_createLinkstring($para_sort);
		$isSgin = false;
		switch (strtoupper(trim($this->sign_type))) {
			case "MD5" :
				$isSgin = $this->_md5Verify($prestr, $sign, $this->_key);
				break;
			default :
				$isSgin = false;
		}
		
		return $isSgin;
	}
}


/* End */