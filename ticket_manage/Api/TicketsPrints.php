<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/29/14
 * Time: 3:09 PM
 * File: TicketsPrints.php
 */

class TicketsPrints extends ApiModel
{
    protected $param_key = 'ticket-url'; #请求api地址，对应config main里面的 key
    protected $c = 'api/prints' ;
} 
