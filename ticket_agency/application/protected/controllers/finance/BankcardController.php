<?php

class BankcardController extends Controller {

    public function actionIndex() {
        $bank = Bank::api()->list();
        $data['bank'] = $bank['body'];

        $param['p'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 10;
        $param['organization_id'] = Yii::app()->user->org_id;
        $bank_list = Bank::api()->list_own($param, 0);
        //var_dump($bank_list);exit;
        $data['list'] = empty($bank_list['body']) ? array() : $bank_list['body']['data'];

        //设置分页
        $pagination = ApiModel::getPagination($bank_list);
        $data['pages'] = new CPagination($pagination['count']);
        $data['pages']->pageSize = 10;

        $this->render('index', $data);
    }

//编辑银行卡
    public function actionEdit() {
        $bank = Bank::api()->list();
        $data['bank'] = $bank['body'];
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['id'] = $_GET['id'];
        $bank_list = Bank::api()->list_own($param);
        $data['list'] = empty($bank_list['body']) ? array() : $bank_list['body']['data'];
        //var_dump($data['list']);
        $this->renderPartial('edit', $data);
    }

    //编辑支付宝
    public function actionEditalipay() {
        $bank = Bank::api()->list();
        $data['bank'] = $bank['body'];
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['id'] = $_GET['id'];
        $bank_list = Bank::api()->list_own($param);
        $data['list'] = empty($bank_list['body']) ? array() : $bank_list['body']['data'];
        //var_dump($data['list']);
        $this->renderPartial('editalipay', $data);
    }

    public function actionSaveBank() {
        if (Yii::app()->request->isPostRequest) {
            if ($_POST['type'] == 'alipay') {
                $_POST['open_bank'] = '支付宝';
            }
            if ($_POST['type'] == 'bank') {
                if (empty($_POST['bank_id']) || empty($_POST['open_bank']) || empty($_POST['account']) || empty($_POST['account_name'])) {
                    $this->_end(1, '信息不可为空！');
                }
                if (strlen($_POST['account']) < 15 || strlen($_POST['account']) > 20) {
                    $this->_end(1, '卡号不正确');
                }
            }
            $_POST['organization_id'] = Yii::app()->user->org_id;
            $data = Bank::api()->add_own($_POST);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    public function actionDelBank() {
        if (Yii::app()->request->isPostRequest) {
            $param['is_del'] = 1;
            $param['id'] = $_GET['id'];
            $param['account'] = $_GET['account'];
            $param['account_name'] = $_GET['account_name'];
            $data = Bank::api()->edit_own($param);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    public function actionEditBank() {
        if (Yii::app()->request->isPostRequest) {
            if ($_POST['type'] == 'alipay') {
                $_POST['open_bank'] = '支付宝';
            }
            if ($_POST['type'] == 'bank') {
                if (empty($_POST['bank_id']) || empty($_POST['open_bank']) || empty($_POST['account']) || empty($_POST['account_name'])) {
                    $this->_end(1, '信息不可为空！');
                }
                if (strlen($_POST['account']) < 15 || strlen($_POST['account']) > 20) {
                    $this->_end(1, '卡号不正确');
                }
            }

            $data = Bank::api()->edit_own($_POST);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    public function actionUpdateBank() {
        if (Yii::app()->request->isPostRequest) {
            $param['organization_id'] = Yii::app()->user->org_id;
            $param['id'] = $_GET['id'];
            $bank_list = Bank::api()->list_own($param);
            $list = empty($bank_list['body']) ? array() : $bank_list['body']['data'];
            $param = empty($list[$_GET['id']]) ? array() : $list[$_GET['id']];
            $param['status'] = 'normal';
            $data = Bank::api()->edit_own($param);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

}
