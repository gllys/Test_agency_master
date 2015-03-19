<?php

class Yii {

    private static $_instance;
    public  $params = array();
    public static function app() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            $rs = unserialize(PARAMS); 
            self::$_instance->params =  $rs['params'];
        }
        return self::$_instance;
    }

}

?>