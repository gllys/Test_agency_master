<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-6-3
 * Time: 上午10:12
 */

class ChannelorgController extends Base_Controller_Api
{

    /**
     * 渠道机构列表
     * author : zqf
     */
    public function listsAction()
    {
        //empty($this->body['source']) && Lang_Msg::error('来源ID必填');
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
        $data['data'] = ChannelOrganizationModel::model()->search($where, $this->getFields(),$this->getSortRule('created_at'), $this->limit);
        $orgs = array();
        if (!empty($data['data'])) {
            $orgIds = array();
            foreach ($data['data'] as $key=>$val) {
                !empty($val['ext']) && $data['data'][$key]['ext'] = unserialize($val['ext']);
                $orgIds[] = $val['organization_id'];
            }
            if(!empty($orgIds)) {
                $orgIds = array_unique($orgIds);
                $orgs = OrganizationModel::model()->search(array('id|in'=>$orgIds),'id,name');
            }
        }

        $data['organizations'] = [];
        if(!empty($orgs)) {
            foreach($orgs as $v) {
                $data['organizations'][$v['id']] = $v['name'];
            }
        }
        if (!empty($data['data'])) {
            foreach ($data['data'] as $key=>$val) {
                $data['data'][$key]['org_name'] = $data['organizations'][$val['organization_id']];
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
     * author : zqf
     */
    public function detailAction()
    {
        $where = array('deleted_at'=>0);
        $id = intval($this->body['id']);
        empty($id) && Tools::lsJson(false,'记录ID不能为空');

        // 数据
        $data = ChannelOrganizationModel::model()->getById($id);
        if (!empty($data)) {
            !empty($data['ext']) && $data['ext'] = unserialize($data['ext']);
        }

        $result = empty($data)? []: $data;
        Lang_Msg::output($result);
    }

    /**
     * 新增渠道机构
     * author : zqf
     */
    public function addAction()
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
     * 渠道机构修改
     * author : zqf
     */
    public function updateAction()
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




}