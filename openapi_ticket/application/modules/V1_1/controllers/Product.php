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
        if(isset($response['body']) && is_array($response['body'])) {
            foreach ($response['body'] as $row) {
                $list[] = $this->packProduct($row);
            }
        }
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_list',
            'list' => $list,
        ));
        
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => array(
                'products' => $list,
            ),
        ));
    }
    
    public function priceAction()
    {
        $required_params = array(
            'id','from','to'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $params = $this->body;

        $from = $params['from'];
        $to = $params['to'];

        if(strtotime($from) < strtotime('today')) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '无法查询过去时间',
                'result' => array(),
            ));
        }

        if(strtotime($from) > strtotime($to)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '开始时间（from）不能大于结束时间(to)',
                'result' => array(),
            ));
        }
        
        $response = ApiOtaModel::model()->productDetail(array(
            'id' => $params['id'],
        ));
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_price',
            'params' => $params,
            'response' => $response,
        ));
        if ($response['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $response['message'],
                'result' => array(),
            ));
        }
        
        $product_params = array(
            'ticket_id' => $response['body']['product_id'],
            'type' => 0,
            'distributor_id' => $this->userinfo['distributor_id'],
        );
        if($from == $to) {
            $date_params = array(
                'use_day' => $from,
            );
        } else if($to > $from) {
            $date_params = array(
                'range' => $from.','.$to,
            );
        }

        $product_params = array_merge($date_params,$product_params);
        $product = ApiProductModel::model()->detail($product_params);
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_price',
            'product_params' => $product_params,
            'product' => $product,
        ));
        if($product['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $product['message'],
                'result' => array(),
            ));
        }
        $reserve = array();
        if($to > $from) {
            if(isset($product['body']['reserve']) && is_array($product['body']['reserve'])) {
                foreach ($product['body']['reserve'] as $key => $row) {
                    $row['date'] = $key;
                    
                    $reserve[] = $row;
                }
            }
        } else if($from == $to) {
            if(isset($product['body'])) {
                $product = $product['body'];
                $reserve[] = array(
                    //9999表示库存不限
                    'day_reserve' => isset($product['day_reserve']) ? $product['day_reserve'] : 9999,
                    'used_reserve' => isset($product['used_reserve']) ? $product['used_reserve'] : 0,
                    'remain_reserve' => isset($product['remain_reserve']) ? $product['remain_reserve'] : 9999,
                    'price' => isset($product['price']) ? $product['price'] : 0,
                    'date' => $from,
                ); 
            }
        }
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_price',
            'reserve' => $reserve,
        ));
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => array(
                'prices' => $reserve
            ),
        ));
    }

    /**
     * 套票id，取其中的景区ids
     */
    private function scenicidsAction() {
        $required_params = array(
            'id',
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $params = $this->body;

        $r = ApiProductModel::model()->detail(array('ticket_id'=>$params['id']));
        if ($product['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ));
        }

        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => array(
                'id'=>$params['id'] ,
                'scenic_ids'=>$r['body']['scenic_id']
            ),
        ));

    }

    /**
     * 产品详情接口
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
            'action' => 'product_detail',
            'params' => $params,
            'message' => '缺少参数ID',
        ));

        $response = ApiOtaModel::model()->productDetail(array(
            'id' => $params['id']
        ),'GET');
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'product_detail',
            'response' => $response,
        ));
        if ($response['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $response['message'],
                'result' => array(),
            ));
        }

        $data = array();
        if(isset($response['body']) && is_array($response['body'])) {
            $data = $this->packProduct($response['body']);
        }
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $data,
        ));
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
