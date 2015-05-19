<?php
use common\huilian\utils\Header;  
/*
 * message
 * created by ccq
 * api:  ticket-api-organization/message
 * */
class MessageController extends Controller {

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
        $field = array();
        if(isset($param) && !empty($param)){
            if($param['is_allow'] != ''){
                if($param['is_allow'] == 1) {
                    $field['send_organization'] = Yii::app()->user->org_id;
                }else {
                    $field['is_allow'] = $param['is_allow'];
                }
            }
            if($param['read_time'] != ''){
                $field['read_time'] = $param['read_time'];
            }
        }

        //获取消息数量相关条件
        $read_num_array = array();      //已读消息数量条件
        $unread_num_array = array();    //未读消息数量条件
        $read_num_array['receiver_organization'] = Yii::app()->user->org_id;
        $unread_num_array['receiver_organization'] = Yii::app()->user->org_id;
        if($sys_type !== '') {
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
            'sys_class' => array('0' => 'success', '1' => 'warning', '2' => 'info', '3' => 'info', '4' => 'primary', '5' => 'danger','6' => 'info'),
            'sys_name' => array('（公告）','订阅消息','机构消息','到期提醒','收藏提醒','（退款提醒）','订单提醒'),
            'type' => $type,
            'is_allow' => isset($param['is_allow']) ? $param['is_allow'] : '',
            'read_time' => isset($param['read_time']) ? $param['read_time'] : '',
            'read_num' => isset($read_num) && $read_num > 0 ? $read_num : 0,
            'unread_num' => isset($unread_num) && $unread_num > 0 ? $unread_num : 0,
        );
        //消息类型
        $field['receiver_organization'] = Yii::app()->user->org_id;
        $field['current'] = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $field['items'] = 10;
    	$result = Message::api()->list($field);
    	if($result['code'] == 'succ'){
    		$data['lists'] = !empty($result['body']['data']) ? $result['body']['data'] : '';
    		$data['pages'] = new CPagination($result['body']['pagination']['count']);
            $data['pages']->pageSize = $field['items'];
    	}

        //获取滚动栏5条未读公告
        $advice_field = array(
            'receiver_organization' => Yii::app()->user->org_id,
            'sys_type' => 0,
            'read_time' => 0,
            'items' => 5
        );
        $advice_result = Message::api()->list($advice_field);
        if($advice_result['code'] == 'succ'){
            $data['advice_list'] = !empty($advice_result['body']['data']) ? $advice_result['body']['data'] : '';
        }

        //echo "<pre>";
        //print_r($data);die("</pre>");
        $this->render('index',$data);
    }
    
    // 头部提醒
    public function actionTopbar()
    {
        $params = array(
            'org_id' => Yii::app()->user->org_id, 'items' => 5,
            'user_id' => Yii::app()->user->uid, 'read_time' => 0, 
            'sms_type' => 0, 'sys_type' => 0
        );
        $result = Message::api()->topBar($params, true, 10);
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
        $result = Message::api()->update($params);
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
        $result = Message::api()->update($params);
        if($result['code'] == 'succ'){
        	$this->_end(0,'删除成功');
        }else{
        	$this->_end(1,$result['message']);
        }
    }

    //发布公告
    public function actionSaveAdvice() {
        $param = $_REQUEST;
        $content = $param['content'];
        /*
         * 过滤富文本框内的html代码，防止空置
         */
        $param['content'] = trim($param['content']);
        $param['content'] = htmlspecialchars_decode($param['content']);
        $param['content'] = preg_replace("/<(.*?)>/","",$param['content']);
        if(empty($param['content'])){
            $this->_end(1,'请填写相应的公告内容！');
            exit;
        }
        if($param['receiver_organization'] == ''){
            $this->_end(1,'请选择公告发送的对象！');
            exit;
        }
        if(empty($param['title'])){
            $this->_end(1,'请填写相应的公告标题！');
            exit;
        }else{
            if(strlen($param['title']) > 60){
                $this->_end(1,'公告标题长度过长！请少于20个字！');
                exit;
            }
        }
        $info = Organizations::api()->show(array('id' => Yii::app()->user->org_id));
        if ($info['code'] == 'succ') {
            $org_name = $info['body']['name'];
        }
        //拼接相关条件
        $field = array(
            'sms_type' => 0,
            'sys_type' => 0,
            'send_source' => 1,
            'send_status' => 1,
            'send_user' => Yii::app()->user->uid,
            'organization_name' => isset($org_name) && !empty($org_name) ? $org_name : '',
            'title' => isset($param['title']) && !empty($param['title']) ? $param['title'] : '',
            'content' => htmlspecialchars_decode($content),
            'send_organization' => Yii::app()->user->org_id
        );

        //判断是否发送给全部供应商
        if($param['receiver_organization'] == 0){
            $field['receiver_organization_type'] = 2;
            $field['is_allow'] = 0;
        }else{
            $credit_result = Credit::api()->lists(array('supplier_id' => Yii::app()->user->org_id,'items' => 1000));
            if($credit_result['code'] == 'succ'){
                $credit_list = !empty($credit_result['body']['data']) ? $credit_result['body']['data'] : '';
                if(is_array($credit_list)) {
                    foreach ($credit_list as $value) {
                        $receiver_organization[] = $value['distributor_id'];
                    }
                    $field['receiver_organization_type'] = 0;
                    $field['receiver_organization'] = implode(',',$receiver_organization);
                    $field['is_allow'] = 1;
                }else{
                    $this->_end(1, '请先绑定分销商后，再进行该操作');
                    exit;
                }
            }
        }

        //调用接口，进行公告发布
        $result = Message::api()->add($field);
        if($result['code'] == 'succ'){
            $this->_end(0, '发布成功');
        }else{
            $this->_end(1, $result['message']);
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

        $result = Message::api()->updateBatch($param);
        if ($result['code'] == 'succ') {
            $this->_end(0, '设置已读成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }

    /**
     * 预览
     * 本方法展现POST过来的公告信息
     * 
     * 备注：
     * - 发布人是机构的名称，通过机构接口调取。
     * －　若用UBB方式，则采用以下方式：'content' => UbbToHtml::Entry($_POST['content']),
     */
    public function actionPreview()
    {	
    	if(Yii::app()->request->isPostRequest) {
    		$organization = Organizations::api()->show(['id' => Yii::app()->user->org_id, ]);
    		$_POST['content'] = htmlspecialchars_decode($_POST['content']);
    		$params = [
    			'publisher' => empty($organization['body']['name']) ? '' : $organization['body']['name'],
    		];
    		$this->render('preview', array_merge($params, $_POST));
    	}
    }
    
}
