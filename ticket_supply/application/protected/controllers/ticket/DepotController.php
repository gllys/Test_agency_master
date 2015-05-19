<?php

class DepotController extends Controller
{
	public function actionIndex()
	{
            /*
            type                	门票类型，0电子票，1任务单，2联票
            organization_id		机构id
            name	                联票名称
            scenic_id	                景区id
            */
            $param = $_GET; 
            if(isset($_GET)){ //判断表单是否提交
                !empty($_GET['name'])?$param['name'] = $_GET['name']:'';
                !empty($_GET['scenic_id'])?$param['scenic_id'] =$_GET['scenic_id']:'';
            }
            
            $param['type'] = isset($param['type'])?$param['type']:0;
            //$data['type_labels'] = array( '0' => '单票', '1' => '任务单','2' => '联票');
            $data['type_labels'] = array( '0' => '单票', '2' => '联票');
            $data['type'] = array_keys($data['type_labels']);
            if (!empty($params)) {
                if (isset($params['type']) && !in_array($params['type'], $data['type'])) {
                    unset($params['type']);
                }
            }
            
            $param['organization_id'] = yii::app()->user->org_id;
            $param['state'] = 0;
            
          // Tickettemplate::api()->debug = true;
            $param['current'] = isset($param['page']) ? $param['page'] : 1;
            $param['items'] = 15;
            $param['fields'] = "id,name";
            $lists = Tickettemplate::api()->store_list($param);
            $list = ApiModel::getLists($lists);
          //  print_r($param);
            //print_r($lists);exit;
            //分页
            $pagination = ApiModel::getPagination($lists);
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            
            $this->render('index',  compact('list','pages','param','data'));
	}
        
        
     //待上货架
    public function actionDownUp() {
        if (Yii::app()->request->isPostRequest) {
            // Landscape::api()->debug = true;
            $rs['id'] = $_POST['id'];
            $rs['state'] = 2;
            $rs['or_id'] = YII::app()->user->org_id;
            $data = Tickettemplate::api()->state($rs);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('downUp');
    }

   //删除票
    public function actionDel($id) {
        if (Yii::app()->request->isPostRequest) {
            $rs['id'] = $_GET['id'];
            $rs['or_id'] = YII::app()->user->org_id;
            $model = Tickettemplate::api()->delete($rs);
        }
    }
    
    //添加规则
     public function actionRule() {
         //票id
         $id = $_GET['id'];
         $field['supplier_id'] = Yii::app()->user->org_id;
         $lists =  Ticketrule::api()->lists($field, 0);
         $list = ApiModel::getLists($lists);
        $this->renderPartial('rule',  compact('list','id'));
    }
    
    //规则绑定票
    public function actionRuleadd() {
        if(Yii::app()->request->isPostRequest){
            $field['id'] = $_POST['id'];
            $field['or_id'] =Yii::app()->user->org_id;
            $field['rule_id'] = $_POST['rid'];
            $data = Tickettemplate::api()->update($field);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            }else{
                $this->_end(1, $data['message']);
            }
        }
    }
    
    
    
    
	
}
