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
		if (!isset($this->body['source'])) Lang_Msg::error("ERROR_AP_1");
        $where['source'] = ' A.source='.intval($this->body['source']).' and';
        // 分销商ID
        isset($this->body['agency_id']) && $where['agency_id'] = ' A.agency_id='.intval($this->body['agency_id']).' and';
        // 商品ID
        isset($this->body['product_id']) && $where['product_id'] = ' A.product_id='.intval($this->body['product_id']).' and';

        $AgencyProduct = AgencyProductModel::model();
		$sql = 'select A.*,T.name,T.fat_price,T.group_price,T.sale_price,T.listed_price,T.rule_id,T.valid,T.max_buy,T.mini_buy,T.scenic_id,'.
		'T.view_point,T.state,T.scheduled_time,T.week_time,T.refund,T.is_del,T.remark,T.organization_id,T.type,T.date_available,T.policy_id,T.valid_flag,T.sms_template'.
		' from '. $AgencyProduct->getTable() .' A left join '. TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id'.
		' where ' . join(' ', $where) . ' 1=1 order by A.'.$this->getSortRule('update_at');
		$countRes = $AgencyProduct->db->selectBySql($sql);
        $this->count = count($countRes);
        $this->pagenation();
		$list = [];
        if($this->count > 0) {
			$list = $AgencyProduct->db->selectBySql($sql. ' limit '. (($this->current-1)*$this->items) . ',' . $this->items);
			foreach ($list as $key=>$val) {
				$list[$key]['extra'] = unserialize($val['extra']);
			}
		}

        $result = [
            'data'         => $list,
            'pagination'   => [
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
		if (!isset($this->body['code'])) Lang_Msg::error("ERROR_AP_2");
        $where['code'] = ' A.code=\''. $this->body['code'] .'\' and';

        $AgencyProduct = AgencyProductModel::model();
		$sql = 'select A.*,T.name,T.fat_price,T.group_price,T.sale_price,T.listed_price,T.rule_id,T.valid,T.max_buy,T.mini_buy,T.scenic_id,'.
		'T.view_point,T.state,T.scheduled_time,T.week_time,T.refund,T.is_del,T.remark,T.organization_id,T.type,T.date_available,T.policy_id, T.valid_flag'.
		' from '. $AgencyProduct->getTable() .' A left join '. TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id'.
		' where ' . join(' ', $where) . ' 1=1 limit 1';
		$result = $AgencyProduct->db->selectBySql($sql);

		if (!empty($result)) {
			$result = current($result);
			$result['extra'] = unserialize($result['extra']);
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
		$data['settle_payment'] = trim($this->body['settle_payment']); //结算方式
		$data['payment']      = trim($this->body['payment']); //支付方式
		$data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
		
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
		$data['update_at']    = 0; //更新时间
		$data['delete_at']    = 0; //删除时间
		
		$data['extra'] = serialize($ext);
		
		if (empty($data['product_id']) || empty($data['agency_id']))
			return Tools::lsJson(0, '请求数据不合法', []);
		
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
			AgencyProductModel::model()->delete(['id' => $id]);
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
		isset($this->body['settle_payment']) && $data['settle_payment'] = trim($this->body['settle_payment']); //结算方式
		isset($this->body['payment'])      && $data['payment']      = trim($this->body['payment']); //支付方式
		isset($this->body['payment_list']) && $data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
		
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
		
		try {
			$AgencyProduct->update($data, ['id' => $id]);
		} catch (\Exception $ex) {
			return Tools::lsJson(0, $ex->getMessage(), $data);
		}
		Lang_Msg::output($data+$agencyProduct);
    }

}