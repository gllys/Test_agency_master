<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/30/14
 * Time: 6:04 PM
 */
class Credit extends REST_API {

    protected $param_key = 'ticket-api-organization'; #请求api地址，对应config main里面的 key
    static public $weekArrayForPartner = array(
        '1' => '周一',
        '2' => '周二',
        '3' => '周三',
        '4' => '周四',
        '5' => '周五',
        '6' => '周六',
        '0' => '周日',
    );

    //获取星期
    static public function getWeekDay($day = '') {
        if ($day != '') {
            return self::$weekArrayForPartner[$day];
        } else {
            return self::$weekArrayForPartner;
        }
    }

}
