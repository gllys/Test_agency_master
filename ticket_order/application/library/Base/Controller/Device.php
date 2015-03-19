<?php
/**
 * 控制器基类
 * @author  mosen
 */
class Base_Controller_Device extends Base_Controller_Abstract
{
    protected $yafAutoRender = false;
    /**
     * 总页码
     * @var int
     */
    public $total =1;
    /**
     * 当前页
     * @var int
     */
    public $current = 1;
    /**
     * 每页记录条数
     * @var int
     */
    public $items = 15;
    /**
     * 总条目
     * @var int
     */
    public $count = 0;

    public function init()
    {
        Lang_Msg::setErrMode(1);
        $this->config = Yaf_Registry::get("config");
        $appSecret = $this->config['api']['appSecret'];
        // 参数
        $this->body = $this->getParams();
        $sign = trim($this->body['sign']);
        if (!$sign) {
            Lang_Msg::error("ERROR_SIGN_1");
        }
        // 验证
        $tmpSign = $this->getSign($this->body, $appSecret);
        if ($sign!='debug' && $sign != $tmpSign) {
            Lang_Msg::error("ERROR_SIGN_2");
        }
    }

    public function getSign($params, $appSecret)
    {
        unset($params['sign']);
        ksort($params);
        return md5(http_build_query($params) . $appSecret);
    }

    /**
     * 分页
     * author : yinjian
     */
    public function pagenation()
    {
        $this->items = intval($this->body['items'])<=0?$this->items:intval($this->body['items']);
        $this->total = ceil($this->count/$this->items);
        $this->current = intval($this->body['current'])<=0?$this->current:intval($this->body['current']);
        $this->limit = ($this->items*($this->current-1)).",".$this->items;
    }

}
