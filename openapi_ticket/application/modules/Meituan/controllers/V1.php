<?php
/**
 * Created by PhpStorm.
 * User: libiying
 * Date: 2015/1/15
 * Time: 15:20
 */

class V1Controller extends Base_Controller_ApiDispatch{

    const SOURCE    = 15;
    const USER_ID   = 1;
    const MEITUAN   = 'Meituan';
    const REMASK    = '美团订单';

    const UNLIMITSTOCK      = 100000000;    //无限库存
    const VOUCHERTYPE       = 0;            //凭证类型，0 短信验证码 ；1 二维码
    const COMMISSIONRATIO   = 0.1;          //双方商务协调的结果

    private $service = NULL;
    private $logger = NULL;

    public function init(){
        parent::init(false);

        $this->service =  Meituan_Service::create();
        $this->logger = Util_Logger::getLogger('meituan');

        //美团采用 application/json 的方式传送数据
        $input = file_get_contents('php://input');
        if(strpos(strtolower($_SERVER['HTTP_CONTENT_TYPE']), 'application/json') !== false){
            $this->body = json_decode($input, true);
        }
        !$this->body && $this->service->outputError('参数为空或解析错误', __METHOD__, '' , $input);
    }

    /**
     * 拉取产品接口
     */
    public function getLvProductAction(){
        $req = $this->body['body'];
        $this->logger->info(__METHOD__, $this->body, '', '拉取产品参数');

        $products = array();
        if($req['method'] == 'multi'){
            //单点、多点，partnerDealId应该是数组
            foreach($req['partnerDealId'] as $id){
                $productDetail = ApiProductModel::model()->getProductByCode(array('code' => $id));
                if($productDetail['code'] != 'succ'){
                    $this->service->outputError('获取产品失败，产品id：'.$req['partnerDealId'], __METHOD__);
                }
                if($productDetail['body']['source'] != self::SOURCE){
                    $this->service->outputError('获取产品失败：ota不匹配', __METHOD__);
                }
                $products[] = $productDetail['body'];
            }
        }else if($req['method'] == 'page'){
            //分页
            $arr = array(
                'current'   => $req['currentPage'],
                'items'     => $req['pageSize'],
                'source'    => self::SOURCE, //source的值？
            );

            $productList = ApiProductModel::model()->getProductListByCode($arr);
            if($productList['code'] != 'succ'){
                $this->service->outputError('分页获取产品失败', __METHOD__);
            }
            $products   = $productList['body']['data'];
            $pagination = $productList['body']['pagination'];
        }else if($req['method'] == 'increment'){
            $this->service->outputError('暂不支持增量方式', __METHOD__);
        }

        //获取相关景点的图片数据
        $ids = '';
        $images = array();
        foreach($products as $product){
            $ids .= $product['scenic_id'] . ',';
        }
        $ids = substr($ids,0,strlen($ids)-1);
        $imgList = ApiScenicModel::model()->imgLists(array('landscape_id' => $ids));
        if($imgList['code'] != 'succ'){
            $this->service->outputError('获取景区失败，景区id：'.$ids, __METHOD__);
        }
        if(is_array($imgList['body']['data'])){
            foreach($imgList['body']['data'] as $img){
                $images[$img['landscape_id']] = $img['url'];
            }
        }
        //构造产品数据
        $body = array();
        foreach($products as $product){

            $extra = $product['extra'];
            $scenicId = explode(',', $product['scenic_id']);
            $pics = array();
            foreach($scenicId as $id){
                $pics = array_merge(array($images[$id]), $pics);
            }
            $buyer_fileds = explode( ',', $extra['buyer_fileds']);
            $firstVisitor = array(
                'name'              => true,
                'pinyin'            => in_array('namePinyinRequired', $buyer_fileds),
                'mobile'            => true,
                'address'           => in_array('addressRequired', $buyer_fileds),
                'postCode'          => in_array('addressRequired', $buyer_fileds),
                'credentials'       => false,
            );
            $otherVisitor = array(
                'name'              => $extra['user_per_infos'] == 1,
                'pinyin'            => false,
                'mobile'            => $extra['user_per_infos'] == 1,
                'address'           => false,
                'postCode'          => false,
                'credentials'       => false,
            );
            $refundRatio = $this->getRefundRadio($extra['refund_type'], $extra['refund_fee'], $product['price']);

            $pro['partnerId']           = $this->body['partnerId'];
            $pro['partnerSupplierId']   = $this->body['partnerId']; //商户id，美团暂时未使用
            $pro['partnerDealId']       = $product['code'];
            $pro['partnerPoiId']        = $product['scenic_id'];
            $pro['name']                = $product['product_name'];
            $pro['pics']                = array('urls'=>$pics);
            $pro['visitorRequire']      = array(
                'firstVisitor' => $firstVisitor,        //第一游玩人信息
                'otherVisitor' => $otherVisitor,        //其他游玩人信息
            );
            $pro['minimum']         = 1;
            $pro['maximum']         = 100;
            $pro['marketPrice']     = $product['listed_price2'] != 0 ? $product['listed_price2'] : $product['listed_price'];
            $pro['sellPrice']       = $product['price'];
            $pro['stock']           = self::UNLIMITSTOCK;                      //日库存？
            $pro['commissionRatio'] = self::COMMISSIONRATIO;
            $pro['purchaseNote']    = array(
                'chargeIncludeNote' => $product['consumption_detail'], //费用包含
                'refundNote'        => $product['refund_detail'],       //退款说明
                'useNote'           => $product['description'],         //使用说明
                'importantNote'     => $product['important_tips'],//重要提示
                'imageTextNote'     => $product['detail'],//图文详情
            );
            $pro['needBook']    = $product['pre_order'];
            $pro['aheadHour']   = $this->getAheadHour($product['scheduled_time']);
            $pro['canRefund']   = $product['refund'];
            $pro['startTime']   = $product['sale_start_time'] == 0 ? date('Y-m-d 01:00:00',strtotime('-1 day')) : date('Y-m-d H:i:s',$product['sale_start_time']);//上架时间（销售开始时间）
            $pro['endTime']     = $product['sale_end_time'] == 0 ? date('Y-m-d 01:00:00',strtotime('+1 year')) : date('Y-m-d H:i:s',$product['sale_end_time']);//下架时间（销售结束时间）
            if($refundRatio){
                $pro['refundRatio'] = $refundRatio;//退款比例
            }

            $body[] = $pro;
        }
        $data = array(
            'totalSize'         => $req['method'] == 'page' ? $pagination['count']: 1,
            'nextIncrementId'   => 0,
            'body'              => $body,
        );

        $this->logger->info(__METHOD__, array('data' => $data, 'products' => $products), '', '拉取产品回传');
        $this->service->outputSucc($data);
    }

    /**
     * 获取产品景区数据
     */
    public function getLvPoiAction(){
        $req = $this->body['body'];
        $this->logger->info(__METHOD__, $this->body, '', '拉取景区参数');

        $time       = 60;
        $redis_key  = 'meituan-getlvpoi-page';
        $sort_key   = 'meituan-getlvpoi-sort';
        $total_key  = 'meituan-getlvpoi-total';
        $redis = Cache_Redis::factory();
        $redis->connect();

        $scenics = array();
        if($req['method'] == 'multi'){
            //单点、多点，partnerDealId应该是数组
            foreach($req['partnerPoiId'] as $id){
                if($redis->get($redis_key . $id)){
                    $scenics[] = unserialize($redis->get($redis_key.$id));
                }else{
                    $scenicDetail = ApiOtaModel::model()->scenicDetail(array('id' => $id, 'source' => self::SOURCE));
                    if($scenicDetail['code'] != 'succ'){
                        $this->service->outputError('获取产品失败，产品id：'.$req['partnerDealId'], __METHOD__);
                    }
                    $scenics[] = $scenicDetail['body'];
                    $redis->set($redis_key . $id, serialize($scenicDetail['body']));
                    $redis->expire($redis_key . $id, $time);
                }
            }
        }else if($req['method'] == 'page'){
            //分页

            if(!$redis->get($redis_key)){
                $scenicList = ApiOtaModel::model()->scenicLists(array('source' => self::SOURCE));
                if($scenicList['code'] != 'succ'){
                    $this->service->outputError('分页获取产品失败', __METHOD__);
                }
                $scenicList = $scenicList['body'];

                //先清空SortedSet
                $redis->delete($sort_key);
                foreach($scenicList as $scenic){
                    //序列化再存入
                    $redis->set($redis_key . $scenic['id'], serialize($scenic));
                    $redis->expire($redis_key . $scenic['id'], $time);
                    $redis->zAdd($sort_key, intval($scenic['id']), intval($scenic['id']));
                }
                $redis->set($total_key, count($scenicList));
                $redis->set($redis_key, 1);
                $redis->expire($redis_key, $time);
            }

            $startInx   = $req['pageSize']*($req['currentPage']-1);
            $endInx     = $req['pageSize']*$req['currentPage']-1;
            $range      = $redis->zRevRange($sort_key, $startInx, $endInx);
            foreach($range as $id){
                //取出后反序列化
                $scenics[] = unserialize($redis->get($redis_key.$id));
            }

            $totalSize = $redis->get($total_key);
        }else if($req['method'] == 'increment'){
            $this->service->outputError('暂不支持增量方式', __METHOD__);
        }

        //构造景点数据
        $body = array();
        foreach($scenics as $scenic){
            $sce = array(
                'partnerId'     => $this->body['partnerId'],
                'partnerPoiId'  => $scenic['id'],
                'name'          => $scenic['name'],
                'city'          => isset($scenic['city_name']) ? $scenic['city_name'] : '',
                'address'       => $scenic['address'],
                'phone'         => $scenic['phone'],
                'description'   => $scenic['biography'],
                'img'           => array('urls'=>$scenic['images']),
                'longitude'     => $scenic['lng'] * 1000000,
                'latitude'      => $scenic['lat'] * 1000000,
            );

            $body[] = $sce;
        }
        $data = array(
            'totalSize'         => $req['method'] == 'page' ? $totalSize: 1,
            'nextIncrementId'   => 0,
            'body'              => $body,
        );

        $this->logger->info(__METHOD__, array('data' => $data, 'scenics' => $scenics), '', '拉取景区回传');
        $this->service->outputSucc($data);
    }

    /**
     * 日历库存价格
     */
    public function queryLvProductPriceAction(){
        $req = $this->body['body'];
        $logKey = $req['partnerDealId'];
        $this->logger->info(__METHOD__, $this->body, '', '日历价格查询参数' , $logKey);

        $productDetail = ApiProductModel::model()->getProductByCode(array('code' => $req['partnerDealId']));
        if($productDetail['code'] != 'succ'){
            $this->service->outputError('获取产品失败，产品id：'.$req['partnerDealId'], __METHOD__, $logKey);
        }
        if($productDetail['body']['source'] != self::SOURCE){
            $this->service->outputError('获取产品失败：ota不匹配', __METHOD__, $logKey);
        }
        $productDetail = $productDetail['body'];

        //获取日历价库存信息
        $rule = ApiProductModel::model()->getAgencyRule(
            array(
                'code' => $req['partnerDealId'],
            ));
        $rule_items = array();
        foreach($rule['body']['data'] as $item){
            $rule_items[$item['date']] = $item;
        }
        //循环查询日历票日期区间
        $body = array();
        $temp = $req['startTime'];
        $endTime = strtotime($req['endTime']);
        while(strtotime($temp) <= $endTime){
            if(isset($rule_items[$temp])){
                $marketPrice    = floatval($rule_items[$temp]['sale_price']);
                $sellPrice      = floatval($rule_items[$temp]['price']);
                $stock          = $rule_items[$temp]['reserve'] == 0 ? self::UNLIMITSTOCK : $rule_items[$temp]['reserve'] - $rule_items[$temp]['used_reserve'];
            }else{
                $marketPrice    = floatval($productDetail['sale_price']);
                $sellPrice      = floatval($productDetail['price']);
                $stock          = self::UNLIMITSTOCK;
            }
            $priceType = array(
                'partnerDealId' => $req['partnerDealId'],
                'priceDate'     => $temp,
                'aheadHour'     => $this->getAheadHour($productDetail['scheduled_time']),
                'marketPrice'   => $marketPrice,
                'sellPrice'     => $sellPrice,
                'stock'         => $stock,
            );
            $body[] = $priceType;

            $temp = date("Y-m-d",strtotime("$temp +1 day"));
        }

        $data = array(
            'partnerDealId' => $req['partnerDealId'],
            'partnerPoiId'  => $req['partnerPoiId'],
            'body'          => $body,
        );

        $this->logger->info(__METHOD__, $data, '', '日历价格查询回传' , $logKey);
        $this->service->outputSucc($data);
    }

    /**
     * 产品预约（检查可定）
     */
    public  function validateLvOrderAction(){
        $req = $this->body['body'];
        $logKey = $req['partnerDealId'];
        $err = array('describe' => '','body' => array('orderStatus' => 1));
        $this->logger->info(__METHOD__, $this->body, '', '产品预约参数' , $logKey);

        $productDetail = ApiProductModel::model()->getProductByCode(array('code' => $req['partnerDealId']));
        if($productDetail['code'] != 'succ'){
            $err['describe'] = '获取产品失败';
            $this->service->outputError($err, __METHOD__, $logKey);
        }
        if($productDetail['body']['source'] != self::SOURCE){
            $err['describe'] = '获取产品失败：ota不匹配';
            $this->service->outputError($err, __METHOD__, $logKey);
        }
        if($productDetail['body']['price'] != $req['sellPrice']){
            $rule = ApiProductModel::model()->getAgencyRule(
                array(
                    'code' => $req['partnerDealId'],
                ));
            $rule_items = array();
            foreach($rule['body']['data'] as $item){
                $rule_items[$item['date']] = $item;
            }
            if($rule_items[$req['visitDate']]['price'] != $req['sellPrice']){
                $err['describe'] = '价格不一致';
                $this->service->outputError($err, __METHOD__, $logKey);
            }
        }
        $productDetail = $productDetail['body'];

        $params = array(
            'product_id'        => $productDetail['product_id'],
            'price_type'        => 0,
            'distributor_id'    => $productDetail['agency_id'],
            'use_day'           => $req['visitDate'],
            'nums'              => $req['quantity'],
        );
        $res = ApiOrderModel::model()->check($params);
        if($res['code'] != 'succ'){
            $err['describe'] = $res['message'];
            $this->service->outputError($err, __METHOD__, $logKey);
        }

        $data = array(
            'body' => array(
                'orderStatus' => 0
            ),
        );

        $this->logger->info(__METHOD__, $data, '', '产品预约回传' ,$logKey);
        $this->service->outputSucc($data, '检验成功');
    }

    /**
     * 创建订单，内部系统并非真正创建
     */
    public function createLvOrderAction(){
        $this->logger->info(__METHOD__, $this->body, '', '创建订单参数' , $this->body['body']['partnerDealId']);

        $data = array(
            'body' => array(
                'partnerOrderId' => '',
                'orderStatus' => 2,
                'voucherType' => '',
                'voucher' => '',
            ),
        );
        $this->service->outputSucc($data, '创建成功');
    }

    /**
     * 支付订单，内部系统为下单并支付
     */
    public function payLvOrderAction(){
        $req = $this->body['body'];
        $logKey = $req['bookOrderId'];
        $err = array('describe' => '','body' => array('partnerOrderId'=> '', 'orderStatus' => 5));
        $this->logger->info(__METHOD__, $this->body, '', '支付订单参数' , $logKey);

        $productDetail = ApiProductModel::model()->getProductByCode(array('code' => $req['partnerDealId']));
        if($productDetail['code'] != 'succ'){
            $err['describe'] = '获取产品失败';
            $this->service->outputError($err, __METHOD__, $logKey);
        }
        if($productDetail['body']['source'] != self::SOURCE){
            $err['describe'] = '获取产品失败：ota不匹配';
            $this->service->outputError($err, __METHOD__, $logKey);
        }
        if($productDetail['body']['price'] != $req['sellPrice']){
            $rule = ApiProductModel::model()->getAgencyRule(
                array(
                    'code' => $req['partnerDealId'],
                ));
            $rule_items = array();
            foreach($rule['body']['data'] as $item){
                $rule_items[$item['date']] = $item;
            }
            if($rule_items[$req['visitDate']]['price'] != $req['sellPrice']){
                $err['describe'] = '价格不一致';
                $this->service->outputError($err, __METHOD__, $logKey);
            }
        }
        $productDetail = $productDetail['body'];

        //构造下单参数
        $visitors[] = array(
            'visitor_name' => $req['firstVisitor']['name'],
        );;
        if(isset($req['otherVisitor'])){
            foreach ($req['otherVisitor'] as $visitor) {
                $visitors[] = array(
                    'visitor_name' => $visitor['name'],
                );
            }
        }
        $payment = array(
            1=> 'alipay',
            2=> 'credit',
            3=> 'advance',
            4=> 'union',
        );
        $params = array(
            'product_id'        => $productDetail['product_id'],
            'distributor_id'    => $productDetail['agency_id'],
            'use_day'           => $req['visitDate'],
            'nums'              => $req['quantity'],
            'source_id'         => $req['bookOrderId'],
            'owner_name'        => $req['firstVisitor']['name'],
            'owner_mobile'      => $req['firstVisitor']['mobile'],
            'owner_card'        => $req['firstVisitor']['credentialsType'] == 0 ? $req['firstVisitor']['credentials'] : '',
            'visitors'          => json_encode($visitors),
            'payment'           => $payment[$productDetail['payment']],
            'price_type'        => 0,
            'local_source'      => 1,
            'source'            => self::SOURCE,
            'ota_type'          => self::MEITUAN,
            'ota_account'       => self::MEITUAN,
            'ota_name'          => self::MEITUAN,
            'user_id'           => self::USER_ID,
            'user_account'      => self::MEITUAN,
            'user_name'         => self::MEITUAN,
            'remask'            => self::REMASK,
        );
        $res = ApiOrderModel::model()->create($params);
        $this->logger->info(__METHOD__, array('params' => $params, 'res' => $res), '', '下单参数与返回' , $res['body']['id']);
        if($res['code'] != 'succ'){
            $err['describe'] = $res['message'];
            $this->service->outputError($err, __METHOD__, $logKey);
        }

        $data = array(
            'body' => array(
                "partnerOrderId"    => $res['body']['id'],
                "orderStatus"       => '4',
                "voucherType"       => self::VOUCHERTYPE,
                "voucher"           => $res['body']['id']
            ),
        );

        $this->logger->info(__METHOD__, $data, '', '支付订单回传' , $res['body']['id']);
        $this->service->outputSucc($data, '交易成功');
    }

    /**
     * 订单查询
     */
    public function queryLvOrderAction(){
        $req = $this->body['body'];
        $logKey = $req['partnerOrderId'];
        $this->logger->info(__METHOD__, $this->body, '', '支付查询参数' , $logKey);

        $params['id'] = $req['partnerOrderId'];
        $res = ApiOrderModel::model()->detail($params);
        if($res['code'] != 'succ'){
            $this->service->outputError($res['message'], __METHOD__, $logKey);
        }
        $res = $res['body'];

        $status = ($res['used_nums'] + $res['refunded_nums']) == $res['nums'] ? 8 : 9;  //8:已全部使用 ; 9:未全部使用
        $data = array(
            'body' => array(
                'bookOrderId'       => $req['bookOrderId'],
                'partnerOrderId'    => $req['partnerOrderId'],
                'orderStatus'       => $status,
                'orderQuantity'     => $res['nums'],
                'usedQuantity'      => $res['used_nums'],
                'refundQuantity'    => $res['refunded_nums'],
            ),
        );

        $this->logger->info(__METHOD__, $data, '', '支付查询参数' , $logKey);
        $this->service->outputSucc($data);
    }

    /**
     * 退款申请
     */
    public function refundLvOrderAction(){
        $req = $this->body['body'];
        $logKey = $req['partnerOrderId'];
        $err = array('describe' => '','body' => array('bookOrderId'=>'', 'partnerOrderId'=> '', 'refundDes'=>'', 'orderStatus' => 5));
        $this->logger->info(__METHOD__, $this->body, '', '退款申请参数' , $logKey);

        //检查是否核销过，若核销过不允许退款
        $params['id'] = $req['partnerOrderId'];
        $order = ApiOrderModel::model()->detail($params);
        if($order['code'] != 'succ'){
            $err['describe'] = $err['body']['refundDes'] = $order['message'];
            $this->service->outputError($err, __METHOD__, $logKey);
        }
        if($order['body']['refunded_nums'] > 0){
            $err['describe'] = $err['body']['refundDes'] = '该码消费过，不允许退款';
            $this->service->outputError($err, __METHOD__, $logKey);
        }

        $params['order_id'] = $req['partnerOrderId'];
        $params['nums']     = $req['refundQuantity'];
        $params['user_id']  = self::USER_ID;
        $params['remark']   = '美团退款申请';
        $res = ApiOrderModel::model()->refundApply($params);
        if($res['code'] != 'succ'){
            $err['describe'] = $err['body']['refundDes'] = $res['message'];
            $this->service->outputError($res['message'], __METHOD__, $logKey);
        }

        $data = array(
            'body' => array(
                'bookOrderId'       => $req['bookOrderId'],
                'partnerOrderId'    => $req['bookOrderId'],
                'orderStatus'       => 6,                           //6:退款成功
                'autoRefund'        => 1,
                'refundType'        => 1,
                'refundDes'         => '退款申请成功',
                'refundPrice'       => $req['refundPrice'],         //退款总金额
                'refundRefactorage' => 0,                           //退款手续费
            ),
        );

        $this->logger->info(__METHOD__, $data, '', '退款申请回传' , $logKey);
        $this->service->outputSucc($data);
    }

    /**
     * 重发短信凭证
     */
    public function resendLvOrderVoucherAction(){
        $req = $this->body['body'];
        $logKey = $req['partnerOrderId'];
        $this->logger->info(__METHOD__, $this->body, '', '重发短信凭证参数' , $logKey);

        $params['id'] = $req['partnerOrderId'];
        $res = ApiOrderModel::model()->sendTicket($params);
        if($res['code'] != 'succ'){
            $this->service->outputError($res['message'], __METHOD__, $logKey);
        }

        $data = array(
            'body' => array(
                'voucherType' => self::VOUCHERTYPE,
                'voucher' => $res['body']['order_id'],
            ),
        );

        $this->logger->info(__METHOD__, $data, '', '重发短信凭证回传' , $logKey);
        $this->service->outputSucc($data);
    }

    private function getAheadHour($time){
        return intval($time / 86400) * 24 + (24 - intval($time / 3600) % 24);
    }

    private function getRefundRadio($refund_type, $refund_fee, $price){
        $p = floatval($price);

        return $refund_type == 1 && $p > 0 ? floatval($refund_fee)/$p : NULL;
    }
}
