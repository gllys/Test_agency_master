<?php

class ApiHelper {

        protected static $secret_key = 'fsdfasfsadfsewrwrwtwerwedgdsfhgfdhfg';

        /**
         * curl POST请求
         * @param string $url
         * @param array $array
         * @return string 
         */
        static public function curlPost($url, $array, $timeout = 5) {
                $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1";
                $fields_string = '';
                foreach ($array as $k => $v) {
                        $fields_string .= $k . '=' . $v . '&';
                }
                rtrim($fields_string, '&');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                //curl_setopt($ch, CURLOPT_COOKIE, $cookie);
                curl_setopt($ch, CURLOPT_POST, count($array));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_REFERER, "http://www.uuzu.asia/");
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                $result = curl_exec($ch);
                //$result = json_decode($result,true);
                return $result;
        }

        /**
         * curl GET请求
         * @param string $url 请求url
         * @param int $timeout 页面超时时间
         * @return string 
         */
        static public function curlGet($url, $timeout = 5) {
                $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                //curl_setopt($ch, CURLOPT_COOKIE, $cookie); // 读取上面所储存的Cookie信息    
                curl_setopt($ch, CURLOPT_REFERER, "http://www.uuzu.asia/");
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                $result = curl_exec($ch);
                //$result = json_decode($result,true);
                return $result;
        }

        /*
          ApiHelper::sendRTX(array('receiver'=>'无双,橙子',
          'title'=>'紧急故障处理通知',
          'msg'=>'您有一条新的A类事件待处理，请前往[http://kf.uuzuonline.com/event/kf/myEvent|http://kf.uuzuonline.com/event/kf/myEvent]',
          'delaytime'=>3000
          ));
          }
         */

        static public function sendRTX(Array $arr) {
                if (!isset($arr['receiver']) || !$arr['receiver'])
                        exit;
                //消息内容
                $msg = array('receiver' => '', 'msg' => '', 'title' => '', 'delaytime' => '', 'okurl' => '', 'errurl' => '');
                foreach ($arr as $k => $v)
                        if (isset($msg[$k]))
                                $msg[$k] = $v;
                $secret = join('_', $msg) . '_' . date('Y-m-d') . '_' . self::$secret_key;
                $secret = md5(base64_encode($secret));
                $msg['secret'] = $secret;
                //echo $this->curlPost('http://api.office.uuzuonline.com/index.php?r=api/sendRTX',$msg);
                //return self::curlPost('http://api.office.uuzuonline.com:8810/api/sendRTX',$msg);//线上的
                return self::curlPost('http://api.office.uuzuonline.com/api/sendRTX', $msg); //本地的
        }

        static public function getIpInfo($ip, $url = "http://ip.taobao.com/service/getIpInfo.php?ip=", $timeout = 5) {
                $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . $ip);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                $result = curl_exec($ch);
                $result = json_decode($result);
                $curl_status = curl_getinfo($ch);
                curl_close($ch);
                if ($curl_status['http_code'] != 200 || $result->code == 1) {
                       return  'invaild ip';
                } else {
                        return $result->data->country.' '.$result->data->region.' '.$result->data->city.' '.$result->data->isp;
                }
        }

}

