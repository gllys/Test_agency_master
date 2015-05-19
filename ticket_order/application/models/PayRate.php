<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-3-6
 * Time: 下午3:33
 * 费率model
 */

class PayRateModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'pay_rate';
    protected $pkKey = 'payment';
    protected $preCacheKey = 'cache|PayRateModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function getRate($payment='kuaiqian'){
        if(!in_array($payment,array_keys(PaymentModel::model()->pay_types))) return 0;
        $data = $this->get(array('payment'=>$payment));
        return $data ? $data['rate']: 0;
    }
}