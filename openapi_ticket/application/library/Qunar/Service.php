<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-10
 * Time: 下午3:48
 */

//define('SMARTY_SPL_AUTOLOAD',1);
//require_once dirname(__FILE__)."/../Smarty/Smarty.class.php";

class Qunar_Service{

    /*头部公共属性*/
    protected $application = '去哪儿门票.Menpiao.Agent';
    protected $processor = 'SupplierDataExchangeProcessor';
    protected $version = 'V1.0.0';
    protected $create_user = 'MEIJINGTEST2';
    protected $supplier_identity = 'MEIJINGTEST2';
    protected $signedKey='MEIJINGTESTSIGN';
    
//    protected $create_user = 'DEBUGSUPPLIER';
//    protected $supplier_identity = 'DEBUGSUPPLIER';
//    protected $signedKey='DEBUGSINGKEY';
    
    protected $type = 'default';
    protected $time;
    /*头部不同属性*/

    public $response_code = "1000";
    public $response_desc = "请求操作成功";
    /*验证所需属性*/
    protected $debug = false;

    protected $securityType = 'MD5';
    protected $signed;
    protected $is_valid = true;
    protected $base64;

    protected $service_type = 0; //0:request  1:response
    protected $smarty;
    protected $xml;

    /*response所需属性*/
    protected $response_xml;
    public $response_header;
    public $response_body;

    /*request所需属性*/
    protected $request_xml;
    public $request_header;
    public $request_body;
    public $qunar_url = 'http://agentat.piao.qunar.com/singleApiDebugData';

    /**
     * @param array $requestData 如果此参数不为空，则是被请求模式，否则是请求模式
     */
    public function __construct($requestData = array()){

        if($requestData){
            $params = json_decode(str_replace('\\"', '"', $requestData), true);
            $request_object = $this->decodeBase64($params['data']);
            $this->request_body = $request_object->body;
            $this->request_header = $request_object->header;

            $this->securityType = $params['securityType'];
            $this->signed = $params['signed'];

            self::validRequestXml();
            self::checkSigned();
        }

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(dirname(__FILE__) . "/Templates");
        $this->smarty->caching = false;

    }

    protected function generateResponseHeader(){
        $this->time = date("Y-m-d H:i:s");
        $this->smarty->assign("response_application", $this->application);
        $this->smarty->assign("response_processor", $this->processor);
        $this->smarty->assign("response_version", $this->version);
        $this->smarty->assign("response_create_user", $this->create_user);
        $this->smarty->assign("response_type", $this->type);
        $this->smarty->assign("response_time", $this->time);
        $this->smarty->assign("response_desc", $this->response_desc);
        $this->smarty->assign("response_code", $this->response_code);
    }
    protected function generateRequestHeader($method){
        $this->time = date("Y-m-d H:i:s");
        $this->type = ucfirst($method).'RequestBody';
        $this->smarty->assign("request_application", $this->application);
        $this->smarty->assign("request_processor", $this->processor);
        $this->smarty->assign("request_version", $this->version);
        $this->smarty->assign("request_create_user", $this->create_user);
        $this->smarty->assign("request_type", $this->type);
        $this->smarty->assign("request_time", $this->time);
        $this->smarty->assign("request_supplier_identity", $this->supplier_identity);

    }

    /**
     * 根据指定模版 构造response数据
     * @param $template     模版
     * @param array $data   数据
     * @return array
     * @throws Exception
     */
    public function generateResponse($template,$data=array()) {
         $this->generateResponseHeader();

         //$this->generateResponse($data);
         if(!$template) {
             throw new Exception("You should set the template");
             return ;
         }

         if($data) {
             foreach($data as $k=>$v) {
                 $this->smarty->assign($k,$v);
             }
         }

         $this->response_xml = $this->smarty->fetch($template);
         $response_base64 = base64_encode($this->response_xml);
         $arr = array(
            'data' => $response_base64,
            'signed' => $this->getSignedValue($response_base64),
             'securityType'=>$this->securityType
         );
        return $arr;
     }

    /**
     * 访问去哪儿API方法，返回结果对象
     * @param string $template      模版
     * @param string $method        要调用的方法
     * @param array $requestData    访问数据
     * @return object
     */
    public function request($template, $method, $requestData){

        //将访问参数封装打包
        $post_req = $this->generateRequest($template, $method, $requestData);
        //curl访问
        $resp = $this->curl($post_req);

        //解析返回的数据
        $params = json_decode(str_replace('\\"', '"', $resp), true);
        $response_object = $this->decodeBase64($params['data']);
        $this->response_body = $response_object->body;
        $this->response_header = $response_object->header;

        $this->securityType = $params['securityType'];
        $this->signed = $params['signed'];

        self::validResponseXml();
        self::checkSigned();

        return $this->response_body;
    }

    protected function generateRequest($template, $method, $requestData) {
        $this->generateRequestHeader($method);

        if(!$template) {
            throw new Exception("You should set the template");
            return ;
        }

        if($requestData) {
            foreach($requestData as $k=>$v) {
                $this->smarty->assign($k,$v);
            }
        }

        $this->xml = $this->smarty->fetch($template);
        $request_base64 = base64_encode($this->xml);
        $arr = array(
            'data' => $request_base64,
            'signed' => $this->getSignedValue($request_base64),
            'securityType'=>$this->securityType
        );

        $request_arr = array(
            'method' => $method,
            'requestParam' => json_encode($arr),
        );
        return $request_arr;
    }

    protected function curl($postFields = null){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->qunar_url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //https 请求
        if(strlen($this->qunar_url) > 5 && strtolower(substr($this->qunar_url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($postFields) && 0 < count($postFields))
        {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v)
            {
                if("@" != substr($v, 0, 1))//判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                }
                else//文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart)
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
            else
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
            }
        }
        $reponse = curl_exec($ch);

        if (curl_errno($ch))
        {
            throw new Exception(curl_error($ch),0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode)
            {
                throw new Exception($reponse,$httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    private function getSignedValue($str){
        $s = strtoupper(MD5($this->signedKey . $str));
        return $s;
    }

    private function decodeBase64($data){

        $this->base64 = $data;
        $this->xml = base64_decode($data);
        $xml = simplexml_load_string($this->xml);
        $json = json_encode($xml);
        $object = json_decode($json);

        return $object;
    }

    protected function validRequestXml(){
        $xml = new DOMDocument();
        $xml->loadXML($this->xml);
        $this->is_valid = $xml->schemaValidate(dirname(__FILE__) . '/Resource/QMRequestDataSchema.xsd');
        if($this->is_valid == false){
            $this->response_code = "1014";
            $this->response_desc = "报文解析异常，请检查报文结构";
        }
    }
    protected function validResponseXml(){
        $xml = new DOMDocument();
        $xml->loadXML($this->xml);
        $this->is_valid = $xml->schemaValidate(dirname(__FILE__) . '/Resource/QMResponseDataSchema.xsd');
    }
    protected function checkSigned(){
        if($this->is_valid == true){
            $signed = strtoupper(md5($this->signedKey.$this->request_base64));
            $this->request_is_valid = $signed == $this->signed;
            if($this->is_valid == false){
                $this->response_code = "1015";
                $this->response_desc = "签证验证不通过";
            }
        }
    }

}
