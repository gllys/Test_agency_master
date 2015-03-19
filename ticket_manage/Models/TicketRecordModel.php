<?php

/**
 *票使用记录
 *
 * 2013-11-12 
 *
 * @author  fangshixiang
 * @version 1.0
 */
class  TicketRecordModel extends BaseModel {

    // 定义要操作的表名
    public $db = 'fx';
    public $table = 'ticket_record';
    public $pk = 'id';
    public $limit = 10;
    public $order = 'id asc';
    public $target = 'navTab'; // dialog|navTab

}
