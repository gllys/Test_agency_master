<?php
/**
 * Created by PhpStorm.
 * User: libiying
 * Date: 15-1-8
 * Time: 下午6:04
 */

class ProductController extends Base_Controller_Ota
{
    public function listAction()
    {
        $response = ApiOtaModel::model()->productList(array(
            'agency_id' => $this->userinfo['distributor_id'],
        ));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_list',
            'response' => $response,
            'params' => $this->body,
        ));
        $list = array();
        if(isset($responsse['body']) && is_array($response['body'])) {
            foreach ($response['body'] as $row) {
                $list[] = $this->packProduct($row);
            }
        }
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_list',
            'list' => $list,
        ));
        
        Lang_Msg::output($list);
    }
    
    public function priceAction()
    {
        $params = $this->body;
        if(!array_key_exists('id', $params) || empty($params['id']))  Lang_Msg::error('缺少参数id');
        if(!array_key_exists('from', $params) || empty($params['from'])) Lang_Msg::error('缺少参数from');
        if(!array_key_exists('to', $params) || empty($params['to'])) Lang_Msg::error('缺少参数to');
        if(strtotime($params['from']) > strtotime($params['to']))   Lang_Msg::error('开始时间（from）不能大于结束时间(to)');
        $delta = (strtotime($params['to']) -  strtotime($params['from'])) / (60 * 60 * 24);
        
        $response = ApiOtaModel::model()->productDetail(array(
            'id' => $params['id'],
        ));
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_price',
            'params' => $params,
            'response' => $response,
        ));
        if ($response['code'] == 'fail') {
            Lang_Msg::error($response['message']);
        }
        
        $product_params = array(
            'ticket_id' => $response['body']['product_id'],
            'type' => 0,
            'distributor_id' => $this->userinfo['distributor_id'],
            'range' => "{$params['from']},{$params['to']}"
        );
        $product = ApiProductModel::model()->detail($product_params);
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_price',
            'product_params' => $product_params,
            'product' => $product,
        ));
        $reserve = array();
        if(isset($product['body']['reserve']) && is_array($product['body']['reserve'])) {
            foreach ($product['body']['reserve'] as $key => $row) {
                $row['date'] = $key;
                
                $reserve[] = $row;
            }
        }
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_price',
            'reserve' => $reserve,
        ));
        Lang_Msg::output($reserve);
    }

    /**
     * 套票id，取其中的景区ids
     */
    private function scenicidsAction() {
        $params = $this->body;
        if(!array_key_exists('id', $params) || empty($params['id']))  Lang_Msg::error('缺少参数id');

        $r = ApiProductModel::model()->detail(array('ticket_id'=>$params['id']));
        isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));

        Lang_Msg::output(array(
            'id'=>$params['id'] ,
            'scenic_ids'=>$r['body']['scenic_id']
        ));

    }

    /**
     * 产品详情接口
     */
    public function detailAction(){
        $params = $this->body;
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_detail',
            'params' => $params,
            'message' => '缺少参数ID',
        ));
        if(!array_key_exists('id', $params) || empty($params['id']))  Lang_Msg::error('缺少参数id');

        $response = ApiOtaModel::model()->productDetail(array(
            'id' => $params['id']
        ));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_detail',
            'response' => $response,
        ));
        if ($response['code'] == 'fail') {
            Lang_Msg::error($response['message']);
        }

        $data = array();
        if(isset($response['body']) && is_array($response['body'])) {
            $data = $this->packProduct($response['body']);
        }
        Lang_Msg::output($data);
    }
    
    private function packProduct($data) {
        list($start, $end) = explode(',', $data['date_available']);
        
        $p = array(
            'name' => $data['product_name'] ? $data['product_name'] : $data['name'],
            'sale_price' => $data['sale_price'],
            'id' => $data['id'],
            'listed_price' => $data['listed_price'],
            'week_time' => $data['week_time'],
            'date_available_start' => date('Y-m-d H:i:s', $start),
            'date_available_end' => date('Y-m-d H:i:s', $end),
            'payment' => in_array(2, explode(',', $data['payment'])) ? '0,1' : 0,
            'pass_type' => $data['pass_type'],
            'pass_address' => $data['pass_address'],
            'detail' => $data['detail'],
            'description' => $data['description'],
            'consumption_detail' => $data['consumption_detail'],
            'refund_detail' => $data['refund_detail'],
            'valid' => $data['valid'],
            'max_buy' => $data['max_buy'],
            'mini_buy' => $data['mini_buy'],
            'scenic_id' => $data['scenic_id'],
            'view_point' => $data['view_point'],
            'scheduled_time' => $data['scheduled_time'],
            'refund' => $data['refund'],
            'remark' => $data['remark'],
            'valid_flag' => $data['valid_flag']
        );

        if(isset($data['scenic_poi_list']) && $data['scenic_poi_list']) {
            $p['scenic_poi_list'] = $data['scenic_poi_list'];
        }
        if(isset($data['fat_price'])) {
            $p['fat_price'] = $data['fat_price'];
        }
        
        return $p;
    }

}
