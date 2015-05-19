<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 10/14/14
 * Time: 4:02 PM
 */
class OrganizationsController extends Base_Controller_Api
{
    /**
     * @var null
     */
    public $organizationModel=null;
    /**
     * 子地区
     * @var array
     */
    static $tree = array();

    /**
     * 初始设置
     * Author : yinjian
     */
    public function init()
    {
        parent::init();
        $this->organizationModel = new OrganizationModel();
    }
    /**
     * 注册分销商和供应商
     * @author yinjian
     * @date   2014-10-15
     * @return [type]     [description]
     */
    public function regAction()
    {
        // 通用验证参数
        !Validate::isString($this->body['type']) && Lang_Msg::error("ERROR_REG_1");
        !Validate::isString($this->body['name']) && Lang_Msg::error("ERROR_REG_2");
        !Validate::isMobilePhone($this->body['mobile']) && Lang_Msg::error("ERROR_REG_3");
        !Validate::isString($this->body['contact']) && Lang_Msg::error("ERROR_REG_4");

        if(isset($this->body['partner_type']) && $this->body['type']=='supply' && $this->body['supply_type']==1) {
            if($this->body['partner_type']>0 && empty($this->body['partner_identify'])) {
                Lang_Msg::error('缺少合作伙伴识别信息');
            }
        }

        //判断是否外部调用接口注册
        $need_check = false;
        if( $this->body[ 'ota_type' ] == 'weixin' )
        {
        	$need_check = true;
        }
        (intval($this->body['status'])>1 || intval($this->body['status'])<0) && Lang_Msg::error("ERROR_REG_5");
        // 校验重复
        $organization = $this->organizationModel->search(array('name'=>$this->getPost('name')));
        $organization && Lang_Msg::error("ERROR_REG_6");
        // 不同类型分别验证
        $this->checktype($this->body);
        // verify_status枚举类型
        $verify_status = ($this->body['verify_status']!='checked' && $this->body['verify_status']!='reject')?'apply':$this->body['verify_status'];
        $this->getRequest()->setParam('verify_status', $verify_status);
        // 地区筛选 同时添加省市区
        if(isset($this->body['district_id']) && intval($this->body['district_id'])>0){
            $district_model = new DistrictModel();
            $district = $district_model->getById(intval($this->body['district_id']));
            !$district && Lang_Msg::error("ERROR_REG_7");
            $city = $district_model->getById($district['parent_id']);
            $this->getRequest()->setParam('city_id', $city['id']);
            $this->getRequest()->setParam('province_id', $city['parent_id']);
        }
        $id = $this->organizationModel->reg($this->getParams());
        !$id && Lang_Msg::error("ERROR_REG_8");
        if( $need_check )
        {     
        	 !Validate::isString($this->body['poikey']) && Lang_Msg::error("没有授权码");
        	 Tools::lsJson(true,'注册成功',array('id'=>$id, 'landscape_id' =>  OrganizationModel::model()->checklandscapes( $this->body[ 'poikey' ]), 'agency_id' => 1));
        }
        else
        {
        	 Tools::lsJson(true,'注册成功',array('id'=>$id ) );
        }
    }

    /**
     * 分销和供应商独立必须字段
     * Author : yinjian
     */
    private function checktype()
    {
        switch ($this->body['type']) {
            case 'supply':
                # 供应商
                !Validate::isString($this->body['address']) && Lang_Msg::error("ERROR_CHECKTYPE_1");
                !Validate::isUnsignedId($this->body['supply_type']) && Lang_Msg::error("ERROR_CHECKTYPE_12");
//                !Validate::isString($this->body['business_license']) && Lang_Msg::error("ERROR_CHECKTYPE_2");
                break;
            case 'agency':
                # 分销商
                !Validate::isUnsignedId($this->body['agency_type']) && Lang_Msg::error("ERROR_CHECKTYPE_3");
                !Validate::isUnsignedId($this->body['is_distribute_person']) && Lang_Msg::error("ERROR_CHECKTYPE_4");
                !Validate::isUnsignedId($this->body['is_distribute_group']) && Lang_Msg::error("ERROR_CHECKTYPE_5");
                (intval($this->body['agency_type'])>1 || intval($this->body['agency_type'])<0) && Lang_Msg::error("ERROR_CHECKTYPE_6");
                (intval($this->body['is_distribute_person'])>1 || intval($this->body['is_distribute_person'])<0) && Lang_Msg::error("ERROR_CHECKTYPE_7");
                (intval($this->body['is_distribute_group'])>1 || intval($this->body['is_distribute_group'])<0) && Lang_Msg::error("ERROR_CHECKTYPE_8");
                // 旅行社必须上传
                /*if(intval($this->body['agency_type'])==1){
                    !Validate::isString($this->body['tax_license']) && Lang_Msg::error("ERROR_CHECKTYPE_9");
                    !Validate::isString($this->body['certificate_license']) && Lang_Msg::error("ERROR_CHECKTYPE_10");
                }*/
                break;
            default:
                Lang_Msg::error("ERROR_CHECKTYPE_11");
                break;
        }
    }

    /**
     * 单个旅行社信息
     * author : yinjian
     */
    public function showAction()
    {
        $where = array('is_del'=>0);
        $id = intval($this->body['id']);
        $id && $where['id'] = $id;

        $account_taobao = trim(Tools::safeOutput($this->body['account_taobao']));
        $account_taobao && $where['account_taobao'] = $account_taobao;
        
        !$id && !$account_taobao && Lang_Msg::error("ERROR_SHOW_1");
        $organization = $this->organizationModel->get($where,$this->getFields());
        !$organization && Lang_Msg::error("ERROR_SHOW_2");
        Tools::lsJson(true,'ok',$organization);
    }

    /**
     * 机构列表筛选
     * author : yinjian
     */
    public function listAction()
    {
        // 类型验证
        if($this->body['type']) $where['type'] = $this->body['type'];
        // 创建时间段
        if($this->body['created_at']) {
            $created_at = explode(' - ',$this->body['created_at']);
            $start_at = intval(strtotime(reset($created_at).' 00:00:00'));
            $end_at = intval(strtotime(end($created_at).'  23:59:59'));
            ($end_at<$start_at || !Validate::isUnsignedInt($start_at) || !Validate::isUnsignedInt($end_at)) && Lang_Msg::error("ERROR_LIST_1");
            $where['created_at|between'] = array($start_at,$end_at);
        }
        if($this->body['province_id'] && !$this->body['city_id'] && !$this->body['district_id']){
            // 省份筛选
//            $this->getDistrictId($this->body['province_id']);
//            $where['district_id|in'] = self::$tree?self::$tree:array($this->body['province_id']);
            $where['province_id|in'] = explode(',',$this->body['province_id']);
        }elseif($this->body['city_id'] && !$this->body['district_id']){
            // 市筛选
//            $this->getDistrictId($this->body['city_id']);
//            $where['district_id|in'] = self::$tree?self::$tree:array($this->body['city_id']);
            $where['city_id|in'] = explode(',',$this->body['city_id']);
        }elseif(isset($this->body['district_id']) && Validate::isUnsignedId($this->body['district_id'])){
            // 区筛选
            $where['district_id|in'] = explode(',',$this->body['district_id']);
        }
        if(intval($this->body['agency_id'])>0) $where['agency_id'] = intval($this->body['agency_id']);
        if(intval($this->body['supply_id'])>0) $where['supply_id'] = intval($this->body['supply_id']);
        if(intval($this->body['landscape_id'])) $where['landscape_id'] = intval($this->body['landscape_id']);
        if($this->body['status']) $where['status'] = $this->body['status'];
        if(isset($this->body['id'])) $where['id|in'] = explode(',',$this->body['id']);
        if($this->body['name']) $where['name|like'] = array('%'.$this->body['name'].'%');
        if($this->body['verify_status']) $where['verify_status'] = $this->body['verify_status'];
		
        $where['is_del'] = 0;
        Tools::walkArray($where,'trim');
        // 分页
        $show_all = intval($this->body['show_all']);
        if($show_all>0) {
            $data['data'] = $this->organizationModel->setCd(0)->search($where,$this->getFields(),$this->getSortRule());
        } else {
            $count = reset($this->organizationModel->setCd(0)->search($where,'count(*) as count'));
            $this->count = $count['count'];
            $this->pagenation();
            $data['data'] = $this->count?$this->organizationModel->setCd(0)->search($where,$this->getFields(),$this->getSortRule(),$this->limit):array();
        }

        if($show_all>0) {
            $data['pagination'] = array(
                'count'=> is_array($data['data']) ? count($data['data']):0,
            );
        } else {
            $data['pagination'] = array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items' => $this->items,
                'total' => $this->total,
            );
        }
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 获取地区id 待改进@TODO
     * author : yinjian
     * @param $district_id
     * @return array
     */
    private function getDistrictId($district_id)
    {
        $district = new DistrictModel();
        $ids = array_keys($district->search(array('parent_id'=>$district_id)));
        if($ids){
            foreach($ids as $key => $val){
                if($tree_id = $this->getDistrictId($val)){
                    self::$tree = array_merge(self::$tree,$tree_id);
                }
            }
            return $ids;
        }
    }

    /**
     * 编辑组织
     * author : yinjian
     */
    public function editAction()
    {
        !Validate::isUnsignedInt($this->body['id']) && Lang_Msg::error("ERROR_EDIT_1");
        $partner_type = intval($this->body['partner_type']); //供应商为景区角色时的合作伙伴类型，0景旅通（默认）,1大漠

        $organization = reset($this->organizationModel->search(array('id'=>$this->body['id'],'is_del'=>0)));
        !$organization && Lang_Msg::error("ERROR_EDIT_2");
        // 通用修改的数据库字段参数
        $data = array_intersect_key($this->body,array_flip(array(
            'name','mobile','contact','fax','province_id','city_id','district_id','abbreviation',
            'email','telephone','address','description','logo','status',
            'business_license','tax_license','certificate_license','agency_type','supply_type',
            'is_distribute_person','is_distribute_group','is_credit','is_balance','is_del','landscape_id',
            'partner_type','partner_identify'
        )));
        // 手机号码
        if(isset($this->body['mobile']) && !Validate::isMobilePhone($this->body['mobile'])){
            Lang_Msg::error("ERROR_EDIT_3");
        }
        // 联系人
        if(isset($this->body['contact']) && !Validate::isString($this->body['contact'])){
            Lang_Msg::error("ERROR_EDIT_4");
        }
        // 名字
        if(isset($this->body['name']) && $this->organizationModel->search(array('name'=>$this->body['name'],'id|<>'=>$organization['id']))) {
            Lang_Msg::error("ERROR_EDIT_5");
        }elseif(isset($this->body['name']) && !Validate::isString($this->body['name'])) {
            Lang_Msg::error("ERROR_EDIT_6");
        }
        // 用户id
        !Validate::isUnsignedInt($this->body['uid']) && Lang_Msg::error("ERROR_EDIT_7");
        // 审核
        if($this->body['verify_status']=='checked') {
            $data['verify_status'] = $this->body['verify_status'];
            $data['verify_by'] = $this->body['uid'];
            $data['verify_at'] = $this->now;
        }elseif(in_array($this->body['verify_status'],array('apply','reject'))){
            $data['verify_status'] = $this->body['verify_status'];
        }elseif(isset($this->body['verify_status']) && !in_array($this->body['verify_status'],array('apply','reject'))){
            Lang_Msg::error("ERROR_EDIT_8");
        }
        // 状态
        if(isset($this->body['status']) && !in_array($this->body['status'],array('0','1'))){
            Lang_Msg::error("ERROR_EDIT_9");
        }
        // 分销
        if($organization['type']=="agency"){
            // 营业执照不得为空
            /*if(isset($this->body['business_license']) && !Validate::isString($this->body['business_license'])){
                Lang_Msg::error("ERROR_EDIT_10");
            }*/
            // 旅行社
            if(isset($this->body['agency_type']) && !in_array($this->body['agency_type'],array('0','1'))){
                Lang_Msg::error("ERROR_EDIT_11");
            }
            // 分销商中旅行社必须上传三证
            /*if(intval($this->body['agency_type']) == 1){
                if(isset($this->body['tax_license']) && !Validate::isString($this->body['tax_license'])){
                    Lang_Msg::error("ERROR_EDIT_12");
                }
                if(isset($this->body['certificate_license']) && !Validate::isString($this->body['certificate_license'])){
                    Lang_Msg::error("ERROR_EDIT_13");
                }
            }*/
            // 平台散客票分销权限
            if(isset($this->body['is_distribute_person']) && !in_array($this->body['is_distribute_person'],array('0','1'))){
                Lang_Msg::error("ERROR_EDIT_14");
            }
            // 平台团体票分销权限
            if(isset($this->body['is_distribute_group']) && !in_array($this->body['is_distribute_group'],array('0','1'))){
                Lang_Msg::error("ERROR_EDIT_15");
            }
        }elseif($organization['type']=="supply"){
            // 详细地址
            if(isset($this->body['address']) && !Validate::isString($this->body['address'])){
                Lang_Msg::error("ERROR_EDIT_16");
            }
        }
        // 删除
        if(isset($this->body['is_del']) && !in_array($this->body['is_del'],array('0','1'))){
            Lang_Msg::error("ERROR_EDIT_17");
        }
        //供应商为景区角色时的合作伙伴类型，0景旅通（默认）,1大漠
        if(isset($this->body['partner_type'])) {
            if( isset($data['supply_type']) ) {
                $data['partner_type'] = $data['supply_type']>0 ? $partner_type : 0;
            } else {
                $data['partner_type'] = $organization['supply_type']>0 ? $partner_type : 0;
            }
        }
        $data['updated_at'] = $this->now;
        Tools::walkArray($data,'trim');
        $res = $this->organizationModel->modify($this->body['id'],$data);
        !$res && Lang_Msg::error("ERROR_EDIT_18");
        Tools::lsJson(true,'操作成功');
    }

    /**
     * 绑定直供景区id
     * author : yinjian
     */
    public function bindSpecLanAction()
    {
        intval($this->body['landscape_id'])<1 && Lang_Msg::error("ERROR_bindSpecLan_1_景区id缺失");
        intval($this->body['supply_id'])<1 && Lang_Msg::error("ERROR_bindSpecLan_2_供应商id缺失");
        // 确认供应商id存在
        !($organization = reset($this->organizationModel->search(array('id' => intval($this->body['supply_id']),'type'=>'supply','isdel'=>0)))) && Lang_Msg::error("ERROR_bindSpecLan_3_不存在该供应商");
        // 已经绑定提示解除绑定
        $organization['landscape_id']>0 && Lang_Msg::error("ERROR_bindSpecLan_4_供应商当前已绑定直供景区");
        ($supply = reset($this->organizationModel->search(array('landscape_id' => intval($this->body['landscape_id']),'isdel'=>0)))) && Lang_Msg::error("ERROR_bindSpecLan_5_该景区已绑定供应商".$supply['name'],array('name'=>$supply['name']));
        // 直接操作
        $res = $this->organizationModel->updateById(intval($this->body['supply_id']),array('landscape_id'=>intval($this->body['landscape_id'])));
        !$res && Lang_Msg::error("ERROR_bindSpecLan_6_绑定直供景区失败");
        Tools::lsJson(true,'操作成功');
    }

    /**
     * 供应商解绑直供景区
     * author : yinjian
     */
    public function unbindSpecLanAction()
    {
        intval($this->body['supply_id'])<1 && Lang_Msg::error("ERROR_unbindSpecLan_1_供应商id缺失");
        !$this->organizationModel->search(array('id' => intval($this->body['supply_id']),'type'=>'supply','isdel'=>0)) && Lang_Msg::error("ERROR_unbindSpecLan_2_不存在该供应商");
        $res = $this->organizationModel->updateById(intval($this->body['supply_id']),array('landscape_id'=>0));
        !$res && Lang_Msg::error("ERROR_unbindSpecLan_3_解绑直供景区失败");
        Tools::lsJson(true,'操作成功');
    }

    /**
     * 绑定直营分销商到供应商
     * author : yinjian
     */
    public function bindSpecAgencyAction()
    {
        intval($this->body['agency_id'])<1 && Lang_Msg::error("ERROR_bindSpecAgency_1_分销商id缺失");
        intval($this->body['supply_id'])<1 && Lang_Msg::error("ERROR_bindSpecAgency_2_供应商id缺失");
        // 确认两者id存在
        !$this->organizationModel->search(array('id' => intval($this->body['agency_id']),'type'=>'agency','isdel'=>0)) && Lang_Msg::error("ERROR_bindSpecAgency_3_不存在该分销商");
        !$this->organizationModel->search(array('id' => intval($this->body['supply_id']),'type'=>'supply','isdel'=>0)) && Lang_Msg::error("ERROR_bindSpecAgency_4_不存在该供应商");
        // 该分销商已绑定供应商
        ($supply =reset($this->organizationModel->search(array('agency_id' => intval($this->body['agency_id']),'isdel'=>0)))) && Lang_Msg::error("ERROR_bindSpecAgency_5_该分销商已绑定直属供应商".$supply['name'],array('name'=>$supply['name']));
        ($organization =reset($this->organizationModel->search(array('supply_id' => intval($this->body['supply_id']),'isdel'=>0)))) && Lang_Msg::error("ERROR_bindSpecAgency_5_该供应商已绑定直营分销商".$supply['name'],array('name'=>$organization['name']));
        // 直接事务操作
        $res = $this->organizationModel->bindSpecAgency(intval($this->body['supply_id']),intval($this->body['agency_id']));
        !$res && Lang_Msg::error("ERROR_bindSpecAgency_4_绑定直营分销商失败");
        Tools::lsJson(true,'操作成功');
    }

    /**
     * 解绑直营分销商到供应商
     * author : yinjian
     */
    public function unBindSpecAgencyAction()
    {
        intval($this->body['supply_id'])<1 && Lang_Msg::error("ERROR_unBindSpecAgency_1_供应商id缺失");
        !($organization = reset($this->organizationModel->search(array('id' => intval($this->body['supply_id']),'type'=>'supply','isdel'=>0)))) && Lang_Msg::error("ERROR_unBindSpecAgency_2_不存在该供应商");
        $organization['agency_id'] == 0 && Lang_Msg::error("ERROR_unBindSpecAgency_3_供应商当前未绑定直营分销");
        // 事务操作
        $res = $this->organizationModel->unBindSpecAgency(intval($this->body['supply_id']));
        !$res && Lang_Msg::error("ERROR_unBindSpecAgency_4_解绑直供景区失败");
        Tools::lsJson(true,'操作成功');
    }

    /**
     * 绑定分销商到供应商
     * author : yinjian
     */
    public function bind_agencyAction()
    {	
        // 通用验证参数
        !Validate::isString($this->body['name']) && Lang_Msg::error("公司名称不能为空");
        !Validate::isMobilePhone($this->body['mobile']) && Lang_Msg::error("手机号码格式不正确");
        !Validate::isString($this->body['contact']) && Lang_Msg::error("联系人不能为空");
        // 校验重复
        $organization = $this->organizationModel->search(array('name'=>$this->getPost('name')));
        $organization && Lang_Msg::error("公司名称已存在，请重新输入");
        //详细地址
        !Validate::isString($this->body['address']) && Lang_Msg::error("详细地址不能为空");
        $agency['name'] = $this->body['name'];
        $agency['mobile'] = $this->body['mobile'];
        $agency['contact'] = $this->body['contact'];
        $agency['address'] = $this->body['address'];
        $agency['fax'] = trim($this->body['fax']);
        $agency['telephone'] = trim($this->body['telephone']);
        if(isset($this->body['agency_type']) && in_array(intval($this->body['agency_type']),array(0,1))){
            $agency['agency_type'] = intval($this->body['agency_type']);
        }
        // 地区筛选 同时添加省市区
        if(isset($this->body['district_id']) && intval($this->body['district_id'])>0){
            $district_model = new DistrictModel();
            $district = $district_model->getById(intval($this->body['district_id']));
            !$district && Lang_Msg::error("该地区暂不支持");
            $city = $district_model->getById($district['parent_id']);
            $agency['district_id'] = intval($this->body['district_id']);
            $agency['city_id'] = intval($city['id']);
            $agency['province_id'] = intval($city['parent_id']);
        }
        // 供应商检测@todo
        !Validate::isUnsignedId($this->body['supply_id']) && Lang_Msg::error("供应商id缺失");
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error("操作用户id缺失");
        $agency['supply_id'] = intval($this->body['supply_id']);
        $tmp = OrganizationModel::model()->search( array( 'id' => $agency['supply_id'], 'type' =>'supply' ,'is_del' => 0) );
        if( !$tmp ) Lang_Msg::error("供应商类型不对" );
        $agency['supplier_name'] = $tmp[ key( $tmp ) ][ 'name' ];
        $agency['uid'] = $this->body['uid'];
        isset($this->body['business_license']) && $agency['business_license'] = $this->body['business_license'];
        isset($this->body['tax_license']) && $agency['tax_license'] = $this->body['tax_license'];
        isset($this->body['certificate_license']) && $agency['certificate_license'] = $this->body['certificate_license'];
        $id = $this->organizationModel->addAgency($agency,$this->body);
        !$id && Lang_Msg::error("添加失败");
        Tools::lsJson(true,'添加成功',array('id'=>$id));
    }

    /**
     * 获取合作分销商
     * author : yinjian
     */
    public function getlistAction()
    {
        !Validate::isUnsignedId($this->body['supply_id']) && Lang_Msg::error("供应商id缺失");
        $where = array('supplier_id'=>intval($this->body['supply_id']));
        // 分页
        if(intval($this->body['show_all'])){
            $data['data'] = SupplyAgencyModel::model()->search($where);
            $data['pagination'] = array(
                'count'=>count($data['data']),
            );
        } else {
            $count = reset(SupplyAgencyModel::model()->search($where,'count(*) as count'));
            $this->count = $count['count'];
            $this->pagenation();
            $data['data'] = SupplyAgencyModel::model()->search($where,'*',null,$this->limit);

            $data['pagination'] = array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items' => $this->items,
                'total' => $this->total,
            );
        }
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 分销商批量添加到供应商
     * author : yinjian
     */
    public function bind_agency_batchAction()
    {
        !Validate::isString($this->body['supply_ids']) && Lang_Msg::error("供应商id缺失");
        !Validate::isUnsignedId($this->body['agency_id']) && Lang_Msg::error("分销商id缺失");
        $relations = CreditModel::model()->search(array('distributor_id'=>intval($this->body['agency_id'])));
        // 待判断
        $agency = reset(OrganizationModel::model()->search(array('id'=>$this->body['agency_id'])));
        !$agency && Lang_Msg::error("分销商未找到");
        foreach($relations as $key=>$val){
            $supply_ids[] = $val['supplier_id'];
        }
        if(empty($supply_ids)){
            $new_supply_ids = explode(',',$this->body['supply_ids']);
        }else{
            $new_supply_ids = array_diff(explode(',',$this->body['supply_ids']),$supply_ids);
        }
        !$new_supply_ids && Tools::lsJson(true,'ok');
        $new_supply = OrganizationModel::model()->search(array('id|in'=>$new_supply_ids));
        $res = OrganizationModel::model()->addSupplyBatch($new_supply,$agency,$this->body);
        !$res && Lang_Msg::error("添加失败");
        Tools::lsJson(true,'ok');
    }

    /**
    * 按供应商IDS获取记录
    */
    public function bysupplierAction()
    {
        $supplier_ids = $this->body['supplier_ids'];
        (!is_array($supplier_ids) && preg_match("/^[\d,]+$/",$supplier_ids)) && $supplier_ids = explode(',',$supplier_ids);
        $where = array('supplier_id|in'=>$supplier_ids);
        $data = SupplyAgencyModel::model()->search($where,$this->getFields(),null,null);
        $res = array();
        if($data){
            foreach($data as $v){
                $res[$v['supplier_id']."_".$v['distributor_id']] = $v;
            }
        }
        Tools::lsJson(true,'ok',$res);
    }

    /**
     * 按分销售id获取绑定记录API
     */
    public function bydistributorAction()
    {
        $distributor_id = intval($this->body['distributor_id']);
        $data = SupplyAgencyModel::model()->search(array('distributor_id'=>$distributor_id),$this->getFields());
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 新增渠道机构
     * author : yinjian
     */
    public function orgAddAction()
    {
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户id不存在');
        !Validate::isString($this->body['account']) && Lang_Msg::error('绑定账号不能为空');
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error('机构不能为空');
        !in_array($this->body['status'],array(0,1)) && Lang_Msg::error('状态不正确');
		
        empty($this->body['source']) && Lang_Msg::error('来源ID必填');
        $organization_id = intval($this->body['organization_id']);
        $organization = OrganizationModel::model()->get(array('id'=>$organization_id,'is_del'=>0));
        !$organization && Lang_Msg::error('机构不存在');
		$ext = json_decode($this->body['ext'],true);
		(!$ext || !is_array($ext)) && $ext = [];
		
        $taobaoOrg = ChannelOrganizationModel::model()->search(array('organization_id'=>$organization_id,'source'=>$this->body['source'],'deleted_at'=>0));
        $taobaoOrg &&  Lang_Msg::error('该机构已绑定账号');
        $res = ChannelOrganizationModel::model()->add(array(
            'account'=>trim($this->body['account']),
            'ext'=>serialize($ext),
            'organization_id' => intval($this->body['organization_id']),
			'source' => intval($this->body['source']),
            'status' => intval($this->body['status']),
            'created_by'=> intval($this->body['uid']),
            'created_at' => time(),
            'updated_at' => time(),
        ));
        Lang_Msg::output();
    }

    /**
     * 渠道机构列表
     * author : yinjian
     */
    public function orgListAction()
    {
        empty($this->body['source']) && Lang_Msg::error('来源ID必填');
        $where = array('deleted_at'=>0);
        if(isset($this->body['ids']) && $this->body['ids']) $where['id|in'] = explode(',',$this->body['ids']);
        if(isset($this->body['organization_id']) && $this->body['organization_id']) $where['organization_id|in'] = explode(',',$this->body['organization_id']);
        if(isset($this->body['account']) && $this->body['account']) $where['account|like'] = array('%'.$this->body['account'].'%');
        if(isset($this->body['source'])) $where['source'] = intval($this->body['source']);
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $where['status'] = intval($this->body['status']);
        $count = reset(ChannelOrganizationModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        $data['data'] = ChannelOrganizationModel::model()->search($where, '*', 'created_at desc', $this->limit);
		if (!empty($data['data'])) {
			foreach ($data['data'] as $key=>$val) {
				!empty($val['ext']) && $data['data'][$key]['ext'] = unserialize($val['ext']);
			}
		}
		$sql = 'select id,name from '.OrganizationModel::model()->getTable().' where id in (select distinct organization_id from '. ChannelOrganizationModel::model()->getTable().' where source='.$where['source'].')';
		$orgs= OrganizationModel::model()->db->query($sql);
		$data['organizations'] = [];
		foreach($orgs as $v) {
			$data['organizations'][$v['id']] = $v['name'];
		}
		if (!empty($data['data'])) {
			foreach ($data['data'] as $key=>$val) {
				$data['data'][$key]['name'] = $data['organizations'][$val['organization_id']];
			}
		}
		
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }
	
    /**
     * 渠道机构详情
     * author : yinjian
     */
    public function orgDetailAction()
    {
        $where = array('deleted_at'=>0);
        if(isset($this->body['ids']) && $this->body['ids']) $where['id|in'] = explode(',',$this->body['ids']);
        if(isset($this->body['organization_id']) && $this->body['organization_id']) $where['organization_id|in'] = explode(',',$this->body['organization_id']);
        if(isset($this->body['account']) && $this->body['account']) $where['account|like'] = array('%'.$this->body['account'].'%');
        if(isset($this->body['source'])) $where['source'] = intval($this->body['source']);
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $where['status'] = intval($this->body['status']);
		
        // 数据
        $data = ChannelOrganizationModel::model()->search($where, '*', 'created_at desc', 1);
		if (!empty($data)) {
			foreach ($data as $key=>$val) {
				!empty($val['ext']) && $data[$key]['ext'] = unserialize($val['ext']);
			}
		}

		$result = empty($data)? []: current($data);
        Lang_Msg::output($result);
    }

    /**
     * 渠道机构修改
     * author : yinjian
     */
    public function orgUpdateAction()
    {
        !Validate::isUnsignedId($this->body['id']) && Lang_Msg::error('id不存在');
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户id不存在');
        $id = intval($this->body['id']);
        $taobaoOrg = reset(ChannelOrganizationModel::model()->search(array('id'=>$id,'deleted_at'=>0)));
        !$taobaoOrg && Lang_Msg::error('机构绑定不存在');
        $data['updated_at'] = time();
        if(isset($this->body['organization_id'])) {
            $data['organization_id'] = intval($this->body['organization_id']);
            $taobaoOrg_isset = ChannelOrganizationModel::model()->search(array('id|<>'=>$id,'deleted_at'=>0,'organization_id'=>$data['organization_id']));
            $taobaoOrg_isset && Lang_Msg::error('该机构已绑定账号');
        }
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $data['status'] = intval($this->body['status']);
        if(Validate::isString($this->body['account'])) $data['account'] = trim($this->body['account']);
        if(isset($this->body['deleted_at']) && $this->body['deleted_at']) $data['deleted_at'] = time();
		
		$ext = json_decode($this->body['ext'], true);
		if (isset($ext) && is_array($ext)) {
			$data['ext'] = serialize($ext);
		}
        try {
            $res = ChannelOrganizationModel::model()->updateByAttr($data, array('id' => $id));
        }catch (Exception $e){
            Lang_Msg::error('更新失败');
        }
        Lang_Msg::output();
    }

    //按名称查询机构列表，zqf 2015-03-16
    public function listByNameAction(){
        $name = trim(Tools::safeOutput($this->body['name']));
        if(!$name) Lang_Msg::output(array());
        $where = array('name|like'=>array("%{$name}%"));
        $type = in_array($this->body['type'],array('supply','agency')) ? $this->body['type']:'';
        $type && $where['type'] = $type;
        $data = OrganizationModel::model()->search($where,$this->getFields());
        Lang_Msg::output($data);
    }

}
