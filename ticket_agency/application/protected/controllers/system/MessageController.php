<?php
/*
 * message
 * create by ccq
 * create at 2014-11-20
 * update at 2015-01-20
 * api:  ticket-api-organization/message
 * */
class MessageController extends Controller
{

    public function init(){
        parent::init();
        $param = array(
            'is_allow' =>  isset($_REQUEST['is_allow']) ? $_REQUEST['is_allow'] : '',
            'read_time' => isset($_REQUEST['read_time']) ? $_REQUEST['read_time'] : '',
            'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : 'all',
        );
        $this->childNav = '/system/message/view/type/' . $param['type'] . '/';
    }

    public function actionView() {
        $param = array(
            'is_allow' =>  isset($_REQUEST['is_allow']) ? $_REQUEST['is_allow'] : '',
            'read_time' => isset($_REQUEST['read_time']) ? $_REQUEST['read_time'] : '',
            'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : 'all',
        );
        $this->actionIndex($param);
    }

    /*
     * 消息首页
     */

    public function actionIndex($param = array())
    {
        $type = isset($param['type']) && !empty($param['type']) ? $param['type'] : 'all';
        switch ($type) {
            case 'advice':
                $sys_type = '0';
                break;
            case 'subscribe':
                $sys_type = '1';
                break;
            case 'organization':
                $sys_type = '2';
                break;
            case 'remind':
                $sys_type = '5';
                break;
            case 'collect':
                $sys_type = '4';
                break;
            case 'order':
                $sys_type = '6';
                break;
            default:
                $sys_type = '';
                break;
        }
        //组织获取消息相关条件 $field
        $field = array();
        if (isset($param) && !empty($param)) {
            if ($param['read_time'] != '') {
                $field['read_time'] = $param['read_time'];
            }
        }

        //获取消息数量相关条件
        $read_num_array = array();      //已读消息数量条件
        $unread_num_array = array();    //未读消息数量条件
        $read_num_array['receiver_organization'] = Yii::app()->user->org_id;
        $unread_num_array['receiver_organization'] = Yii::app()->user->org_id;
        if ($sys_type !== '') {
            $field['sys_type'] = $sys_type;
            //获取消息数量相关条件
            $read_num_array['sys_type'] = $sys_type;
            $unread_num_array['sys_type'] = $sys_type;
        }
        $read_num_array['read_time'] = 1;
        $unread_num_array['read_time'] = 0;
        //获取已读消息数量
        $read = Message::api()->list($read_num_array);
        if ($read['code'] == 'succ') {
            $read_num = $read['body']['pagination']['count'];
        }
        //获取未读消息数量
        $unread = Message::api()->list($unread_num_array);
        if ($unread['code'] == 'succ') {
            $unread_num = $unread['body']['pagination']['count'];
        }

        //返回前端相关数据
        $data = array(
            'sys_class' => array('0' => 'success', '1' => 'warning', '2' => 'info', '3' => 'info', '4' => 'primary', '5' => 'danger', '6' => 'info'),
            'sys_name' => array('公告', '收藏', '机构', '提醒', '收藏', '提醒', '订单'),
            'type' => $type,
            'send_status' => isset($param['send_status']) ? $param['send_status'] : '',
            'read_time' => isset($param['read_time']) ? $param['read_time'] : '',
            'read_num' => isset($read_num) && $read_num > 0 ? $read_num : 0,
            'unread_num' => isset($unread_num) && $unread_num > 0 ? $unread_num : 0,
        );
        //消息类型
        $field['receiver_organization'] = Yii::app()->user->org_id;
        $field['current'] = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $field['items'] = 10;
        $result = Message::api()->list($field);
        if ($result['code'] == 'succ') {
            $data['lists'] = !empty($result['body']['data']) ? $result['body']['data'] : '';
            $data['pages'] = new CPagination($result['body']['pagination']['count']);
            $data['pages']->pageSize = $field['items'];
        }

        //获取滚动栏5条未读公告
        $advice_field = array(
            'receiver_organization' => Yii::app()->user->org_id,
            'sys_type' => 0,
            'read_time' => 0,
            'items' => 5,
//            'is_allow' => 1
        );
        $advice_result = Message::api()->list($advice_field);
        if ($advice_result['code'] == 'succ') {
            $data['advice_list'] = !empty($advice_result['body']['data']) ? $advice_result['body']['data'] : '';
        }
        $this->render('index', $data);
    }

    //变更状态
    public function actionRead()
    {
        $params['id'] = $_POST['id'];
        $params['uid'] = Yii::app()->user->uid;
        $params['read_time'] = strtotime(date('y-m-d', time()));
        $result = Message::api()->update($params);
        if ($result['code'] == 'succ') {
            $this->_end(0, date('Y年m月d日', time()));
        } else {
            $this->_end(1, $result['message']);
        }
    }

    //变更状态
    public function actionDelete()
    {
        $params['id'] = $_POST['id'];
        $params['uid'] = Yii::app()->user->uid;
        $params['is_del'] = 1;
        $result = Message::api()->update($params);
        if ($result['code'] == 'succ') {
            $this->_end(0, '删除成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }

    //批量变更状态
    public function actionUpdateAll() {
        //$param['id'] = $_POST['ids'];
        $param['id'] = trim(str_replace("undefined","",str_replace("on","",$_POST['ids'])),",");
        $param['uid'] = Yii::app()->user->uid;
        if(empty($_POST['ids'])){
            $this->_end(1,'至少选择一项进行删除');
            exit;
        }
        if($_POST['type'] == 'del'){
            $param['is_del'] = 1;
        }
        $result = Message::api()->updateBatch($param);
        if ($result['code'] == 'succ') {
            $this->_end(0, '删除成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }


}