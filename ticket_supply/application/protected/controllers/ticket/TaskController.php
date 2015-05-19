<?php

class TaskController extends Controller {

    public function actionIndex() {
        $this->render('index');
    }

    public function actionEditTask() {
        $get = $this->getGet();
        if (isset($get['id'])) {
            $ticket = Tickettemplate::api()->detail($get);
            if (!empty($ticket)) {
                $this->render('edit', compact($ticket));
            } else {
                throw new CHttpException(404, '未知的任务单.');
            }
        } else {
            throw new CHttpException(400, '请求的页面不存在.');
        }
    }

    public function actionAddTask() {
        if (Yii::app()->request->isPOSTRequest) {
            $data = Tickettemplate::api()->add($this->getPost());
            if ($data['code'] == 'succ') {
                
            } else {
                
            }
        }
        throw new CHttpException(400, '请求的页面不存在.');
    }

    public function actionUpdateTask() {
        if (Yii::app()->request->isPOSTRequest) {
            $data = Tickettemplate::api()->update($this->getPost());
            if ($data['code'] == 'succ') {
                
            } else {
                
            }
        }
        throw new CHttpException(400, '请求的页面不存在.');
    }

}
