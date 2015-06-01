<?php
/**
 * 分销商商品控制器
 *
 * @Package controller
 * @Date 2015-3-10
 * @Author Joe
 */
class AgencyproductController extends Base_Controller_Api {
    /**
     * 分销商商品列表
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function listsAction() {
        $where = [];
        // 来源ID
        //if (!isset($this->body['source'])) Lang_Msg::error("ERROR_AP_1");
        isset($this->body['source']) && $where['source']    = ' A.source='.intval($this->body['source']).' and';
        $where['delete_at'] = ' A.delete_at=0 and';
        // 分销商ID
        isset($this->body['agency_id']) && $where['agency_id'] = ' A.agency_id='.intval($this->body['agency_id']).' and';
        // 商品ID
        isset($this->body['product_id']) && $where['product_id'] = ' A.product_id='.intval($this->body['product_id']).' and';
        // 商品CODE
        isset($this->body['code']) && $where['code'] = ' A.code=\''.trim($this->body['code']).'\' and';
        // 商品名称
        isset($this->body['product_name']) && $where['product_name'] = ' A.product_name like \'%'.trim($this->body['product_name']).'%\' and';
        // 上下架状态
        isset($this->body['is_sale']) && $where['is_sale'] = ' A.is_sale='.intval($this->body['is_sale']).' and';
        // 支付方式
        isset($this->body['payment']) && $where['payment'] = ' A.payment='.intval($this->body['payment']).' and';

        // 支付方式
        isset($this->body['gid']) && $where['gid'] = " A.gid='".trim($this->body['gid'])."' and"; //按分组ID

        $AgencyProduct = AgencyProductModel::model();
        $sql = 'select A.*,T.name,T.fat_price,T.group_price,T.sale_price,T.listed_price,T.rule_id,T.valid,T.max_buy,T.mini_buy,T.scenic_id,'.
            'T.view_point,T.state,T.fat_scheduled_time scheduled_time,T.week_time,T.refund,T.is_del,T.remark,'.
            'T.sale_start_time,T.sale_end_time,T.organization_id,T.type,T.date_available,T.policy_id,T.valid_flag,T.sms_template'.
            ' from '. $AgencyProduct->getTable() .' A left join '. TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id'.
            ' where ' . join(' ', $where) . ' 1=1 order by A.'.$this->getSortRule('update_at');
        $countRes = $AgencyProduct->db->selectBySql($sql);
        $this->count = count($countRes);
        $this->pagenation();
        $list = [];
        if($this->count > 0) {
            $limit = intval($this->body['show_all']) ? '' : ' limit '. (($this->current-1)*$this->items) . ',' . $this->items;
            $list = $AgencyProduct->db->selectBySql($sql.$limit );
            if(!empty($list)) {
                foreach ($list as $key=>$val) {
                    $list[$key]['extra'] = unserialize($val['extra']);
                }
            }
        }

        $result = [
            'data'         => $list,
            'pagination'   => intval($this->body['show_all'])? ['count'=> $this->count] :[
                'count'    => $this->count,
                'current'  => $this->current,
                'items'    => $this->items,
                'total'    => $this->total
            ]
        ];
        Lang_Msg::output($result);
    }
    
    /**
     * 分销商商品详情
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function detailAction() {
        // filter
        $code = trim($this->body['code']);
        $gid = trim($this->body['gid']);

        $agency_id = intval($this->body['agency_id']);
        $product_id = intval($this->body['product_id']);
        $source = intval($this->body['source']); //外部来源

        if (empty($code) && empty($gid) && ($agency_id <= 0 || $product_id <= 0 || $source <= 0)) {
            Tools::lsJson(false, '缺少渠道产品对接码，或分组ID，或分销商ID、产品ID、外部来源参数');
        }

        $where = array();
        if (!empty($code)) {
            $where['code'] = ' A.code=\''. $code .'\' and';
        }
        if (!empty($gid)) {
            $where['gid'] = ' A.gid=\''. $gid .'\' and';
        }
        if ($agency_id > 0) {
            $where['agency_id'] = ' A.agency_id=\''. $agency_id .'\' and';
        }
        if ($product_id > 0) {
            $where['product_id'] = ' A.product_id=\''. $product_id .'\' and';
        }
        if ($source > 0) {
            $where['source'] = ' A.source=\''. $source .'\' and';
        }

        $AgencyProduct = AgencyProductModel::model();
        $sql = 'select A.*,T.name,T.fat_price,T.group_price,T.sale_price,T.listed_price,T.rule_id,T.valid,T.max_buy,T.mini_buy,T.scenic_id,'.
            'T.view_point,T.state,T.fat_scheduled_time scheduled_time,T.week_time,T.refund,T.is_del,T.remark,'.
            'T.sale_start_time,T.sale_end_time,T.organization_id,T.type,T.date_available,T.policy_id, T.valid_flag'.
            ' from '. $AgencyProduct->getTable() .' A left join '. TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id'.
            ' where ' . join(' ', $where) . ' 1=1 ';
        $data = $AgencyProduct->db->selectBySql($sql);

        if (!empty($data)) {
            $result = current($data);
            $result['extra'] = unserialize($result['extra']);
            foreach($data as $v){
                $result['extras'][$v['source']] = unserialize($v['extra']);
            }
        } else {
            $result = [];
        }
        Lang_Msg::output($result);
    }
    
    /**
     * 新增分销商商品
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function addAction() {
        $data['product_id']   = intval($this->body['product_id']); //票种id
        $data['agency_id']    = intval($this->body['agency_id']); //分销商id
        $data['product_name'] = trim($this->body['product_name']); //票标题
        $data['listed_price2']= trim($this->body['listed_price2']); //票面价格
        $data['price']        = trim($this->body['price']); //价格
        $data['source']       = trim($this->body['source']); //来源
        $data['pass_type']    = !empty($this->body['pass_type'])? intval($this->body['pass_type']): 1; //入园方式
        $data['pass_address'] = trim($this->body['pass_address']); //入园地址
        $data['detail']       = trim($this->body['detail']); //产品描述
        $data['description']  = trim($this->body['description']); //使用说明
        $data['consumption_detail'] = trim($this->body['consumption_detail']); //费用明细
        $data['refund_detail']  = trim($this->body['refund_detail']); //退款说明
        $data['important_tips']  = trim($this->body['important_tips']); //重要提示
        $data['settle_payment'] = trim($this->body['settle_payment']); //结算方式
        $data['payment']      = trim($this->body['payment']); //支付方式
        $data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
        $data['is_sale']      = intval($this->body['is_sale']); //上下架(0下架1上架)
        $data['pre_order']    = intval($this->body['pre_order']); //是否预约(1是0否)
        
        $ext['cancel_time']   = !empty($this->body['cancel_time'])? trim($this->body['cancel_time']): 120; //订单未支付自动取消时间分钟计
        $ext['user_per_infos']= intval($this->body['user_per_infos']); //每几个游客共享一个游客信息0=每个人都需要,1=只需要一个人,其他数字=每几个人需要一个
        $ext['buyer_fileds']  = trim($this->body['buyer_fileds']); //购票人手机号,姓名,拼音等必填字段
        $ext['user_fileds']   = trim($this->body['user_fileds']); //用票人手机号,姓名,拼音等必填字段
        $ext['mobile_limit']  = trim($this->body['mobile_limit']); //同一手机号可预订多少张票
        $ext['card_limit']    = trim($this->body['card_limit']); //同一身份证可预订多少张票
        $ext['derate']        = trim($this->body['derate']); //返现金额
        $ext['refund_time']   = trim($this->body['refund_time']); //有效退款时间
        $ext['refund_fee']    = trim($this->body['refund_fee']); //退款手续费
        $ext['refund_type']   = trim($this->body['refund_type']); //退款手续费计算方式1每张票手续费2每个订单手续费
        $ext['safeguard']     = trim($this->body['safeguard']); //入园保障1加入2不加入
        $ext['phone']         = trim($this->body['phone']); //服务电话
        $ext['msg_custom']    = trim($this->body['msg_custom']); //自定义短信内容
        
        list($usec, $sec)     = explode(' ', microtime());
        $data['code']         = md5($data['agency_id'] .'|'. $data['product_id'] .'|'. $data['source'] .'|'. time() .'|'. substr($usec, 3, 2) . rand(100000, 9999999)); //对接码
        $data['create_at']    = time(); //创建时间
        $data['update_at']    = $data['create_at']; //更新时间
        $data['delete_at']    = 0; //删除时间
        
        $data['extra'] = serialize($ext);
        
        if (empty($data['product_id']) || empty($data['agency_id']))
            return Tools::lsJson(0, '请求数据不合法', []);

        if($data['is_sale']==1) {
            $data['state_time'] = $data['create_at'];
        }

        try {
            AgencyProductModel::model()->add($data);
        } catch (\Exception $ex) {
            return Tools::lsJson(0, $ex->getMessage(), $data);
        }
        Lang_Msg::output($data);
    }
    
    /**
     * 删除分销商商品
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function delAction() {
        $id   = intval($this->body['id']); // pkid
        !$id && Lang_Msg::error('ERROR_TICKET_1');
        
        try {
            AgencyProductModel::model()->update(['delete_at' => time()], ['id' => $id]);
        } catch (\Exception $ex) {
            return Tools::lsJson(0, $ex->getMessage(), []);
        }
        Lang_Msg::output([]);
    }
    
    /**
     * 更新分销商商品
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function updateAction() {
        $id   = intval($this->body['id']); // pkid
        !$id && Lang_Msg::error('ERROR_TICKET_1');
        
        $data['update_at'] = time(); //更新时间
        
        isset($this->body['product_name']) && $data['product_name'] = trim($this->body['product_name']); //票标题
        isset($this->body['listed_price2'])&& $data['listed_price2']= trim($this->body['listed_price2']); //票面价格
        isset($this->body['price'])        && $data['price']        = trim($this->body['price']); //价格
        isset($this->body['source'])       && $data['source']       = trim($this->body['source']); //来源
        isset($this->body['pass_type'])    && $data['pass_type']    = intval($this->body['pass_type']); //入园方式
        isset($this->body['pass_address']) && $data['pass_address'] = trim($this->body['pass_address']); //入园地址
        isset($this->body['detail'])       && $data['detail']       = trim($this->body['detail']); //产品描述
        isset($this->body['description'])  && $data['description']  = trim($this->body['description']); //使用说明
        isset($this->body['consumption_detail'])  && $data['consumption_detail']  = trim($this->body['consumption_detail']); //费用明细
        isset($this->body['refund_detail'])  && $data['refund_detail']  = trim($this->body['refund_detail']); //退款说明
        isset($this->body['important_tips'])  && $data['important_tips']  = trim($this->body['important_tips']); //重要提示
        isset($this->body['settle_payment']) && $data['settle_payment'] = trim($this->body['settle_payment']); //结算方式
        isset($this->body['payment'])      && $data['payment']      = trim($this->body['payment']); //支付方式
        isset($this->body['payment_list']) && $data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
        isset($this->body['is_sale'])      && $data['is_sale']      = intval($this->body['is_sale']); //上下架(0下架1上架)
        isset($this->body['pre_order'])    && $data['pre_order']    = intval($this->body['pre_order']); //是否预约(1是0否)
        
        $ext = [];
        isset($this->body['cancel_time'])  && $ext['cancel_time']   = trim($this->body['cancel_time']); //订单未支付自动取消时间分钟计
        isset($this->body['buyer_fileds']) && $ext['buyer_fileds']  = trim($this->body['buyer_fileds']); //购票人手机号,姓名,拼音等必填字段
        isset($this->body['user_per_infos']) && $ext['user_per_infos']  = trim($this->body['user_per_infos']); //每几个游客共享一个游客信息0=每个人都需要,1=只需要一个人,其他数字=每几个人需要一个
        isset($this->body['user_fileds'])  && $ext['user_fileds']   = trim($this->body['user_fileds']); //用票人手机号,姓名,拼音等必填字段
        isset($this->body['mobile_limit']) && $ext['mobile_limit']  = trim($this->body['mobile_limit']); //同一手机号可预订多少张票
        isset($this->body['card_limit'])   && $ext['card_limit']    = trim($this->body['card_limit']); //同一身份证可预订多少张票
        isset($this->body['derate'])       && $ext['derate']        = trim($this->body['derate']); //返现金额
        isset($this->body['refund_time'])  && $ext['refund_time']   = trim($this->body['refund_time']); //有效退款时间
        isset($this->body['refund_fee'])   && $ext['refund_fee']    = trim($this->body['refund_fee']); //退款手续费
        isset($this->body['refund_type'])  && $ext['refund_type']   = trim($this->body['refund_type']); //退款手续费计算方式1每张票手续费2每个订单手续费
        isset($this->body['safeguard'])    && $ext['safeguard']     = trim($this->body['safeguard']); //入园保障1加入2不加入
        isset($this->body['phone'])        && $ext['phone']         = trim($this->body['phone']); //服务电话
        isset($this->body['msg_custom'])   && $ext['msg_custom']    = trim($this->body['msg_custom']); //自定义短信内容
        
        $AgencyProduct = AgencyProductModel::model();
        $agencyProduct = $AgencyProduct->getById($id);
        empty($agencyProduct) && Lang_Msg::error('ERROR_TICKET_1');
        
        $data['extra'] = serialize($ext + unserialize($agencyProduct['extra']));

        if(isset($this->body['is_sale']) && $data['is_sale']==1) {
            $data['state_time'] = $data['update_at'];
        }
        
        try {
            $r = $AgencyProduct->update($data, ['id' => $id]);
            if($r) {
                //产品变动异步通知
                $params = array(
                    'product_id' => $agencyProduct['product_id'],
                    'code' => $agencyProduct['code'],
                    'agency_id' => $agencyProduct['agency_id'],
                    'source' => $agencyProduct['source'],
                );
                Process_Async::send(
                    array("OtaCallbackModel","productChangedAsync"),
                    array($params)
                );
            }
        } catch (\Exception $ex) {
            return Tools::lsJson(0, $ex->getMessage(), $data);
        }
        Lang_Msg::output($data+$agencyProduct);
    }

    /**
     * 设置分销商商品上下架
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function setSaleAction() {
        $id   = intval($this->body['id']); // pkid
        !$id && Lang_Msg::error('ERROR_TICKET_1');
        
        isset($this->body['is_sale']) ? $is_sale = intval($this->body['is_sale']): 0; //上下架状态
        
        $AgencyProduct = AgencyProductModel::model();
        $agencyProduct = $AgencyProduct->getById($id);
        empty($agencyProduct) && Lang_Msg::error('ERROR_TICKET_1');
        
        try {
            $r= $AgencyProduct->update(['is_sale'=>$is_sale,'state_time'=>time()], ['id' => $id]);
            if($r) {
                //产品变动异步通知
                $params = array(
                    'product_id' => $agencyProduct['product_id'],
                    'code' => $agencyProduct['code'],
                    'agency_id' => $agencyProduct['agency_id'],
                    'source' => $agencyProduct['source'],
                    'is_sale' => ($is_sale>0?1:0),
                );
                Process_Async::send(
                    array("OtaCallbackModel","productChangedAsync"),
                    array($params)
                );
            }
        } catch (\Exception $ex) {
            return Tools::lsJson(0, $ex->getMessage(), ['is_sale'=>$is_sale]);
        }
        Lang_Msg::output([]);
    }

    /** 批量添加ota产品记录
     * @date 2015-05-25
     * @author zqf
     */
    public function addBatchAction() {
        try {
            $data['product_id']   = intval($this->body['product_id']); //票种id
            $data['agency_id']    = intval($this->body['agency_id']); //分销商id
            $data['product_name'] = trim($this->body['product_name']); //票标题
            $data['listed_price2']= trim($this->body['listed_price2']); //票面价格
            $data['price']        = trim($this->body['price']); //价格
            //$data['source']       = trim($this->body['source']); //来源
            $data['pass_type']    = !empty($this->body['pass_type'])? intval($this->body['pass_type']): 1; //入园方式
            $data['pass_address'] = trim($this->body['pass_address']); //入园地址
            $data['detail']       = trim($this->body['detail']); //产品描述
            $data['description']  = trim($this->body['description']); //使用说明
            $data['consumption_detail'] = trim($this->body['consumption_detail']); //费用明细
            $data['refund_detail']  = trim($this->body['refund_detail']); //退款说明
            $data['important_tips'] = trim($this->body['important_tips']); //重要提示
            $data['settle_payment'] = trim($this->body['settle_payment']); //结算方式
            $data['payment']      = trim($this->body['payment']); //支付方式
            $data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
            $data['is_sale']      = intval($this->body['is_sale']); //上下架(0下架1上架)
            $data['pre_order']    = intval($this->body['pre_order']); //是否预约(1是0否)

            if (empty($data['product_id']) || empty($data['agency_id']))
                return Tools::lsJson(0, '缺少产品ID或分销商ID', []);

            $ext['cancel_time']   = !empty($this->body['cancel_time'])? trim($this->body['cancel_time']): 120; //订单未支付自动取消时间分钟计
            $ext['user_per_infos']= intval($this->body['user_per_infos']); //每几个游客共享一个游客信息0=每个人都需要,1=只需要一个人,其他数字=每几个人需要一个
            $ext['buyer_fileds']  = trim($this->body['buyer_fileds']); //购票人手机号,姓名,拼音等必填字段
            $ext['user_fileds']   = trim($this->body['user_fileds']); //用票人手机号,姓名,拼音等必填字段
            $ext['mobile_limit']  = trim($this->body['mobile_limit']); //同一手机号可预订多少张票
            $ext['card_limit']    = trim($this->body['card_limit']); //同一身份证可预订多少张票
            $ext['derate']        = trim($this->body['derate']); //返现金额
            $ext['refund_time']   = trim($this->body['refund_time']); //有效退款时间
            $ext['refund_fee']    = trim($this->body['refund_fee']); //退款手续费
            $ext['refund_type']   = trim($this->body['refund_type']); //退款手续费计算方式1每张票手续费2每个订单手续费
            $ext['safeguard']     = trim($this->body['safeguard']); //入园保障1加入2不加入
            $ext['phone']         = trim($this->body['phone']); //服务电话
            $ext['msg_custom']    = trim($this->body['msg_custom']); //自定义短信内容

            $channelData = trim($this->body['channel']); //渠道自定义字段集，已source为键
            $channelData = json_decode($channelData,true);
            if(empty($channelData)) { //请选择要发布的渠道
                Tools::lsJson(false,'请选择要发布的渠道');
            }

            $now = time();
            $data['gid']= md5($data['agency_id'] .'|'. $data['product_id'] .'|'. $data['source'] .'|'. Tools::getmicrotime()); //分组ID
            $data['create_at'] = $now; //创建时间
            $data['update_at'] = $now; //更新时间
            $data['delete_at'] = 0; //删除时间

            $values = array();
            foreach($channelData as $source=>$chan){
                $data['source'] = $source;
                if(isset($chan['product_name']))    $data['product_name'] = $chan['product_name'];
                if(isset($chan['listed_price2']))   $data['listed_price2'] = $chan['listed_price2'];
                if(isset($chan['price']))           $data['price'] = $chan['price'];

                foreach($chan as $fk=>$fv) {
                    if(is_string($fv)) {
                        $chan[$fk]= addslashes($fv);
                    }
                }

                list($usec, $sec) = explode(' ', microtime());
                $data['code']  = md5($data['agency_id'] .'|'. $data['product_id'] .'|'. $data['source'] .'|'. $now .'|'. substr($usec, 3, 2) . rand(100000, 9999999)); //对接码
                $chan = array_merge($ext,$chan);
                $data['extra'] = serialize($chan);
                if($data['is_sale']==1) {
                    $data['state_time'] = $data['create_at'];
                }
                $values[] = $data;
            }

            array_unshift($values,array_keys(reset($values)));

            $r = AgencyProductModel::model()->add($values);
            if($r) {
                Tools::lsJson(true, '操作成功');
            } else {
                Tools::lsJson(false, '操作失败');
            }
        } catch (Exception $e) {
            Tools::lsJson(false, $e->getMessage(), $data);
        }
    }

    /** 批量编辑ota产品记录
     * @date 2015-05-25
     * @author zqf
     */
    public function updateBatchAction() {
        try {
            $gid = trim($this->body['gid']);
            if(empty($gid)) {
                Tools::lsJson(false, '缺少记录ID或分组ID');
            }

            $AgencyProductModel= new AgencyProductModel();

            $records = $AgencyProductModel->search(array('gid'=>$gid));
            if(empty($records)) {
                Tools::lsJson(false, '记录不存在');
            }

            $now = time();
            $data['update_at'] = $now; //更新时间
            $data['gid'] = $gid;

            isset($this->body['product_name']) && $data['product_name'] = trim($this->body['product_name']); //票标题
            isset($this->body['listed_price2'])&& $data['listed_price2']= trim($this->body['listed_price2']); //票面价格
            isset($this->body['price'])        && $data['price']        = trim($this->body['price']); //价格
            isset($this->body['source'])       && $data['source']       = trim($this->body['source']); //来源
            isset($this->body['pass_type'])    && $data['pass_type']    = intval($this->body['pass_type']); //入园方式
            isset($this->body['pass_address']) && $data['pass_address'] = trim($this->body['pass_address']); //入园地址
            isset($this->body['detail'])       && $data['detail']       = trim($this->body['detail']); //产品描述
            isset($this->body['description'])  && $data['description']  = trim($this->body['description']); //使用说明
            isset($this->body['consumption_detail'])  && $data['consumption_detail']  = trim($this->body['consumption_detail']); //费用明细
            isset($this->body['refund_detail'])  && $data['refund_detail']  = trim($this->body['refund_detail']); //退款说明
            isset($this->body['important_tips'])  && $data['important_tips']  = trim($this->body['important_tips']); //重要提示
            isset($this->body['settle_payment']) && $data['settle_payment'] = trim($this->body['settle_payment']); //结算方式
            isset($this->body['payment'])      && $data['payment']      = trim($this->body['payment']); //支付方式
            isset($this->body['payment_list']) && $data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
            isset($this->body['is_sale'])      && $data['is_sale']      = intval($this->body['is_sale']); //上下架(0下架1上架)
            isset($this->body['pre_order'])    && $data['pre_order']    = intval($this->body['pre_order']); //是否预约(1是0否)

            $ext = [];
            isset($this->body['cancel_time'])  && $ext['cancel_time']   = trim($this->body['cancel_time']); //订单未支付自动取消时间分钟计
            isset($this->body['buyer_fileds']) && $ext['buyer_fileds']  = trim($this->body['buyer_fileds']); //购票人手机号,姓名,拼音等必填字段
            isset($this->body['user_per_infos']) && $ext['user_per_infos']  = trim($this->body['user_per_infos']); //每几个游客共享一个游客信息0=每个人都需要,1=只需要一个人,其他数字=每几个人需要一个
            isset($this->body['user_fileds'])  && $ext['user_fileds']   = trim($this->body['user_fileds']); //用票人手机号,姓名,拼音等必填字段
            isset($this->body['mobile_limit']) && $ext['mobile_limit']  = trim($this->body['mobile_limit']); //同一手机号可预订多少张票
            isset($this->body['card_limit'])   && $ext['card_limit']    = trim($this->body['card_limit']); //同一身份证可预订多少张票
            isset($this->body['derate'])       && $ext['derate']        = trim($this->body['derate']); //返现金额
            isset($this->body['refund_time'])  && $ext['refund_time']   = trim($this->body['refund_time']); //有效退款时间
            isset($this->body['refund_fee'])   && $ext['refund_fee']    = trim($this->body['refund_fee']); //退款手续费
            isset($this->body['refund_type'])  && $ext['refund_type']   = trim($this->body['refund_type']); //退款手续费计算方式1每张票手续费2每个订单手续费
            isset($this->body['safeguard'])    && $ext['safeguard']     = trim($this->body['safeguard']); //入园保障1加入2不加入
            isset($this->body['phone'])        && $ext['phone']         = trim($this->body['phone']); //服务电话
            isset($this->body['msg_custom'])   && $ext['msg_custom']    = trim($this->body['msg_custom']); //自定义短信内容

            $channelData = trim($this->body['channel']); //渠道自定义字段集，已source为键
            $channelData = json_decode($channelData,true);
            if(empty($channelData)) { //请选择要发布的渠道
                Tools::lsJson(false,'请选择要发布的渠道');
            }

            $upValues = $newValues = array();
            foreach($channelData as $source=>$chan){
                $data['source'] = $source;
                if(isset($chan['product_name']))    $data['product_name'] = $chan['product_name'];
                if(isset($chan['listed_price2']))   $data['listed_price2'] = $chan['listed_price2'];
                if(isset($chan['price']))           $data['price'] = $chan['price'];

                foreach($chan as $fk=>$fv) {
                    if(is_string($fv)) {
                        $chan[$fk]= addslashes($fv);
                    }
                }

                if(!empty($chan['id'])) {
                    $tmp = $chan;
                    unset($tmp['id']);
                    $tmp = array_merge($ext,$tmp);
                    $data['extra'] = serialize($tmp);
                    $upValues[$chan['id']] = $data;
                } else {
                    list($usec, $sec) = explode(' ', microtime());
                    $data['code']  = md5($data['agency_id'] .'|'. $data['product_id'] .'|'. $data['source'] .'|'. $now .'|'. substr($usec, 3, 2) . rand(100000, 9999999)); //对接码
                    $chan = array_merge($ext,$chan);
                    $data['extra'] = serialize($chan);
                    $newValues[] = $data;
                }
            }
            if(!empty($upValues)) {
                foreach($upValues as $id=>$v) {
                    $r = $AgencyProductModel->updateById($id,$v);
                    if(!$r) {
                        Tools::lsJson(false, '操作失败');
                    }
                }
            }

            if(!empty($newValues)) {
                array_unshift($newValues,array_keys(reset($newValues)));
                $r = $AgencyProductModel->add($newValues);
                if(!$r) {
                    Tools::lsJson(false, '操作失败');
                }
            }

            Tools::lsJson(true, '操作成功');
        } catch (Exception $e) {
            Tools::lsJson(false, $e->getMessage(), $data);
        }
    }


}