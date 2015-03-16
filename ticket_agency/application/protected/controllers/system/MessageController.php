<?php

class MessageController extends Controller {

	public function actionAll() {
        $this->actionIndex();
    }
    
    public function actionSub() {
        $this->actionIndex(1);
    }

    public function actionOrg() {
        $this->actionIndex(2);
    }

    public function actionRem()
    {
    	$this->actionIndex(3);
    }

    public function actionCol()
    {
    	$this->actionIndex(4);
    }


    /**
     * 已发送
     */
    //public function actionSent() {
    //    $this->actionIndex('sent');
    //}

    public function actionIndex($type = '') {
        $data['sms_label'] = array('0' => '系统', '1' => '订单' );
        $data['sms_class'] = array('0' => 'success', '2' => 'warning' );
        $params = $_REQUEST;
        $data['type'] = $type;
        $params['sys_type'] = $type;
        if($type == null){
        	unset($params['sys_type']);
        }
        $params['receiver_organization'] = Yii::app()->user->org_id;
        $params['current'] = isset($params['page']) ? $params['page'] : 1;
        $params['items'] = 10;
    	$result = Message::api()->list($params); 
    	if($result['code'] == 'succ'){
    		$data['messages'] = $result['body']['data'];
    		$data['pages'] = new CPagination($result['body']['pagination']['count']);
            $data['pages']->pageSize = $params['items'];
    	}
        $this->render('index',$data);
    }

    //public function actionWrite() {
    //    $this->render('write');
    //}

    /**
     * 发送
     */
    //public function actionSend() {
    //    $receiver_id = Yii::app()->request->getParam('receiver_id');
    //    $content = Yii::app()->request->getParam('content');
    //}

    // Uncomment the following methods and override them if needed
    /*
      public function filters()
      {
      // return the filter configuration for this controller, e.g.:
      return array(
      'inlineFilterName',
      array(
      'class'=>'path.to.FilterClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }

      public function actions()
      {
      // return external action classes, e.g.:
      return array(
      'action1'=>'path.to.ActionClass',
      'action2'=>array(
      'class'=>'path.to.AnotherActionClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }
     */
    //变更状态
    public function actionRead() {
        $params['id'] = $_POST['id'];
        $params['uid'] = Yii::app()->user->uid;
        $params['read_time'] = strtotime(date('y-m-d',time()));
        $result = Message::api()->update($params);
        if($result['code'] == 'succ'){
        	$this->_end(0,'阅读成功');
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

}
