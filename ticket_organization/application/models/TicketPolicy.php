<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-24
 * Time: 上午9:55
 */

class TicketPolicyModel extends Base_Model_Api{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/Ticketpolicy/';
    protected $method = 'POST';
	
    /**
     * 解绑分销商-删除策略的绑定数据
     */
    public function unbindDistributor($distributorId, $supply_id)
    {
		$this->url .= 'unbindDistributor';
    	$this->params = array('distributor_id'=>$distributorId,'supply_id'=>$supply_id);
        $this->method = 'POST';
       	$this->request();
    }
}