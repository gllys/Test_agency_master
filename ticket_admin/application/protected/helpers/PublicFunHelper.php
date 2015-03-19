<?php

/**
 * public function helper
 * @author wangKun
 */
class PublicFunHelper {

    public static function getIP() {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
            $ip = explode(',', $ip);
            $ip = $ip[0];
            $ip = trim($ip);
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER ['REMOTE_ADDR']) && $_SERVER ['REMOTE_ADDR'] && strcasecmp($_SERVER ['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER ['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return ($ip);
    }

    public static function curlQuery($url, $postTag = false, $postData = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($postTag) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function changeSecondToView($second) {
        $floorHour = floor($second / 3600);
        $remain = $second % 3600;
        $hour = $floorHour == 0 ? '00' : $floorHour;
        $floorMin = floor($remain / 60);
        $remain = $remain % 60;
        $min = strlen($floorMin) == 1 ? '0' . $floorMin : $floorMin;
        $second = strlen($remain) == 1 ? '0' . $remain : $remain;
        return $hour . ':' . $min . ':' . $second;
    }

    //生成十八位的随机数字
    //PublicFunHelper::getActivationString();
    public static function getActivationString($length = 18) {
        PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
        $rand_str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $len = strlen($rand_str) - 1;
        $key = "";

        for ($i = 0; $i < $length; $i++) {
            $key .= $rand_str[mt_rand(0, $len)];
        }
        return $key;
    }

    //得到分表求余
    //PublicFunHelper::getCrcDividend($val);
    public static function getCrcDividend($val, $dividend = 100) {
        $crc = sprintf("%u", crc32($val));
        return fmod($crc, $dividend);
    }

    /**
     * 按照游族平台API规则发送POST请求
     * @param string $url
     * @param string $key
     * @param array $postData
     * @return boolean/array
     */
    public static function curlApiPost($url, $postData = array(), $key = '12345') {
        ksort($postData);
        $verify = md5(http_build_query($postData) . $key);
        $postData = array_merge($postData, array('time' => time(), 'verify' => $verify));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        $result = curl_exec($curl);
        $curl_status = curl_getinfo($curl);
        curl_close($curl);
        if ($curl_status['http_code'] != 200) {
            $result = FALSE;
        }
        return $result;
    }

    /**
     * 获取该系统使用的所有用户
     * @return array
     */
    public function allMembers() {
        $sql = "SELECT * FROM `kf_member`";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $members = array();

        foreach ($result as $item) {
            $members[$item['account']] = $item;
        }
        return $members;
    }

    //PublicFunHelper::arrayKey($model,$key)
    //model得到模型中的一个键
    public static function arrayKey($model, $key) {
        if (!$model) {
            return array();
        }

        $_model = array();
        foreach ($model as $val) {
            $_model[] = $val[$key];
        }
        return $_model;
    }

    //以model以model中的一个键为唯一键返回数组
    //PublicFunHelper::ArrayByUniqueKey
    public static function ArrayByUniqueKey($model, $key) {
        $_model = array();
        $type = is_array($model);
        foreach ($model as $val) {
            if ($type)
                $_model[$val[$key]] = $val;
            else
                $_model[$val[$key]] = $val->attributes;
        }
        return $_model;
    }

    //以model以model中的一个键为唯一键返回多维数组
    //PublicFunHelper:: ArrayByKeys
    public static function ArrayByKeys($model, $key) {
        $_model = array();
        $type = is_array($model);
        foreach ($model as $val) {
            if ($type)
                $_model[$val[$key]][] = $val;
            else
                $_model[$val[$key]][] = $val->attributes;
        }
        return $_model;
    }

    //以model以model中的一个键为唯一键返回数组中的一列
    //PublicFunHelper::ArrayKeyByUniqueKey
    public static function ArrayKeyByUniqueKey($model, $key, $key1) {
        $_model = array();
        $type = is_array($model);
        foreach ($model as $val) {
            if ($type)
                $_model[$val[$key]] = $val[$key1];
            else
                $_model[$val[$key]] = $val->$key1;
        }
        return $_model;
    }

    //以model以model中的一个键为唯一键返回多维数组中的一列
    //PublicFunHelper::ArrayKeyByKeys($model,$key,$key1)
    public static function ArrayKeyByKeys($model, $key, $key1) {
        $_model = array();
        $type = is_array($model);
        foreach ($model as $val) {
            if ($type)
                $_model[$val[$key]][] = $val[$key1];
            else
                $_model[$val[$key]][] = $val->$key1;
        }
        return $_model;
    }

    //PublicFunHelper::print_view($this);
    public static function print_view($obj) {
        //只支持最多一层模块打印
        $modulesController = Yii::getPathOfAlias('application.modules.controllers');
        $modulesView = Yii::getPathOfAlias('application.modules.views');
        $controllerFile = $modulesController . '/' . $obj->id . 'Controller.php';
        $viewFile = $modulesView . '/' . $obj->id . '/' . $obj->action->id . '.php';

        if (file_exists($modulesController))
            return realpath($viewFile);

        $modulesView = Yii::getPathOfAlias('application.views');
        $viewFile = $modulesView . '/' . $obj->id . '/' . $obj->action->id . '.php';
        return realpath($viewFile);
    }

    //PublicFunHelper::print_controller($this);
    public static function print_controller($obj) {
        //只支持最多一层模块打印
        $modulesController = Yii::getPathOfAlias('application.modules.controllers');
        $controllerFile = $modulesController . '/' . $obj->id . 'Controller.php';
        if (file_exists($modulesController))
            return realpath($modulesController);
        $controller = Yii::getPathOfAlias('application.controllers');
        return realpath($controller . '/' . $obj->id . 'Controller.php');
        ;
    }

    //打印变量
    //PublicFunHelper::print_extract($arr);
    public function print_extract($arr, $val) {
        foreach ($arr as $k => $v) {
            echo "\${$k} = \${$val}['{$k}'] ;</br>";
        }
    }

    //搜索词加生成高亮
    //PublicFunHelper::highlight($words,$str)
    public function highlight($words, $str, $color = '#cc0000') {
        foreach ($words as $val)
            $str .= str_replace($val, '<font color="' . $color . '">' . $val . '</font>', $str);
        return $str;
    }

    //个性化时间
    //PublicFunHelper::time_ago($cur_time)
    public static function time_ago($cur_time) {
        $diff = time() - $cur_time;
        if ($diff < 60)
            return $diff . '秒前';
        else if ($diff > 60 && $diff < 3600)
            return intval($diff / 60) . '分钟前';
        else if ($diff > 3600 && $diff < 3600 * 24)
            return intval($diff / 3600) . '小时前';
        else if ($diff > 3600 * 24 && $diff < 3600 * 24 * 30)
            return intval($diff / (3600 * 24)) . '天前';
        else
            return date('Y-m-d H:i:s', $cur_time);
    }

    //文件大小转换
    static public function sizecount($filesize) {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize . ' Bytes';
        }
        return $filesize;
    }

}
