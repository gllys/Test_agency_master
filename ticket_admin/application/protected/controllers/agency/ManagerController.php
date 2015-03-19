<?php

class ManagerController extends Controller {

    public function actionIndex() {
        $name = isset($_GET['name']) ? $_GET['name'] : "";
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $param = array('supplier_id' => Yii::app()->user->org_id, 'name' => $name, 'p' => $page);
        //Credit::api()->debug = true;
        $rs = Credit::api()->lists($param);
        $data['lists'] = empty($rs['body']) ? array() : $rs['body']['data'];
        $list_ids = array();
        if(count( $data['lists']) != 0){
            foreach($data['lists'] as $key => $val){

                $arr_id[] = $val['distributor_id'];
            }
            $ids = implode(',',$arr_id);
            $list_i = Organizations::api()->list(array('type'=>'agency','id'=>$ids));
            $list_ids = apiModel::getLists($list_i);
        }

        //print_r($rs);exit;
        if ($rs['code'] == 'succ') {
            $data['lists'] = empty($rs['body']) ? array() : $rs['body']['data'];
            $data['list_ids']  = $list_ids;
            $pagination = ApiModel::getPagination($rs);
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            $data['pages'] = $pages;
            $rs = Organizations::api()->show(array('id' => Yii::app()->user->org_id), 0);
            $data['orgInfo'] = ApiModel::getData($rs);
            $this->render('index', $data);
        } else {
            throw new CHttpException(500, $rs['message']);
        }
    }

    public function actionRes() {
        $param['name'] = isset($_POST['name']) ? $_POST['name'] : "";
        $param['type'] = 'agency';
        $param['items'] = 500;
        $rs = Organizations::api()->list($param);
        if ($rs['code'] == 'succ') {
            $data['lists'] = empty($rs['body']) ? array() : $rs['body']['data'];
            $this->render('res', $data);
        } else {
            throw new CHttpException(500, $rs['message']);
        }
    }

    public function actionHistory() {
        $params = $_REQUEST;
        $params['supply_id'] = Yii::app()->user->org_id;
        $params['current'] = isset($params['page']) ? $params['page'] : 1;
        $params['items'] = 10;
        $rs = Credit::api()->history($params);
        if ($rs['code'] == 'succ') {
            $data['lists'] = empty($rs['body']) ? array() : $rs['body']['data'];
            $data['pages'] = new CPagination($rs['body']['pagination']['count']);
            $data['pages']->pageSize = $params['items'];
            $this->render('history', $data);
        } else {
            throw new CHttpException(500, $rs['message']);
        }
    }

    public function actionCredit() {
        $id = $_GET['id'];
        $name = isset($_GET['remark']) ? $_GET['remark'] : "";
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        if (!empty($id)) {
            $data['id'] = $id;
            $result = Credit::api()->detail(array('id' => $id));
            $info = $result['body'];
            if (!empty($info)) {
                $param = array('type' => 0, 'supplier_id' => $info['supplier_id'], 'p' => $page,
                    'distributor_id' => $info['distributor_id'], 'remark' => $name);
                $rs = Credit::api()->listWithModif($param);
                $data['lists'] = empty($rs['body']) ? array() : $rs['body']['data'];
                $pagination = ApiModel::getPagination($rs);
                $data['pages'] = new CPagination($pagination['count']);
                $data['info'] = $info;
                //print_r($data);exit;
                $this->render('credit', $data);
            } else {
                throw new CHttpException('404', "找不到你请求的页面");
            }
        } else {
            throw new CHttpException(400, "找不到你请求的页面!");
        }
    }

    public function actionAdvance() {
        $id = $_GET['id'];
        $name = isset($_GET['remark']) ? $_GET['remark'] : "";
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        if (!empty($id)) {
            $data['id'] = $id;
            $result = Credit::api()->detail(array('id' => $id));
            $info = $result['body'];
            $return = Credit::api()->getMoney(array('supplier_id' => $info['supplier_id'],
                'distributor_id' => $info['distributor_id']));
            $data['balance_over'] = empty($return['body']) ? 0 : $return['body']['balance_money'];
            if (!empty($info)) {
                $param = array('type' => 1, 'supplier_id' => $info['supplier_id'], 'p' => $page,
                    'distributor_id' => $info['distributor_id'], 'remark' => $name);
                $rs = Credit::api()->listWithModif($param);
                $data['lists'] = empty($rs['body']) ? array() : $rs['body']['data'];
                $pagination = ApiModel::getPagination($rs);
                $data['pages'] = new CPagination($pagination['count']);
                $this->render('advance', $data);
            } else {
                throw new CHttpException('404', "找不到你请求的页面");
            }
        } else {
            throw new CHttpException(400, "找不到你请求的页面!");
        }
    }

    public function actionSaveCredit() {
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['type'] = 1;
            if ($param['infinite'] == 0) {
                $param['num'] = $_POST['type'] == 0 ? (0 - $param['num']) : $param['num'];
            }
            //$user = Yii::app()->redis->hMget('session_' . Yii::app()->getSession()->getSessionId(), array('id', 'organization_id'));
            $param['user_id'] = Yii::app()->user->uid;

            $rs = Credit::api()->update($param);
            if ($rs['code'] == 'succ') {
                $this->_end(0, "保存成功");
            } else {
                $this->_end(1, $rs['message']);
            }
        }
    }

    public function actionSaveAdvance() {
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['type'] = 0;
            $param['user_id'] = Yii::app()->user->uid;
            $rs = Credit::api()->update($param);
            if ($rs['code'] == 'succ') {
                $this->_end(0, "保存成功");
            } else {
                $this->_end(1, $rs['message']);
            }
        }
    }

    public function actionSetCycle() {
        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['type'] = $_POST['account_cycle'];
            $param['day'] = $_POST['account_cycle_day'];
            $rs = Credit::api()->setDay($param);
            if ($rs['code'] == 'succ') {
                $this->_end(0, "设置结算周期成功");
            } else {
                $this->_end(1, "设置结算周期失败");
            }
        }
    }

    public function actionOver() {
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $rs = Credit::api()->over($param);
            if ($rs['code'] == 'succ') {
                $this->_end(0, "设置透支额度成功");
            } else {
                $this->_end(1, "设置透支额度失败");
            }
        }
    }

    public function actionAddCredit() {
        if (Yii::app()->request->isPostRequest) {
            $param['distributor_id'] = $_POST['id'];
            $param['supplier_id'] = Yii::app()->user->org_id;
            $result = Organizations::api()->show(array('id' => $param['distributor_id']));
            $param['distributor_name'] = $result['body']['name'];
            $rs = Credit::api()->bind($param);
            if ($rs['code'] == 'succ') {
                $this->_end(0, '添加成功');
            } else {
                $this->_end(1, $rs['message']);
            }
        }
    }

    public function actionCheckCredit() {
        $result = Organizations::api()->show(array('id' => $_POST['id']));   //id调取相应地省市ID
        $data['city'] = Districts::model()->findByPk($result['body']['city_id']);
        if ($data['city']['name'] == '市辖区' || $data['city']['name'] == '县') {
            $data['city'] = Districts::model()->findByPk($result['body']['province_id']);
        } else {
            $data['city'] = $data['city'];
        }
        $data['detail'] = $result['body'];

        $param['name'] = $_POST['name'];
        $param['distributor_id'] = $_POST['id'];
        $param['supplier_id'] = Yii::app()->user->org_id;
        $rs = Credit::api()->lists($param);
        if (!empty($rs['body']['data'])) {
            $this->_end(0, $data);
        } else {
            $this->_end(1, $data);
        }
    }

    public function actionDelCredit() {
        $params['id'] = $_REQUEST['id'];
        $rs = Credit::api()->del($params);
        //print_r($rs);die;
        if($rs['code'] == 'succ'){
            $this->_end(0, "解除合作成功");
        }else{
            $this->_end(1,"解除合作失败");
        }  
    }

    //立刻结算
    public function actionGenbill() {
        if (Yii::app()->request->isPostRequest) {
            $details = Credit::api()->detail($_POST);
            if (!empty($details['body'])) {
                $param['supplier_id'] = $details['body']['supplier_id'];
                $param['distributor_id'] = $details['body']['distributor_id'];
                $rs = Bill::api()->genbill($param);
                if ($rs['code'] == 'succ') {
                    $this->_end(0, "操作成功！");
                } else {
                    $this->_end(1, $rs['message']);
                }
            } else {
                $this->_end(1, '分销商数据不存在！');
            }
        }
    }


    /*
     * 查看分销商的详细详细
     *xj
     * */
     public function actionLook(){
         $look['id'] = $_GET['id'];
         $look['type'] = 'agency';

         $lookdata = Organizations::api()->list($look);
         $ldata = apiModel::getLists($lookdata);
         $lookd = $ldata[$look['id']];

         $this->render('look',compact('lookd'));
     }
}