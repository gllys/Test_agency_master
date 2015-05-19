<?php

class Organizations extends ApiModel {

    protected $param_key = 'ticket-api-organization'; #请求api地址，对应config main里面的 key

    //获取所有供应商
    public function getAll() {
        $param = array(
            'type' => 'supply',
            'fields' => 'id,name',
            //'supply_type' => 1,
            'items' => 2000
        );
        $result = Organizations::api()->list($param,true,500);
        return ApiModel::getLists($result);
    }
    
    //获取所有景区供应商
    public function lanOrgs() {
        $param = array(
            'type' => 'supply',
            'fields' => 'id,name',
            'supply_type' => 1,
            'items' => 2000
        );
        $result = Organizations::api()->list($param);
        return ApiModel::getLists($result);
    }

}
