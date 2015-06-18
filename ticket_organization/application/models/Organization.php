<?php

/**
 * Class OrganizationModel
 */
class OrganizationModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'organizations';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|OrganizationModel|';
    
    public function getTable() {
        return $this->tblname;
    }

    /**
     * 注册机构
     * Author : yinjian
     * @param $organization
     * @return mixed
     */
    public function reg($organization)
    {
        Tools::walkArray($organization,'trim');
        $now = time();
        $this->add(array(
            'type' => $organization['type'],
            'name' => $organization['name'],
            'mobile' => $organization['mobile'],
            'contact' => $organization['contact'],
            'fax' => isset($organization['fax'])?$organization['fax']:'',
            'email' => isset($organization['email'])?$organization['email']:'',
            'telephone' => isset($organization['telephone'])?$organization['telephone']:'',
            'province_id' => isset($organization['province_id'])?intval($organization['province_id']):0,
            'city_id' => isset($organization['city_id'])?intval($organization['city_id']):0,
            'district_id' => isset($organization['district_id'])?intval($organization['district_id']):0,
            'address' => empty($organization['address'])?'':$organization['address'],
            'business_license' => empty($organization['business_license'])?'':$organization['business_license'],
            'tax_license' => isset($organization['tax_license'])?$organization['tax_license']:'',
            'certificate_license' => isset($organization['certificate_license'])?$organization['certificate_license']:'',
            'agency_type' => isset($organization['agency_type'])?intval($organization['agency_type']):0,
            'supply_type' => isset($organization['supply_type'])?intval($organization['supply_type']):0,
            'partner_type' => (isset($organization['partner_type']) && intval($organization['supply_type'])>0)?intval($organization['partner_type']):-1,  //供应商为景区角色时的类型，0景旅通（默认）,1大漠
            'verify_status' => $organization['verify_status'],
            'verify_by' => $organization['verify_status']== 'checked'?intval($organization['uid']):0,
            'verify_at' => $organization['verify_status']== 'checked'?$now:0,
            'status' => isset($organization['status'])?intval($organization['status']):0,
            'is_distribute_person' => isset($organization['is_distribute_person'])?intval($organization['is_distribute_person']):0,
            'is_distribute_group' => isset($organization['is_distribute_group'])?intval($organization['is_distribute_group']):0,
            'created_by' => isset($organization['uid'])?intval($organization['uid']):0,
            'created_at' => $now,
            'updated_at' => $now,
            'abbreviation' => isset($organization['abbreviation'])?$organization['abbreviation']:'',
            'description' => isset($organization['description'])?$organization['description']:'',
            //partner_identify: 外部供应商(合作伙伴)标识,JSON格式:{“username”:xxx,“password”:xxxx,“key”:xxx,“url”:xx,“paymentType”:2,...}
            'partner_identify' => isset($organization['partner_identify'])?$organization['partner_identify']:''
        ));
        return $this->getInsertId();
    }

    /**
     * 修改机构
     * author : yinjian
     * @param $data
     */
    public function modify($id,$data)
    {
        return $this->updateById($id,$data);
    }

    /**
     * 添加分销商到供应商
     * author : yinjian
     */
    public function addAgencyToSupply()
    {
        // 事务处理
    }

    /**
     * 供应商添加分销商
     * author : yinjian
     */
    public function addAgency($agency,$body)
    {
        try {
            $now = time();
            $this->begin();
            $organization = array(
                'type' => 'agency',
                'agency_type' => isset($agency['agency_type']) ? $agency['agency_type']:0,
                'name' => $agency['name'],
                'mobile' => $agency['mobile'],
                'contact' => $agency['contact'],
                'fax' => $agency['fax'],
                'telephone' => $agency['telephone'],
                'province_id' => isset($agency['province_id'])? $agency['province_id']:0,
                'city_id' => isset($agency['city_id'])? $agency['city_id']:0,
                'district_id' => isset($agency['district_id'])? $agency['district_id']:0,
                'address' => $body['address'],
                'business_license' => isset($body['business_license'])? $body['business_license']:'',
                'tax_license' => isset($body['tax_license'])? $body['tax_license']:'',
                'certificate_license' => isset($body['certificate_license'])? $body['certificate_license']:'',
                'status' => 1,
                'created_by' => $agency['uid'],
                'created_at' => $now,
                'updated_at' => $now
            );
            if(isset($body['verify_status']) && $body['verify_status']){
                $organization['verify_status'] = trim($body['verify_status']);
            }
            if(isset($body['status']) && in_array($body['status'],array(0,1))){
                $organization['status'] = intval($body['status']);
            }
            if(isset($body['is_distribute_person']) && in_array($body['is_distribute_person'],array(0,1))){
                $organization['is_distribute_person'] = intval($body['is_distribute_person']);
            }
            if(isset($body['is_distribute_group']) && in_array($body['is_distribute_group'],array(0,1))){
                $organization['is_distribute_group'] = intval($body['is_distribute_group']);
            }
            $this->add($organization);
            $agency_id = $this->getInsertId();
            
            CreditModel::model()->add(array(
                'supplier_id' => $agency['supply_id'],
            	'supplier_name' => $agency['supplier_name'],
                'distributor_name' => $agency['name'],
                'distributor_id' => $agency_id,
            	'add_time' => time(),
				'source'	=> 1
            ));
		
            SupplyAgencyHistoryModel::model()->add(array(
                'supply_id' => $agency['supply_id'],
                'agency_name' => $agency['name'],
                'agency_id' => $agency_id,
                'created_time' => $now,
            ));
            $this->commit();
            return $agency_id;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }

    /**
     * 批量添加供应商
     * author : yinjian
     */
    public function addSupplyBatch($new_supply,$agency,$body)
    {
        try {
            $now = time();
            $this->begin();
            foreach($new_supply as $k=>$v){
                CreditModel::model()->add(array(
                    'distributor_id' => intval($body['agency_id']),
                    'supplier_id' => $v['id'],
                    'distributor_name' => $agency['name'],
                    'supplier_name' => $v['name'],
                    'add_time' => $now,
                    'source' => 2,
                ));
            }
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }
    /**
     *验证景区密钥
     * 
     */
   public function checklandscapes( $key )
   {
   		$header[ 'pwd' ] = 'itourism-distribution-api' . ':' . 'itourism-distribution-api';
      	$url ='http://itourism-api.api.jinglvtong.com/advanced/landscapes?filter=key:equal_'.$key;
      	$tmp = Tools::curl($url,"", "", $header );
     	$poiData = json_decode( $tmp , true ) ;
      	if( $poiData[ 'data' ]  )
      	{
      		$t =  reset( $poiData[ 'data' ] );
      		return $t[ 'id' ];
      	}
      	else
      	{	
      		Lang_Msg::error('密钥不对。');
      	}
   }
}
