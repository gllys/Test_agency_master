<?php
class Response{
    /**
     * 清理URL中的http头
     *
     * @param      $url
     * @param bool $cleanall
     *
     * @return mixed|string
     */
    public static function cleanUrl($url, $cleanall = true) {
        if (strpos($url, 'http://') !== false) {
            if ($cleanall) {
                return '/';
            } else {
                return str_replace('http://', '', $url);
            }
        }

        return $url;
    }

    /**
     * 获取当前域名
     *
     * @param bool $http
     * @param bool $entities
     *
     * @return string
     */
    public static function getHttpHost($http = false, $entities = false) {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
        if ($entities)
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        if ($http) {
            $host = self::getCurrentUrlProtocolPrefix() . $host;
        }

        return $host;
    }

    /**
     * 获取当前服务器名
     *
     * @return mixed
     */
    public static function getServerName() {
        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * 获取用户来源地址
     *
     * @return null
     */
    public static function getReferer() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            return null;
        }
    }

    /**
     * 判断是否使用了HTTPS
     *
     * @return bool
     */
    public static function usingSecureMode() {
        if (isset($_SERVER['HTTPS']))
            return ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
        if (isset($_SERVER['SSL']))
            return ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');

        return false;
    }

    /**
     * 获取当前URL协议
     *
     * @return string
     */
    public static function getCurrentUrlProtocolPrefix() {
        if (Response::usingSecureMode())
            return 'https://';
        else
            return 'http://';
    }

    /**
     * 判断是否本站链接
     *
     * @param $referrer
     *
     * @return string
     */
    public static function secureReferrer($referrer) {
        if (preg_match('/^http[s]?:\/\/' . Response::getServerName() . '(:443)?\/.*$/Ui', $referrer))
            return $referrer;

        return '/';
    }

    /**
     * 获取POST或GET的指定字段内容
     *
     * @param      $key
     * @param bool $default_value
     *
     * @return bool|string
     */
    public static function getValue($key, $default_value = false) {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;
        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

        if (is_string($ret) === true)
            $ret = trim(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));

        return !is_string($ret) ? $ret : stripslashes($ret);
    }

    /**
     * 判断POST或GET中是否包含指定字段
     *
     * @param $key
     *
     * @return bool
     */
    public static function getIsset($key) {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;

        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * 判断是否为提交操作
     *
     * @param $submit
     *
     * @return bool
     */
    public static function isSubmit($submit) {
        return (isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y']) || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y']));
    }
}