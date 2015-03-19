<?php

/**
 *票务订单子景点关联模型
 *
 * 2013-11-12 
 *
 * @author  fangshixiang
 * @version 1.0
 */
class TicketUsedModel extends Model {

    // 定义要操作的表名
    public $db = 'fx';
    public $table = 'ticket_used';
    public $pk = 'id';
    public $limit = 10;
    public $order = 'id asc';
    public $target = 'navTab'; // dialog|navTab

}
