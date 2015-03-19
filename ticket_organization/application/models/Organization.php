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
        $now = time();
        $this->add(array(
            'type' => $organization['type'],
            'name' => $organization['name'],
            'mobile' => $organization['mobile'],
            'contact' => $organization['contact'],
            'fax' => isset($organization['fax'])?$organization['fax']:'',
            'email' => isset($organization['email'])?$organization['email']:'',
            'telephone' => isset($organization['telephone'])?$organization['telephone']:'',
            'district_id' => isset($organization['district_id'])?intval($organization['district_id']):0,
            'address' => $organization['address'],
            'business_license' => $organization['business_license'],
            'tax_license' => isset($organization['tax_license'])?$organization['tax_license']:'',
            'certificate_license' => isset($organization['certificate_license'])?$organization['certificate_license']:'',
            'agency_type' => isset($organization['agency_type'])?intval($organization['agency_type']):0,
            'verify_status' => $organization['verify_status'],
            'verify_by' => $organization['verify_status']== 'checked'?intval($organization['uid']):0,
            'verify_at' => $organization['verify_status']== 'checked'?$now:0,
            'status' => isset($organization['status'])?intval($organization['status']):0,
            'is_distribute_person' => isset($organization['is_distribute_person'])?intval($organization['is_distribute_person']):0,
            'is_distribute_group' => isset($organization['is_distribute_group'])?intval($organization['is_distribute_group']):0,
            'created_by' => isset($organization['uid'])?intval($organization['uid']):0,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        return $this->getInsertId();
    }

    public function update()
    {

    }
}
