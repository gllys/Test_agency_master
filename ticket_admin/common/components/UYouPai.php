<?php

class UYouPai extends CApplicationComponent {

    public $user = '';
    public $password = '';
    public $formApiSecret = '0yVJzh9T6e4L+EcBydx3Ep+fpsk=';
    public $bucket = 'shehuan-image' ;
    public $uploadDir = 'admin_permission' ;
    public $returnUrl = '/site/upyunAgent/' ;
    public $host = 'http://image.shehuan.net' ;
    public $outtime = 86400 ;
    public $x_gmkerl_type = 'fix_width' ;
    public $x_gmkerl_value = '560';
    
    //�����ļ���
    public function getCode() {
        /// (�ص��е�������Ϣ��Ϊ UTF-8 ���룬ǩ����֤��ʱ����Ҫע������Ƿ�һ��)
        $options = array();
        $options['bucket'] = $this->bucket; /// �ռ���
        $options['expiration'] = time() + intval($this->outtime); /// ��Ȩ����ʱ��
        $options['save-key'] = '/'.$this->uploadDir.'/{year}/{mon}/{day}/{filemd5}{.suffix}'; /// �ļ������ɸ�ʽ������� API �ĵ�
        $options['allow-file-type'] = 'jpg,jpeg,gif,png'; /// �����ļ��ϴ������ͣ���ѡ
        $options['content-length-range'] = '0,5120000'; /// �����ļ���С����ѡ
        $options['x-gmkerl-type'] = $this->x_gmkerl_type;
        $options['x-gmkerl-value'] = $this->x_gmkerl_value;

        $options['return-url'] = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"].$this->returnUrl; // ҳ����ת�ͻص���ַ !!! iframe �ص���ַ��ע��ͻ���վ��Ҫ���� agent.html ���п������
        //$options['notify-url'] = 'http://www.shehuan.com/tpogao/callback/'; /// ������첽�ص���ַ, ��ע��õ�ַ���빫��������������
        $policy = base64_encode(json_encode($options));
        $sign = md5($policy . '&' . $this->formApiSecret); /// �� API ���ܵ��ܳף�����������ƹ����̨�Ŀռ����ҳ���ȡ��
        return json_encode(array('policy' => $policy, 'signature' => $sign));
    }

}
