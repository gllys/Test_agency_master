<?php

class ProductController extends Controller {
    public function actionIndex() {
        $field = $_GET;
        $field['or_id'] = Yii::app()->user->org_id;
        $field['p']  = isset($field['page']) ? $field['page'] : 1;
        $field['items'] = 15;
        
        $lists = TicketTemplate::api()->lists($field);
        $list = ApiModel::getLists($lists);
         //分页
            $pagination = ApiModel::getPagination($lists);
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            
        $this->render('index',  compact('list','pages'));
    }
    
    public function actionIndex1() {       
        //供应商绑定的景区
        $field['organization_id'] = Yii::app()->user->org_id;
       // $field['on_shelf']        = 1;  //状态：1上架
       // $field['status']          = 'normal';  //状态
        
        $landscape = Landscape::api()->lists($field);
        $lanList = ApiModel::getLists($landscape);
        
        
        $this->render('index1',  compact('lanList'));
    }
    
    //发布产品1
    public function actionList(){
         if (Yii::app()->request->isPostRequest) {
             $param['scenic_id'] = $_POST['scenic_id'];
             Tickettemplatebase::api()->bug=true;
             $ticketlist = Tickettemplatebase::api()->lists($param);
             $list = ApiModel::getLists($ticketlist);
             if(empty($list)){
                 $this->_end(1,'该景区问基础票，请先添加');
             }else{
                $lists = $list[$param['scenic_id']];  
                echo json_encode($lists);
             }
         }
    }
 
    //发布产品2
    public function actionIndex2(){
       if(empty($_GET['scenic_id'])){
            $this->renderPartial('index');
       }else{
            //查询相关的票信息
          //  Tickettemplatebase::api()->debug = true;
           $field['ids'] = implode(',',$_GET['scenic_id']);
           $ticket = Tickettemplatebase::api()->lists($field);
           $info = ApiModel::getLists($ticket);
       }
        $this->render('index2',  compact('info'));
    }
    
    //发布产品 
    public function actionNewticket(){
         if (Yii::app()->request->isPostRequest) {
             
            foreach ($_REQUEST['ticket_id'] as $k=>$val){
                $field['scenic'][] = $k;
                if(is_array($val)){
                    foreach ($val as $v){
                        $field['point'][] = $v ;
                    }
                }else{
                    $field['point'][] = $v ;
                }
            }

            $field['scenic_id'] = implode(',',$field['scenic']);
            $field['ticket_template_base_ids'] = implode(',',$field['point']);
            unset($field['point']); unset($field['scenic']);
             
             if (strtotime($_REQUEST['date_available'][1]) <= strtotime($_REQUEST['date_available'][2])) {
                $field['date_available'] = strtotime($_REQUEST['date_available'][1].' 00:00:00').','.strtotime($_REQUEST['date_available'][2].' 23:59:59');
                unset($_REQUEST['date_available'][1]);unset($_REQUEST['date_available'][2]);
            } else {
                $this->_end(1, '开始时间不得晚于结束时间');
            }
            
            $field['scheduled'] = $_REQUEST['scheduled'] * 3600 * 24;
            $arr = explode(':', $_REQUEST['scheduledtime']);
            $field['scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
            $field['scheduled_time'] = $field['scheduled'] + $field['scheduledtime'];
            
            
            $field['refund'] = $_REQUEST['refund'];
            $field['fat_price'] = $_REQUEST['fat_price'];
            $field['group_price'] = $_REQUEST['group_price'];
            $field['name'] = $_REQUEST['name'];
            
            $field['mini_buy'] = $_REQUEST['mini_buy'];
            $field['max_buy'] = 1000;
           
            $field['organization_id'] = yii::app()->user->org_id;
            $field['user_id'] = yii::app()->user->uid;
            
          //  print_r($field);
            $addt = TicketTemplate::api()->addGenerate($field);
            //print_r($addt);
            if (ApiModel::isSucc($addt)) {
                $this->_end(0, $addt['message'],$addt['body']['id']);
            } else {
                //跳转到添加页面 然后给一个提示 说添加失败
                $this->_end(0, $addt['message']);
            }
            
         }
         
    }    
    //发布产品3
    public function actionIndex3(){
      $id = Yii::app()->request->getParam('id');
      echo $id;
		$data['id'] = $id;
		if (intval($id) > 0) {
			$result = Ticketrule::api()->detail(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'show_items' => 1
			));
			if ($result['code'] == 'succ') {
				$data = $result['body'];
				foreach ($data['rule_items'] as $item) {
					$rule = array();
					$rule['date'] = $item['date'];
					$rule['s_price'] = $item['fat_price'];
					if (strpos($rule['s_price'], '%') === 0) {
						$rule['s_price']{0} = '';
						$rule['s_price'] .= '%';
					}
					$rule['g_price'] = $item['group_price'];
					if (strpos($rule['g_price'], '%') === 0) {
						$rule['g_price']{0} = '';
						$rule['g_price'] .= '%';
					}
					$rule['storage'] = $item['reserve'];
					$data['rules'][] = $rule;
				}
			}
		}
	$this->render('index3', $data);
    }
   
    
    public function actionCommit() {
		header('Content-type: application/json');

		$params = Yii::app()->request->getParam('params');
		$pid = Yii::app()->request->getParam('pid');

		$data['supplier_id'] = Yii::app()->user->org_id;
		$data['name'] = Yii::app()->request->getParam('name');
		$data['desc'] = Yii::app()->request->getParam('desc');
		$data['user_id'] = Yii::app()->user->uid;
		$data['user_name'] = Yii::app()->user->account;
		if (intval($pid) > 0) {
			$data['id'] = intval($pid);
			Ticketrule::api()->update($data);
		} else {
			$result = Ticketrule::api()->add($data);
			if ($result['code'] == 'succ') {
				$data['id'] = $pid = $result['body']['id'];
			} else {
				echo json_encode(array(
					'code' => 500,
				    'message' => '保存失败'.$result['message']
				), JSON_UNESCAPED_UNICODE);
				exit;
			}
		}

		if ($pid > 0 && count($params) > 0) {
			$data = array();
			$data['rule_id'] = $pid;
			$data['supplier_id'] = Yii::app()->user->org_id;
			$data['days'] = implode(',', $params);
			$data['fat_price'] = Yii::app()->request->getParam('s_price');
			if (intval($data['fat_price']) > 0) {
				$data['fat_price'] = $this->types[Yii::app()->request->getParam('s_type')] . $data['fat_price'];
			} else {
				unset($data['fat_price']);
			}
			$data['group_price'] = Yii::app()->request->getParam('g_price');
			if (intval($data['group_price']) > 0) {
				$data['group_price'] = $this->types[Yii::app()->request->getParam('g_type')] . $data['group_price'];
			} else {
				unset($data['group_price']);
			}
			$data['reserve'] = Yii::app()->request->getParam('storage');
			if (intval($data['reserve']) == 0) {
				unset($data['reserve']);
			}
			$data['user_id'] = Yii::app()->user->uid;
			$data['user_name'] = Yii::app()->user->account;
			$result = Ticketrule::api()->setitem($data);
			if ($result['code'] == 'succ') {
				echo json_encode(array(
					'code' => 200,
					'id' => $pid,
					'message' => '保存成功'
				), JSON_UNESCAPED_UNICODE);
				exit;
			} else {
				echo json_encode(array(
					'code' => 500,
					'message' => '保存失败'.$result['message']
				), JSON_UNESCAPED_UNICODE);
				exit;
			}
		}

		echo json_encode(array(
			'code' => 200,
			'id' => $pid,
			'message' => '保存成功'
		), JSON_UNESCAPED_UNICODE);
	}
    
 
}
