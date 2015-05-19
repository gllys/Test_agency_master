<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-24
 * Time: 上午9:55
 */

class PvModel extends Base_Model_Api{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/Poi/instant';
    protected $method = 'POST';
	
    /**
     * 增减统计数据
     */
    public function updateStatics($landscape_id, $num)
    {
    	$this->params = array('landscape_id'=>$landscape_id, 'num'=>$num);
        $this->method = 'POST';
       	$this->request();
    }
}