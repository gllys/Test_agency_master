<?php
/**
 * Created by yuanwei
 * User: yuanwei
 * Date: 13-12-23
 * Time: 下午3:34
 */
class BankcardAccountModel extends BaseModel{
    // 定义要操作的表名
    public $db         = 'fx';
    public $table      = 'bankcard_account';
    public $pk         = 'id';
}