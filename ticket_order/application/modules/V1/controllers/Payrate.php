<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-3-6
 * Time: 下午3:32
 * 费率
 */


class PayrateController extends  Base_Controller_Api
{
    /**
     * 列表
     * author : zqf
     */
    public function listsAction()
    {
        $data = PayRateModel::model()->setListKey('')->search(array());
        Tools::lsJson(true,'ok',$data);
    }


    /**
     * 设置费率
     * author : zqf
     */
    public function setAction()
    {
        $payment = trim(Tools::safeOutput($this->body['payment']));
        $name = trim(Tools::safeOutput($this->body['name']));
        $rate = doubleval($this->body['rate']);
        !$payment && Lang_Msg::error('请制定支付方式');
        !$name && Lang_Msg::error('请制定第三方支付合作者名称');
        $rate <=0 && Lang_Msg::error('请设置费率');

        $params = $this->getOperator();
        $data = array(
            'payment'=>$payment,
            'name'=>$name,
            'rate'=>$rate,
            'setted_at'=>time(),
        );
        $data = $data+$params;
        $res = PayRateModel::model()->replace($data);
        !$res && Lang_Msg::error('添加失败');
        Lang_Msg::output();
    }
}