<?php
/**
 * @author mosen
 * @date 2014-10-26
 */
final class Payment_Kuaiqian
{
    private static $req;
    //基本参数
    private $merchantAcctId; //快钱账号
    private $accountName; //快钱账号名
    private $contact; //联系人邮箱
    private $pemPwd; //商户证书密码
    private $pemPath; //商户证书
    private $cerPath; //快钱证书
    private $payUrl; //支付接口URL
    private $backKey; //退款密钥
    private $backUrl; //退款接口URL
    private $backVersion = "bill_drawback_api_1"; // 固定值: bill_drawback_api_1
    private $backCommandType = "001";//固定值: 001    表示下订单请求退款
    private $backTimeout; //退款超时(秒)
    private $queryBackUrl; //查询退款接口URL
    private $queryBackTimeout; //退款查询超时(秒)
    private $querybackVersion = 'v2.0';
    private $querybackSignType = '1';//退款查询签名类型
    private $inputCharset = "1";//编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
    private $version =  "v2.0";//网关版本，固定值：v2.0,该参数必填。
    private $language =  "1";//语言种类，1代表中文显示，2代表英文显示。默认为1,该参数必填。
    private $signType =  "4";//签名类型,该值为4，代表PKI加密方式,该参数必填。
    private $payType = "00";//支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10，必填。
    private $bankId = "";//银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
    private $pid = "";//快钱合作伙伴的帐户号，即商户编号，可为空。
    private $redoFlag = "";//同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
    private $pageUrl; //接收支付结果的页面地址，该参数一般置为空即可。
    private $bgUrl; //服务器接收支付结果的后台地址，该参数务必填写，不能为空。

    //支付参数
    private $orderId;//商户订单号，以下采用时间来定义订单号，商户可以根据自己订单号的定义规则来定义该值，不能为空。
    private $orderAmount;//订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。该参数必填。
    private $orderTime;//订单提交时间，格式：yyyyMMddHHmmss，如：20071117020101，不能为空。
    private $productName; //商品名称，可以为空。
    private $productNum;//商品数量，可以为空。
    private $productId;//商品代码，可以为空。
    private $productDesc;//商品描述，可以为空。
    private $callback;//回调方法，支付完快钱会原值返回，可以为空。
    private $ext2;//扩展自段2，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
    private $payerName; //支付人姓名,可以为空。
    private $payerContactType =  "2";//支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式。可以为空。
    
    //退款参数
    private $backId;//退款流水号 只允许使用字母、数字、- 、_, 必须是数字字母开头 必须在商家自身账户交易中唯一(50)
    private $backAmount;//退款金额 可以是2位小数 ，人民币 元为单位(10)
    private $backTime;//退款提交时间 格式 20071117020101 共14位(14)
    private $backOrderid;//原商户的订单号
    private static $instance;

    public static function inst() {
        $className = get_called_class();
        if (!self::$instance) {
            self::$instance = new $className();
        }
        return self::$instance;
    }

    public function __construct() {
        if (!$this->merchantAcctId) $this->init();
    }

    public function init() {
        $config = Yaf_Registry::get("config");
        $options = $config['kuaiqian'];
        if ($options) {
            foreach($options as $key => $option) {
                $this->$key = $option;
            }
        }
    }

    public function pay($params) {
        $this->callback = $params['callback'];
        //商户订单号 指支付单号
        $this->orderId = $params['orderId'];
        //付款金额
        $this->orderAmount = $params['orderAmount'] * 100;
        //订单提交时间
        $this->orderTime = date('YmdHis', $params['orderTime']);
        //订单名称
        $this->productName = $params['productName'];
        //订单描述
        $this->productDesc = $params['productDesc'];
        $this->productNum = $params['productNum'];
        $this->productId = $params['productId'];

        //发送请求
        $this->dopay();
    }

    private function dopay() {
        header("content-Type: text/html; charset=utf-8");
        $signMsg = $this->getRsaSign();
echo <<< EOT
<h3>正在提交请求，请稍后...</h3>
<form name="form1" action="{$this->payUrl}" method="post">
<input type="hidden" name="inputCharset" value="{$this->inputCharset}" />
<input type="hidden" name="version" value="{$this->version}" />
<input type="hidden" name="language" value="{$this->language}" />
<input type="hidden" name="signType" value="{$this->signType}" />
<input type="hidden" name="signMsg" value="{$signMsg}" />
<input type="hidden" name="merchantAcctId" value="{$this->merchantAcctId}" />
<input type="hidden" name="payType" value="{$this->payType}" />
<input type="hidden" name="bankId" value="{$this->bankId}" />
<input type="hidden" name="pid" value="{$this->pid}" />
<input type="hidden" name="payerName" value="{$this->payerName}" />
<input type="hidden" name="payerContactType" value="{$this->payerContactType}" />
<input type="hidden" name="redoFlag" value="{$this->redoFlag}" />
<input type="hidden" name="ext2" value="{$this->ext2}" />
<input type="hidden" name="pageUrl" value="{$this->pageUrl}" />
<input type="hidden" name="bgUrl" value="{$this->bgUrl}" />
<input type="hidden" name="ext1" value="{$this->callback}" />
<input type="hidden" name="orderId" value="{$this->orderId}" />
<input type="hidden" name="orderAmount" value="{$this->orderAmount}" />
<input type="hidden" name="orderTime" value="{$this->orderTime}" />
<input type="hidden" name="productName" value="{$this->productName}" />
<input type="hidden" name="productNum" value="{$this->productNum}" />
<input type="hidden" name="productId" value="{$this->productId}" />
<input type="hidden" name="productDesc" value="{$this->productDesc}" />
</form>
<script>document.forms['form1'].submit();</script>
EOT;
        exit();
    }

    private function getRsaSign() {
        $tmp  = $this->kqCkNull($this->inputCharset,'inputCharset');
        $tmp .= $this->kqCkNull($this->pageUrl,"pageUrl");
        $tmp .= $this->kqCkNull($this->bgUrl,'bgUrl');
        $tmp .= $this->kqCkNull($this->version,'version');
        $tmp .= $this->kqCkNull($this->language,'language');
        $tmp .= $this->kqCkNull($this->signType,'signType');
        $tmp .= $this->kqCkNull($this->merchantAcctId,'merchantAcctId');
        $tmp .= $this->kqCkNull($this->payerName,'payerName');
        $tmp .= $this->kqCkNull($this->payerContactType,'payerContactType');
        $tmp .= $this->kqCkNull($this->orderId,'orderId');
        $tmp .= $this->kqCkNull($this->orderAmount,'orderAmount');
        $tmp .= $this->kqCkNull($this->orderTime,'orderTime');
        $tmp .= $this->kqCkNull($this->productName,'productName');
        $tmp .= $this->kqCkNull($this->productNum,'productNum');
        $tmp .= $this->kqCkNull($this->productId,'productId');
        $tmp .= $this->kqCkNull($this->productDesc,'productDesc');
        $tmp .= $this->kqCkNull($this->callback,'ext1');
        $tmp .= $this->kqCkNull($this->ext2,'ext2');
        $tmp .= $this->kqCkNull($this->payType,'payType');
        $tmp .= $this->kqCkNull($this->bankId,'bankId');
        $tmp .= $this->kqCkNull($this->redoFlag,'redoFlag');
        $tmp .= $this->kqCkNull($this->pid,'pid');

        $transBody = substr($tmp, 0, -1);
        $fp = fopen($this->pemPath, "r");
        $privKey = fread($fp, $this->pemPwd);
        fclose($fp);
        $privKeyId = openssl_get_privatekey($privKey);
        openssl_sign($transBody, $signMsg, $privKeyId, OPENSSL_ALGO_SHA1);
        openssl_free_key($privKeyId);

        return base64_encode($signMsg);
    }

    public static function test($req) {
        Log_Base::save('payment', 'test'.var_export($req, true));
    }

    /**
     * Payment_Kuaiqian::synccall()
     * 
     */
    public static function synccall($req) {
        self::inst()->syncdeal($req);
        exit();
    }

    /**
     * Payment_Kuaiqian::asynccall()
     */
    public static function asynccall($req) {
        $result = self::inst()->syncdeal($req);
        echo "<result>{$result}</result>";
        exit();
    }

    private function syncdeal($req) {
        $result = 0;
        $this->req = $req;
        //验证快钱返回的数据
        // $r = $this->verifyReqSign();
        // if ($r == 1) {
        //验证成功时
        switch($this->req['payResult']) {
            case 10: //支付成功
                $PaymentModel = PaymentModel::model();
                try {
                    $PaymentModel->begin();
                    PaymentModel::model()->finishPayment($this->req);
                    if (strpos('::', $this->req['ext1'])>0) {
                        list($class, $method) = explode('::', $this->req['ext1']);
                        call_user_func_array(array($class, $method), $this->req);
                    }
                    $result = 1;
                    $PaymentModel->commit();
                } catch (Exception $e) {
                    $PaymentModel->rollback();
                    Log_Base::save('payment', $e->getMessage());
                }
                break;
        }
        // }
        return $result;
    }

    private function verifyReqSign() {
        //人民币网关账号，该账号为11位人民币网关商户编号+01,该值与提交时相同。
        $tmp = $this->kqCkNull($this->req['merchantAcctId'],'merchantAcctId');
        //网关版本，固定值：v2.0,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['version'],'version');
        //语言种类，1代表中文显示，2代表英文显示。默认为1,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['language'],'language');
        //签名类型,该值为4，代表PKI加密方式,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['signType'],'signType');
        //支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['payType'],'payType');
        //银行代码，如果payType为00，该值为空；如果payType为10,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['bankId'],'bankId');
        //商户订单号，,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['orderId'],'orderId');
        //订单提交时间，格式：yyyyMMddHHmmss，如：20071117020101,该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['orderTime'],'orderTime');
        //订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试,该值与支付时相同。
        $tmp .= $this->kqCkNull($this->req['orderAmount'],'orderAmount');
        // 快钱交易号，商户每一笔交易都会在快钱生成一个交易号。
        $tmp .= $this->kqCkNull($this->req['dealId'],'dealId');
        //银行交易号 ，快钱交易在银行支付时对应的交易号，如果不是通过银行卡支付，则为空
        $tmp .= $this->kqCkNull($this->req['bankDealId'],'bankDealId');
        //快钱交易时间，快钱对交易进行处理的时间,格式：yyyyMMddHHmmss，如：20071117020101
        $tmp .= $this->kqCkNull($this->req['dealTime'],'dealTime');
        //商户实际支付金额 以分为单位。比方10元，提交时金额应为1000。该金额代表商户快钱账户最终收到的金额。
        $tmp .= $this->kqCkNull($this->req['payAmount'],'payAmount');
        //费用，快钱收取商户的手续费，单位为分。
        $tmp .= $this->kqCkNull($this->req['fee'],'fee');
        //扩展字段1，该值与提交时相同
        $tmp .= $this->kqCkNull($this->req['ext1'],'ext1');
        //扩展字段2，该值与提交时相同。
        $tmp .= $this->kqCkNull($this->req['ext2'],'ext2');
        //处理结果， 10支付成功，11 支付失败，00订单申请成功，01 订单申请失败
        $tmp .= $this->kqCkNull($this->req['payResult'],'payResult');
        //错误代码 ，请参照《人民币网关接口文档》最后部分的详细解释。
        $tmp .= $this->kqCkNull($this->req['errCode'],'errCode');

        $transBody = substr($tmp, 0, -1);
        $fp = fopen($this->cerPath, 'r');
        $cert = fread($fp, 8192);
        fclose($fp);
        $pubKeyId = openssl_get_publickey($cert);
        return openssl_verify($transBody, base64_decode($this->req['signMsg']), $pubKeyId);
    }

    private function kqCkNull($value, $key) {
        if($value != "") {
            return $key . '=' . $value . '&';
        }
        return '';
    }

    public function refund($id) {
        $RefundApplyModel  = RefundApplyModel::model();
        $info = $RefundApplyModel->getById($id);
        $this->backId = $id;

        //原商户的订单号
        $paymentsModel = $this->load->model('payments');
        $paymentInfo = $paymentsModel->getOne('payment_bn='.$info['payment_bn'], '','id,status');
        $this->kq_orderid = $paymentInfo['id'];

        try {
            //查询退款记录
            $isExists = $this->_isRefundedData($this->kq_txOrder, $this->kq_orderid, $result);
            //若快钱的退款已经存在，并成功退款时
            if ($isExists && !empty($result['status']) && $result['status'] == self::PAY_BACK_SUCC) {
                //更新退款单
                $this->_updateRefundData($ret, $refund);
                $msg = "已经成功退款！";
                header("Location:".base_url()."/refund_prefund_{$refund['refund_apply_id']}.html?succmsg=".$msg);
                exit();
            }
        } catch(Exception $e) {
            //outputLog($e);
            $msg = "网络繁忙或网络不可用，请稍后重试！";
            header("Location:".base_url()."/refund_prefund_{$refund['refund_apply_id']}.html?errmsg=".$msg);
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
            header("Location:".base_url()."/refund_prefund_{$refund['refund_apply_id']}.html?errmsg=".$msg);
            exit();
        }

        //解析快钱退款返回的结果
        $paybackResult = $this->parseBackData($fcontents);
        if($paybackResult['judge_re'] == "Y"){
            //快钱成功退款时
            $this->_updateRefundData($paybackResult, $refund);
            exit();
        }else{
            //快钱退款失败时
            echo $msg = $paybackResult['error_code'];
            exit();
        }
    }

    private function _updateRefundData(&$ret, &$refund)
    {
        $refundsModel  = $this->load->model('refunds');
        $updateData     = array(
            'bank'       => $this->appName,
            //'payment_bn' => $ret['orderid_id'],
            'updated_at' => date('Y-m-d H:i:s'),
            'money'      => $ret['amount'],
            'status'     => 'succ',
        );

        $filter = 'payment_bn=\''.$refund['payment_bn'].'\' AND status <> \'succ\' AND batch_no=\''.$ret['txorder_id'].'\'';
        $result       = $refundsModel->update($updateData, $filter);
        $affectedRows = $refundsModel->affectedRows();
        if($result && $affectedRows >= 1) {
            $this->_log("update local succ", 'refund');
            $newRefundInfo     = $refundsModel->getOne('payment_bn=\''.$refund['payment_bn'].'\' AND batch_no=\''.$ret['txorder_id'].'\'');
            $refundApplyCommon = $this->load->common('refundApply');
            $refundApplyCommon->refundFinish($newRefundInfo, $msg);
            return true;
        } else {
            $this->_log(" update local fail", 'refund');
            return false;
        }
    }

    private function isRefunded($refundId, $paymentId, &$result = array()) {
        $startDate = date('Ymd', strtotime('-2 day'));
        $endDate = date('Ymd');
        $requestPage = '1';

        $tmp = $this->kqCkNull($this->querybackVersion, 'version');
        $tmp .= $this->kqCkNull($this->querybackSignType, 'signType');
        $tmp .= $this->kqCkNull($this->merchantAcctId, 'merchantAcctId');
        $tmp .= $this->kqCkNull($startDate, 'startDate');
        $tmp .= $this->kqCkNull($endDate, 'endDate');
        $tmp .= $this->kqCkNull($refundId, 'ordered');
        $tmp .= $this->kqCkNull($requestPage, 'requestPage');
        $tmp .= $this->kqCkNull($paymentId, 'rOrderId');
        $signMsg = strtoupper(md5($tmp."key=".$this->backKey));

        $params['version']=$this->querybackVersion;
        $params['signType']=$this->querybackSignType;
        $params['merchantAcctId']=$this->merchantAcctId;
        $params['startDate']=$startDate;
        $params['endDate']=$endDate;
        $params['orderId'] = $refundId;
        $params['requestPage']=$requestPage;
        $params['rOrderId'] = $paymentId;
        $params['signMsg']=$signMsg;

        try {
            $cli = new SoapClient($this->queryBackUrl, array('connection_timeout'=>$this->queryBackTimeout));
            $result=$cli->__soapCall('query',array($params));
            $re = $this->formatArray($result);
            if (!empty($re['result'])) {
                $result = array_pop($re['result']);
                return true;
            } else {
                return false;
            }
        } catch (SOAPFault $e) {
            throw new Exception($e);
        }
    }

    private function getRefundUrl() {
        $tmp = array();
        $tmp[] = 'merchant_id='.$this->merchantAcctId;
        $tmp[] = 'version='.$this->backVersion;
        $tmp[] = 'command_type='.$this->backCommandType;
        $tmp[] = 'orderid='.$this->backOrderid;
        $tmp[] = 'amount='.$this->backAmount;
        $tmp[] = 'postdate='.$this->backTime;
        $tmp[] = 'txOrder='.$this->backId;
        $mac = strtoupper(md5(join($tmp).'merchant_key='.$this->backKey));  // 加密字符串
        return $this->backUrl.'?'.implode('&', $tmp).'&mac='.$mac;
    }

    private function parseBackData($data) {
        preg_match("/<MERCHANT>(.*)<\/MERCHANT>/i", $data, $MERCHANT);
        preg_match("/<ORDERID>(.*)<\/ORDERID>/i", $data, $ORDERID);
        preg_match("/<TXORDER>(.*)<\/TXORDER>/i", $data, $TXORDER);
        preg_match("/<AMOUNT>(.*)<\/AMOUNT>/i", $data, $AMOUNT);
        preg_match("/<RESULT>(.*)<\/RESULT>/i", $data, $RESULT);
        preg_match("/<CODE>(.*)<\/CODE>/i", $data, $CODE);

        $ret['merchant_id'] = $MERCHANT[1];
        $ret['orderid_id'] = $ORDERID[1];
        $ret['txorder_id'] = $TXORDER[1];
        $ret['amount'] = $AMOUNT[1];
        $ret['judge_re'] = $RESULT[1];
        $ret['error_code'] = $CODE[1];
        return $ret;
    }
 
    private function formatArray($arr) {
        if(is_object($arr)) $arr = (array)$arr;
        if(is_array($arr)) {
            foreach($arr as $key=>$value) {
                $arr[$key] = $this->formatArray($value);
            }
        }
        return $arr;
    }
    
}
