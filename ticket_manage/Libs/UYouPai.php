<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/29/14
 * Time: 8:15 PM
 * File: UYouPai.php
 */

class UYouPai {

    public $user = '';
    public $password = '';
    public $formApiSecret = 'tIBpPPoqLDZ07XvWjMYXzyI8Ulw=';
    public $bucket = 'piaowu' ;
    public $uploadDir = 'gongying' ;
    public $returnUrl = '/upyun.php' ;
    public $host = 'http://piaowu.b0.upaiyun.com' ;
    public $outtime = 86400 ;

    //得又拍加密
    public function getCode() {
        /// (回调中的所有信息均为 UTF-8 编码，签名验证的时候需要注意编码是否一致)
        $options = array();
        $options['bucket'] = $this->bucket; /// 空间名
        $options['expiration'] = time() + intval($this->outtime); /// 授权过期时间
        $options['save-key'] = '/'.$this->uploadDir.'/{year}/{mon}/{day}/{filemd5}{.suffix}'; /// 文件名生成格式，请参阅 API 文档
        $options['allow-file-type'] = 'jpg,jpeg,gif,png'; /// 控制文件上传的类型，可选
        $options['content-length-range'] = '0,5120000'; /// 限制文件大小，可选
        $options['x-gmkerl-type'] = 'fix_width';
        $options['x-gmkerl-value'] = '560';

        $options['return-url'] = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$this->returnUrl; // 页面跳转型回调地址 !!! iframe 回调地址，注意客户网站上要部署 agent.html 进行跨域代理
        //$options['notify-url'] = 'http://www.shehuan.com/tpogao/callback/'; /// 服务端异步回调地址, 请注意该地址必须公网可以正常访问
        $policy = base64_encode(json_encode($options));
        $sign = md5($policy . '&' . $this->formApiSecret); /// 表单 API 功能的密匙（请访问又拍云管理后台的空间管理页面获取）
        return json_encode(array('policy' => $policy, 'signature' => $sign));
    }

}
