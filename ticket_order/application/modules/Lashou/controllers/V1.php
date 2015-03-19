<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/12/5
 * Time: 17:57
 */
class V1Controller extends Base_Controller_Abstract
{
    /**
     * 密钥
     */
    const SECRET = '!!lswang';
    /**
     * request参数组
     * @var array
     */
    public $body = array();
    /**
     * 访问方法
     * @var string
     */
    public $ask = '';

    /**
     * 初始化
     */
    public function init()
    {
        // 验证请求
        !$this->getRequest()->getPost() && Tools::lsJson(false,'请求方式出错');
        // 获取参数
        $this->account = trim($this->getRequest()->getPost('account'));
        $sign = trim($this->getRequest()->getPost('sign'));
        $this->ask = trim($this->getRequest()->getPost('ask'));
        $this->body = json_decode(urldecode($this->getRequest()->getPost('body')),true);
        !$this->account && Tools::lsJson(false,'account缺失');
        !$sign && Tools::lsJson(false,'sign缺失');
        !$this->ask && Tools::lsJson(false,'ask缺失');
        !$this->body && Tools::lsJson(false,'body缺失');
        if(!$this->account || !$sign || !$this->ask || !$this->body){
            Tools::lsJson(false,'必要验证信息缺失');
        }
        // 验证密钥
        if(md5($this->account.self::SECRET)!=$sign){
            Tools::lsJson(false,'验证失败');
        }
        $this->initOta();
    }

    private function initOta()
    {
        switch ($this->account){
            // 拉手
            case 'lashou':
                // 验证OTA
                $this->ota = reset(OrganizationModel::model()->getOrgInfoByAttr(array('name'=>$this->account,'type'=>'agency','status'=>1)));
                !$this->ota && Tools::lsJson(false,'请创建拉手网分销商账户');
                break;
            default:
                Tools::lsJson(false,'用户不存在');
        }
    }

    /**
     * 分发控制器
     * @author yinjian
     * @date   2014-12-08
     * @return void
     */
    public function routeaskAction()
    {
        switch ($this->ask) {
            // 下单
            case 'addorder':
                $this->addorderAction();
                break;
            // 查询
            case 'selectorder':
                $this->selectorderAction();
                break;
            // 重发
            case 'resendorder':
                $this->resendorderAction();
                break;
            // 转发
            case 'resendorderChange':
                $this->resendorderChangeAction();
                break;
            // 退票
            case 'cancelorder':
                $this->cancelorderAction();
                break;
            // 默认返回错误
            default:
                Tools::lsJson(false,'请求路径出错');
                break;
        }
    }

    /**
     * 生成订单
     * amount=>nums,tatal_fee=>amount
     * @author yinjian
     * @date   2014-12-08
     * @return void [type]     [description]
     */
    private function addorderAction()
    {
        // 获取字段
        $ticket_template_id    = isset($this->body['product_id'])?$this->body['product_id']:null;
        $nums         = intval(isset($this->body['amount'])?$this->body['amount']:null);
        $owner_mobile = isset($this->body['mobile'])?$this->body['mobile']:null;
        $owner_name   = $this->body['username'];
        $amount = floatval($this->body['total_fee']);
        $trade_no     = Tools::lsPost($this->body['trade_no']);
        $use_day = date('Y-m-d');
        $pay_at = strtotime($this->body['pay_time']);
        // 表单验证
        if (!Validate::isString($ticket_template_id) ||
            !Validate::isUnsignedInt($nums) ||
            !Validate::isMobilePhone($owner_mobile) ||
            !Validate::isString($trade_no) ||
            !Validate::isString($owner_name) ||
            !Validate::isUnsignedFloat($amount)||
            !Validate::isTimestamp($pay_at)) {
            Tools::lsJson(false,'下单参数缺失');
        }
        // 拉手票号防止重复提交 提交过的直接过
        $order_id = OrderModel::model()->redis->hget(OrderModel::model()->tradeNoKey.$trade_no,'order_id');
        $order = reset(OrderModel::model()->setTable($order_id)->search(array('id'=>$order_id,'remark'=>$trade_no,'deleted_at'=>0)));
        $order && Tools::lsJson(true,'重复提交',array(
            'order_id' => $order_id,
            'trade_no' => $trade_no,
            'code' => $order_id,
            'ctime' => date('Y-m-d H:i:s',$order['created_at']),
        ));
        //获取票种含价格库存黑白名单判断
        $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($ticket_template_id,0,$this->ota['id'],$use_day,$nums);
        !$ticketTemplateInfo && Tools::lsJson(false,'票种记录不存在');
        $ticketTemplateInfo['fat_price'] = $amount/$nums;
        $ticketTemplateInfo['price'] = $amount/$nums;
        !$ticketTemplateInfo['can_buy'] && Lang_Msg::error('当前门票不能购买');
//        $ticketTemplateInfo['fat_price'] != $amount/$nums && Lang_Msg::error('拉手票价和平台单价不一致');
//        Tools::dump($ticketTemplateInfo);
        isset($ticketTemplateInfo['code']) && $ticketTemplateInfo['code']=='fail' && Lang_Msg::error($ticketTemplateInfo['message']);
        // 获取分销商和供应商名字
        $orgs = OrganizationModel::model()->getList(array($ticketTemplateInfo['organization_id'],$this->ota['id']));
        $supplier_name = empty($orgs['data'][$ticketTemplateInfo['organization_id']])?' ':$orgs['data'][$ticketTemplateInfo['organization_id']]['name'];
        $distributor_name = empty($orgs['data'][$this->ota['id']])?' ':$orgs['data'][$this->ota['id']]['name'];
        // 确认支付方式
        $supply_agency = OrganizationModel::model()->getSupplyAgency(array('supplier_id'=>$ticketTemplateInfo['organization_id'],'distributor_id'=>$this->ota['id']));
        !$supply_agency['body']['data'] && Lang_Msg::error('账户余额不足');
        $supply_agency_credit = reset($supply_agency['body']['data']);
        if($supply_agency_credit['credit_infinite']==1 || $supply_agency_credit['credit_money']>=$amount){
            $pay_type = 'credit';
            $payment = 'credit';
        }elseif($supply_agency_credit['balance_money']>=$amount){
            $pay_type = 'advance';
            $payment = 'advance';
        }elseif(count(explode(':',$supply_agency_credit['balance_over']))==2 && reset(explode(':',$supply_agency_credit['balance_over']))>=$amount){
            // 透支储值额度 @TODO
            $pay_type = 'advance';
            $payment = 'advance';
        }else{
            // 账户余额不足
            Lang_Msg::error('账户余额不足');
        }
        // 订单，订单明细，支付单，支付单明细，订单的票跟子景点关联，生成票号，生成流水
        $res = OrderModel::model()->addLashouOrder(array(
            'ticket_template_id' => $ticket_template_id,
            'nums' => $nums,
            'owner_mobile' => $owner_mobile,
            'owner_name' => $owner_name,
            'trade_no' => $trade_no,
            'use_day' => $use_day,
            'amount' => $amount,
            'pay_at' => $pay_at,
            'distributor_id' => $this->ota['id'],
            'supplier_name' => $supplier_name,
            'distributor_name' => $distributor_name,
            'pay_type' => $pay_type,
            'payment' => $payment,
        ),$ticketTemplateInfo);
        !$res && Lang_Msg::error('添加失败');
        // 发短信
        $this->sendSms(array(
            'name' => $ticketTemplateInfo['name'],
            'nums' => $nums,
            'id' => $res,
            'use_day' => $use_day,
            'expire_end' => date('Y-m-d',$ticketTemplateInfo['expire_end']),
        ),$owner_mobile);
        // 写入redis
        OrderModel::model()->redis->hset(OrderModel::model()->tradeNoKey.$trade_no , 'order_id',  $res);
        // succ
        Tools::lsJson(true,'succ',array(
            'order_id' => $res,
            'trade_no' => $trade_no,
            'code' => $res,
            'ctime' => date('Y-m-d H:i:s'),
        ));
        Yaf_DisPatcher::getInstance()->disableView();
    }

    /**
     * 查询
     * @author yinjian
     * @date   2014-12-08
     * @return void [type]     [description]
     */
    private function selectorderAction()
    {
        $order_id = Tools::lsPost($this->body['order_id']);
        $trade_no = Tools::lsPost($this->body['trade_no']);
        if(!Validate::isString($order_id) || !Validate::isString($trade_no)){
            Tools::lsJson(false,'参数缺失');
        }
        $order = reset(OrderModel::model()->setTable($order_id)->search(array('id'=>$order_id,'remark'=>$trade_no,'deleted_at'=>0)));
        !$order && Lang_Msg::error('单号不存在');
        // 查询使用情况
        $quantity = $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'];
        // 订单状态
        if($quantity>0 && $quantity==$order['nums']) {
            // 可用
            $status = 1;
        }elseif($quantity>0 && $quantity<$order['nums']){
            // 使用中
            $status = 2;
        }elseif($order['nums'] == $order['used_nums']){
            // 已经使用
            $status = 3;
        }else{
            // 已经退单
            $status = 4;
        }
        Tools::lsJson(true,'succ',array(
            'order_id' => $order_id,
            'trade_no' => $trade_no,
            'quantity' => $quantity,
            'used' => $order['used_nums'],
            'refoud_num' => $order['refunded_nums'],
            'status' => $status,
        ));
        Yaf_DisPatcher::getInstance()->disableView();
    }

    /**
     * 重发
     * @author yinjian
     * @date   2014-12-08
     * @return void [type]     [description]
     */
    private function resendorderAction()
    {
        $order_id = Tools::lsPost($this->body['order_id']);
        $trade_no = Tools::lsPost($this->body['trade_no']);
        if(!Validate::isString($order_id) || !Validate::isString($trade_no)){
            Tools::lsJson(false,'参数缺失');
        }
        $order_item = reset(OrderItemModel::model()->setTable($order_id)->search(array('order_id'=>$order_id,'deleted_at'=>0)));
        !$order_item && Lang_Msg::error('单号详情不存在');
        $order = reset(OrderModel::model()->setTable($order_id)->search(array('id'=>$order_id,'remark'=>$trade_no,'deleted_at'=>0)));
        !$order && Lang_Msg::error('单号不存在');
        // 发短信
        $res = $this->sendSms(array(
            'name' => $order_item['name'],
            'nums' => $order_item['nums'],
            'id' => $order_item['order_id'],
            'use_day' => $order_item['use_day'],
            'expire_end' => date('Y-m-d',$order_item['expire_end']),
        ),$order['owner_mobile']);
        $message = $res ? '发送成功':'发送失败';
        Tools::lsJson(true,'succ',array(
            'order_id' => $order_id,
            'trade_no' => $trade_no,
            'message' => $message,
        ));
        Yaf_DisPatcher::getInstance()->disableView();
    }

    /**
     * 转发
     * @author yinjian
     * @date   2014-12-08
     * @return void [type]     [description]
     */
    private function resendorderChangeAction()
    {
        $order_id = Tools::lsPost($this->body['order_id']);
        $trade_no = Tools::lsPost($this->body['trade_no']);
        $new_mobile = Tools::lsPost($this->body['new_mobile']);
        if(!Validate::isString($order_id) || !Validate::isString($trade_no) || !Validate::isMobilePhone($new_mobile)){
            Tools::lsJson(false,'参数缺失');
        }
        $order_item = reset(OrderItemModel::model()->setTable($order_id)->search(array('order_id'=>$order_id,'deleted_at'=>0)));
        !$order_item && Lang_Msg::error('单号不存在');
        // 发短信
        $res = $this->sendSms(array(
            'name' => $order_item['name'],
            'nums' => $order_item['nums'],
            'id' => $order_item['order_id'],
            'use_day' => $order_item['use_day'],
            'expire_end' => date('Y-m-d',$order_item['expire_end']),
        ),$new_mobile);
        $message = $res ? '发送成功':'发送失败';
        Tools::lsJson(true,'succ',array(
            'order_id' => $order_id,
            'trade_no' => $trade_no,
            'message' => $message,
        ));
        Yaf_DisPatcher::getInstance()->disableView();
    }

    // 发短信
    private function sendSms($orderInfo,$mobile)
    {
        $str = '【景旅通票台】';
        $str .= '您已成功预订 「'.$orderInfo['name']."」门票 ".$orderInfo['nums'].' 张，订单号：'.$orderInfo['id'].'，可于：'.$orderInfo['use_day'].'-'.$orderInfo['expire_end'].'游玩，';
        $url = 'http://www.piaotai.com/qr/'.$orderInfo['id'];
        $str.='点击以下链接，向工作人员展示二维码，工作人员扫描后即可入园。 '."\n".$url;
        return Sms::sendSMS($mobile,urlencode($str));
    }

    /**
     * 退票
     * @author yinjian
     * @date   2014-12-08
     * @return void [type]     [description]
     */
    private function cancelorderAction()
    {
        // 获取参数
        $order_id = Tools::lsPost($this->body['order_id']);
        $trade_no = Tools::lsPost($this->body['trade_no']);
        $cancel_num = Tools::lsPost($this->body['cancel_num']);
        if(!Validate::isString($order_id) ||
            !Validate::isString($trade_no) ||
            !Validate::isUnsignedInt($cancel_num)){
            Tools::lsJson(false,'参数缺失');
        }
        if(intval($cancel_num)<1) Lang_Msg::error('退票数量最少为1张');
        // 验证是否可以取消
        $order = reset(OrderModel::model()->setTable($order_id)->search(array('id'=>$order_id,'remark'=>$trade_no,'deleted_at'=>0)));
        !$order && Lang_Msg::error('单号不存在');
        // 查询订单详情
        $order_item = reset(OrderItemModel::model()->setTable($order_id)->search(array('order_id'=>$order_id,'deleted_at'=>0)));
        !$order_item && Lang_Msg::error('单号不存在');
        // 可退票数=总票数-已使用票数-退款中票数-已退款张数
        $remain_ticket = $order['nums'] - $order['used_nums'] -$order['refunding_nums'] -$order['refunded_nums'];
        ($cancel_num>$remain_ticket) && Lang_Msg::error('退款票数不正确');
        // 票模板为可退属性
        $order_item['refund']==0 && Lang_Msg::error('当前票不允许退款');
        // 获取退票
        $return_ticket = TicketModel::model()->setTable($order_id)->search(array('order_id'=>$order_id,'status'=>1,'poi_used_num'=>0,'deleted_at'=>0),'*',null,$cancel_num);
        // 退票申请并审核 @TODO user_id暂时设置为0
        $res = RefundApplyModel::model()->refundOrderForLashou($order,$order_item,$return_ticket,array('nums'=>$cancel_num,'user_id'=>0,'remark'=>$trade_no));
        !$res && Tools::lsJson(false,'退款失败');
        // 发短信 @TODO
        // json
        Tools::lsJson(true,'succ',array(
            'order_id' => $order_id,
            'trade_no' => $trade_no,
            'message' => '退款成功',
            'cancel_num' => $cancel_num,
        ));
    }
}