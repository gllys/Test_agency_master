<?php
/**
 * Created by PhpStorm.
 * User: libiying
 * Date: 15-1-8
 * Time: 下午6:04
 */

class ScenicController extends Base_Controller_Ota
{
    public function indexAction()
    {
        //

        echo Lang_Msg::error('ERROR_GLOBEL_1');

        //Lang_Msg::output($data);
    }

    /**
     * 查询景区列表
     * @param
     *  name        否   string      景点名字模糊搜索。
     *  keyword     否   string      关键词检索。
     *  district    否   int         位置所属的行政区编码。
     *  token       是   string      访问接口授权码。
     *  timestamp   是   timestamp   发起请求时的时间戳。
     *  sign        是   string      参数签名。
     */
    public function listAction(){

        //参数转换处理
        $params = $this->body;
        if(isset($this->body['district'])) $params['district_ids'] =$this->body['district'];

        //调用接口查询数据
        $response = ApiScenicModel::model()->lists($params);
        if($response['code'] == 'fail'){
            Lang_Msg::error('ERROR_GLOBEL_1');
        }else if($response['code'] == 'succ'){

            //字段转换处理
            $result = array();
            $data   = $response['body']['data'];
            foreach($data as $ll){

                $res['id']          = $ll['id'];
                $res['name']        = $ll['name'];
                $res['star']        = $ll['landscape_level_id'];
                $res['address']     = $ll['address'];
                $res['thumbnail']   = $ll['thumbnail_img'];
                $res['province']    = array('id'=>$ll['province_id'],   'name'=>$ll['district'][0]);
                $res['city']        = array('id'=>$ll['city_id'],       'name'=>$ll['district'][1]);
                $res['region']      = array('id'=>$ll['district_id'],   'name'=>$ll['district'][2]);

                $result['data'][]   = $res;
            }
            $result['pagination'] = $response['body']['pagination'];

            Lang_Msg::output($result);
        }
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

        $params = $this->body;
        if(!isset($params['id']))
            Lang_Msg::error('id错误');

        //调用接口查询 景点详情 数据
        $response = ApiScenicModel::model()->detail(array('id' => $params['id']));

        if($response['code'] == 'fail'){
            Lang_Msg::error('ERROR_GLOBEL_1');
        }else if($response['code'] == 'succ'){

            //字段转换处理
            $result = array();
            $body   = $response['body'];

            $result['id']               = $body['id'];
            $result['name']             = $body['name'];
            $result['star']             = $body['landscape_level_id'];
            $result['address']          = $body['address'];
            $result['thumbnail']        = $body['thumbnail_img'];
            $result['latitude']         = $body['lat'];
            $result['longitude']        = $body['lng'];
            $result['notice']           = $body['note'];
            $result['introduction']     = $body['biography'];
            $result['transportation']   = $body['transit'];
            $result['safety']           = '';
            $result['province']         = array('id'=>$body['province_id'], 'name'=>$body['province_name']);
            $result['city']             = array('id'=>$body['city_id'], 'name'=>$body['city_name']);
            $result['region']           = array('id'=>$body['district_id'], 'name'=>$body['district_name']);

            //调用接口查询 对应景点的产品 数据
            $req_products = array(
                'scenic_id' => $result['id'],
                'state'     => 1,
                'items'     => 100,
            );
            $res_products   = ApiProductModel::model()->products($req_products);

            $products       = array();
            
            if($res_products['code'] == 'succ' && !empty($res_products['body']['data'])){
                foreach($res_products['body']['data'] as $pro) {
                    $product = array(
                        'id'        => $pro['id'],
                        'name'      => $pro['name'],
                        'scenic_id' => $pro['scenic_id'],
                        'payment'   => 2,
                        //以下信息尚未明确
                        'fee'       => '',
                        'exchange'  => '',
                        'term'      => '',
                        'audience'  => $pro['remark'],
                        'advance_booking_min_days'  => '',
                        'advance_booking_max_days'  => '',
                        'requirement'   => '',
                        'price'         => $pro['sale_price'],
                        'market_price'  => $pro['listed_price'],
                    );
                    $products[] = $product;
                }
            }
            $result['products'] = $products;

            Lang_Msg::output($result);
        }

    }

    public function differenceAction()
    {
        $params = $this->body;
        if(!isset($params['last_sync_at']))
        {
            Lang_Msg::error('ERROR_GLOBEL_1');
        }

        $page = array_key_exists('page', $params) ? (int)$params['page'] : 1;
        $items = array_key_exists('items', $params) ? (int)$params['items'] : 15;
        $start = ($page - 1) * $items;
        $total = ScenicDifferenceModel::model()->countResult(array('created_at|>' => $params['last_sync_at']));
        $flag = $total % $items;
        $count = $flag ? (int)($total / $items + 1) : ($total / $items);
        $collection = ScenicDifferenceModel::model()->search(array('created_at|>' => $params['last_sync_at']), '*', null, array($start, $items), 'id');

        $data = array();
        foreach($collection as $diff)
        {
            $data[] = $diff['id'];
        }
        $pagination = array(
            'count' => $count,
            'current' => $page,
            'items' => $items,
            'total' => $total
        );

        Lang_Msg::output(array('data' => $data, 'pagination' => $pagination));
    }

}
