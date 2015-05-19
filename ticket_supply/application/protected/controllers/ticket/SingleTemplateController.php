<?php

class SingleTemplateController extends Controller {

    public $childNav = '/ticket/single/';

    public function actionIndex() {
        $param['or_id'] = Yii::app()->user->org_id;
        $param['ticket_id'] = $_GET['id'];
        if (Yii::app()->request->isPostRequest) {
            $param['data'] = json_encode($_POST['price']);
            //Tickettemplate::api()->debug = true;
            $rs = Tickettemplate::api()->setfxp($param);
            if (ApiModel::isSucc($rs)) {
                $this->_end(0, $rs['message']);
            } else {
                $this->_end(1, $rs['message']);
            }
        }

        //Tickettemplate::api()->debug = true;
        $rs = Tickettemplate::api()->listByCity($param);
        $_lists = ApiModel::getData($rs);
        $lists = PublicFunHelper::ArrayByKeys($_lists, 'city_id');

        $this->render('index', compact('lists'));
    }

    //得到城市的所有分销售
    public function getAgencysByCityList() {
        $province_city = Districts::provinceCity(); #省级市 
        $cityLists = array(); #返回结果存放
        $param['type'] = 'agency';
        $param['items'] = 10000;
        $rs = Organizations::api()->list($param);
        $list = ApiModel::getLists($rs);
        foreach ($list as $item) {
            if (array_key_exists($item['province_id'], $province_city)) {
                $cityLists[$item['province_id']][] = $item;
            } else {
                $cityLists[$item['city_id']][] = $item;
            }
        }
        return array_reverse($cityLists, true);
    }

    //分销商查询
    public function actionCityAgencys($id) {
        header("Content-type: text/html; charset=utf-8");
        $province_city = Districts::provinceCity(); #省级市 

        if (array_key_exists($id, $province_city)) {
            $param['province_id'] = $id;
        } else {
            $param['city_id'] = $id;
        }
        $param['type'] = 'agency';
        $param['items'] = 10000;
        $rs = Organizations::api()->list($param);
        $list = ApiModel::getLists($rs);
        if ($list) {
            $this->_end(0, $list);
        } else {
            $this->_end(1, $list);
        }
    }

}
