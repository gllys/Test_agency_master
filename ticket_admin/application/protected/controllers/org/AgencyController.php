<?php

class AgencyController extends Controller {

     public $verify = array(
        'apply' => '待审核',
        'checked' => '已审核',
        'reject' => '驳回'
    );

    public function actionIndex() {
        //搜索条件
        $params = array_filter($_GET); //过滤掉空值
        $params['type'] = 'agency';
        $params['current'] = isset($params['page']) ? $params['page'] : 1;

        if (empty($params['start_date']) && !empty($params['end_date'])) {
            $params['start_date'] = '2000-01-01';
        }

        if (!empty($params['start_date']) && empty($params['end_date'])) {
            $params['start_date'] = date('Y-m-d');
        }

        if (!empty($params['start_date'])) {
            $params['created_at'] = $params['start_date'] . ' - ' . $params['end_date'];
            unset($params['start_date']);
            unset($params['end_date']);
        }

        //列表
        $data = Organizations::api()->list($params);
        $lists = ApiModel::getLists($data);

        //分页
        $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目

        $this->render('index', compact('lists', 'pages'));
    }

    /**
     * 机构审核
     *
     * @return void
     * @author cuiyulei
     * */
    public function actionVerify() {
        $params = $_POST;
        $params['uid'] = Yii::app()->user->uid;
        $params['verify_status'] = $params['status'] == 'checked' ? 'reject' : 'checked';
        unset($params['status']);
        $data = Organizations::api()->edit($params);
        if ($data['code'] == 'succ') {
            echo 1;
        } else {
            echo $data['message'];
        }
    }

    //供应商详情
    public function actionView($id) {
        $rs = Organizations::api()->show(array('id' => $id));
        $data['data'] = array($rs['body']);
        $this->render('view', $data);
    }

    //供应商员工
    public function actionStaff($id) {
        $rs = AgencyUser::api()->lists(array('organization_id' => $id));
		$datas = array();
		if($rs["code"] == 'succ') {
			$datas["data"] = $rs['data'];
			$datas["userroles"] = $this->getUserRoles($rs['data']);
		}
		$this->render('staff', $datas);
    }

	private function getUserRoles($datas) {
		$ids = array();
		
		foreach ($datas as $val) {
			$ids[] = $val["id"];
		}
		$ids = array_unique($ids);
		
		$rs = AgencyUser::api()->roles(array("ids" => implode(",",$ids)));
		if($rs["code"] == "succ") {
			return $rs["data"];
		} else {
			return array();
		}
	}

    //供应商编辑
    public function actionEdit($id) {
        if (Yii::app()->request->isPostRequest) {
            if (empty($_POST['province_id']) && empty($_POST['city_id']) && empty($_POST['district_id'])) {
                echo json_encode(array('errors' => '省市区至少选择一个'));
                Yii::app()->end();
            }
            $params = $_POST;
            $params['uid'] = Yii::app()->user->uid;
            $params['id'] = $id;
            $rs = Organizations::api()->edit($params);
            if ($rs['code'] == 'succ') {
                echo json_encode(array('data' => $params));
            } else {
                echo json_encode(array('errors' => $rs['message']));
            }
            Yii::app()->end();
        }
        $organizations = array();
        $info = Organizations::api()->show(array('id' => $id));
        $organizations = ApiModel::getData($info);
        $this->render('edit', compact('organizations'));
    }

}
