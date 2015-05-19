<?php

/**
 * Class SupplyAgencyModel
 */
class SupplyAgencyModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'supply_agency';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|SupplyAgencyModel|';
    
    public function getTable() {
        return $this->tblname;
    }

    /**
     * 供应商绑定直营分销商，分销商绑定供应商
     * author : yinjian
     */
    public function bindSpecAgency($supply_id,$agency_id)
    {
        try {
            $this->begin();
            // 更新供应商
            $this->updateById($supply_id,array('agency_id'=>$agency_id));
            // 更新分销商
            $this->updateById($agency_id,array('supply_id'=>$supply_id));
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }

    /**
     * 同时解绑供应商和分销商之间的关系
     * author : yinjian
     */
    public function unBindSpecAgency($supply_id)
    {
        try {
            $this->begin();
            // 更新供应商
            $this->updateById($supply_id,array('agency_id'=>0));
            // 更新分销商
            $this->updateByAttr(array('supply_id'=>0),array('supply_id'=>$supply_id));
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }
}
