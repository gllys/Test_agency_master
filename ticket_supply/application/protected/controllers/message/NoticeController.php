<?php
use common\huilian\utils\Header;  
/*
 * message
 * created by ccq
 * api:  ticket-api-organization/message
 * */
class NoticeController extends Controller {

    public function init(){
        parent::init();
        $param = array(
            'is_allow' =>  isset($_REQUEST['is_allow']) ? $_REQUEST['is_allow'] : '',
            'read_time' => isset($_REQUEST['read_time']) ? $_REQUEST['read_time'] : '',
            'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : 'all',
        );
        $this->childNav = '/message/notice/view/type/' . $param['type'] . '/';
        // print_r(Yii::app()->user->lan_id);
    }

    public function actionView() {
        $param = array(
            'is_allow' =>  isset($_REQUEST['is_allow']) ? $_REQUEST['is_allow'] : '',
            'read_time' => isset($_REQUEST['read_time']) ? $_REQUEST['read_time'] : '',
            'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : 'all',
        );
        $this->actionIndex($param);
    }

    public function actionIndex($param = array()) {
        $type = isset($param['type']) && !empty($param['type']) ? $param['type'] : 'all';
        switch($type){
            case 'refund':
                $sys_type = '5';
                break;
            case 'due':
                $sys_type = '3';
                break;
            case 'advice':
                $sys_type = '0';
                break;
            case 'order':
                $sys_type = '6';
                break;
            default:
                $sys_type = '';
                break;
        }
        //echo Yii::app()->user->lan_id;die;
        //组织获取消息相关条件 $field
        
        //Yii::app()->user->lan_id;
        
        //exit;
        $field = array();
        if(isset($param) && !empty($param)){
            
            if($param['read_time'] != ''){
                $field['read_time'] = $param['read_time'];
            }
        }
        
        $field["sms_type"] = 0;
        
        $lan_id = Yii::app()->user->lan_id;
        
        //获取消息数量相关条件
        $read_num_array = array();      //已读消息数量条件
        $unread_num_array = array();    //未读消息数量条件
        $read_num_array['check_scenic_id'] = $lan_id;
        $unread_num_array['check_scenic_id'] = $lan_id;
        if($sys_type !== '') {
            $field['sys_type'] = $sys_type;
            //获取消息数量相关条件
            $read_num_array['sys_type'] = $sys_type;
            $unread_num_array['sys_type'] = $sys_type;
        }
        $read_num_array['read_time'] = 1;
        $unread_num_array['read_time'] = 0;
        //获取已读消息数量
        $read = Message::api()->checkSceniclist($read_num_array);
        if ($read['code'] == 'succ') {
            $read_num = $read['body']['pagination']['count'];
        }
        
        //获取未读消息数量
        $unread = Message::api()->checkSceniclist($unread_num_array);
        if ($unread['code'] == 'succ') {
            $unread_num = $unread['body']['pagination']['count'];
        }

        //返回前端相关数据
        $data = array(
            'sys_class' => array('0' => 'success', '1' => 'warning', '2' => 'info', '3' => 'info', '4' => 'primary', '5' => 'danger','6' => 'info'),
            'sys_name' => array('（公告）','订阅消息','机构消息','到期提醒','收藏提醒','（退款提醒）','订单提醒'),
            'type' => $type,
            'is_allow' => isset($param['is_allow']) ? $param['is_allow'] : '',
            'read_time' => isset($param['read_time']) ? $param['read_time'] : '',
            'read_num' => isset($read_num) && $read_num > 0 ? $read_num : 0,
            'unread_num' => isset($unread_num) && $unread_num > 0 ? $unread_num : 0,
        );
        //消息类型
        $field['check_scenic_id'] = $lan_id;
        $field['current'] = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $field['items'] = 10;
    	$result = Message::api()->checkSceniclist($field);
    	if($result['code'] == 'succ'){
    		$data['lists'] = !empty($result['body']['data']) ? $result['body']['data'] : '';
    		$data['pages'] = new CPagination($result['body']['pagination']['count']);
            $data['pages']->pageSize = $field['items'];
    	}

        //获取滚动栏5条未读公告
        $advice_field = array(
            'check_scenic_id' => $lan_id,
            'sys_type' => 0,
            'read_time' => 0,
            'items' => 5
        );
        $advice_result = Message::api()->checkSceniclist($advice_field);
        if($advice_result['code'] == 'succ'){
            $data['advice_list'] = !empty($advice_result['body']['data']) ? $advice_result['body']['data'] : '';
        }

        //echo "<pre>";
        //print_r($data);die("</pre>");
        // print_r(Yii::app()->user->lan_id);
        $this->render('index',$data);
    }
    
    // 头部提醒
    public function actionTopbar()
    {
        $params = array(
            'check_scenic_id' => intval(Yii::app()->user->lan_id), 'items' => 5,
            'user_id' => Yii::app()->user->uid, 'read_time' => 0, 
            'sms_type' => 0, 'sys_type' => 0
        );
        $result = Message::api()->topBar($params);
        
        header('Content-type: application/json');
        if ($result['code'] == 'succ') {
            $this->_end(0, null, $result['body']);
        }else{
            $this->_end(1, $result['message']);
        }
    }

    //变更状态
    public function actionRead() {
        $params['id'] = $_POST['id'];
        $params['uid'] = Yii::app()->user->uid;
        $params['read_time'] = strtotime(date('y-m-d',time()));
        $result = Message::api()->update($params,0);
        if($result['code'] == 'succ'){
        	$this->_end(0,date('Y年m月d日',time()));
        }else{
        	$this->_end(1,$result['message']);
        }
    }
    //变更状态
    public function actionDelete() {      
        $params['id'] = $_POST['id'];
        $params['uid'] = Yii::app()->user->uid;
        $params['is_del'] = 1;
        $result = Message::api()->update($params,0);
        
        if($result['code'] == 'succ'){
        	$this->_end(0,'删除成功');
        }else{
        	$this->_end(1,$result['message']);
        }
    }

    //批量变更状态
    public function actionUpdateAll() {
        $param['id'] = trim(str_replace("undefined","",str_replace("on","",$_POST['ids'])),",");
        if(empty($param['id'])){
            $this->_end(1,'请勾选需要删除的消息');
            exit;
        }

        $param['uid'] = Yii::app()->user->uid;
        if($_POST['type'] == 'del'){
            $param['is_del'] = 1;
        }

        //print_r($param);die;
        $result = Message::api()->updateBatch($param);
        if ($result['code'] == 'succ') {
            $this->_end(0, '删除成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }

    /*
     *批量设置已读
     * xujuan
     */
    public function actionUpdateBatch()
    {
        $param['id'] = trim(str_replace("undefined","",str_replace("on","",$_POST['ids'])),",");
        if(empty($param['id'])){
            $this->_end(1,'请勾选需要设置已读的消息');
            exit;
        }
        $param['uid'] = Yii::app()->user->uid;
        $param['read_time'] =time(); //必传参数

        $result = Message::api()->updateBatch($param, 0);
        if ($result['code'] == 'succ') {
            $this->_end(0, '设置已读成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }
    
}
