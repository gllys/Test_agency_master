<?php
/**
 * 控制器基类
 * @author  mosen
 */
class Base_Controller_Api extends Base_Controller_Abstract
{
    protected $yafAutoRender = false;
    
    public function init()
    {
        $this->config = Yaf_Registry::get("config");
        $appSecret = $this->config['api']['appSecret'];
        $this->now = time();
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
}
