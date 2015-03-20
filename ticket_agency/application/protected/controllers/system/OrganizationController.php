<?php

class OrganizationController extends Controller
{
    //机构信息页 编辑
	public function actionIndex()
	{
		$org_id = Yii::app()->user->org_id;
        if(!empty($org_id) && intval($org_id) > 0){
        	$info = Organizations::api()->show(array('id' => $org_id),0);
        	if($info['code'] == 'succ'){
        		$data['info'] = $info['body'];
                /*
                 * 老数据遗留问题，省市区三联动无法达成，导致部分机构只保留了其中一些数值，故删除
                 */
                if(empty($info['body']['province_id'])){
                    unset($data['info']['city_id']);
                    unset($data['info']['district_id']);
                }
                if(empty($info['body']['city_id'])){
                    unset($data['info']['district_id']);
                }

        		$this->render('index', $data);
        	}else{
		        $this->redirect('/system/organization/compile');
        	}       	
        }else{
        	$this->redirect('/system/organization/compile');
        }
	}


	public function actionCompile() {	
		$this->render('compile');
	}


	//注册分销商
	public function actionSaveAgency(){
		if(Yii::app()->request->isPostRequest){
			$_POST['business_license'] = addslashes($_POST['business_license']);
			$_POST['tax_license'] = addslashes($_POST['tax_license']);
			$_POST['certificate_license'] = addslashes($_POST['certificate_license']);
			if(!isset($_POST['province_id']) || empty($_POST['province_id']) || $_POST['province_id'] == '__NULL__'){
				echo json_encode(array('errors' => '省市区至少选择一项'));
				exit;
			}
        	if(isset($_POST['id'])){
				$_POST['uid'] = Yii::app()->user->uid;
				$result = Organizations::api()->edit($_POST);
			}else{
				$_POST['type'] = 'agency';
				$_POST['uid'] = Yii::app()->user->uid;
				$_POST['verify_status'] = 'apply';
				$_POST['status'] = '1';
				$_POST['is_distribute_person'] = '0';
				$_POST['is_distribute_group'] = '0';
				$result = Organizations::api()->reg($_POST);
				if($result['code'] == 'succ'){
					$users = Users::model()->findByPk(Yii::app()->user->uid);
					$users->organization_id = $result['body']['id'];
					$users->save();
					$session_id = Yii::app()->getSession()->getSessionId();
                	Yii::app()->user->setState('org_id', $result['body']['id']);
				}
			}
			if($result['code'] == 'succ'){
	        	echo json_encode(array('succ' => '保存成功'));
	        }else{
	        	echo json_encode(array('errors' => $result['message']));	        		     
	        }             
        }
	}


}
