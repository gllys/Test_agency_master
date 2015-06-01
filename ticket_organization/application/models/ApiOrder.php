<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 2015/04/20
 * Time: 13:34
 * 需要用的订单相关API
 */

class ApiOrderModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_order';
    protected $url = '/v1/cart/count';
    protected $method = 'POST';

    public function cartCount($user_id) //购物车记录数
    {
        if (!$user_id)
            return false;
        $this->preCacheKey = 'cache|cartModel|';

        $this->url = '/v1/cart/count';
        $this->params = array('user_id' => $user_id);

        $r = $this->customCache('cartCount_'.$user_id);
        if($r==null) $r = $this->customCache('cartCount_'.$user_id,json_decode($this->request(), true));

        if (!$r || empty($r['body']['pagination']))
            return false;
        return $r['body']['pagination']['count'];
    }

}