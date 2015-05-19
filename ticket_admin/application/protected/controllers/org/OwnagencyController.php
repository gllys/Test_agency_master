<?php
class OwnagencyController extends Controller {

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

        //属于供应商 查找
        $distributor_id = array_unique(ArrayColumn::i_array_column($lists, 'id'));
        $result= Credit::api()->listbyxf(array('distributor_id'=>implode(',',$distributor_id),'items' => 1000));

      //  $result = Credit::api()->listbyxf(array('distributor_id' => $value['id']));

        //分页
        $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目

        $this->render('index', compact('lists', 'pages','result'));
    }

    public function actionGetAttach() {//获得用户所属的供应商及所有供应商列表
        $id = $_POST['id'];
        $data['mySupply'] = array();
        $data['supplyList'] = array();
        $rs = Credit::api()->listbyxf(array('distributor_id' => $id));
        if (isset($rs['body']['data']) && !empty($rs['body']['data'])) {
            //如果存在所属供应商
            $data['mySupply'] = $rs['body']['data'];
        }
        $result = Organizations::api()->list(array('type' => 'supply', 'items' => 1000));
        if ($result['code'] == "succ" && isset($result['body']['data'])) {
            $data['supplyList'] = $result['body']['data'];
        }
        echo json_encode($data);
    }

    public function actionSaveAttach() {
        $agency_id = $_POST['agency_id'];
        $ids = $_POST['ids'];
        $rs = Organizations::api()->bind_agency_batch(array('supply_ids' => $ids, 'agency_id' => $agency_id));
        if ($rs['code'] == "succ") {
            $data['error'] = 1;
            $data['message'] = "保存成功";
        } else {
            $data['error'] = 0;
            $data['message'] = $rs['message'];
        }
        echo json_encode($data);
    }

}
