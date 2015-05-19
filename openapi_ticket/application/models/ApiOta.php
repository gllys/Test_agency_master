<?php

/**
 * Ota
 *
 * @author wfdx1_000
 */
class ApiOtaModel extends Base_Model_Api {
    
    protected $method = 'POST';
    protected $srvKey = 'ticket_info';
    
    /**
     * 
     * @return ApiOtaModel
     */
    public static function model() {
        return parent::model();
    }
    
    /**
     * 
     * @param array $params
     * @return list
     */
    public function scenicLists($params){
        $this->url = '/v1/ota/scenicList';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
    
    public function scenicDetail($params){
        $this->url = '/v1/ota/scenicInfo';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function productList($params) {
        $this->url = '/v1/ota/productList';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
    
    public function productDetail($params) {
        $this->url = '/v1/ota/productInfo';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
    
}
