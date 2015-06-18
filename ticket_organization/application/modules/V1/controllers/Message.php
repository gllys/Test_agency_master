<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/11/19
 * Time: 11:25
 */
class MessageController extends Base_Controller_Api
{
    // 新增消息
    public function addAction()
    {
        $message = array();
        $message['updated_at'] = $message['created_at'] = time();
        // 参数校验
        !in_array($this->body['sms_type'], array(0, 1, 2)) && Lang_Msg::error('错误的消息类型');
        !Validate::isString($this->body['content']) && Lang_Msg::error('消息内容不能为空');
        !in_array($this->body['receiver_organization_type'], array(0, 1, 2, 3, 4,5)) && Lang_Msg::error('接收机构类型不正确');
        !in_array($this->body['send_source'], array(0, 1, 2)) && Lang_Msg::error('发送来源不正确');
        // 机构发机构 判断@TODO
        $message['sms_type'] = intval($this->body['sms_type']);
        $message['content'] = trim($this->body['content']);
        $message['title'] = trim($this->body['title']);
        $message['send_user'] = intval($this->body['send_user']) > 0 ? intval($this->body['send_user']) : 0;
        $message['send_status'] = intval($this->body['send_status']);
        $message['send_organization'] = intval($this->body['send_organization']) > 0 ? intval($this->body['send_organization']) : 0;
        $message['send_backend'] = intval($this->body['send_backend']) > 0 ? intval($this->body['send_backend']) : 0;
        $message['send_source'] = intval($this->body['send_source']) > 0 ? intval($this->body['send_source']) : 0;
        $message['sys_type'] = intval($this->body['sys_type']) > 0 ? intval($this->body['sys_type']) : 0;
        $message['receiver_organization_type'] = trim($this->body['receiver_organization_type']);
        Validate::isString($this->body['organization_name']) && $message['organization_name'] = trim($this->body['organization_name']);
        // receiver_organization 存在则发给对应的机构
        $message['receiver_organization'] = trim($this->body['receiver_organization']) ? trim($this->body['receiver_organization']) : 0;
        if ($message['send_organization'] == 0) {
            $message['time_flag'] = time();
            $message['is_allow'] = 1;
        }
        try {
            MessageModel::model()->begin();
            if ($message['receiver_organization'] != 0) {
                if ($message['sys_type'] == 0) {
                    $message['receiver_organization'] = $message['send_organization'];
                    MessageModel::model()->add($message);
                    $message['parent_id'] = MessageModel::model()->getInsertId();
                    if($message['receiver_organization_type'] == 0){
                        // 发给指定的分销时给后台的默认是审核状态
                        $message['is_allow'] = 1;
                        $message['parent_id'] = 0;
                    }
                    $message['receiver_organization'] = 0;
                    MessageModel::model()->add($message);
                    $message['parent_id'] = MessageModel::model()->getInsertId();
                }
                // 发给指定的分销商
                !Validate::isUnsignedId($this->body['send_organization']) && Lang_Msg::error('发送机构不能为空');
                $message['is_allow'] = 1;
                if($this->body['receiver_organization']){
                    foreach (explode(',', $this->body['receiver_organization']) as $k => $v) {
                        $message['receiver_organization'] = $v;
                        MessageModel::model()->add($message);
                    }
                }
            } else {
                // 发给全平台，全分销，全供应
                MessageModel::model()->add($message);
            }
            MessageModel::model()->commit();
            Lang_Msg::output();
        } catch (Exception $e) {
            Lang_Msg::error('添加失败');
        }
    }

    /**
     * 发送给电子票务系统
     * author : yinjian
     */
    public function sceniclistAction($isReturn=false)
    {
        !Validate::isUnsignedId($this->body['reveive_landscape']) && Lang_Msg::error('景区不能为空');
        $reveive_landscape = intval($this->body['reveive_landscape']);
        /*$organization = reset(OrganizationModel::model()->search(array('id' => $reveive_landscape, 'is_del' => 0)));
        !$organization && Lang_Msg::error('机构不存在');*/
        // 获取为注册时间后的系统消息
        $own_sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'reveive_landscape' => $reveive_landscape,
        );
        $own_sys_message = reset(MessageModel::model()->search($own_sys_message_where, 'time_flag', 'time_flag desc', 1));
        !$own_sys_message && $own_sys_message['time_flag'] = 0;
        // 新增系统消息限制最多100条
        $sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'reveive_landscape' => 0,
            'check_scenic_id' => 0,
            'receiver_organization'=>0,
            'is_allow' => 1,
            'is_cancel'=> 0,
            'is_del' => 0,
            'time_flag|>' => $own_sys_message['time_flag']);
        $sys_message_where['receiver_organization_type|in'] = array(1, 4);
        $sys_message = MessageModel::model()->search($sys_message_where, '*', 'created_at desc', 100);
        foreach ($sys_message as $k => $v) {
            if($v['receiver_organization']>0 || $v['check_scenic_id']>0 || $v['reveive_landscape']>0){
                continue 1;
            }
            $v['parent_id'] = $v['id'];
            unset($v['id']);
            unset($v['read_time']);
            $v['reveive_landscape'] = $reveive_landscape;
            MessageModel::model()->add($v);
        }
        // 获取机构消息
        $where = array(
            'reveive_landscape' => $reveive_landscape,
            'is_del' => 0
        );
        if (isset($this->body['sms_type'])) $where['sms_type'] = intval($this->body['sms_type']);
        if (isset($this->body['sys_type'])) $where['sys_type'] = intval($this->body['sys_type']);
        if (isset($this->body['read_time']) && $this->body['read_time'] == 0) {
            $where['read_time'] = 0;
        } elseif ($this->body['read_time']) {
            $where['read_time|>'] = 0;
        }
        if(isset($this->body['is_allow'])) $where['is_allow'] = intval($this->body['is_allow']);
        // 分页
        $count = reset(MessageModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        if(!isset($this->body['only_show_num']) || $this->body['only_show_num'] != 1){
            $data['data'] = MessageModel::model()->search($where, '*', 'is_read asc,created_at desc', $this->limit);
        }
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        if($isReturn===true) {
            return $data;
        }
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     * 发送给验票
     * author : yinjian
     */
    public function checkScenicListAction($isReturn=false)
    {
        !Validate::isUnsignedId($this->body['check_scenic_id']) && Lang_Msg::error('景区不能为空');
        $check_scenic_id = intval($this->body['check_scenic_id']);
        /*$organization = reset(OrganizationModel::model()->search(array('id' => $reveive_landscape, 'is_del' => 0)));
        !$organization && Lang_Msg::error('机构不存在');*/
        // 获取为注册时间后的系统消息
        $own_sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'check_scenic_id' => $check_scenic_id,
        );
        $own_sys_message = reset(MessageModel::model()->search($own_sys_message_where, 'time_flag', 'time_flag desc', 1));
        !$own_sys_message && $own_sys_message['time_flag'] = 0;
        // 新增系统消息限制最多100条
        $sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'check_scenic_id' => 0,
            'receiver_organization'=>0,
            'reveive_landscape'=>0,
            'is_allow' => 1,
            'is_cancel'=> 0,
            'is_del' => 0,
            'time_flag|>' => $own_sys_message['time_flag']);
        $sys_message_where['receiver_organization_type|in'] = array(1, 5);
        $sys_message = MessageModel::model()->search($sys_message_where, '*', 'created_at desc', 100);
        foreach ($sys_message as $k => $v) {
            if($v['receiver_organization']>0 || $v['check_scenic_id']>0 || $v['reveive_landscape']>0){
                continue 1;
            }
            $v['parent_id'] = $v['id'];
            unset($v['id']);
            unset($v['read_time']);
            $v['check_scenic_id'] = $check_scenic_id;
            MessageModel::model()->add($v);
        }
        // 获取机构消息
        $where = array(
            'check_scenic_id' => $check_scenic_id,
            'is_del' => 0
        );
        if (isset($this->body['sms_type'])) $where['sms_type'] = intval($this->body['sms_type']);
        if (isset($this->body['sys_type'])) $where['sys_type'] = intval($this->body['sys_type']);
        if (isset($this->body['read_time']) && $this->body['read_time'] == 0) {
            $where['read_time'] = 0;
        } elseif ($this->body['read_time']) {
            $where['read_time|>'] = 0;
        }
        if(isset($this->body['is_allow'])) $where['is_allow'] = intval($this->body['is_allow']);
        // 分页
        $count = reset(MessageModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        if(!isset($this->body['only_show_num']) || $this->body['only_show_num'] != 1){
            $data['data'] = MessageModel::model()->search($where, '*', 'is_read asc,created_at desc', $this->limit);
        }
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        if($isReturn===true) {
            return $data;
        }
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     * 消息列表，机构在第一次查看时插入系统消息
     * author : yinjian
     */
    public function listAction($isReturn=false)
    {
        !Validate::isUnsignedId($this->body['receiver_organization']) && Lang_Msg::error('机构不能为空');
        $receiver_organization = intval($this->body['receiver_organization']);
        $organization = reset(OrganizationModel::model()->search(array('id' => $receiver_organization, 'is_del' => 0)));
        !$organization && Lang_Msg::error('机构不存在');
        // 获取为注册时间后的系统消息
        $own_sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'receiver_organization' => $receiver_organization,
        );
        $own_sys_message = reset(MessageModel::model()->search($own_sys_message_where, 'time_flag', 'time_flag desc', 1));
        !$own_sys_message && $own_sys_message['time_flag'] = $organization['created_at'];
        // 新增系统消息限制最多100条
        $sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'receiver_organization' => 0,
            'reveive_landscape' => 0,
            'check_scenic_id' => 0,
            'send_organization|!='=>$receiver_organization,
            'is_allow' => 1,
            'is_cancel'=> 0,
            'is_del' => 0,
            'time_flag|>' => $own_sys_message['time_flag']);
        if ($organization['type'] == 'agency') {
            $sys_message_where['receiver_organization_type|in'] = array(1, 2);
        } elseif ($organization['type'] == 'supply') {
            $sys_message_where['receiver_organization_type|in'] = array(1, 3);
        }
        $sys_message = MessageModel::model()->search($sys_message_where, '*', 'created_at desc', 100);
        foreach ($sys_message as $k => $v) {
            if($v['receiver_organization']>0 || $v['check_scenic_id']>0 || $v['reveive_landscape']>0){
                continue 1;
            }
            $v['parent_id'] = $v['id'];
            unset($v['id']);
            unset($v['read_time']);
            $v['receiver_organization'] = $receiver_organization;
            MessageModel::model()->add($v);
        }
        // 获取机构消息
        $where = array(
            'or' => array(
                'receiver_organization' => $receiver_organization,
                'and'=>
                    array(
                        'send_organization' => $receiver_organization,
                        'receiver_organization_type|>'=>0,
                        'is_allow'=>0,
                        'time_flag'=>0,
                        'receiver_organization'=>0
                    )
            ),
            'is_del' => 0
        );
        if (isset($this->body['sms_type'])) $where['sms_type'] = intval($this->body['sms_type']);
        if (isset($this->body['sys_type'])) $where['sys_type'] = intval($this->body['sys_type']);
        if (isset($this->body['read_time']) && $this->body['read_time'] == 0) {
            $where['read_time'] = 0;
        } elseif ($this->body['read_time']) {
            $where['read_time|>'] = 0;
        }
        if(isset($this->body['send_organization']) && $this->body['send_organization']) $where['send_organization'] = $this->body['send_organization'];
        if(isset($this->body['is_allow'])) $where['is_allow'] = intval($this->body['is_allow']);
        // 分页
        $count = reset(MessageModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        $data['data'] = MessageModel::model()->search($where, '*', 'is_read asc,created_at desc', $this->limit);
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        if($isReturn===true) {
            return $data;
        }
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     * 消息数
     * author : yinjian
     * 2015-04-20 zqf 添加 $isReturn 参数
     */
    public function countAction($isReturn=false,$fromTopBar=false)
    {
        !Validate::isUnsignedId($this->body['receiver_organization']) && Lang_Msg::error('机构不能为空');
        $receiver_organization = intval($this->body['receiver_organization']);
        $organization = reset(OrganizationModel::model()->search(array('id' => $receiver_organization, 'is_del' => 0)));
        !$organization && Lang_Msg::error('机构不存在');
        // 获取为注册时间后的系统消息
        $own_sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'receiver_organization' => $receiver_organization,
        );
        $own_sys_message = reset(MessageModel::model()->search($own_sys_message_where, 'time_flag', 'time_flag desc', 1));
        !$own_sys_message && $own_sys_message['time_flag'] = $organization['created_at'];
        // 新增系统消息限制最多100条
        $sys_message_where = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'receiver_organization' => 0,
            'reveive_landscape' => 0,
            'check_scenic_id' => 0,
            'send_organization|!='=>$receiver_organization,
            'is_allow' => 1,
            'is_cancel'=> 0,
            'is_del' => 0,
            'time_flag|>' => $own_sys_message['time_flag']);
        if ($organization['type'] == 'agency') {
            $sys_message_where['receiver_organization_type|in'] = array(1, 2);
        } elseif ($organization['type'] == 'supply') {
            $sys_message_where['receiver_organization_type|in'] = array(1, 3);
        }
        $sys_message = MessageModel::model()->search($sys_message_where, '*', 'created_at desc', 100);
        foreach ($sys_message as $k => $v) {
            if($v['receiver_organization']>0 || $v['check_scenic_id']>0 || $v['reveive_landscape']>0){
                continue 1;
            }
            $v['parent_id'] = $v['id'];
            unset($v['id']);
            unset($v['read_time']);
            $v['receiver_organization'] = $receiver_organization;
            MessageModel::model()->add($v);
        }
        // 获取机构消息
        $where = array('or' => array('receiver_organization' => $receiver_organization, 'and'=>array('send_organization' => $receiver_organization,'receiver_organization_type|>'=>0,'is_allow'=>0,'time_flag'=>0,'receiver_organization'=>0)), 'is_del' => 0);

        if (isset($this->body['sms_type']) && $fromTopBar===false) $where['sms_type'] = intval($this->body['sms_type']);
        if (isset($this->body['sys_type']) && $fromTopBar===false) $where['sys_type'] = intval($this->body['sys_type']);
        if (isset($this->body['read_time']) && $this->body['read_time'] == 0) {
            $where['read_time'] = 0;
        } elseif ($this->body['read_time']) {
            $where['read_time|>'] = 0;
        }
        if(isset($this->body['send_organization']) && $this->body['send_organization']) $where['send_organization'] = $this->body['send_organization'];
        if(isset($this->body['is_allow'])) $where['is_allow'] = intval($this->body['is_allow']);
        // 分页
        $count = reset(MessageModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];

        if($isReturn===true) {
            return $this->count;
        }

        $this->pagenation();
        // 数据
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     * 消息列表，后台消息列表
     * author : yinjian
     */
    public function backendlistAction()
    {
        $where = array(
            'sys_type' => 0,
            'sms_type' => 0,
            'reveive_landscape' => 0,
            'check_scenic_id' => 0,
            'or' =>
                array(
                    'receiver_organization' => 0,
                    'and' =>
                        array(
                            'is_allow' => 0,
                            'receiver_organization_type' => 2
                        )
                ),
            'is_del' => 0,
        );
        if (isset($this->body['sms_type'])) $where['sms_type'] = intval($this->body['sms_type']);
        if (isset($this->body['sys_type'])) $where['sys_type'] = intval($this->body['sys_type']);
        if (isset($this->body['send_source'])) $where['send_source'] = intval($this->body['send_source']);
        if (isset($this->body['receiver_organization_type'])) $where['receiver_organization_type'] = intval($this->body['receiver_organization_type']);
        if (isset($this->body['is_allow'])) $where['is_allow'] = intval($this->body['is_allow']);
        if (isset($this->body['read_time']) && $this->body['read_time'] == 0) {
            $where['read_time'] = 0;
        } elseif ($this->body['read_time']) {
            $where['read_time|>'] = 0;
        }
        if(isset($this->body['id']) && Validate::isString($this->body['id'])) $where['id|in'] = explode(',',$this->body['id']);
        if(isset($this->body['created_at']) && Validate::isString($this->body['created_at'])) {
            $created_at = explode(' - ',$this->body['created_at']);
            $start_at = reset($created_at).' 00:00:00';
            $end_at = end($created_at).' 23:59:59';
            $where['created_at|between'] = array(strtotime($start_at),strtotime($end_at));
        }
        // 分页
        $count = reset(MessageModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        $data['data'] = MessageModel::model()->search($where, '*', 'created_at desc', $this->limit);
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     * 后台数量单独获取
     * author : yinjian
     */
    public function countBackendAction()
    {
        $where = array(
            'sys_type' => 0,
            'sms_type' => 0,
            'or' =>
                array(
                    'receiver_organization' => 0,
                    'and' =>
                        array(
                            'is_allow' => 0,
                            'receiver_organization_type' => 2
                        )
                ),
            'is_del' => 0,
        );
        if (isset($this->body['sms_type'])) $where['sms_type'] = intval($this->body['sms_type']);
        if (isset($this->body['sys_type'])) $where['sys_type'] = intval($this->body['sys_type']);
        if (isset($this->body['send_source'])) $where['send_source'] = intval($this->body['send_source']);
        if (isset($this->body['receiver_organization_type'])) $where['receiver_organization_type'] = intval($this->body['receiver_organization_type']);
        if (isset($this->body['is_allow'])) $where['is_allow'] = intval($this->body['is_allow']);
        if (isset($this->body['read_time']) && $this->body['read_time'] == 0) {
            $where['read_time'] = 0;
        } elseif ($this->body['read_time']) {
            $where['read_time|>'] = 0;
        }
        if(isset($this->body['id']) && Validate::isString($this->body['id'])) $where['id|in'] = explode(',',$this->body['id']);
        if(isset($this->body['created_at']) && Validate::isString($this->body['created_at'])) {
            $created_at = explode(' - ',$this->body['created_at']);
            $start_at = reset($created_at).' 00:00:00';
            $end_at = end($created_at).' 23:59:59';
            $where['created_at|between'] = array(strtotime($start_at),strtotime($end_at));
        }
        // 分页
        $count = reset(MessageModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true, 'ok', $data);
    }

    /**
     * 信息详情
     * author : yinjian
     */
    public function detailAction()
    {
        $id = intval($this->body['id']);
        !$id && Lang_Msg::error('信息不存在');
        $message = reset(MessageModel::model()->search(array('id'=>$id)));
        Lang_Msg::output($message);
    }

    // 编辑消息
    public function updateAction()
    {
        !Validate::isUnsignedId($this->body['id']) && Lang_Msg::error('id不能为空');
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户不能为空');
        $old_message = $message = reset(MessageModel::model()->search(array('id' => $this->body['id'], 'is_del' => 0)));
        !$message && Lang_Msg::error('消息不存在');
        $data['updated_at'] = time();
        $data['op_uid'] = intval($this->body['uid']);
        // 删除
        if (isset($this->body['is_del'])) $data['is_del'] = 1;
        // 阅读
        if (isset($this->body['read_time'])) {
            $data['read_time'] = time();
            $data['is_read'] = 1;
        }
        // 审核
        if (isset($this->body['is_allow']) && in_array($this->body['is_allow'], array(1, 2))) {
            $message['is_allow'] != 0 && Lang_Msg::error('后台已审核该公告');
            $data['is_allow'] = intval($this->body['is_allow']);
            $data['send_status'] = $data['is_allow'];
            $this->body['is_allow'] == 2 && $data['remark'] = trim($this->body['remark']);
            if($this->body['is_allow'] == 1) {
                // 审核通过
                $data['time_flag'] = $data['updated_at'];
                $data['is_cancel'] = 0;
            }
        }
        try {
            MessageModel::model()->begin();
            MessageModel::model()->updateByAttr($data, array('id' => $message['id']));
            // 发送备份
            if (isset($this->body['is_allow']) && in_array($this->body['is_allow'], array(1, 2))) {
                unset($message['id']);
                if($this->body['is_allow'] == 2) $message['remark'] = strval($data['remark']);
                $message['receiver_organization'] = $message['send_organization'];
                $message['is_allow'] = $this->body['is_allow'];
                $message['send_status'] = $this->body['is_allow'];

                if($message['send_organization']>0 && $message['receiver_organization_type']!=0) {
                    // 分销供应额外新增消息
                    MessageModel::model()->add($message);
                }
            }
            // 供应发给合作分销的驳回后允许
            if($message['receiver_organization_type'] == 0 && $old_message['is_allow']==0 && $this->body['is_allow']==1){
                MessageModel::model()->updateByAttr(array('is_del'=>0),array('parent_id'=>$this->body['id']));
            }
            // 撤销 @TODO
            if(isset($this->body['is_cancel']) && $this->body['is_cancel']==1){
                $message['is_allow'] !=1 && Lang_Msg::error('未发布的公告不需要撤销');
                MessageModel::model()->updateByAttr(array('is_cancel'=>1,'is_allow'=>0),array('id'=>$this->body['id'],'is_del'=>0));
                if($message['receiver_organization_type'] == 0){
                    MessageModel::model()->updateByAttr(array('is_del'=>1),array('parent_id'=>$this->body['id'],'is_del'=>0));
                }else {
                    MessageModel::model()->delete(array('parent_id' => $this->body['id'], 'is_del' => 0));
                }
            }
            MessageModel::model()->commit();
            Lang_Msg::output();
        } catch(Exception $e) {
            MessageModel::model()->rollback();
            Lang_Msg::error('修改失败');
        }
    }

    /**
     * 批量更新
     * author : yinjian
     */
    public function updateBatchAction()
    {
        !Validate::isString($this->body['id']) && Lang_Msg::error('id不能为空');
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户不能为空');
        $message = MessageModel::model()->search(array('id|in' => explode(',',$this->body['id']), 'is_del' => 0));
        !$message && Lang_Msg::error('消息不存在');
        $data['updated_at'] = time();
        $data['op_uid'] = intval($this->body['uid']);
        if (isset($this->body['is_del'])) $data['is_del'] = 1;
        if (isset($this->body['read_time'])) {
            $data['read_time'] = time();
            $data['is_read'] = 1;
        }
        try {
            MessageModel::model()->begin();
            foreach (array_unique(array_keys($message)) as $k => $v) {
                $res = MessageModel::model()->updateByAttr($data, array('id' => $v));
            }
            MessageModel::model()->commit();
            Lang_Msg::output();
        } catch(Exception $e) {
            MessageModel::model()->rollback();
            Lang_Msg::error('修改失败');
        }
    }

    /**
     * 分销、供应商 票务页首整合信息（公告、消息数、购物车、产品订阅数）
     */
    public function topBarAction(){
        if($this->body['reveive_landscape']){
            $this->topbarscenic();
        }elseif($this->body['check_scenic_id']){
            $this->topbarcheck();
        }
        $orgId = intval($this->body['org_id']); //机构ID
        if(!$orgId) {
            Lang_Msg::error('机构不能为空');
        }
        $organization = OrganizationModel::model()->getById($orgId);
        if(!$organization) {
            Lang_Msg::error('机构不存在');
        }

        $this->body['receiver_organization']=$orgId;

        //消息统计
        $msgCount = MessageModel::model()->customCache('topBarMessageCount_'.$orgId);
        if($msgCount==null) $msgCount = MessageModel::model()->customCache('topBarMessageCount_'.$orgId,$this->countAction(true,true));

        /*$MessageCacheNs = MessageModel::model()->getCacheNS();
        $msgCountData = Cache_Memcache::factory()->get('topBarMessageCount_'.$orgId);
        if(empty($msgCountData) || $msgCountData['cacheNS']!=$MessageCacheNs) {
            $msgCount =  $this->countAction(true,true);
            Cache_Memcache::factory()->set('topBarMessageCount_'.$orgId,array('data'=>$msgCount,'cacheNS'=>$MessageCacheNs),3600);
        } else {
            $msgCount = $msgCountData['data'];
        }
        //公告
        $noticeList = Cache_Memcache::factory()->get('topBarNoticeList_'.$orgId);
        if(empty($noticeList)) {
            $noticeList = $this->listAction(true);
            Cache_Memcache::factory()->set('topBarNoticeList_'.$orgId,$noticeList,1);
        }*/

        $noticeList = MessageModel::model()->customCache('topBarNoticeList_'.$orgId);
        if($noticeList==null) $noticeList = MessageModel::model()->customCache('topBarNoticeList_'.$orgId,$this->listAction(true));

        if($organization['type']=='agency') {
            $userId = intval($this->body['user_id']); //用户UID
            if(!$userId) {
                Lang_Msg::error('确少登录用户UID');
            }
            $cartCount = ApiOrderModel::model()->cartCount($userId); //购物车

            $subscribeCount = ApiProductModel::model()->subscribeCount($orgId); //产品订阅数
            $ret = array(
                'notice_list'=>$noticeList,
                'message_count'=>$msgCount,
                'cart_count'=>$cartCount,
                'subscribe_count'=>$subscribeCount,
            );
        } else {
            $ret = array(
                'notice_list'=>$noticeList,
                'message_count'=>$msgCount,
            );
        }
        //$ret['cacheNs'] = $MessageCacheNs;
        Tools::lsJson(true, 'ok', $ret);
    }

    /**
     * 电子票务topbar
     * author : yinjian
     */
    public function topbarscenic()
    {
        $notice_list = $this->sceniclistAction(true);
        Lang_Msg::output(array(
            'notice_list' => $notice_list,
            'message_count' => $notice_list['pagination']['count'],
        ));
    }

    /**
     * 验票
     * author : yinjian
     */
    public function topbarcheck()
    {
        $notice_list = $this->checkScenicListAction(true);
        Lang_Msg::output(array(
            'notice_list' => $notice_list,
            'message_count' => $notice_list['pagination']['count'],
        ));
    }
}