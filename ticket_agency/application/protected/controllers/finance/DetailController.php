<?php

class DetailController extends Controller {

    public function actionIndex() {
        $param['id'] = $_GET['id'];
        $bill = Bill::api()->detail($param,0);
        $data['detail'] = $bill['body'];
        $this->render('index', $data);
    }

	public function actionFinish(){
		$param['id'] = $_POST['id'];
		$param['type'] = 0;
		$data = Bill::api()->finish($param,0);
		if($data['code'] == 'succ'){
            $this->_end(0,$data['message'] );
        }else{
            $this->_end(1,$data['message'] );
        }
    }

	public function actionUpimg(){
		$param['payed_img'] = $_POST['payed_img'];
		$param['id'] = $_POST['id'];
		$data = Bill::api()->upimg($param,0);
		if($data['code'] == 'succ'){
            $this->_end(0,$data['message'] );
        }else{
            $this->_end(1,$data['message'] );
        }
    }

}
