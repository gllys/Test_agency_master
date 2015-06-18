<?php

class Lang_Msg 
{
    protected static $local = 'zh';
    protected static $_languages = array();
    protected static $status = array(
        200,//请求成功,数据将以JSON格式返回。
        201,//创建请求成功。
        204,//删除请求成功。
        301,//接口已迁移到新的地址。(通常情况下不会返回该状态。)
        302,//接口临时迁移到新的地址。(通常情况下不会返回该状态。)
        400,//请求错误。(通常是参数输入有误。)
        404,// 所访问的服务不存在。(通常是地址输入有误。)
        405,// 通常情况下写数据用POST获取数据用GET,其他的HTTP方式不会支持。
        500,// 系统服务器出错,请联系接口方技术人员。
        503,// 系统服务器暂不可用,通常是服务器处于维护状态。
    );

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

    public static function error($id, $code = 400, $params = array()) {
        header("Content-Type: application/json; charset=utf-8");
        header("HTTP/1.1 $code");

        $msg = self::getLang($id, $params);
        exit(Pack_Json::encode(array(
            'error'=>preg_match("/^\w$/",$id)?$id:'fail',
            'error_message'=>$msg
            )));
    }

    public static function output($data = null, $code = 200, $type = NULL) {
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=utf-8");
            header("HTTP/1.1 $code");
        }
        exit(Pack_Json::encode($data, $type));
    }

}