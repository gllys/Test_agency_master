<?php

class HistoryController extends Controller {

    public function actionView() {
        $this->actionIndex();
    }

    public function actionIndex() {
        $params = $_REQUEST;
        $data['status_labels'] = array('unpaid' => '未支付', 'cancel' => '已取消', 'paid' => '已付款', 'finish' => '已完成', 'billed' => '已结款');
        $data['status_class'] = array('unpaid' => 'danger', 'cancel' => 'warning', 'paid' => 'success', 'finish' => 'info', 'billed' => 'error');
        $data['status'] = array_keys($data['status_labels']);
        if (!empty($params)) {
            if (isset($params['status']) && !in_array($params['status'], $data['status'])) {
                unset($params['status']);
            }
        }

        $data['get'] = $params;
        $org_id = Yii::app()->user->org_id;
    
        if (intval($org_id) > 0) {
            $params['supplier_id'] = $org_id;
            $params['current'] = isset($params['page']) ? $params['page'] : 1;
            $params['items'] = 20;
            $params['type'] = 0;
            $result = Order::api()->lists($params);
            if ($result['code'] == 'succ') {
                $data['lists'] = $result['body'];
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }
        }
        $this->render('index', $data);
    }

    // Uncomment the following methods and override them if needed
    /*
      public function filters()
      {
      // return the filter configuration for this controller, e.g.:
      return array(
      'inlineFilterName',
      array(
      'class'=>'path.to.FilterClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }

      public function actions()
      {
      // return external action classes, e.g.:
      return array(
      'action1'=>'path.to.ActionClass',
      'action2'=>array(
      'class'=>'path.to.AnotherActionClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }
     */
}
