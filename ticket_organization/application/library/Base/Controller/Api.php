<?php
/**
 * 控制器基类
 * @author  mosen
 */
class Base_Controller_Api extends Base_Controller_Abstract
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

    /**
     * post和get的参数
     */
    public $body  = array();
    
    public function init()
    {
        $this->config = Yaf_Registry::get("config");
        $appSecret = $this->config['api']['appSecret'];
        $this->now = time();
        // 参数
        $this->body = $this->getParams();
        $sign = trim($this->body['sign']);
        if (!$sign) {
            Lang_Msg::error("签名缺失");
        }
        // 验证
        $tmpSign = $this->getSign($this->body, $appSecret);
        if ($sign!='debug' && $sign != $tmpSign) {
            Lang_Msg::error("签名不正确");
        }
    }

    public function getSign($params, $appSecret)
    {
        unset($params['sign']);
        ksort($params);
        return md5(http_build_query($params) . $appSecret);
    }

    /**
     *获取排序参数
     * @author ：zhaqinfeng
     */
    public function getSortRule($field = 'id'){
        $sort_by = trim(Tools::safeOutput($this->body['sort_by']));
        $sort_by = explode(':',$sort_by);
        $field = $sort_by[0] ? $sort_by[0] : $field;
        $dir = $sort_by[1]=='asc'?'asc':'desc';
        return $field." ".$dir; //初始值 也可array('updated_at'=>'desc')
    }

    /**
     * 获取字段参数
     * @author zhaqinfeng
     * @date 2014-12-11
    */
    public function getFields($primaryKey = 'id'){
        $fields = trim(Tools::safeOutput($this->body['fields']));
        $fields = $fields ? $fields :"*"; //要获取的字段
        if($fields!="*"){
            $fieldArr = explode(',',$fields);
            !in_array($primaryKey,$fieldArr) && array_unshift($fieldArr,$primaryKey);
            $fields = implode(',',$fieldArr);
        }
        return $fields;
    }

    /**
     * 分页
     * author : yinjian
     */
    public function pagenation()
    {
        $this->items = intval($this->body['items'])<=0?$this->items:$this->body['items'];
        $this->total = ceil($this->count/$this->items);
        $this->current = intval($this->body['current'])<=0?$this->current:$this->body['current'];
        $this->limit = ($this->items*($this->current-1)).",".$this->items;
    }
}
