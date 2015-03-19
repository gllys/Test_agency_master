<?php
/**
 * 公共方法
 * @author  mosen
 */
class Util_Common
{
	protected static $code = '0123456789bcdefghijklmnopqrstuvwxyzBCDEFGHIJKLMNOPQRSTUVWXYZ';
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
    protected static function to60($num) {
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
    protected static function from60($num, $base = 60) {
        $rt = 0;
        $code = array_flip(str_split(self::$code));
        $arr = array_reverse(str_split($num));
        foreach ($arr as $k => $v) $rt+= $code[$v] * pow($base, $k);
        return $rt;
    }
    
    /**
     * [toBase description]
     * @param  [type]  $num  [description]
     * @param  integer $base [description]
     * @return [type]        [description]
     */
    protected static function toBase($num, $base = 2) {
        
        $rt = array();
        while ($num >= $base) {
            $rt[] = $num % $base;
            $num = intval($num / $base);
        }
        if ($num > 0) $rt[] = $num;
        
        return array_reverse($rt);
    }

    /**
     * [makeId description]
     * @param  [type] $channel [description]
     * @param  [type] $oid     [description]
     * @param  [type] $uid     [description]
     * @return [type]          [description]
     */
    public static function makeId($channel, $oid, $uid) {
        $remote = $_SERVER['FX_REMOTE'] == 1 ? 'A' : 'B';
        $channel = self::to60($channel);
        $oid = self::to60($oid);
        $uid = self::to60($uid);

        list($usec, $sec) = explode(' ', microtime());
        $sec = self::to60($sec);
        $usec = self::to60(intval($usec * 1000000));
        
        return "{$remote}{$channel}a{$oid}a{$uid}a{$sec}{$usec}";
    }
    
}
