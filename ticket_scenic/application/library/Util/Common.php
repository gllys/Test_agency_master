<?php
/**
 * 公共方法
 * @author  mosen
 */
class Util_Common
{
	protected static $code = '012356789';
	/**
	 * [escape description]
	 * @param  [type] $data    [description]
	 * @param  string $charset [description]
	 * @return [type]          [description]
	 */
	public static function escape(&$data, $charset = 'UTF-8') { 
		if (preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/Usi", $data, $matches)) {
			$s = $r = array();
			foreach ($matches[0] as $v) { 
				$s[] = $v;
				if (ord($v[0]) < 128) {
					$r[] = rawurlencode($v); 
				} else { 
					$r[] = "%u".bin2hex(iconv($charset, "UCS-2", $v)); 
				} 
			}
			if ($s) {
				$data = str_replace($s, $r, $data);
			}
		}
	} 
	
	/**
	 * [unescape description]
	 * @param  [type] $data    [description]
	 * @param  string $charset [description]
	 * @return [type]          [description]
	 */
	public static function unescape(&$data, $charset = 'UTF-8') { 
		$data = rawurldecode($data); 
		if (preg_match_all("/(?:%u.{4})/Usi", $data, $matches)) {
			$s = $r = array();
			foreach ($matches[0] as $k => $v) { 
				if(substr($v,0,2) == "%u" && strlen($v) == 6) {
					$s[] = $v;
					$r[] = iconv("UCS-2", $charset, pack("H4", substr($v,-4))); 
				}
			}
			if ($s) {
				$data = str_replace($s, $r, $data);
			}
		}
	}
	
	/**
	 * [json_encode description]
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	public static function json_encode($arr) {
		return urldecode(json_encode(self::url_encode($arr)));
	}
	
	/**
	 * [url_encode description]
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	public static function url_encode($arr) {
		if (!is_array($arr)) {
			return urlencode($arr);
		}
		foreach ($arr as &$value) {
			if (is_array($value)) {
				$value = self::url_encode($value);
			} else {
				$value = urlencode($value);
			}
		}
		return $arr;
	}
	
	/**
	 * [zip_encode description]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public static function zip_encode($str) {
	    return base64_encode(gzdeflate($str));
	}
	
	/**
	 * [zip_decode description]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public static function zip_decode($str) {
	    return gzinflate(base64_decode($str));
	}

	/**
	 * [is_arr2 description]
	 * @param  [type]  $arr    [description]
	 * @param  integer $strict [description]
	 * @return boolean         [description]
	 */
	public static function is_arr2($arr, $strict = 1) {
		$flag = false;
		if (!is_array($arr) || empty($arr))
			return $flag;

		foreach ($arr as $value) {
			if (is_array($value)) {
				$flag = true;
			}
			break;
		}

		return $flag;
	}

	/**
	 * [to60 description]
	 * @param  [type] $num [description]
	 * @return [type]      [description]
	 */
    protected static function to9($num) {
        $rt = array();
        $arr = self::toBase($num, strlen(self::$code));
        foreach ($arr as $k => $v) $rt[] = self::$code[$v];
        return join($rt);
    }
    
    /**
     * [from60 description]
     * @param  [type]  $num  [description]
     * @param  integer $base [description]
     * @return [type]        [description]
     */
    public static function from9($num, $base = 9) {
        $rt = '0';
        $code = array_flip(str_split(self::$code));
        $arr = array_reverse(str_split($num));
        foreach ($arr as $k => $v) $rt = bcadd($rt, bcmul($code[$v], bcpow("$base", "$k")));
        return $rt+0;
    }
    
    /**
     * [toBase description]
     * @param  [type]  $num  [description]
     * @param  integer $base [description]
     * @return [type]        [description]
     */
    protected static function toBase($num, $base = 2) {
        $rt = array();
        while ($num+0 >= $base) {
            $rt[] = bcmod("$num", "$base");
            $num = bcdiv("$num", "$base");
        }
        if ($num+0 > 0) $rt[] = $num;
        return array_reverse($rt);
    }

    /**
     * 生成唯一ID 15位纯数字 用于订单号、票号
     * @param  integer $channel 1订单号 2票号
     * @return [type]           [description]
     */
    public static function uniqid($channel = 1) {
    	$lang = LanguageModel::model();
    	do {
    		$us = microtime();
    		$mt = substr($us, 11) . substr($us, 2, 4);
    		$key = 'uniqid|'.$channel.'|'.$mt;
    	} while(!$lang->memcache->add($key,1,5));
    	$nid = $channel.self::to9($mt);
    	// echo $mt . '<br/>';
    	// echo $nid . '<br/>';
    	// echo self::from9($nid);
        return $nid;
    }

    /**
     * Util_Common::uniqid2date($id);
     * @param  [type] $id     [description]
     * @param  string $format [description]
     * @return [type]         [description]
     */
    public static function uniqid2date($id, $format = 'Ym') {
    	$mt = self::from9(substr("$id", 1));
    	return date($format, substr("$mt", 0, 10));
    }

    /**
     * 生成流水号 18位纯数字含日期时间
     * @return [type] [description]
     */
    public static function payid() {
    	$lang = LanguageModel::model();
    	do {
    		$us = microtime();
    		$mt = date('YmdHis') . substr($us, 2, 4);
    		$key = 'payid|'.$mt;
    	} while(!$lang->memcache->add($key,1,5));
    	// echo $mt . '<br/>';
        return $mt;
    }

    public static function payid2date($id, $format = 'Ym') {
    	$mt = strtotime(substr("$id", 0, 8));
    	return date($format, $mt);
    }
}
