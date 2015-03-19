<?php

class OrganizationController extends Controller {

    //机构信息页 编辑
    public function actionIndex() {
        $org_id = Yii::app()->user->org_id;
        if (!empty($org_id) && intval($org_id) > 0) {
            $info = Organizations::api()->show(array('id' => $org_id), 0);
            if ($info['code'] == 'succ') {
                $data['organizations'] = $info['body'];
                /*
                 * 老数据遗留问题，省市区三联动无法达成，导致部分机构只保留了其中一些数值，故删除
                 */
                if(empty($info['body']['province_id'])){
                    unset($data['organizations']['city_id']);
                    unset($data['organizations']['district_id']);
                }
                if(empty($info['body']['city_id'])){
                    unset($data['organizations']['district_id']);
                }

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
        if (Yii::app()->request->isPostRequest) {
            $_POST['business_license'] = addslashes($_POST['business_license']); 
            $_POST['supply_type'] = 0;
            if(!isset($_POST['province_id']) || empty($_POST['province_id'])  || $_POST['province_id'] == '__NULL__'){
                echo json_encode(array('errors' => '省市区至少选择一项'));
                exit;
            }
            if (isset($_POST['id'])) {
                $_POST['uid'] = Yii::app()->user->uid;
                $_POST['verify_status'] = 'checked';
                $result = Organizations::api()->edit($_POST);
            } else {
                $_POST['type'] = 'supply';
                $result = Organizations::api()->reg($_POST);
                if ($result['code'] == 'succ' && isset($result['body']['id'])) {
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
