<?php
/**
 * Created by PhpStorm.
 * User: libiying
 * Date: 15-1-8
 * Time: 下午6:04
 */

class ScenicController extends Base_Controller_Ota
{

    public function listAction(){
        $response = ApiOtaModel::model()->scenicLists(array(
            'agency_id' => $this->userinfo['distributor_id'],
        ));
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'scenic_list',
            'response' => $response,
            'params' => $this->body,
        ));
        if ($response['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $response['message'],
                'result' => array(),
            ));
        }
        
        $list = array();
        if(isset($response['body']) && is_array($response['body'])) {
            foreach ($response['body'] as $row) {
                $list[] = $this->packScenic($row);
            }
        }
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'scenic_list',
            'list' => $list,
        ));
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => array(
                'scenics' => $list,
            ),
        ));
    }

    /**
     * 获取景点详情
     * @param
     *  id          是   int         所查询的景区ID。
     *  token       是   string      访问接口授权码。
     *  timestamp   是   timestamp   发起请求时的时间戳。
     *  sign        是   string      参数签名。
     */
    public function detailAction(){
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $params = $this->body;
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'scenic_detail',
            'params' => $this->body,
        ));

        //调用接口查询 景点详情 数据
        $response = ApiOtaModel::model()->scenicDetail(array(
            'id' => $params['id']
        ));

        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'scenic_detail',
            'response' => $response,
        ));
        if($response['code'] == 'fail'){
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $response['message'],
                'result' => array(),
            ));
        }else if($response['code'] == 'succ'){
            $data = array();
            if(isset($response['body']) && is_array($response['body'])) {
                $data = $this->packScenic($response['body']);
            }
            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => $data
            ));
        }
    }

    private function packScenic($data) {
        $tmp = array(
            'id' => $data['id'],
            'name' => $data['name'],
            'star' => $data['landscape_level_id'],
            'address' => $data['address'],
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'phone' => $data['phone'],
            'introduction' => isset($data['biography']) ? $data['biography'] : '',
            'images' => isset($data['images']) ? $data['images'] : array(),
            'hours' => $data['hours'],
            'exaddress' => $data['exaddress'],
            'note' => $data['note'],
            'transit' => $data['transit'],
            'updated_at' => $data['updated_at'],
            'products' => array_map(function($item) {
                return (string)$item;
            }, $data['product_ids']),
        );
        if ($data['district_id']) {
            $tmp['district'] = array(
                'id' => $data['district_id'],
                'name' => $data['district_name']
            );
        }
        if ($data['province_id']) {
            $tmp['province'] = array(
                'id' => $data['province_id'],
                'name' => $data['province_name']
            );
        }
        if ($data['city_id']) {
            $tmp['city'] = array(
                'id' => $data['city_id'],
                'name' => $data['city_name']
            );
        }
        if(isset($data['scenic_poi_list']) && $data['scenic_poi_list']) {
            $tmp['scenic_poi_list'] = $data['scenic_poi_list']; 
        }
        
        return $tmp;
    }

}
