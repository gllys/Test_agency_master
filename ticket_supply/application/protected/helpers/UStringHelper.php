<?php

class UStringHelper {

        function random_string($length) {
                PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
                $hash = '';
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
                $max = strlen($chars) - 1;
                for ($i = 0; $i < $length; $i++) {
                        $hash .= $chars[mt_rand(0, $max)];
                }
                return $hash;
        }

        public static function truncate_utf8_string($string, $length, $etc = '...') {
                $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
                $strlen = strlen($string);
                for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
                        if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                                if ($length < 1.0) {
                                        break;
                                }
                                $result .= substr($string, $i, $number);
                                $length -= 1.0;
                                $i += $number - 1;
                        } else {
                                $result .= substr($string, $i, 1);
                                $length -= 0.5;
                        }
                }
                $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
                if ($i < $strlen) {
                        $result .= $etc;
                }
                return $result;
        }

        /**
         * 解析js escape 编码字符串 UTF8
         * @param string $str
         * @return string
         */
        public static function unescape($str) {
                $ret = '';
                $len = strlen($str);
                for ($i = 0; $i < $len; $i++) {
                        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                                $val = hexdec(substr($str, $i + 2, 4));
                                if ($val < 0x7f)
                                        $ret .= chr($val);
                                else if ($val < 0x800)
                                        $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                                else
                                        $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                                $i += 5;
                        }
                        else if ($str[$i] == '%') {
                                $ret .= urldecode(substr($str, $i, 3));
                                $i += 2;
                        }
                        else
                                $ret .= $str[$i];
                }
                return $ret;
        }
        
         private static $app ;
      private function __construct(){
	          //Yii::app()->db->createCommand('SET NAMES utf8')->execute() ;
     }
	  
      public static function app(){
	         if(empty(self::$app)){
			    self::$app = new self() ;
			 }
			 return self::$app ;
       }
        /**
	* 加密
	*
	* @param str txt	 
	* @param str encrypt_key	 加密key
	* @return string
	**/
        private $key = "sdfasdsfasdfdf^&^%&dfasddsfasdfasdf";
	public function encrypt($txt) 
	{
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0,$len = strlen($txt);$i < $len; $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode($this->passportKey($tmp));
	}
	
	/**
	* 解密
	*
	* @param str txt	 
	* @param str encrypt_key	 加密key
	* @return string
	**/
	public function decrypt($txt) 
	{
		$txt = $this->passportKey(base64_decode($txt));
		$tmp = '';
		for ($i = 0,$len = strlen($txt);$i < $len; $i++) {
			$md5 = $txt[$i];
			$next = ++$i;
			$tmp .= isset($txt[$next]) ? $txt[$next] ^ $md5 : "";		
		}
		return $tmp;
	}
	
	/**
	* 对字符串做加密
	*
	* @param str txt	 
	* @param str encrypt_key	 加密key
	* @return string
	**/
	public function passportKey($txt) 
	{
		$encrypt_key = md5($this->key);
		$ctr = 0;
		$tmp = '';
		for($i = 0,$len = strlen($txt); $i < $len; $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}
}