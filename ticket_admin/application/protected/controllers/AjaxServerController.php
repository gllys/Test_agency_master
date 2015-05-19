<?php

class AjaxServerController extends CController {

    public function actionGetChildern() {
    	$id = $_REQUEST['id'];
        $rs = Districts::model()->findAllByAttributes(array('parent_id' => $id));
        echo CJSON::encode($rs);
    }

    /**
     * 获取机构下的景区
     */
    public function actionLandscapes() {
        $term = Yii::app()->request->getParam('term');
        $return_arr = array();
        if (!empty($term)) {
            $param['organization_id'] = Yii::app()->user->org_id;
            $param['keyword'] = $term;
            $param['items'] = Yii::app()->request->getParam('page_limit');
            $rs = Landscape::api()->lists($param);
            $data = ApiModel::api()->getData($rs);
            foreach ($data['data'] as $row) {
                $row_array['id'] = $row['id'];
                $row_array['text'] = $row['name'];
                array_push($return_arr, $row_array);
            }
        }

        if (empty($return_arr)) {
            $row_array['id'] = 0;
            $row_array['text'] = "没有找到匹配的景区";
            array_push($return_arr, $row_array);
        }

        echo json_encode(array('results' => $return_arr));
    }

    /**
     * 搜索所有分销商
     */
    public function actionAgency() {
        $term = Yii::app()->request->getParam('term');
        $return_arr = array();
        if (!empty($term)) {
            $param['type'] = "agency";
            //$param['verify_status'] = 'checked';
            $param['status'] = 1;
            $param['name'] = $term;
            $param['items'] = Yii::app()->request->getParam('page_limit');
            $rs = Organizations::api()->list($param);
            $data = $rs['body'];
            foreach ($data['data'] as $row) {
                $row_array['id'] = $row['id'];
                $row_array['text'] = $row['name'];
                $row_array['province_id'] = $row['province_id'];
                array_push($return_arr, $row_array);
            }
        }

        if (empty($return_arr)) {
            $row_array['id'] = 0;
            $row_array['text'] = "没有找到匹配的分销商";
            array_push($return_arr, $row_array);
        }

        echo json_encode(array('results' => $return_arr));
    }

    /**
     * 根据省市获取分销商
     */
    public function actionGetAgency() {
        if (Yii::app()->request->isAjaxRequest) {
            $province_id = Yii::app()->request->getParam('pid');
            $city_id = Yii::app()->request->getParam('cid');
            $param['type'] = "agency";
            //$param['verify_status'] = 'checked';
            $param['status'] = 1;
            $param['province_id'] = $province_id;
            if (!empty($city_id) && $city_id != "__NULL__") {
                $param['city_id'] = $city_id;
            }
            $rs = Organizations::api()->list($param);
            if($rs['code']=="succ"){
                $return_arr = array();
                if(isset($rs['body']['data'])){
                    foreach ($rs['body']['data'] as $row) {
                        $row_array['id'] = $row['id'];
                        $row_array['text'] = $row['name'];
                        array_push($return_arr, $row_array);
                    }
                }
                echo json_encode(array('results'=>$return_arr));
            }else{
                echo json_encode(array('error' => $rs['message']));
            }
        }
    }

    public function actionAgencyByIds() {
        if (Yii::app()->request->isAjaxRequest) {
            $ids = Yii::app()->request->getParam('ids');
            $param['type'] = "agency";
            $param['id'] = $ids;
            $param['verify_status'] = 'checked';
            $param['status'] = 1;
            $rs = Organizations::api()->list($param);
            $data = Organizations::api()->getData($rs);
            $return_arr = array();
            foreach ($data['data'] as $row) {
                $row_array['id'] = $row['id'];
                $row_array['text'] = $row['name'];
                $row_array['province_id'] = $row['province_id'];
	            //todo optimize
                $cityList = Districts::provinceCity();
                if (in_array($row['province_id'], $cityList)) {
                    $row_array['city_id'] = $row['province_id'];
                    $row_array['province_text'] = $cityList[$row['province_id']];
                    $row_array['city_text'] = $cityList[$row['province_id']];
                } else {
                    $row_array['city_id'] = $row['city_id'];
	                //todo optimize
                    $row_array['province_text'] = Districts::model()->findByPk($row['province_id'])->name;
                    $row_array['city_text'] = $row['city_id'] == 0 ? "其他" : Districts::model()->findByPk($row['city_id'])->name;
                }
                array_push($return_arr, $row_array);
            }
            echo json_encode(array('results' => $return_arr));
        }
    }

    public function actionPoiNames() {
        if (Yii::app()->request->getIsAjaxRequest()) {
            header('Content-type: application/json; charset=utf-8');
            $ids = Yii::app()->request->getParam('ids');
            $items = substr_count($ids, ',') + 1;
            $result = Poi::api()->lists(array(
                'ids' => $ids,
//                'organization_id' => Yii::app()->user->org_id,
                'fields' => 'name',
                'items' => $items,
                'show_deleted'=>1
            ));
            if ($result['code'] == 'succ') {
                echo json_encode(array(
                    'code' => 1,
                    'data' => $result['body']['data']
                ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('code' => 0));
            }
        }
    }

    public function actionLandscapeNames() {
        if (Yii::app()->request->getIsAjaxRequest()) {
            header('Content-type: application/json; charset=utf-8');
            $ids = Yii::app()->request->getParam('ids');
            $items = substr_count($ids, ',') + 1;
            $result = Landscape::api()->lists(array(
                'ids' => $ids,
//                'organization_id' => Yii::app()->user->org_id,
                'fields' => 'name',
                'items' => $items,
                'status' => 1
            ));
            if ($result['code'] == 'succ') {
                echo json_encode(array(
                    'code' => 1,
                    'data' => $result['body']['data']
                ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('code' => 0));
            }
        }
    }

    public function actionTicketruleNames() {
        if (Yii::app()->request->getIsAjaxRequest()) {
            header('Content-type: application/json; charset=utf-8');
            $ids = Yii::app()->request->getParam('ids');
            $items = substr_count($ids, ',') + 1;
            $result = Ticketrule::api()->lists(array(
                'ids' => $ids,
                'organization_id' => Yii::app()->user->org_id,
                'fields' => 'name',
                'items' => $items
            ));
            if ($result['code'] == 'succ') {
                echo json_encode(array(
                    'code' => 1,
                    'data' => $result['body']['data']
                ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('code' => 0));
            }
        }
    }

    public function actionTicketdiscountruleNames() {
        if (Yii::app()->request->getIsAjaxRequest()) {
            header('Content-type: application/json; charset=utf-8');
            $ids = Yii::app()->request->getParam('ids');
            $items = substr_count($ids, ',') + 1;
            $result = Ticketdiscountrule::api()->lists(array(
                'ids' => $ids,
                'organization_id' => Yii::app()->user->org_id,
                'fields' => 'name',
                'items' => $items
            ));
            if ($result['code'] == 'succ') {
                echo json_encode(array(
                    'code' => 1,
                    'data' => $result['body']['data']
                ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('code' => 0));
            }
        }
    }

    public function actionOrganizationsNames() {
        if (Yii::app()->request->getIsAjaxRequest()) {
            header('Content-type: application/json; charset=utf-8');
            $ids = Yii::app()->request->getParam('ids');
            $items = substr_count($ids, ',') + 1;
            $result = Organizations::api()->lists(array(
                'id' => $ids,
                'organization_id' => Yii::app()->user->org_id,
                'fields' => 'name',
                'items' => $items
            ));
            if ($result['code'] == 'succ') {
                echo json_encode(array(
                    'code' => 1,
                    'data' => $result['body']['data']
                ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('code' => 0));
            }
        }
    }

}
