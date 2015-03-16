<?php

class Lang_Msg 
{
    protected static $local = 'zh';
    protected static $_languages = array();
    protected static $_code = 'succ';
    protected static $_message = '';
    protected static $_body = array();

    public static function getLang($id, $params = array()) {
        if (!isset(self::$_languages[$id])) {
            if ($info = LanguageModel::model()->getById($id)) {
                self::$_languages[$id] = $info[self::$local];
            } else {
                self::$_languages[$id] = $id;
            }
        }
        if ($params) {
            $s = $r = array();
            foreach($params as $key => $val) {
                $s[] = '{' . $key . '}';
                $r[] = $val;
            }
            return str_replace($s, $r, self::$_languages[$id]);
        }
        return self::$_languages[$id];
    }

    public static function error($id, $params = array()) {
        $msg = self::getLang($id, $params);
        self::$_code = 'fail';
        self::$_message = $msg;
        self::$_body = array();
        self::output();
    }

    public static function setBody($body, $name = null) {
        if ($name === null) {
            self::$_body = $body;
        } else {
            self::$_body[$name] = $body;
        }
    }

    public static function output($body = null) {
        if ($body !== null) {
            self::$_body = $body;
        }
        if (!headers_sent())
        {
            header("Content-Type: application/json; charset=utf-8");
        }
        exit(Pack_Json::encode(array(
            'code' => self::$_code,
            'message' => self::$_message,
            'body' => self::$_body,
            )));
    }
}