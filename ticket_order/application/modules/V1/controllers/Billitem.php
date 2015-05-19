<?php
/**
 * 结款订单控制器
 *
 * @Package controller
 * @Date 2015-4-07
 * @Author Joe
 */
class BillitemController extends Base_Controller_Api {
    /**
     * 结款订单列表
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function listsAction() {
        $where = [];
        // 订单状态
        isset($this->body['status']) && $where['status'] = ' O.status='.trim($this->body['status']).' and';
        // 游玩日期 
        isset($this->body['use_day']) && $where['use_day'] = ' B.use_day='.trim($this->body['use_day']).' and';
        // 入园时间
        isset($this->body['use_time']) && $where['use_time'] = ' O.use_time='.intval($this->body['use_time']).' and';
        // 预订日期
        isset($this->body['ordered_at']) && $where['ordered_at'] = ' B.ordered_at='.intval($this->body['ordered_at']).' and';

        $Billitem = BillitemModel::model();
		$sql = 'select B.*,O.*'.
		' from '. $Billitem->getTable() .' B left join '. OrderModel::model()->getTable() . ' O on B.order_id=O.id'.
		' where ' . join(' ', $where) . ' 1=1 order by B.'.$this->getSortRule();
		$countRes = $Billitem->db->selectBySql($sql);
        $this->count = count($countRes);
        $this->pagenation();
		$list = [];
        if($this->count > 0) {
			$list = $Billitem->db->selectBySql($sql. ' limit '. (($this->current-1)*$this->items) . ',' . $this->items);
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
}