<?php

class DetailController extends Controller {

    public function actionIndex() {
        $param['id'] = $_GET['id'];
        $bill = Bill::api()->detail($param);
        $data['detail'] = $bill['body'];
        $this->render('index', $data);
    }

    public function actionFinish() {
        $param['id'] = $_POST['id'];
        $param['type'] = 1;
        $data = Bill::api()->finish($param);
        if ($data['code'] == 'succ') {
            $this->_end(0, $data['message']);
        } else {
            $this->_end(1, $data['message']);
        }
    }

}
