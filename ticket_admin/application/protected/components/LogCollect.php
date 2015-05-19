<?php

if (!function_exists("fastcgi_finish_request")) {

    function fastcgi_finish_request() {
        
    }

}

class LogCollect {
    /*
     * @param $log 需记录日志
     * @param $data 返回日志 
     * @param $param 传过去的参数
     */

    public static function add($log='', $data = null, $param = null) {
        return false;
        if (!Yii::app()->request->isPostRequest){
            return false;
        }
        
        $rs = debug_backtrace();

        foreach ($rs as $item) {
            if (!empty($item['class']) && stripos($item['class'], 'controller')) {
                $controller = $item['object']; #得到调用的控制器对象
            }
        }

        if (empty($controller))
            return false;

        $attr = array();
        $attr['user_id'] = Yii::app()->user->id;
        $attr['user_name'] = Yii::app()->user->name;
        $attr['controller'] = $controller->id;
        $attr['action'] = $controller->action->id;
        $attr['url'] = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

        if (!$data) {
            $data = ApiModel::$outResult;
        }
        $attr['data'] = is_array($data) ? json_encode($data) : $data;

        if (!$param) {
            $param = ApiModel::$inParam;
            if (!$param) {
                $param = $_POST + $_GET;
            }
        }

        if (!$log && $data) {
            if (!is_array($data)) {
                $data = json_decode($data);
            }
            $log = @$data['message'];
        }
        $attr['msg'] = $log;

        $attr['param'] = is_array($param) ? json_encode($param) : $param;
        $attr['dateline'] = time();

        fastcgi_finish_request();
        $model = new Log();
        $model->attributes = $attr;
        try {
            $model->save();
        } catch (Exception $ex) {
            
        }
    }

}

?>
