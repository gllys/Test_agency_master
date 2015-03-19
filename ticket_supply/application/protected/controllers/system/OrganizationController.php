<?php

class OrganizationController extends Controller {

    //机构信息页 编辑
    public function actionIndex() {
        $org_id = Yii::app()->user->org_id;
        if (!empty($org_id) && intval($org_id) > 0) {
            $info = Organizations::api()->show(array('id' => $org_id), 0);
            if ($info['code'] == 'succ') {
                $data['organizations'] = $info['body'];
                $this->render('index', $data);
            } else {
                $this->redirect('/system/organization/compile');
            }
        } else {
            $this->redirect('/system/organization/compile');
        }
    }

    public function actionCompile() {
        $organizations = array();
        $org_id = Yii::app()->user->org_id;
        if (!empty($org_id) && intval($org_id) > 0) {
            $info = Organizations::api()->show(array('id' => $org_id), 0);
            if ($info['code'] == 'succ') {
                $organizations = ApiModel::getData($info);
                if ($organizations['status'] != 0) {
                     $this->redirect('/');
                }
            }
        }
        $this->render('compile', compact('organizations'));
    }

    //注册分销商
    public function actionSaveSupply() { 
	    //todo
        // $user = Yii::app()->redis->hMget('session_' . Yii::app()->getSession()->getSessionId(), array('id', 'organization_id'));
      
        if (Yii::app()->request->isPostRequest) {
            $_POST['business_license'] = addslashes($_POST['business_license']); 
            if(!isset($_POST['province_id']) || empty($_POST['province_id'])){
                echo json_encode(array('errors' => '省市区至少选择一项'));
                exit;
            }
            if (isset($_POST['id'])) {
                $_POST['uid'] = Yii::app()->user->uid;
                $_POST['verify_status'] = 'checked';
                $result = Organizations::api()->edit($_POST);
            } else {
                $_POST['type'] = 'supply';
                $result = Organizations::api()->reg($_POST); //var_dump($_POST); var_dump($result);exit;
                if ($result['code'] == 'succ' && isset($result['body']['id'])) {
                   //echo "string".$result['body']['id'];exit;
                        $users = Users::model()->findByPk(Yii::app()->user->uid);
                        $users->organization_id = $result['body']['id'];
                        $users->save();
                        $session_id = Yii::app()->getSession()->getSessionId();
                        Yii::app()->redis->hMset('session_' . $session_id, array('organization_id' => $result['body']['id']));
                        Yii::app()->user->setState('org_id', $result['body']['id']);
                }
            }
            if ($result['code'] == 'succ') {
                echo json_encode(array('succ' => '保存成功'));
            } else {
                echo json_encode(array('errors' => $result['message']));
            }
        }
    }

}
