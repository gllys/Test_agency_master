<?php

/**
 * 分销商商品日价格日库存
 *
 * @Package Models
 * @Date 2015-5-20
 * @Author zhaqf
 */
class AgencyProductRuleModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'agency_product_rule';
    protected $pkKey = '';
    protected $preCacheKey = 'cache|AgencyProductRuleModel|';

    public function getTable()
    {
        return $this->tblname;
    }

    public function addList($data, $days, $daysReserve=array()) //批量添加明细
    {
        $values = array();
        foreach ($days as $v) {
            $tmp = array('date' => $v);
            if($data['reserve']==0 && isset($daysReserve[$v])) { //如果为设置库存，且产品有日库存则使用产品日库存
                $tmp['reserve'] = $daysReserve[$v];
            }
            $values[] = array_merge($data, $tmp);
        }
        if (empty($values)) return false;
        array_unshift($values, array_keys(reset($values)));
        $r = $this->replace($values);
        return $r;
    }
}