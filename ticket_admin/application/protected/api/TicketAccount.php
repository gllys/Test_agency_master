<?php
/**
 * Created by PhpStorm.
 * User: ccq
 * Date: 03/11/15
 * Time: 4:10 PM
 */

class TicketAccount extends ApiModel
{
    protected $param_key = 'ticket-url'; #请求api地址，对应config main里面的 key
    protected $c = 'api/account' ;
}
