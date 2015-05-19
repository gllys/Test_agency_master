<?php

class Landscape extends ApiModel {

    protected $param_key = 'ticket-api-scenic'; #请求api地址，对应config main里面的 key

    //如果有缓存，从缓存中读取景区信息,如果没有,调用接口,缓存1小时

    public function getSimpleByIds($ids, $cache = true) {
        if (empty($ids)) {
            return array();
        }
        $_list = array(); //返回的数据
        $noCacheIds = array(); //未缓存的ID 

        if ($cache) {
            //从缓存中读取
            $cacheKeys = array();
            foreach ($ids as $id) {
                $cacheKeys[] = 'landscape_simple:' . $id;
            }
            $rs = Yii::app()->redis->getMultiple($cacheKeys);
            //得到未缓存ids
            foreach ($rs as $key => $val) {
                $_id = $ids[$key]; //得到id
                if ($val !== false) {
                    $_list[$_id] = CJSON::decode($val, true);
                } else {
                    $noCacheIds[] = $_id;
                }
            }
            //如果没有未缓存的直接返回
            if (empty($noCacheIds)) {
                return $_list;
            }
        } else {
            $noCacheIds = $ids;
        }
        //得到未缓存的数据
        $param = array();
        $param['ids'] = join(',', $noCacheIds);
        $param['items'] = 100000;
        $param['fields'] = 'id,name';
        $data = Landscape::api()->lists($param);
        $lists = ApiModel::getLists($data);
        foreach ($lists as $item) {
            $_list[$item['id']] = $item;
            if ($cache) {
                Yii::app()->redis->setex('landscape_simple:' . $item['id'], 60 * 10, CJSON::encode($item)); //缓存一个小时
            }
        }
        return $_list;
    }

}
