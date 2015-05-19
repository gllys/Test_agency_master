<?php

class ElectronicController extends Controller {

    public function actionIndex() {
        $landscape = Landscape::api()->detail(array('id' => $_GET['id']));
        if ($landscape['code'] == 'succ') {
            $data['landscape'] = $landscape['body'];
            $param['landscape_ids'] = $landscape['body']['id'];
            $param['organization_ids'] = Yii::app()->user->org_id;
            $param['status'] = 1;
            $poi = Poi::api()->lists($param);
            if ($poi['code'] == 'succ') {
                $data['poi'] = empty($poi['body']) ? array() : $poi['body']['data'];
            }
        }
        $data['province'] = Districts::model()->findAllByAttributes(array("level" => 1));
        $this->render('index', $data);
    }

    public function actionEdit() {
        $ticket = Tickettemplate::api()->ticketinfo($_GET);
        $data['ticket'] = $ticket['body'];
        $data['ticket']['from_to_time'] = explode(',', $data['ticket']['date_available']);
        $data['ticket']['from_to_time'][0] = date('Y-m-d', $data['ticket']['from_to_time'][0]);
        $data['ticket']['from_to_time'][1] = date('Y-m-d', $data['ticket']['from_to_time'][1]);
        $data['ticket']['more_time'][0] = floor($data['ticket']['scheduled_time'] / 86400);
        $second = $data['ticket']['scheduled_time'] % 86400;
        $time = date('H:i',$second);
        $data['ticket']['more_time'][1] = $time;
        $landscape = Landscape::api()->detail(array('id' => $ticket['body']['scenic_id']));
		if ($landscape && $landscape['code'] == 'succ') {
			$data['landscape'] = $landscape['body'];
			$param['landscape_ids'] = $landscape['body']['id'];
		}
        $param['organization_ids'] = Yii::app()->user->org_id;
        $param['status'] = 1;
        $poi = Poi::api()->lists($param);
        $data['poi'] = empty($poi['body']) ? array() : $poi['body']['data'];
        $data['province'] = Districts::model()->findAllByAttributes(array("level" => 1));
        $this->render('edit', $data);
    }

    public function actionSaveElectronic() {
        if (Yii::app()->request->isPOSTRequest) {
            if ($_POST['fat_price'] + $_POST['group_price'] == 0) {
                echo json_encode(array('errors' => '散客价团队价至少填写一个'));
            } else {
                if (!isset($_POST['view_point'])) {
                    echo json_encode(array('errors' => "至少需要选择一个子景点"));
                } else {
                    $_POST['view_point'] = implode(',', $_POST['view_point']);
                    if (strtotime($_POST['from_to_time'][0]) > strtotime($_POST['from_to_time'][1])) {
                        echo json_encode(array('errors' => "开始时间不得晚于结束时间"));
                    } else {
                        $_POST['from_to_time'][0] = strtotime($_POST['from_to_time'][0]);
                        $_POST['from_to_time'][1] = strtotime($_POST['from_to_time'][1]);
                        $_POST['date_available'] = implode(',', array($_POST['from_to_time'][0], $_POST['from_to_time'][1]));
                        unset($_POST['from_to_time']);
                        $_POST['organization_id'] = Yii::app()->user->org_id;
                        $_POST['user_id'] = Yii::app()->user->uid;
                        $_POST['week_time'] = implode(',', $_POST['week_time']);
                        $_POST['fit_platform'] = 1;
                        $_POST['full_platform'] = 1;
                        $_POST['max_buy'] = 100;
	                    $_POST['mini_buy'] = min(100, $_POST['mini_buy']);
                        $day = $_POST['more_time'][0] * 3600 * 24;
                        $second = strtotime('1970-01-01' . $_POST['more_time'][1]);
                        $_POST['scheduled_time'] = $day + $second;
                        unset($_POST['more_time']);
                        //$_POST['remark'] = preg_match("/[ '.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['remark']);
                        $data = Tickettemplate::api()->addGenerate($_POST);
                        if ($data['code'] == 'succ') {
                            echo json_encode(array('succ' => '保存成功'));
                        } else {
                            echo json_encode(array('errors' => $data['message']));
                        }
                    }
                }
            }
        }
    }

    public function actionUpdateElectronic() {
        if (Yii::app()->request->isPOSTRequest) {
            if ($_POST['fat_price'] + $_POST['group_price'] == 0) {
                echo json_encode(array('errors' => '散客价团队价至少填写一个'));
            } else {
                if (!isset($_POST['view_point'])) {
                    echo json_encode(array('errors' => "至少需要选择一个子景点"));
                } else {
                    $_POST['view_point'] = implode(',', $_POST['view_point']);
                    if (strtotime($_POST['from_to_time'][0]) > strtotime($_POST['from_to_time'][1])) {
                        echo json_encode(array('errors' => "开始时间不得晚于结束时间"));
                    } else {
                        $_POST['from_to_time'][0] = strtotime($_POST['from_to_time'][0]);
                        $_POST['from_to_time'][1] = strtotime($_POST['from_to_time'][1]);
                        $_POST['date_available'] = implode(',', array($_POST['from_to_time'][0], $_POST['from_to_time'][1]));
                        unset($_POST['from_to_time']);
                        $_POST['week_time'] = implode(',', $_POST['week_time']);
                        $_POST['max_buy'] = 100;
                        $_POST['fit_platform'] = 1;
                        $_POST['full_platform'] = 1;
                        $day = $_POST['more_time'][0] * 3600 * 24;
                        $second = strtotime('1970-01-01' . $_POST['more_time'][1]);
                        $_POST['scheduled_time'] = $day + $second;
                        unset($_POST['more_time']);
                        //$_POST['remark'] = preg_match("/[ '.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['remark']);
                        $data = Tickettemplate::api()->update($_POST);
                        if ($data['code'] == 'succ') {
                            echo json_encode(array('succ' => '保存成功'));
                        } else {
                            echo json_encode(array('errors' => $data['message']));
                        }
                    }
                }
            }
        }
    }

}
