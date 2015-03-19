<?php

class Bill extends ApiModel
{
    protected $param_key = 'ticket-api-order'; #请求api地址，对应config main里面的 key

    static public $weekArray = array(
        '1' => '周一',
        '2' => '周二',
        '3' => '周三',
        '4' => '周四',
        '5' => '周五',
        '6' => '周六',
        '0' => '周日',
    );

    /**
     * 获取星期
     *
     * @return mixed
     * @author cuiyulei
     **/
    static public function getWeekDay($day = '')
    {
        if(!empty($day)){
            return self::$weekArray[$day];
        }else{
            return self::$weekArray;
        }
    }

}