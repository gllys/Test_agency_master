<?php

class GoodsController extends Controller {

    public function actionIndex() {
        $this->render('index');
    }

    //单票发布
    public function actionSingle() {
        //获取改机构下面的景区
        $param['organization_id'] = yii::app()->user->org_id;

        $lists = Landscape::api()->byorg($param,0);
        $list = ApiModel::getData($lists); //景区
        //选中的景区 获取景点
        if (Yii::app()->request->isPostRequest) {
            $field['landscape_ids'] = $ids = $_POST['ids'];
            $field['status'] = 1;
            $field['items'] = 1000;
            $field['organization_ids'] = Yii::app()->user->org_id; //机构id
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            if (!empty($ids)) {
                $vals = '';
                foreach ($data as $key => $item) {
                    $vals = $vals . '<div>'
                        . '<input type="checkbox" value="' . $item['id'] . '" id="remember' . $item['id'] . '" checked="checked" name="view_point[]">'
                        . '<label for="remember' . $item['id'] . '">' . $item['name'] . '</label></div>';
                }
                echo json_encode($vals);
                Yii::app()->end();
            }
        }

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            //获取票基本信息
            $tparam['ticket_id'] = $_GET['id'];
            $ticketinfo = TicketTemplate::api()->ticketinfo($tparam);
            $ticket = ApiModel::getData($ticketinfo);
            $this->render('singleedit', compact('list', 'ticket'));
        } else {
            $this->render('single', compact('list'));
        }
    }

    //联票发布
    public function actionUnion() {
        //获取改机构下面的景区
        $param['organization_id'] = yii::app()->user->org_id;

        $lists = Landscape::api()->byorg($param);
        $list = ApiModel::getData($lists); //景区
        //选中的景区 获取景点
        if (Yii::app()->request->isPostRequest) {
            $field['landscape_ids'] = $ids = $_POST['ids'];
            $field['status'] = 1;
            $field['organization_ids'] = Yii::app()->user->org_id; //机构id
            $field['items'] = 1000;
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            $str = '';
            foreach ($list as $key => $model) {
                if ($ids == $model['id']) {
                    $str = $str . '<tr class="landscape-box landscape-box-' . $ids . '" box-by-id="' . $ids . '"><th style="width:140px"><a>' .
                        $model['name'] . "</a></th><td style=\"text-align:left\">";
                }
            }
            $vals = '';
            foreach ($data as $key => $item) {

                if ($ids == $item['landscape_id']) {
                    $vals = $vals . '<span class="mr20">'
                        . '<input type="checkbox" value="' . $item['id'] . '" id="remember' . $item['id'] . '" checked="checked" pid="' . $item['landscape_id'] . '"  name="view_point[]">'
                        . '<label for="remember' . $item['id'] . '">' . $item['name'] . '</label></span>';
                }
            }
            $str = $str . $vals . '</td></tr>';
            echo json_encode($str);
            Yii::app()->end();
        }
        $this->render('union', compact('list'));
    }

    //任务单发布
    public function actionTask() {
        //获取改机构下面的景区
        $param['organization_id'] = yii::app()->user->org_id;

        $lists = Landscape::api()->byorg($param);
        $list = ApiModel::getData($lists); //景区
        //选中的景区 获取景点
        if (Yii::app()->request->isPostRequest) {
            $field['landscape_ids'] = $ids = $_POST['ids'];
            $field['status'] = 1;
            $field['organization_ids'] = Yii::app()->user->org_id; //机构id
            $field['items'] = 1000;
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            if (!empty($ids)) {
                $vals = '';
                foreach ($data as $key => $item) {
                    $vals = $vals . '<div>'
                        . '<input type="checkbox" value="' . $item['id'] . '" id="remember' . $item['id'] . '" checked="checked" name="view_point[]">'
                        . '<label for="remember' . $item['id'] . '">' . $item['name'] . '</label></div>';
                }
                echo json_encode($vals);
                Yii::app()->end();
            }
        }


        $this->render('task', compact('list'));
    }

    //添加任务单 联票
    public function actionAdd() {
        if (Yii::app()->request->isPostRequest) {
            $field = $_REQUEST;


            if (!empty($_REQUEST['view_point'][0]) && isset($_REQUEST['view_point'][0])) {
                $field['view_point'] = implode(',', $_REQUEST['view_point']);
            } else {
                $this->_end(1, '景点不可以为空！');
            }
            if (empty($_REQUEST['mini_buy']) && isset($_REQUEST['mini_buy'])) {
                $this->_end(1, '最小购票数,不可为空！');
            }
            $field['scheduled'] = $_REQUEST['scheduled'] * 3600 * 24;
            $arr = explode(':', $_REQUEST['scheduledtime']);
            $field['scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
            $field['scheduled_time'] = $field['scheduled'] + $field['scheduledtime'];

            if (strtotime($_REQUEST['date_available'][1]) <= strtotime($_REQUEST['date_available'][2])) {
                $field['date_available'] = strtotime($_REQUEST['date_available'][1].' 00:00:00').','.strtotime($_REQUEST['date_available'][2].' 23:59:59');
            } else {
                $this->_end(1, '开始时间不得晚于结束时间');
            }
            if (empty($field['remark'])) {
                $this->_end(1, '门票说明不可为空！');
            }
            if (count($_REQUEST['week_time']) > 0) {
                $field['week_time'] = implode(',', $_REQUEST['week_time']);
            } else {
                $this->_end(1, '适用日期不可为空！');
            }

            $field['organization_id'] = yii::app()->user->org_id;
            $field['user_id'] = yii::app()->user->uid;
            $field['max_buy'] = 100;
            $field['state'] = 0;

            unset($field['scheduled']);
            unset($field['scheduledtime']);
            unset($field['hour']);
            unset($field['minute']);
            //保存任务单
            if ($_REQUEST['type'] == 'task') {
                $datas = TicketTemplate::api()->addTask($field);
                if ($datas['code'] == 'succ') {
                    //任务单添加成功 跳转到任务单仓库          
                    $this->_end(0, $datas['message']);
                } else {
                    $this->_end(1, '保存失败,请重新添加');
                }
            }

            //保存联票
            if ($_REQUEST['type'] == 'union') {
                if (count($_REQUEST['scenic_id']) >= 2) {
                    $field['scenic_id'] = join(',', $_REQUEST['scenic_id']);
                } else {
                    $this->_end(1, '联票最少选择2个景区');
                }
                $lists = TicketTemplate::api()->addUnion($field);
                if (ApiModel::isSucc($lists)) {
                    $this->_end(0, $lists['message']);
                } else {
                    //跳转到添加页面 然后给一个提示 说添加失败
                    $this->_end(0, $lists['message']);
                }
            }

            //保存单票
            if ($_REQUEST['type'] == 'single') {
                $list = TicketTemplate::api()->addGenerate($field);
                if ($list['code'] == 'succ') {
                    $this->_end(0, $list['message']);
                } else {
                    $this->_end(1, $list['message']);
                }
            }
        }
    }

    //单票编辑展示
    public function actionSingleedit() {
        //获取改机构下面的景区
        $param['organization_id'] = yii::app()->user->org_id;

        $lists = Landscape::api()->byorg($param);
        $list = ApiModel::getData($lists); //景区
        //票基本信息
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            //获取票基本信息
            $tparam['ticket_id'] = $_GET['id'];
            $ticketinfo = TicketTemplate::api()->ticketinfo($tparam);
            $ticket = ApiModel::getData($ticketinfo);
        }


        //选中的景区 获取景点
        if (Yii::app()->request->isPostRequest) {
            $field['landscape_ids'] = $ids = $_POST['ids'];
            $field['status'] = 1;
            $field['organization_ids'] = Yii::app()->user->org_id;
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            if (!empty($ids)) {
                $vals = '';
                foreach ($data as $key => $item) {
                    if (strstr($_POST['view'], $item['id'])) {
                        $strs = 'checked="checked"';
                    } else {
                        $strs = "";
                    }
                    $vals = $vals . '<div>'
                        . '<input type="checkbox" value="' . $item['id'] . '" id="remember1"  name="view_point[]" ' . $strs . '>'
                        . '<label for="remember1">' . $item['name'] . '</label></div>';
                }
                echo json_encode($vals);
                Yii::app()->end();
            }
        }

        //print_r($ticket);
        $this->render('singleedit', compact('list', 'ticket', 'view'));
    }

    //联票发布
    public function actionUnionedit() {
        //获取改机构下面的景区
        $param['organization_id'] = yii::app()->user->org_id;

        $lists = Landscape::api()->byorg($param);
        $list = ApiModel::getData($lists); //景区
        //选中的景区 获取景点
        if (Yii::app()->request->isPostRequest) {
            $field['landscape_ids'] = $ids = $_POST['ids'];
            $field['status'] = 1;
            $field['organization_ids'] = Yii::app()->user->org_id;
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            $str = '';
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    foreach ($list as $key => $model) {
                        if ($id == $model['id']) {
                            $str = $str . '<tr class="landscape-box landscape-box-' . $ids . '" box-by-id="' . $ids . '"><th><a>' . $model['name'] . "<i class='glyphicon glyphicon-remove'></i></a></th><td>";
                        }
                    }
                    $vals = '';
                    foreach ($data as $key => $item) {

                        if ($id == $item['landscape_id']) {
                            if (strstr($_POST['view'], $item['id'])) {
                                $strs = 'checked="checked"';
                            } else {
                                $strs = "";
                            }
                            $vals = $vals . '<div>'
                                . '<input type="checkbox" value="' . $item['id'] . '" id="remember1" ' . $strs . '  name="view_point[]">'
                                . '<label for="remember1">' . $item['name'] . '</label></div>';
                        }
                    }
                    $str = $str . $vals . '</td></tr>';
                }
                echo json_encode($str);
                Yii::app()->end();
            }
        }

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            //获取票基本信息
            $tparam['ticket_id'] = $_GET['id'];
            $ticketinfo = TicketTemplate::api()->ticketinfo($tparam);
            $ticket = ApiModel::getData($ticketinfo);
        }

        $this->render('unionedit', compact('list', 'ticket'));
    }

    //任务单发布
    public function actionTaskedit() {
        //获取改机构下面的景区
        $param['organization_id'] = yii::app()->user->org_id;

        $lists = Landscape::api()->byorg($param);
        $list = ApiModel::getData($lists); //景区
        //选中的景区 获取景点
        if (Yii::app()->request->isPostRequest) {
            $field['landscape_ids'] = $ids = $_POST['ids'];
            $field['status'] = 1;
            $field['organization_ids'] = Yii::app()->user->org_id;
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            if (!empty($ids)) {
                $vals = '';
                foreach ($data as $key => $item) {
                    if (strstr($_POST['view'], $item['id'])) {
                        $strs = 'checked="checked"';
                    } else {
                        $strs = "";
                    }
                    $vals = $vals . '<div>'
                        . '<input type="checkbox" value="' . $item['id'] . '" id="remember1"  name="view_point[]" ' . $strs . '>'
                        . '<label for="remember1">' . $item['name'] . '</label></div>';
                }
                echo json_encode($vals);
                Yii::app()->end();
            }
        }

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            //获取票基本信息
            $tparam['ticket_id'] = $_GET['id'];
            $ticketinfo = TicketTemplate::api()->ticketinfo($tparam);
            $ticket = ApiModel::getData($ticketinfo);
        }
        $this->render('taskedit', compact('list', 'ticket'));
    }

    //编辑任务单 联票
    public function actionUpdate() {
        if (Yii::app()->request->isPostRequest) {
            $field = $_REQUEST;
            if (!empty($_REQUEST['view_point'][0]) && isset($_REQUEST['view_point'][0])) {
                $field['view_point'] = implode(',', $_REQUEST['view_point']);
            } else {
                $this->_end(1, '景点不可以为空！');
            }
            if (empty($_REQUEST['mini_buy']) && isset($_REQUEST['mini_buy'])) {
                $this->_end(1, '最小购票数,不可为空！');
            }

            $field['scheduled'] = $_REQUEST['scheduled'] * 3600 * 24;
            $arr = explode(':', $_REQUEST['scheduledtime']);
            $field['scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
            $field['scheduled_time'] = $field['scheduled'] + $field['scheduledtime'];

            if (strtotime($_REQUEST['date_available'][1]) <= strtotime($_REQUEST['date_available'][2])) {
               $field['date_available'] = strtotime($_REQUEST['date_available'][1].' 00:00:00').','.strtotime($_REQUEST['date_available'][2].' 23:59:59');
            } else {
                $this->_end(1, '开始时间不得晚于结束时间');
            }
            $field['week_time'] = join(',', $_REQUEST['week_time']);
            //$field['organization_id'] = yii::app()->user->org_id;
            $field['or_id'] = yii::app()->user->org_id;
            $field['user_id'] = yii::app()->user->uid;
            $field['max_buy'] = 100;
            $field['state'] = 0;

            unset($field['scheduled']);
            unset($field['scheduledtime']);
            unset($field['hour']);
            unset($field['minute']);
            // TicketTemplate::api()->debug = true;
            //保存任务单 单票
            if ($_REQUEST['type'] == 'task') {
                $field['type'] = 1;
                $data = TicketTemplate::api()->update($field);
                if (ApiModel::isSucc($data)) {
                    //任务单添加成功 跳转到任务单仓库          
                    $this->_end(0, $data['message']);
                } else {
                    $this->_end(0, '保存失败,请重新添加');
                }
            }

            if ($_REQUEST['type'] == 'single') {
                $field['type'] = 0;
                $datas = TicketTemplate::api()->update($field);
                if (ApiModel::isSucc($datas)) {
                    //任务单添加成功 跳转到任务单仓库          
                    $this->_end(0, $datas['message']);
                } else {
                    $this->_end(0, '保存失败,请重新添加');
                }
            }

            //保存联票
            if ($_REQUEST['type'] == 'union') {
                //$field['type'] = 2 ;
                $field['scenic_id'] = join(',', $_REQUEST['scenic_id']);
                //print_r($field);
                $lists = TicketTemplate::api()->update($field);
                // print_r($lists);exit;
                if (ApiModel::isSucc($lists)) {
                    $this->_end(0, $lists['message']);
                } else {
                    //跳转到添加页面 然后给一个提示 说添加失败
                    $this->_end(0, $lists['message']);
                }
            }
        }
    }

    //获取景区的省份id
    public function actionLandscape() {
        $param['id'] = $_REQUEST['id'];
        $result = Landscape::api()->detail($param);
        if($result['code'] == 'succ'){
            $landlist = $result['body'];
            echo json_encode(array('land' => $landlist));
        }else{
            $this->_end(0, '该景区不存在');
        }    
    }

}
