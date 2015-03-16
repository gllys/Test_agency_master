<?php

/**
 * 功能：实现快钱接口
 * 1）人民币网关支付API（V3.0.3）
 * 2）人民币网关订单退款API（V2.0.3）：
 * 3）人民币网关退款查询接口API（V2.0.8）：
 * @author shilei
 * Created at: 2014-05-15
 */
final class PaymentsKuaiqian {

    public $appKey = 'kuaiqian';
    public $appName = '快钱';
    public $displayName = '快钱';
    public $payType = 'online';
    public $version = "v2.0";

    //支付成功
    const PAY_SUCC = '10';
    //退款成功
    const PAY_BACK_SUCC = '1';

    //快钱账号名，默认为"汇联"
    private $accountName;

    ############################### 支付参数设定 开始 ###################################
    //支付用URL
    private $sendUrl;
    //人民币网关账号(支付接口时，该账号为11位人民币网关商户编号+01），该参数必填。
    private $merchantAcctId;
    //编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
    private $inputCharset = "1";
    //接收支付结果的页面地址，该参数一般置为空即可。
    private $pageUrl;
    //服务器接收支付结果的后台地址，该参数务必填写，不能为空。
    private $bgUrl;
    //网关版本，固定值：v2.0,该参数必填。
    private $kqVersion = "v2.0";
    //语言种类，1代表中文显示，2代表英文显示。默认为1,该参数必填。
    private $language = "1";
    //签名类型,该值为4，代表PKI加密方式,该参数必填。
    private $signType = "4";
    //支付人姓名,可以为空。
    private $payerName;
    //支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式。可以为空。
    private $payerContactType = "1";
    //支付人联系方式，与payerContactType设置对应，payerContactType为1，则填写邮箱地址；payerContactType为2，则填写手机号码。可以为空。
    private $payerContact;
    //商户订单号，以下采用时间来定义订单号，商户可以根据自己订单号的定义规则来定义该值，不能为空。
    private $orderId;
    //订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。该参数必填。
    private $orderAmount;
    //订单提交时间，格式：yyyyMMddHHmmss，如：20071117020101，不能为空。
    private $orderTime;
    //商品名称，可以为空。
    private $productName;
    //商品数量，可以为空。
    private $productNum;
    //商品代码，可以为空。
    private $productId;
    //商品描述，可以为空。
    private $productDesc;
    //扩展字段1，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
    private $ext1;
    //扩展自段2，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
    private $ext2;
    //支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10，必填。
    private $kqPayType = "00";
    //银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
    private $bankId = "";
    //同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
    private $redoFlag = "";
    //快钱合作伙伴的帐户号，即商户编号，可为空。
    private $pid = "";
    ############################### 支付参数设定 结束 ###################################
    ############################### 退款参数设定 开始 ###################################
    //退款网关提交地址
    private $kq_target;
    //*商家用户编号
    private $kq_merchant_id;
    //*密钥
    private $kq_key;
    //*固定值: bill_drawback_api_1    (20)
    private $kq_version = "bill_drawback_api_1";
    //*固定值: 001    表示下订单请求退款 (3)
    private $kq_command_type = "001";
    //*退款流水号 只允许使用字母、数字、- 、_, 必须是数字字母开头 必须在商家自身账户交易中唯一(50)
    private $kq_txOrder;
    //*退款金额 可以是2位小数 ，人民币 元为单位(10)
    private $kq_amount;
    //*退款提交时间 格式 20071117020101 共14位(14)
    private $kq_postdate;
    //*原商户的订单号
    private $kq_orderid;
    //退款超时
    private $paybackTimeout;
    ############################### 退款参数设定 结束 ###################################
    ############################### 退款查询参数设定 开始 ################################
    //退款查询接口URL
    private $searchPaybackUrl;
    //退款查询接口版本
    private $searchPaybackVersion = 'v2.0';
    //退款查询签名类型
    private $searchPaybackSignType = '1';
    //退款查询超时
    private $searchPaybackTimeout;
    ############################### 退款查询参数设定 结束 ################################
    ############################### 证书设定 开始 #######################################
    //商户证书
    private $merchantPemFilePath;
    //商户证书密码
    private $merchantPemPassword;
    //快钱证书
    private $billCerFilePath;
    ############################### 证书设定 结束 #######################################
    private $payerIP;
    private $orderTimestamp;
    private $orderTimeOut;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->_init();
    }

    /**
     * 快钱支付
     * @param array $payment 支付单的数据
     * @param array $orderInfo 订单数据
     * @param $msg 
     * return bool
     */
    public function doPay($payment, $orderInfo, &$msg = '') {
        //商户订单号 指支付单号
        $this->orderId = $payment['id'];
        //订单名称
        $this->productName = isset($orderInfo['subject']) ? $orderInfo['subject'] : $orderInfo['landscape']['name'] . '-' . $orderInfo['ticket']['name'];
        //付款金额：分
        $this->orderAmount = $payment['amount'] * 100;
        //订单描述
        $this->productDesc = isset($orderInfo['remark']) ? $orderInfo['remark'] : '订单编号:' . $payment['order_ids'];
        //订单提交时间
        $this->orderTime = date('YmdHis', $payment['updated_at']);
        //扩展字段1 格式：参数名1:参数值1;参数名2:参数值2;....
        $this->ext1 = isset($orderInfo['ext1']) ? $orderInfo['ext1'] : "order_id:{$payment['order_ids']};";
        $this->ext2 = "org_id:" . Yii::app()->user->org_id;
        $this->orderTimestamp = date('YmdHis');
        $this->orderTimeOut = 2700; // 45 minutes
        //输出日志
        $this->_log("start \n order_id:{$payment['order_ids']} ;payment_id:{$payment['id']}", 'pay');

        //发送请求
        header("content-Type: text/html; charset=utf-8");

        $this->_buildSendForm($sHtml);
        echo $sHtml;
    }

    /**
     * 支付接口同步回调-GET
     */
    public function syncCallback() {
        return $this->_syncCallback();
    }

    /**
     * 支付接口异步回调-POST 异步成功须echo "<result>1</result>"
     */
    public function asyncCallback() {
        return $this->_syncCallback();
    }

    /**
     * 实现快钱人名币退款接口
     * 特别事项:如果出现网络连接超时异常,未接收到快钱退款结果通知,务必通过退款查询接口进行 查询,以免重复提交退款交易。
     * @param unknown $refund
     * @param unknown $msg
     */
    public function doRefund($refund, &$msg) {
        $this->_log("start \n order_id:{$refund['order_id']}", 'refund');

        //退款流水号
        $refundsModel = $this->load->model('refunds');
        $refundInfo = $refundsModel->getID($refund['id'], 'batch_no');
        if (empty($refundInfo['batch_no'])) {
            //流水号不存在时，新建退款流水号
            $this->kq_txOrder = $refundsModel->genBatchNo($refund['id']);
            //更新该退款单的流水号
            $refundsModel->update(array('batch_no' => $this->kq_txOrder), array('id' => $refund['id']));
        } else {
            //流水号已经存在时
            $this->kq_txOrder = $refundInfo['batch_no'];
        }

        //原商户的订单号
        $paymentsModel = $this->load->model('payments');
        $paymentInfo = $paymentsModel->getOne('payment_bn=' . $refund['payment_bn'], '', 'id,status');
        $this->kq_orderid = $paymentInfo['id'];

        try {
            //查询退款记录
            $isExists = $this->_isRefundedData($this->kq_txOrder, $this->kq_orderid, $result);
            //若快钱的退款已经存在，并成功退款时
            if ($isExists && !empty($result['status']) && $result['status'] == self::PAY_BACK_SUCC) {
                //更新退款单
                $this->_updateRefundData($ret, $refund);
                $msg = "已经成功退款！";
                header("Location:" . base_url() . "/refund_prefund_{$refund['refund_apply_id']}.html?succmsg=" . $msg);
                exit();
            }
        } catch (Exception $e) {
            //outputLog($e);
            $msg = "网络繁忙或网络不可用，请稍后重试！";
            header("Location:" . base_url() . "/refund_prefund_{$refund['refund_apply_id']}.html?errmsg=" . $msg);
            exit();
        }

        //退款金额
        $this->kq_amount = $refund['money'];
        //退款时间
        $this->kq_postdate = date('YmdHis');

        //获取退款URL
        $refundUrl = $this->_getRefundUrl();
        $this->_log("refund url:{$refundUrl}", 'refund');
        //发送退款请求
        $strm = stream_context_create(array(
            'http' => array(
                'timeout' => $this->paybackTimeout,
            ),
        ));
        $fcontents = file_get_contents($refundUrl, false, $strm);
        //若网络异常时
        if ($fcontents === false) {
            $msg = "网络繁忙或网络不可用，请稍后重试！";
            header("Location:" . base_url() . "/refund_prefund_{$refund['refund_apply_id']}.html?errmsg=" . $msg);
            exit();
        }

        //解析快钱退款返回的结果
        $this->_parsePaybackData($fcontents, $paybackResult);
        if ($paybackResult['judge_re'] == "Y") {
            //快钱成功退款时
            $this->_log("success \n order_id:{$refund['order_id']}", 'refund');

            //更新退款单
            $this->_updateRefundData($paybackResult, $refund);

            $msg = "已经成功退款！";
            header("Location:" . base_url() . "/refund_prefund_{$refund['refund_apply_id']}.html?succmsg=" . $msg);
            exit();
        } else {
            //快钱退款失败时
            $this->_log("failed \n order_id:{$refund['order_id']} ;error_code:{$paybackResult['error_code']}", 'refund');

            $msg = "退款失败，错误信息：{$paybackResult['error_code']}！";
            header("Location:" . base_url() . "/refund_prefund_{$refund['refund_apply_id']}.html?errmsg=" . $msg);
            exit();
        }
    }

    /**
     * 初始化
     */
    private function _init() {
        $iniData = parse_ini_file(Yii::app()->basePath . '/extensions/Payments/Config.ini', true);

        $this->accountName = $iniData['KUAIQIAN']['ACCOUNT_NAME'];
        $this->merchantAcctId = $iniData['KUAIQIAN']['MERCHANT_ACCOUNT_ID'] . "01";
        // $this->payerContact = $iniData['KUAIQIAN']['PAYER_CONTACT'];

        $this->sendUrl = $iniData['KUAIQIAN']['SEND_URL'];
        $this->kq_merchant_id = $iniData['KUAIQIAN']['MERCHANT_ACCOUNT_ID'];
        $this->merchantPemFilePath = Yii::app()->basePath . $iniData['KUAIQIAN']['MERCHANT_PEM_FILE_PATH'];
        $this->merchantPemPassword = $iniData['KUAIQIAN']['MERCHANT_PEM_PASSWORD'];
        $this->billCerFilePath = Yii::app()->basePath . $iniData['KUAIQIAN']['KUAIQIAN_CER_FILE_PATH'];
        $this->paybackTimeout = $iniData['KUAIQIAN']['PAY_BACK_TIMEOUT'];

        $this->bgUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/order/payments/api/callback/async/way/99bill";
        $this->pageUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/order/payments/api/callback/sync/way/99bill";

        $this->kq_target = $iniData['KUAIQIAN']['PAY_BACK_URL'];
        $this->kq_key = $iniData['KUAIQIAN']['KUAIQIAN_PAY_BACK_KEY'];

        $this->searchPaybackUrl = $iniData['KUAIQIAN']['SEARCH_PAY_BACK_URL'];
        $this->searchPaybackTimeout = $iniData['KUAIQIAN']['SEARCH_PAY_BACK_TIMEOUT'];

        $this->payerIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    /**
     * 支付同步/异步共通处理
     *
     * @param array
     */
    private function _syncCallback() {
        $ret['result'] = '0';
        $ret['orderId'] = $_REQUEST['orderId'];

        //输出日志
        $this->_log("payment_id:{$_REQUEST['orderId']} callback params:" . var_export($_REQUEST, true), 'pay');

        //验证快钱返回的数据
        $verifyFlg = $this->_verifyReceiveData($_REQUEST);
        if ($verifyFlg == 1) {
            //验证成功时
            $this->_log("sign OK", 'pay');

            switch ($_REQUEST['payResult']) {
                case self::PAY_SUCC:
                    //支付成功
                    $ret['result'] = '1';
                    //$this->_updatePaymentData($_REQUEST,$ret);
                    Payment::api()->update(array(
                        'id' => $_REQUEST['orderId'],
                        'distributor_id' => substr($_REQUEST['ext2'], 7),
                        'status' => 'succ',
                        'payment' => 'kuaiqian',
                        //'user_id' => Yii::app()->user->uid,
                        //'user_name' => Yii::app()->user->account
                    ));
                    break;
                default:
                    break;
            }
        } else {
            //验证失败时
            $this->_log("error:error_msg:Invalid Sign \n payment_id:{$_REQUEST['orderId']}\n callback params:" . var_export($_REQUEST, true), 'pay');

            $ret['message'] = 'Invalid Sign';
            $ret['status'] = 'error';
        }
        return $ret;
    }

    /**
     * 查询退款是否已经存在
     * @param unknown $refundId
     * @param unknown $paymentId
     * @param unknown $result
     * @throws Exception
     * @return boolean
     */
    private function _isRefundedData($refundId, $paymentId, &$result = array()) {
        $startDate = date('Ymd', time() - 24 * 3600 * 2);
        $endDate = date('Ymd');
        $requestPage = '1';

        $kq_all_para = "";
        $kq_all_para = $this->_appendParam($kq_all_para, 'version', $this->searchPaybackVersion);
        $kq_all_para = $this->_appendParam($kq_all_para, 'signType', $this->searchPaybackSignType);
        $kq_all_para = $this->_appendParam($kq_all_para, 'merchantAcctId', $this->merchantAcctId);
        $kq_all_para = $this->_appendParam($kq_all_para, 'startDate', $startDate);
        $kq_all_para = $this->_appendParam($kq_all_para, 'endDate', $endDate);
        $kq_all_para = $this->_appendParam($kq_all_para, 'ordered', $refundId);
        $kq_all_para = $this->_appendParam($kq_all_para, 'requestPage', $requestPage);
        $kq_all_para = $this->_appendParam($kq_all_para, 'rOrderId', $paymentId);
        $signMsg = strtoupper(md5($kq_all_para . "key=" . $this->kq_key));

        $para['version'] = $this->searchPaybackVersion;
        $para['signType'] = $this->searchPaybackSignType;
        $para['merchantAcctId'] = $this->merchantAcctId;
        $para['startDate'] = $startDate;
        $para['endDate'] = $endDate;
        $para['orderId'] = $refundId;
        $para['requestPage'] = $requestPage;
        $para['rOrderId'] = $paymentId;
        $para['signMsg'] = $signMsg;

        try {
            //  开始 读取 WEB SERVERS 上的 数据
            $this->_log("refund search url:{$this->searchPaybackUrl}", 'refund');
            $this->_log("refund search params:" . var_export($para, true), 'refund');
            $clientObj = new SoapClient($this->searchPaybackUrl, array('connection_timeout' => $this->searchPaybackTimeout));
            $this->_log("refund search result:" . var_export($clientObj, true), 'refund');
            $result = $clientObj->__soapCall('query', array($para));
            $re = $this->_object_array($result);
            if (!empty($re['result'])) {
                $this->_log("search refund result:" . var_export($re['result'], true), 'refund');
                $result = array_pop($re['result']);
                return true;
            } else {
                $this->_log("search refund result is empty.", 'refund');
                return false;
            }
        } catch (SOAPFault $e) {
            $this->_log("error :" . var_export($e, true), 'refund');
            throw new Exception($e);
        }
    }

    /**
     * 更新支付单数据
     * @param $ret 支付单的数据
     * return bool
     */
    private function _updatePaymentData($ret, &$data = array()) {
        $paymentsModel = $this->load->model('payments');
        $oldPaymentInfo = $paymentsModel->getOne('id=' . $ret['orderId']);
        $data['channel'] = $oldPaymentInfo['channel'];
        //若订单已经支付过
        if ($oldPaymentInfo['status'] == 'succ') {
            $this->_log("has been paid \n payment_id:{$ret['orderId']} ;money:{$ret['orderAmount']}", 'pay');
            return;
        }

        $this->_log("callback succ \n payment_id:{$ret['orderId']} ;money:{$ret['orderAmount']}", 'pay');

        $updateData = array(
            'account' => $this->accountName, //$ret['merchantAcctId'],
            'bank' => $this->appName,
            'pay_account' => !empty($ret['bindCard']) ? $ret['bindCard'] : (!empty($ret['bankId']) ? $ret['bankId'] : 'unknown'),
            'remark' => '', //$ret['ext1']." ".$ret['ext2'],
            'payment_bn' => $ret['dealId'],
            'updated_at' => date('Y-m-d H:i:s', strtotime($ret['dealTime'])),
            'status' => 'succ',
        );

        $result = $paymentsModel->update($updateData, "id='{$ret[orderId]}'");
        $affectedRows = $paymentsModel->affectedRows();

        if ($result && $affectedRows >= 1) {
            $this->_log("update local succ", 'pay');
            $newPaymentsInfo = $paymentsModel->getOne('id=\'' . $ret['orderId'] . '\'');
            switch ($data['channel']) {
                case 1:
                    $this->load->common('money')->afterPayFinish($newPaymentsInfo['order_id'], $ret);
                    break;
                case 2:
                    $this->load->common('order')->paymentFinish($newPaymentsInfo);
                    break;
                default:
                    $this->load->common('order')->payFinish($newPaymentsInfo['order_id'], $newPaymentsInfo, $msg);
                    break;
            }
            return true;
        } else {
            $this->_log(" update local fail", 'pay');
            return false;
        }
    }

    /**
     * 更新退款单数据
     * @param $ret 支付单的数据
     * return bool
     */
    private function _updateRefundData(&$ret, &$refund) {
        $refundsModel = $this->load->model('refunds');
        $updateData = array(
            'bank' => $this->appName,
            //'payment_bn' => $ret['orderid_id'],
            'updated_at' => date('Y-m-d H:i:s'),
            'money' => $ret['amount'],
            'status' => 'succ',
        );

        $filter = 'payment_bn=\'' . $refund['payment_bn'] . '\' AND status <> \'succ\' AND batch_no=\'' . $ret['txorder_id'] . '\'';
        $result = $refundsModel->update($updateData, $filter);
        $affectedRows = $refundsModel->affectedRows();
        if ($result && $affectedRows >= 1) {
            $this->_log("update local succ", 'refund');
            $newRefundInfo = $refundsModel->getOne('payment_bn=\'' . $refund['payment_bn'] . '\' AND batch_no=\'' . $ret['txorder_id'] . '\'');
            $refundApplyCommon = $this->load->common('refundApply');
            $refundApplyCommon->refundFinish($newRefundInfo, $msg);
            return true;
        } else {
            $this->_log(" update local fail", 'refund');
            return false;
        }
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $sHtml 提交表单HTML文本
     */
    private function _buildSendForm(&$sHtml = '') {
        //RSA 签名计算	
        $signMsg = $this->_generateSignMsg();
        $sHtml = "<form name=\"kqPay\" action=\"{$this->sendUrl}\" method=\"post\">
                <input type=\"hidden\" name=\"inputCharset\" value=\"{$this->inputCharset}\" />
                <input type=\"hidden\" name=\"pageUrl\" value=\"{$this->pageUrl}\" />
                <input type=\"hidden\" name=\"bgUrl\" value=\"{$this->bgUrl}\" />
                <input type=\"hidden\" name=\"version\" value=\"{$this->kqVersion}\" />
                <input type=\"hidden\" name=\"language\" value=\"{$this->language}\" />
                <input type=\"hidden\" name=\"signType\" value=\"{$this->signType}\" />
                <input type=\"hidden\" name=\"signMsg\" value=\"{$signMsg}\" />
                <input type=\"hidden\" name=\"merchantAcctId\" value=\"{$this->merchantAcctId}\" />
                <input type=\"hidden\" name=\"payerName\" value=\"{$this->payerName}\" />
                <input type=\"hidden\" name=\"payerContactType\" value=\"{$this->payerContactType}\" />
                <input type=\"hidden\" name=\"orderId\" value=\"{$this->orderId}\" />
                <input type=\"hidden\" name=\"orderAmount\" value=\"{$this->orderAmount}\" />
                <input type=\"hidden\" name=\"orderTime\" value=\"{$this->orderTime}\" />
                <input type=\"hidden\" name=\"productName\" value=\"{$this->productName}\" />
                <input type=\"hidden\" name=\"productNum\" value=\"{$this->productNum}\" />
                <input type=\"hidden\" name=\"productId\" value=\"{$this->productId}\" />
                <input type=\"hidden\" name=\"productDesc\" value=\"{$this->productDesc}\" />
                <input type=\"hidden\" name=\"ext1\" value=\"{$this->ext1}\" />
                <input type=\"hidden\" name=\"ext2\" value=\"{$this->ext2}\" />
                <input type=\"hidden\" name=\"payType\" value=\"{$this->kqPayType}\" />
                <input type=\"hidden\" name=\"bankId\" value=\"{$this->bankId}\" />
                <input type=\"hidden\" name=\"redoFlag\" value=\"{$this->redoFlag}\" />
                <input type=\"hidden\" name=\"pid\" value=\"{$this->pid}\" />
                <input type=\"submit\" value=\"提交到快钱\" style=\"display:none;\">
            </form>正在提交请求，请稍后。。。";
        //$this->_log($sHtml, 'pay');
        Yii::log($sHtml, 'info');
        echo $sHtml = $sHtml . "<script>document.forms['kqPay'].submit();</script>";
    }

    /**
     * 制作退款用的URL
     * @return string
     */
    private function _getRefundUrl() {
        $kq_all_para_o = 'merchant_id=' . $this->kq_merchant_id . 'version=' . $this->kq_version . 'command_type=' . $this->kq_command_type . 'orderid=' . $this->kq_orderid . 'amount=' . $this->kq_amount . 'postdate=' . $this->kq_postdate . 'txOrder=' . $this->kq_txOrder;
        $kq_mac = strtoupper(md5($kq_all_para_o . "merchant_key=" . $this->kq_key));  // 加密字符串
        $kq_all_para = 'merchant_id=' . $this->kq_merchant_id . '&version=' . $this->kq_version . '&command_type=' . $this->kq_command_type . '&orderid=' . $this->kq_orderid . '&amount=' . $this->kq_amount . '&postdate=' . $this->kq_postdate . '&txOrder=' . $this->kq_txOrder;

        $kq_get_url = $this->kq_target . '?' . $kq_all_para . '&mac=' . $kq_mac;
        return $kq_get_url;
    }

    /**
     * 快钱支付
     * @param array $orderInfo 充值
     * @param $msg 
     * return bool
     */
    public function doPayUnion(Array $params = array(), &$msg = '') {
        $this->bgUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/finance/platform/api/callback/async/way/99bill";
        $this->pageUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/finance/platform/api/callback/sync/way/99bill";

        //商户订单号 指支付单号
        $this->orderId = $params['order_id'];
        //订单名称
        $this->productName = '智慧旅游票务平台-充值';
        //付款金额：分
        $this->orderAmount = $params['amount'] * 100;
        //订单描述
        $this->productDesc = isset($params['remark']) ? $params['remark'] : '充值编号:' . $params['order_id'];
        //订单提交时间
        $this->orderTime = date('YmdHis');
        //扩展字段1 格式：参数名1:参数值1;参数名2:参数值2;....
        $this->ext1 = isset($params['ext1']) ? $params['ext1'] : "order_id:{$params['order_id']};";
        $this->ext2 = "org_id:" . Yii::app()->user->org_id;
        $this->orderTimestamp = date('YmdHis');
        $this->orderTimeOut = 2700; // 45 minutes
        //输出日志
        $this->_log("start \n order_id:{$params['order_id']} ;}", 'pay');

        //发送请求
        header("content-Type: text/html; charset=utf-8");

        $this->_buildSendForm($sHtml);
        echo $sHtml;
    }

    /**
     * 支付接口同步回调-GET
     */
    public function syncUnionCallback() {
        return $this->_syncUnionCallback();
    }

    
    /**
     * 支付接口异步回调-POST 异步成功须echo "<result>1</result>"
     */
    public function asyncUnionCallback() {
        return $this->_syncUnionCallback();
    }
    
    /**
     * 支付同步/异步共通处理
     *
     * @param array
     */
    private function _syncUnionCallback() {
        $ret['result'] = '0';
        $ret['orderId'] = $_REQUEST['orderId'];

        //输出日志
        $this->_log("payment_id:{$_REQUEST['orderId']} callback params:" . var_export($_REQUEST, true), 'pay');

        //验证快钱返回的数据
        $verifyFlg = $this->_verifyReceiveData($_REQUEST);
        if ($verifyFlg == 1) {
            //验证成功时
            $this->_log("sign OK", 'pay');

            switch ($_REQUEST['payResult']) {
                case self::PAY_SUCC:
                    $ret['result'] = '1'; 
                    break;
                default:
                    break;
            }
        } else {
            //验证失败时
            $this->_log("error:error_msg:Invalid Sign \n payment_id:{$_REQUEST['orderId']}\n callback params:" . var_export($_REQUEST, true), 'pay');

            $ret['message'] = 'Invalid Sign';
            $ret['status'] = 'error';
        }
        return $ret;
    }

    /**
     * RSA 签名计算
     * @return string
     */
    private function _generateSignMsg() {
        // signMsg 签名字符串 不可空，生成加密签名串
        $kq_all_para = $this->_kq_ck_null($this->inputCharset, 'inputCharset');
        $kq_all_para .= $this->_kq_ck_null($this->pageUrl, "pageUrl");
        $kq_all_para .= $this->_kq_ck_null($this->bgUrl, 'bgUrl');
        $kq_all_para .= $this->_kq_ck_null($this->kqVersion, 'version');
        $kq_all_para .= $this->_kq_ck_null($this->language, 'language');
        $kq_all_para .= $this->_kq_ck_null($this->signType, 'signType');
        $kq_all_para .= $this->_kq_ck_null($this->merchantAcctId, 'merchantAcctId');
        $kq_all_para .= $this->_kq_ck_null($this->payerName, 'payerName');
        $kq_all_para .= $this->_kq_ck_null($this->payerContactType, 'payerContactType');
        // $kq_all_para .= $this->_kq_ck_null($this->payerContact,'payerContact');
        $kq_all_para .= $this->_kq_ck_null($this->orderId, 'orderId');
        $kq_all_para .= $this->_kq_ck_null($this->orderAmount, 'orderAmount');
        $kq_all_para .= $this->_kq_ck_null($this->orderTime, 'orderTime');
        $kq_all_para .= $this->_kq_ck_null($this->productName, 'productName');
        $kq_all_para .= $this->_kq_ck_null($this->productNum, 'productNum');
        $kq_all_para .= $this->_kq_ck_null($this->productId, 'productId');
        $kq_all_para .= $this->_kq_ck_null($this->productDesc, 'productDesc');
        $kq_all_para .= $this->_kq_ck_null($this->ext1, 'ext1');
        $kq_all_para .= $this->_kq_ck_null($this->ext2, 'ext2');
        $kq_all_para .= $this->_kq_ck_null($this->kqPayType, 'payType');
        $kq_all_para .= $this->_kq_ck_null($this->bankId, 'bankId');
        $kq_all_para .= $this->_kq_ck_null($this->redoFlag, 'redoFlag');
        $kq_all_para .= $this->_kq_ck_null($this->pid, 'pid');

        $kq_all_para = substr($kq_all_para, 0, strlen($kq_all_para) - 1);

        /////////////  RSA 签名计算 ///////// 开始 //
        $fp = fopen($this->merchantPemFilePath, "r");
        $priv_key = fread($fp, $this->merchantPemPassword);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);
        // compute signature
        openssl_sign($kq_all_para, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1);
        // free the key from memory
        openssl_free_key($pkeyid);

        $signMsg = base64_encode($signMsg);
        return $signMsg;
        /////////////  RSA 签名计算 ///////// 结束 //
    }

    /**
     * 验证快钱返回的数据
     * @param unknown $req
     * @return number
     */
    private function _verifyReceiveData($req) {
        //人民币网关账号，该账号为11位人民币网关商户编号+01,该值与提交时相同。
        $kq_check_all_para = $this->_kq_ck_null($req['merchantAcctId'], 'merchantAcctId');
        //网关版本，固定值：v2.0,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['version'], 'version');
        //语言种类，1代表中文显示，2代表英文显示。默认为1,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['language'], 'language');
        //签名类型,该值为4，代表PKI加密方式,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['signType'], 'signType');
        //支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['payType'], 'payType');
        //银行代码，如果payType为00，该值为空；如果payType为10,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['bankId'], 'bankId');
        //商户订单号，,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['orderId'], 'orderId');
        //订单提交时间，格式：yyyyMMddHHmmss，如：20071117020101,该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['orderTime'], 'orderTime');
        //订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试,该值与支付时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['orderAmount'], 'orderAmount');
        // 快钱交易号，商户每一笔交易都会在快钱生成一个交易号。
        $kq_check_all_para .= $this->_kq_ck_null($req['dealId'], 'dealId');
        //银行交易号 ，快钱交易在银行支付时对应的交易号，如果不是通过银行卡支付，则为空
        $kq_check_all_para .= $this->_kq_ck_null($req['bankDealId'], 'bankDealId');
        //快钱交易时间，快钱对交易进行处理的时间,格式：yyyyMMddHHmmss，如：20071117020101
        $kq_check_all_para .= $this->_kq_ck_null($req['dealTime'], 'dealTime');
        //商户实际支付金额 以分为单位。比方10元，提交时金额应为1000。该金额代表商户快钱账户最终收到的金额。
        $kq_check_all_para .= $this->_kq_ck_null($req['payAmount'], 'payAmount');
        //费用，快钱收取商户的手续费，单位为分。
        $kq_check_all_para .= $this->_kq_ck_null($req['fee'], 'fee');
        //扩展字段1，该值与提交时相同
        $kq_check_all_para .= $this->_kq_ck_null($req['ext1'], 'ext1');
        //扩展字段2，该值与提交时相同。
        $kq_check_all_para .= $this->_kq_ck_null($req['ext2'], 'ext2');
        //处理结果， 10支付成功，11 支付失败，00订单申请成功，01 订单申请失败
        $kq_check_all_para .= $this->_kq_ck_null($req['payResult'], 'payResult');
        //错误代码 ，请参照《人民币网关接口文档》最后部分的详细解释。
        $kq_check_all_para .= $this->_kq_ck_null($req['errCode'], 'errCode');

        $trans_body = substr($kq_check_all_para, 0, strlen($kq_check_all_para) - 1);
        $MAC = base64_decode($req['signMsg']);

        $fp = fopen($this->billCerFilePath, "r");
        $cert = fread($fp, 8192);
        fclose($fp);
        $pubkeyid = openssl_get_publickey($cert);

        $ok = openssl_verify($trans_body, $MAC, $pubkeyid);
        return $ok;
    }

    /**
     * 解析快钱退款接口返回的数据
     * @param unknown $fcontents
     * @param unknown $ret
     */
    private function _parsePaybackData(&$fcontents = array(), &$ret = array()) {
        $this->_log($fcontents, 'refund');

        preg_match("/<MERCHANT>(.*)<\/MERCHANT>/i", $fcontents, $merchant_id);
        preg_match("/<ORDERID>(.*)<\/ORDERID>/i", $fcontents, $orderid_id);
        preg_match("/<TXORDER>(.*)<\/TXORDER>/i", $fcontents, $txorder_id);
        preg_match("/<AMOUNT>(.*)<\/AMOUNT>/i", $fcontents, $amount);
        preg_match("/<RESULT>(.*)<\/RESULT>/i", $fcontents, $judge_re);
        preg_match("/<CODE>(.*)<\/CODE>/i", $fcontents, $error_code);

        $ret['merchant_id'] = $merchant_id[1];
        $ret['orderid_id'] = $orderid_id[1];
        $ret['txorder_id'] = $txorder_id[1];
        $ret['amount'] = $amount[1];
        $ret['judge_re'] = $judge_re[1];
        //TODO:for test.
        //$ret['judge_re'] = 'Y';
        $ret['error_code'] = $error_code[1];
    }

    /**
     * 拼接URL(快钱提供内部使用函数)
     * @param unknown $kq_va
     * @param unknown $kq_na
     * @return string
     */
    private function _kq_ck_null($kq_va, $kq_na) {
        if ($kq_va == "") {
            $kq_va = "";
        } else {
            return $kq_va = $kq_na . '=' . $kq_va . '&';
        }
    }

    /**
     * 拼接URL(快钱提供内部使用函数)
     * @param unknown $smval
     * @param unknown $valname
     * @param unknown $valvlue
     * @return string
     */
    private function _appendParam($smval, $valname, $valvlue) {
        if ($valvlue == "") {
            return $smval.="";
        } else {
            return $smval.=$valname . '=' . $valvlue . '&';
        }
    }

    /**
     * object => array
     * @param unknown $array
     * @return array
     */
    private function _object_array($array) {
        if (is_object($array)) {
            $array = (array) $array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->_object_array($value);
            }
        }
        return $array;
    }

    private function _loadReceiveExt(&$req, &$ext = array(), $paramName = 'ext1') {
        $arr = explode(";", $req[$paramName]);
        foreach ($arr as $str) {
            $kv = explode(":", $str);
            $ext[$kv[0]] = $kv[1];
        }
    }

    /**
     * 获取日志的路径， pay 、refund
     * @param unknown $type
     * @return string
     */
    private function _getLogPath($type) {
        $path = PI_LOG_BASE_PATH . "{$this->appKey}/{$type}/" . date('Y-m-d') . ".log";
        return $path;
    }

    /**
     * 输出日志
     * @param string $msg
     * @param string $method
     */
    private function _log($msg, $method) {

        $msg = date('Y-m-d H:i:s') . " {$this->appKey} {$method} {$msg} \n";
        Yii::log($msg, 'warning');
    }

}

/* End */
