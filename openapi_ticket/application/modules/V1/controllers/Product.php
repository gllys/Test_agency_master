<?php
/**
 * Created by PhpStorm.
 * User: libiying
 * Date: 15-1-8
 * Time: 下午6:04
 */

class ProductController extends Base_Controller_Ota
{
    public function indexAction()
    {
        Lang_Msg::error('ERROR_GLOBEL_1');
    }

    public function priceAction()
    {
        $params = $this->body;
        if(!array_key_exists('id', $params) || empty($params['id']))  Lang_Msg::error('缺少参数id');
        if(!array_key_exists('from', $params) || empty($params['from'])) Lang_Msg::error('缺少参数from');
        if(!array_key_exists('to', $params) || empty($params['to'])) Lang_Msg::error('缺少参数to');
        if(strtotime($params['from']) > strtotime($params['to']))   Lang_Msg::error('开始时间（from）不能大于结束时间(to)');
        $delta = (strtotime($params['to']) -  strtotime($params['from'])) / (60 * 60 * 24);
        if($delta >= 2) {
            Lang_Msg::error('最多只能查询两天的价格。');
        }


        //TODO: 获取产品
        $product = ApiProductModel::model()->detail(array('ticket_id' => $params['id'], 'price_type'=>0, 'distributor_id' => $this->userinfo['distributor_id'], 'sign' => 'debug'));

        //TODO: 判断from和to的正确区间，如果都小于expire_start 则取expire_start作为标准，如果都大于expire_end 则取expire_end作为标准
        $from = strtotime($params['from']);
        $to = strtotime($params['to']);
        //若from 和 to 都小于expire_start 则值都取 expire_start
        if($from < $product['body']['expire_start'] && $to < $product['body']['expire_end']){
            $from = $product['body']['expire_start'];
            $to = $product['body']['expire_start'];
        }
        //若from 和 to 都大于expire_start 则值都取 expire_end
        if($from > $product['body']['expire_end'] && $to > $product['body']['expire_end']){
            $from = $product['body']['expire_end'];
            $to = $product['body']['expire_end'];
        }
        //若from小于expire_start 则值取 expire_start
        if($from < $product['body']['expire_start'])
            $from = $product['body']['expire_start'];
        //若to大于expire_end 则值取 expire_end
        if($to > $product['body']['expire_end'])
            $to = $product['body']['expire_end'];
        $from = date('Y-m-d', $from);
        $to = date('Y-m-d', $to);
        $data = array();
        while(strtotime($from) <= strtotime($to)){
            //$product = ApiProductModel::model()->detail(array('ticket_id' => $id, 'price_type'=>0, 'distributor_id' => $this->userinfo['distributor_id'], 'use_day' => $from));
            $product = ApiProductModel::model()->detail(array('ticket_id' => $params['id'], 'price_type'=>0, 'distributor_id' => $this->userinfo['distributor_id'], 'use_day' => $from));

            //$data[$from] = $product['body']['sale_price'];
            $data[$from] = $product['body']['price'];
            if($from === date('Y-m-d', strtotime($to)))
                break;
            $from = date('Y-m-d', strtotime($from) + 60 * 60 * 24);
        }

        Lang_Msg::output($data);
    }

    /**
     * 套票id，取其中的景区ids
     */
    public function scenicidsAction() {
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
        if(!array_key_exists('id', $params) || empty($params['id']))  Lang_Msg::error('缺少参数id');

        try{
            $req = array(
                'ticket_id' => $params['id'],
                'price_type'=>0,
                'distributor_id' => $this->userinfo['distributor_id'],
            );
            $product = ApiProductModel::model()->detail($req);
            if($product['code'] == 'succ'){
                $product = $product['body'];

                $data['id']             = $product['id'];
                $data['name']           = $product['name'];
                $data['price']          = floatval($product['fat_price']);        //结算单价
                $data['market_price']   = floatval($product['listed_price']);//市场价（门市挂牌价）
                $data['payment']        = strpos($product['payment'], '2') === false ? '0' : '0,1';      //可支持的支付方式
                $data['valid']          = $product['valid_flag'] == 1 ? 9999 : intval($product['valid']);     //预订后多少天有效 0 表示当前有效
                $data['scenic_id']      = $product['scenic_id']; //景区ids
                $data['week_time']      = $product['week_time']; //星期几有效 1,2,3,4,5,6,0 其中0表示星期日

                $date_available = explode(',', $product['date_available']);
                $data['date_available']     = date("Y-m-d H:i:s",$date_available[0]) .','. date("Y-m-d H:i:s",$date_available[1]);  //可玩日期  int(11),int(11) 表示一个时间段 ，逗号分隔
                $data['sale_start_time']    = date("Y-m-d H:i:s",$product['sale_start_time']); //销售起始日
                $data['sale_end_time']      = date("Y-m-d H:i:s",$product['sale_start_time']);     //销售结束日

                $data['scenic'] = array();
                foreach($product['items'] as $items){
                    $data['scenic'][] =  array(
                        'scenic_id' => $items['scenic_id'],      //景区id
                        'sceinc_name' => $items['sceinc_name'],  //景区名称
                    );
                }

                Lang_Msg::output($data);

            }

            Lang_Msg::error($product['message']);
        }catch (Exception $ee){
            Lang_Msg::error($ee->getMessage());
        }

    }

}
