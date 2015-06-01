<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-9
 * Time: 下午2:20
 */

class ApiProductModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/TicketTemplate/lists';
    protected $method = 'POST';

    //申请提现
    public function products($params){
        $this->url = '/v1/TicketTemplate/lists';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function detail($params){
        $this->url = '/v1/TicketTemplate/ticketinfo';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
    
    public function getProductByCode($params){
//        return json_decode('{"code":"succ","message":"","body":{"id":"903","agency_id":"2","product_id":"2","product_name":"\u6d4b\u8bd5","price":"123.00","source":"1","code":"123321","payment":"3","payment_list":"alipay","create_at":"1413980456","update_at":"1413980456","delete_at":"1413980456","organization_id":"2","name":"22","fat_price":"23.00","group_price":"1.00","sale_price":"3.00","listed_price":"3.00","valid":"3","max_buy":"3","mini_buy":"3","scenic_id":"2","view_point":"3","state":"1","scheduled_time":"3","week_time":"3","refund":"0","is_del":"1","remark":"3\t3\t3","type":"0","date_available":"0","sale_start_time":"0","sale_end_time":"0","created_by":"0","created_at":"0","updated_at":"0","province_id":"0","city_id":"0","district_id":"0","is_fit":"0","is_full":"0","rule_id":null,"policy_id":"0","namelist_id":"0","discount_id":"0","ota_type":"system","ota_code":"9e193256fda3001d3eca01f8c0c33b93","is_union":"0","expire_start":"0","expire_end":"0","real_expire_end":"-3","user_id":null,"user_account":null,"user_name":null,"is_infinite":"0","base_org_num":"1","valid_flag":"0","force_out":"0","force_out_remark":"","sms_template":null}}
//',true);
        $this->url = '/v1/AgencyProduct/detail';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function getProductListByCode($params){
        $this->url = '/v1/AgencyProduct/lists';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    //获取日库存设置（由供应商商设置）
    public function getTicketRule($params){
        $this->url = '/v1/ticketrule/detail';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    //获取日库存设置（由分销商设置）
    public function getAgencyRule($params){
        $this->url = '/v1/Agencypdrule/items';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
}