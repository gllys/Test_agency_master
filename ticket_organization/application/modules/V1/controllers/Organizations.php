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
        (intval($this->body['status'])>1 || intval($this->body['status'])<0) && Lang_Msg::error("ERROR_REG_5");
        // 校验重复
        $organization = $this->organizationModel->search(array('name'=>$this->getPost('name')));
        $organization && Lang_Msg::error("ERROR_REG_6");
        // 分别验证
        $this->checktype($this->body);
        // verify_status枚举类型
        $verify_status = ($this->body['verify_status']!='checked' && $this->body['verify_status']!='reject')?'apply':$this->body['verify_status'];
        $this->getRequest()->setParam('verify_status', $verify_status);
        $id = $this->organizationModel->reg($this->getParams());
        !$id && Lang_Msg::error("ERROR_REG_7");
        Tools::lsJson(true,'注册成功',array('id'=>$id));
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
                !Validate::isString($this->body['business_license']) && Lang_Msg::error("ERROR_CHECKTYPE_2");
                break;
            case 'agency':
                # 分销商
                !Validate::isString($this->body['agency_type']) && Lang_Msg::error("ERROR_CHECKTYPE_3");
                !Validate::isString($this->body['is_distribute_person']) && Lang_Msg::error("ERROR_CHECKTYPE_4");
                !Validate::isString($this->body['is_distribute_group']) && Lang_Msg::error("ERROR_CHECKTYPE_5");
                (intval($this->body['agency_type'])>1 || intval($this->body['agency_type'])<0) && Lang_Msg::error("ERROR_CHECKTYPE_6");
                (intval($this->body['is_distribute_person'])>1 || intval($this->body['is_distribute_person'])<0) && Lang_Msg::error("ERROR_CHECKTYPE_7");
                (intval($this->body['is_distribute_group'])>1 || intval($this->body['is_distribute_group'])<0) && Lang_Msg::error("ERROR_CHECKTYPE_8");
                // 旅行社必须上传
                if(intval($this->body['agency_type'])==1){
                    !Validate::isString($this->body['tax_license']) && Lang_Msg::error("ERROR_CHECKTYPE_9");
                    !Validate::isString($this->body['certificate_license']) && Lang_Msg::error("ERROR_CHECKTYPE_10");
                }
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
        !Validate::isUnsignedInt($this->body['id']) && Lang_Msg::error("ERROR_SHOW_1");
        $organization = reset($this->organizationModel->search(array('id'=>$this->body['id'],'is_del'=>0)));
        !$organization && Lang_Msg::error("ERROR_SHOW_1");
        Tools::lsJson(true,'ok',$organization);
    }

    /**
     * 编辑组织
     * author : yinjian
     */
    public function editAction()
    {
        
    }
}