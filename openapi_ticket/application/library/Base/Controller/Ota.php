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
    protected $now = 0;
    protected $timeout = 300;
    public $userinfo = array();

    /**
     * @param bool $need_sign 是否需要采用内部验证方式
     */
    public function init($need_sign = true)
    {
        $this->now = time();
        $this->config = Yaf_Registry::get("config");
        // 参数
        $this->body = $this->getParams();
        if($need_sign){
            $sign = trim($this->body['sign']);
            if (!$sign) Lang_Msg::error("ERROR_SIGN_1");
            // 验证
            //$debugMode = $sign === 'debug' && getenv('APP_ENV') !== 'product';
            $debugMode = $sign === 'debug';
            if (!$debugMode && (!$this->body['timestamp'] || $this->now > $this->timeout + $this->body['timestamp'])) Lang_Msg::error("请求超时");
            $this->userAuth($sign);
            $tmpSign = $this->getSign($this->body, $this->userinfo['secret']);
            if (!$debugMode && $sign != $tmpSign) {
                Log_Base::save('ota', 'sign error: '.var_export(array(
                    'body'=>$this->body,
                    'secret' => $this->userinfo['secret'],
                    'tmpSign' => $tmpSign,
                    ),true));
                Lang_Msg::error("ERROR_SIGN_2");
            }
        }
    }

    public function getSign($params, $secret)
    {
        unset($params['sign']);
        ksort($params);
        $str = '';
        foreach($params as $key=>$value) {
            $str .= '&'.$key.'='.$value;
        }
        $str = substr($str, 1);
        return md5($str .'|'. $secret);
    }

    public function userAuth($sign) {
        $account = $this->body['client_id'];
        $pwd = isset($this->body['client_secret']) ? trim(Tools::safeOutput($this->body['client_secret'])) : trim(Tools::safeOutput($this->body['password']));
        $token = $this->body['token'];
        if ($token) {
            session_id($token);
            $this->sess->start();
            if (!$this->sess->userinfo) {
                Lang_Msg::error('token已过期');
            }
            $this->userinfo = $this->sess->userinfo;
        }
        if (empty($this->userinfo)) {
            if(!$account || !$pwd) Lang_Msg::error("ERROR_SIGN_3");
            $this->userinfo = OtaAccountModel::model()->verify($account,$pwd);
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

    /**
     * 输出日志
     * @param string $title     数据标题
     * @param string $content   数据内容
     * @param string $filename  文件名称
     * @param string $path      文件目录，默认根目录下的log
     */
    public function echoLog($title, $content, $filename = 'default.log', $path = '../log/'){
        //根据配置文件log.enabled决定是否输出日志
        if($this->config['log']['enabled']){
            $dateTime = date("Y-m-d H:i:s",time());
            $date = date("Y-m-d",time());
            $fullname = $path . $filename . $date;
            error_log("|$title $dateTime|", 3, $fullname);
            error_log($content, 3, $fullname);
            error_log("|$title end|\n", 3, $fullname);
        }
    }
}
