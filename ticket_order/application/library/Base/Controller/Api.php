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

    public function init()
    {
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
     *获取操作者
     * @author ：zhaqinfeng
     */
    public function getOperator(){
        $user_id = intval($this->body['user_id']); //操作者uid
        $user_name = trim(Tools::safeOutput($this->body['user_name'])); //操作者用户名
        $user_account = trim(Tools::safeOutput($this->body['user_account'])); //操作者用户名
        if(!$user_name) {
            $user_name = $this->body['user_name'] = $user_account;
        }
        //'debug'!=$this->body['sign'] && !$user_id &&  Lang_Msg::error("ERROR_OPERATOR_1"); //缺少操作者UID参数
        //'debug'!=$this->body['sign'] && !$user_name &&  Lang_Msg::error("ERROR_OPERATOR_2"); //缺少操作者用户名参数
        return array('user_id'=>$user_id,'user_account'=>$user_account,'user_name'=>$user_name);
    }

    /**
     *获取排序参数
     * @author ：zhaqinfeng
     */
    public function getSortRule(){
        $sort_by = trim(Tools::safeOutput($this->getParam('sort_by')));
        $sort_by = explode(':',$sort_by);
        $field = $sort_by[0] ? $sort_by[0] : 'created_at';
        $dir = $sort_by[1]=='asc'?'asc':'desc';
        return $field." ".$dir; //初始值 也可array('updated_at'=>'desc')
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
