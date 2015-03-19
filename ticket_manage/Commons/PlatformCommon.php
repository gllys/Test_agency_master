<?php

/**
 *  
 * 
 * 2014-03-12 
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class PlatformCommon extends BaseCommon {

    //分销
    const PLATFORM_FX = 1;
    //窗口
    const PLATFORM_LOCAL = 2;
    //景区离线
    const PLATFORM_LOCAL_OFFLINE = 3;
    //分销后台
    const PLATFORM_FX_BACKEND = 4;

    private $arr = array(
        1 => '分销',
        2 => '窗口',
        3 => '景区离线',
        4 => '分销后台',
    );

    public function findByPk($id) {
        return $this->arr[$id];
    }

}
