<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 05/20/15
 * Time: 11:40 AM
 */
class ChannelController extends Base_Controller_Api {
    /**
     * 列表
     * author : yinjian
     */
    public function listsAction()
    {
        $args = array('deleted_at'=>0);
        if(isset($this->body['id']) && $this->body['id']) $args['id|in'] = explode(',',$this->body['id']);
        if(isset($this->body['name']) && $this->body['name']) $args['name|like'] = array("%".trim($this->body['name'])."%");
        if(isset($this->body['template_name']) && $this->body['template_name']) $args['template_name|like'] = array("%".trim($this->body['template_name'])."%");
        if(isset($this->body['author']) && $this->body['author']) $args['author|like'] = array("%".trim($this->body['author'])."%");
        if(isset($this->body['op_user']) && $this->body['op_user']) $args['op_user|like'] = array("%".trim($this->body['op_user'])."%");
        if(isset($this->body['op_uid']) && $this->body['op_uid']) $args['op_uid'] = intval($this->body['op_uid']);
        if(isset($this->body['remark']) && $this->body['remark']) $args['remark'] = trim($this->body['remark']);
        if(isset($this->body['status']) && $this->body['status']) $args['status'] = trim($this->body['status']);
        if(isset($this->body['created_by']) && $this->body['created_by']) $args['created_by'] = trim($this->body['created_by']);
        if(isset($this->body['is_template_name_empty'])) {
            if($this->body['is_template_name_empty']){
                $args['template_name'] = '';
            }else{
                $args['template_name|<>'] = '';
            }
        }

        if(isset($this->body['created_at_start']) || isset($this->body['created_at_end'])){
            $created_at_start = Validate::isUnixName($this->body['created_at_start'])?strtotime($this->body['created_at_start']):0;
            $created_at_end = Validate::isUnixName($this->body['created_at_end'])?strtotime($this->body['created_at_end']." 23:59:59"):time();
            $args['created_at|between'] = array($created_at_start,$created_at_end);
        }

        $this->count = ChannelModel::model()->countResult($args);
        $this->pagenation();
        if($this->body['show_all'] == 1){
            $this->limit = null;
        }
        $data['data'] = ChannelModel::model()->search($args,$this->getFields(),$this->getSortRule(),$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }

    /**
     * 添加
     * author : yinjian
     */
    public function addAction()
    {
        $args = array();
        $args['created_at'] = $args['updated_at'] = time();
        if(isset($this->body['name']) && $this->body['name']) $args['name'] = trim($this->body['name']);
        if(isset($this->body['template']) && $this->body['template']) $args['template'] = trim($this->body['template']);
        if(isset($this->body['template_name'])) $args['template_name'] = trim($this->body['template_name']);
        if(isset($this->body['author']) && $this->body['author']) $args['author'] = trim($this->body['author']);
        if(isset($this->body['op_user']) && $this->body['op_user']) $args['op_user'] = trim($this->body['op_user']);
        if(isset($this->body['remark']) && $this->body['remark']) $args['remark'] = trim($this->body['remark']);
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $args['status'] = trim($this->body['status']);
        if(isset($this->body['op_uid']) && $this->body['op_uid']) $args['op_uid'] = trim($this->body['op_uid']);
        if(isset($this->body['created_by']) && $this->body['created_by']) $args['created_by'] = trim($this->body['created_by']);

        $channel = new ChannelModel();
        $r = $channel->add($args);
        !$r && Lang_Msg::error('添加失败');
        Lang_Msg::output(array('id'=>$channel->getInsertId()));
    }

    /**
     * 编辑
     * author : yinjian
     */
    public function editAction()
    {
        !Validate::isUnsignedId($this->body['id']) && Lang_Msg::error('id不能为空');
        $channel = ChannelModel::model()->get(array('id'=>intval($this->body['id']),'deleted_at'=>0));
        !$channel && Lang_Msg::error('该渠道不存在');

        $args = array();
        $args['updated_at'] = time();
        if(isset($this->body['name']) && $this->body['name']) $args['name'] = trim($this->body['name']);
        if(isset($this->body['template'])) {
            $args['template'] = trim($this->body['template']);
            $args['created_at'] = $args['updated_at'];
        }
        if(isset($this->body['template_name'])) $args['template_name'] = trim($this->body['template_name']);
        if(isset($this->body['author']) && $this->body['author']) $args['author'] = trim($this->body['author']);
        if(isset($this->body['op_user']) && $this->body['op_user']) $args['op_user'] = trim($this->body['op_user']);
        if(isset($this->body['remark'])) $args['remark'] = trim($this->body['remark']);
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $args['status'] = trim($this->body['status']);
        if(isset($this->body['op_uid']) && $this->body['op_uid']) $args['op_uid'] = trim($this->body['op_uid']);
        if(isset($this->body['deleted_at']) && $this->body['deleted_at']) $args['deleted_at'] = $args['updated_at'];

        $r = ChannelModel::model()->updateByAttr($args,array('id'=>$channel['id']));
        !$r && Lang_Msg::error('修改失败');
        Lang_Msg::output();
    }
}