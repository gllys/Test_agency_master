<?php
/**
 * Created by yuanwei
 * User: yuanwei
 * Date: 13-12-24
 * Time: 下午5:25
 */
class BankModel extends BaseModel{
    // 定义要操作的表名
    public $db         = 'fx';
    public $table      = 'banks';
    public $pk         = 'id';
}