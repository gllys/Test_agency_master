<?php
/**
 * 分销商商品控制器
 *
 * @Package controller
 * @Date 2015-3-101
 * @Author Joe
 */
class AgencyproductController extends Base_Controller_Api {
    /**
     * 分销商商品列表
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function listsAction() {
        $where = array();
        // 来源ID
		if (!isset($this->body['source'])) Lang_Msg::error("ERROR_AP_1");
        $where['source'] = ' A.source='.$this->body['source'].' and';
        // 分销商ID
        isset($this->body['agency_id']) && $where['agency_id'] = ' A.agency_id='.$this->body['agency_id'].' and';
        // 商品ID
        isset($this->body['product_id']) && $where['product_id'] = ' A.product_id='.$this->body['product_id'].' and';

        $AgencyProduct = AgencyProductModel::model();
		$sql = 'select * from '. $AgencyProduct->getTable() .' A left join '. TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id'.
		' where ' . join(' ', $where) . ' 1=1 order by A.id desc';
		$countRes = $AgencyProduct->db->selectBySql($sql);
        $this->count = count($countRes);
        $this->pagenation();
		$list = [];
        if($this->count > 0) {
			$list = $AgencyProduct->db->selectBySql($sql. ' limit '. (($this->current-1)*$this->items) . ',' . $this->items);
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
        $where['code'] = ' A.code='. $this->body['code'] .' and';

        $AgencyProduct = AgencyProductModel::model();
		$sql = 'select * from '. $AgencyProduct->getTable() .' A left join '. TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id'.
		' where ' . join(' ', $where) . ' 1=1 limit 1';
		$result = $AgencyProduct->db->selectBySql($sql);

		$result = empty($result)? []: $result[0];
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
		$data['price']        = trim($this->body['price']); //价格
		$data['source']       = trim($this->body['source']); //来源
		$data['payment']      = trim($this->body['payment']); //支付方式
		$data['payment_list'] = trim($this->body['payment_list']); //可用支付方式
		
		$data['code']         = md5($data['agency_id'] .'|'. $data['product_id']); //对接码
		$data['create_at']    = time(); //创建时间
		$data['update_at']    = 0; //更新时间
		$data['delete_at']    = 0; //删除时间
		
		try {
			AgencyProductModel::model()->add($data);
			Lang_Msg::output($data);
		} catch (Exception $ex) {
			Tools::lsJson($ex->getCode(), $ex->getMessage(), $data);
		}
    }

}