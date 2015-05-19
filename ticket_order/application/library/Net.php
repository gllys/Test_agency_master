<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-4-28
 * Time: 下午2:32
 */


class Net {

    /**
     * 跳转
     *
     * @param      $url
     * @param null $headers
     */
    public static function redirect($url, $headers = null) {
        if (!empty($url))
        {
            if ($headers)
            {
                if (!is_array($headers))
                    $headers = array($headers);

                foreach ($headers as $header)
                    header($header);
            }

            header('Location: ' . $url);
            exit;
        }
    }

    /**
     * 清理URL中的http头
     *
     * @param      $url
     * @param bool $cleanall
     *
     * @return mixed|string
     */
    public static function cleanUrl($url, $cleanall = true) {
        if (strpos($url, 'http://') !== false)
        {
            if ($cleanall)
            {
                return '/';
            }
            else
            {
                return str_replace('http://', '', $url);
            }
        }

        return $url;
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
     * 获取用户IP地址
     *
     * @return mixed
     */
    public static function getRemoteAddr() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR']))))
        {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ','))
            {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            }
            else
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 获取用户来源地址
     *
     * @return null
     */
    public static function getReferer() {
        if (isset($_SERVER['HTTP_REFERER']))
        {
            return $_SERVER['HTTP_REFERER'];
        }
        else
        {
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
        if (self::usingSecureMode())
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
        if (preg_match('/^http[s]?:\/\/' . self::getServerName() . '(:443)?\/.*$/Ui', $referrer))
            return $referrer;

        return '/';
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
        if ($http)
        {
            $host = self::getCurrentUrlProtocolPrefix() . $host;
        }

        return $host;
    }

    /**
     * 判断是否爬虫，范围略大
     *
     * @return bool
     */
    public static function isSpider() {
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
            $spiders = array('spider', 'bot');
            foreach ($spiders as $spider)
            {
                if (strpos($ua, $spider) !== false)
                {
                    return true;
                }
            }
        }

        return false;
    }

    public static function sendToBrowser($file, $delaftersend = true, $exitaftersend = true) {
        if (file_exists($file) && is_readable($file))
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename = ' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            if ($delaftersend)
            {
                unlink($file);
            }
            if ($exitaftersend)
            {
                exit;
            }
        }
    }

    public static function redirectTo($link) {
        if (strpos($link, 'http') !== false)
        {
            header('Location: ' . $link);
        }
        else
        {
            header('Location: ' . self::getHttpHost(true) . '/' . $link);
        }
        exit;
    }

    public static function returnAjaxJson($array) {
        if (!headers_sent())
        {
            header("Content-Type: application/json; charset=utf-8");
        }
        echo(json_encode($array));
        ob_end_flush();
        exit;
    }




}