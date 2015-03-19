<?php
/**
 * Ota控制器基类
 * @author  mosen
 */
class Base_Controller_Ota extends Base_Controller_Abstract
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
    public $userinfo = array();

    public function init()
    {
        $this->config = Yaf_Registry::get("config");
        // 参数
        $this->body = $this->getParams();
        $sign = trim($this->body['sign']);
        if (!$sign) Lang_Msg::error("ERROR_SIGN_1");
        // 验证
        $this->userAuth();
        $tmpSign = $this->getSign($this->body, $this->userinfo['secret']);
        if ($sign!='debug' && $sign != $tmpSign) {
            Log_Base::save('ota', 'sign error: '.var_export(array(
                'body'=>$this->body,
                'secret' => $this->userinfo['secret'],
                'tmpSign' => $tmpSign,
                ),true));
            Lang_Msg::error("ERROR_SIGN_2");
        }
    }

    public function getSign($params, $secret)
    {
        unset($params['sign']);
        ksort($params);
        return md5($this->http_build_query($params) . $secret);
    }

    public function http_build_query($params) {
        $str = '';
        foreach($params as $key=>$value) {
            $str .= '&'.$key.'='.$value;
        }
        return substr($str, 1);
    }

    public function userAuth() {
        $account = intval($this->body['account']);
        $token = $this->body['token'];
        if ($token) {
            session_id($token);
            $this->sess->start();
            if (!$this->sess->userinfo) Lang_Msg::error("ERROR_SIGN_2");
            $this->userinfo = $this->sess->userinfo;
        }
        if (empty($this->userinfo)) {
            if($account <= 0) Lang_Msg::error("ERROR_SIGN_3");
            $this->userinfo = OtaAccountModel::model()->getById($account);
            if(empty($this->userinfo)) Lang_Msg::error("ERROR_SIGN_3");
        }
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
