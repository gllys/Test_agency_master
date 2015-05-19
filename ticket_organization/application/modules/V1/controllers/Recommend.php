<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/19
 * Time: 15:13
 */
class RecommendController extends Base_Controller_Api
{
    /**
     *
     * author : yinjian
     */
    public function addAction()
    {
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户id不存在');
        !Validate::isString($this->body['title']) && Lang_Msg::error('主题不能为空');
        !Validate::isTimestamp($this->body['start_time']) && Lang_Msg::error('起始时间不能为空');
        !Validate::isTimestamp($this->body['end_time']) && Lang_Msg::error('结束时间不能为空');
        !Validate::isString($this->body['bimg']) && Lang_Msg::error('图片路径不能为空');
        !Validate::isString($this->body['pos_id']) && Lang_Msg::error('位置不存在');
        !in_array($this->body['status'],array(0,1)) && Lang_Msg::error('状态不正确');
        $res = AdModel::model()->add(array(
            'pos_id'=>trim($this->body['pos_id']),
            'title' => trim($this->body['title']),
            'bimg' => trim($this->body['bimg']),
            'url' => isset($this->body['url'])?trim($this->body['url']):'',
            'start_time' => intval($this->body['start_time']),
            'end_time' => intval($this->body['end_time']),
            'status' => intval($this->body['status']),
            'width' => 0,
            'height' => 0,
            'created_by'=> intval($this->body['uid']),
            'created_at' => time(),
            'updated_at' => time(),
            'detail' => isset($this->body['detail'])?trim($this->body['detail']):'',
        ));
        !$res && Lang_Msg::error('添加失败');
        Lang_Msg::output();
    }

    public function poslistAction()
    {
        Lang_Msg::output(AdPosModel::model()->search());
    }

    /**
     * 更新操作
     * author : yinjian
     */
    public function updateAction()
    {
        !Validate::isUnsignedId($this->body['id']) && Lang_Msg::error('id不存在');
        !Validate::isUnsignedId($this->body['uid']) && Lang_Msg::error('用户id不存在');
        $ad = reset(AdModel::model()->search(array('id'=>$this->body['id'],'deleted_at'=>0)));
        !$ad && Lang_Msg::error('推荐不存在');
        $data['updated_at'] = time();
//        $data['uid'] = intval($this->body['uid']);
        if(isset($this->body['detail'])) $data['detail'] = trim($this->body['detail']);
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $data['status'] = intval($this->body['status']);
        if(Validate::isString($this->body['title'])) $data['title'] = trim($this->body['title']);
        if(Validate::isTimestamp($this->body['start_time'])) $data['start_time'] = intval($this->body['start_time']);
        if(Validate::isTimestamp($this->body['end_time'])) $data['end_time'] = intval($this->body['end_time']);
        if(Validate::isString($this->body['bimg'])) $data['bimg'] = trim($this->body['bimg']);
        if(Validate::isString($this->body['url'])) $data['url'] = trim($this->body['url']);
        if(isset($this->body['deleted_at']) && $this->body['deleted_at']) $data['deleted_at'] = time();
        if(Validate::isString($this->body['pos_id'])) $data['pos_id'] = trim($this->body['pos_id']);
        $res = AdModel::model()->updateByAttr($data, array('id' => $ad['id']));
        !$res && Lang_Msg::error('更新失败');
        Lang_Msg::output();
    }

    /**
     *
     * author : yinjian
     */
    public function listsAction()
    {
        $where = array('deleted_at'=>0);
        if(isset($this->body['ids']) && $this->body['ids']) $where['id|in'] = explode(',',$this->body['ids']);
        if(isset($this->body['pos_id']) && $this->body['pos_id']) {
            //@TODO
            $where['find_in_set|EXP'] = '('.intval($this->body['pos_id']).',pos_id)';
        }
        if(isset($this->body['expire_time'])){
            $where['start_time|<'] = time();
            $where['end_time|>'] = time();
        }
        if(isset($this->body['status']) && in_array($this->body['status'],array(0,1))) $where['status'] = intval($this->body['status']);
        $count = reset(AdModel::model()->search($where, 'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        // 数据
        $data['data'] = AdModel::model()->search($where, '*', 'updated_at desc', $this->limit);
        $data['pagination'] = array(
            'count' => $this->count,
            'current' => $this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }

}