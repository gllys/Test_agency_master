<?php

/**
 * 门票管理控制器
 *
 * @author cyl
 * 2014-1-9
 * 
 * @version 1.0
 */
class TicketController extends BaseController {

    public function index() {
        $landscapesModel = $this->load->model('landscapes');
        $ticketTemplatesModel = $this->load->model('ticketTemplates');
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'relate' => 'thumbnail,poi,organization',
            'with' => 'districts',
            'order' => $landscapesModel->table . '.updated_at DESC',
            'filter' => array(
                $landscapesModel->table . '.deleted_at' => NULL
            )
        );

        $get = $this->getGet();

        //提交时间 按照更新时间检索
        if (!empty($get['updated_at']) && isset($get['updated_at'])) {
            $timeFilter = explode(' - ', $get['updated_at']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$landscapesModel->table . '.updated_at|between'] = $timeFilter;
        }

        //状态
        if (!empty($get['status']) && isset($get['status'])) {
            $param['filter'][$landscapesModel->table . '.status'] = $get['status'];
        }

        //名称
        if (!empty($get['landscape_name']) && isset($get['landscape_name'])) {
            $param['filter'][$landscapesModel->table . '.name|like'] = $get['landscape_name'];
        }

        //机构
        if (!empty($get['organization_name']) && isset($get['organization_name'])) {
            $organizationsModel = $this->load->model('organizations');
//            $param['join'][] = array(
//                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $landscapesModel->table . '.organization_id',
//            );
            $param['filter'][$organizationsModel->table . '.name|like'] = $get['organization_name'];
        }

        //机构类型
        $get['organization_type'] = 'landscape' ;
        if (!empty($get['organization_type']) && isset($get['organization_type'])) {
            $organizationsModel = $this->load->model('organizations');
            $param['join'][] = array(
                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $landscapesModel->table . '.organization_id',
            );
            $param['filter'][$organizationsModel->table . '.type'] = $get['organization_type'];
        }

        //接入状态
        if (!empty($get['location_hash']) && isset($get['location_hash'])) {
            if ($get['location_hash'] == 'no') {
                $param['filter'][$landscapesModel->table . '.location_hash|lethan'] = 0;
            } else {
                $param['filter'][$landscapesModel->table . '.location_hash|gthan'] = 0;
            }
        }

        $data['get'] = $get;
        $allStatus = LandscapeCommon::getLandscapeStatus();
        $data['allStatus'] = $allStatus;
        $landscapesList = $landscapesModel->commonGetList($param);

        $data['pagination'] = $this->getPagination($landscapesList['pagination']);
        $data['landscapesList'] = $landscapesList['data'];

        //TODO::查询二级票务
        $locationHashs = array();
        foreach ($data['landscapesList'] as $key => $value) {
            $filter = " source=1  AND deleted_at is null AND landscape_id=" . $value['id'];
            $order = "status DESC";
            $fields = "DISTINCT status";
            $second = $ticketTemplatesModel->getList($filter, '', $order, $fields);
            $data['landscapesList'][$key]['second'] = $second;

            //从数据中心获取接入的景区名称
            if ($value['location_hash']) {
                $locationHashs[] = $value['location_hash'];
            }
        }

        //从数据中心获取到的景区数据集合
        if ($locationHashs) {
            $itourismApiParam = array(
                'items' => $param['items'] ? $param['items'] : 9999,
                'filter' => 'hash:in_' . implode('_', $locationHashs),
            );
            $itourismApiCommon = $this->load->common('itourismApi');
            $result = $itourismApiCommon->getItourismLocationsInfo($itourismApiParam);
            $resultArr = json_decode($result, 1);
            if ($resultArr['status'] == 'succ') {
                $itourismLandscapesList = $resultArr['data']['data'];
                $newItourismLandscapesList = array();
                foreach ($itourismLandscapesList as $islKey => $islVal) {
                    $newItourismLandscapesList[$islVal['hash']] = $islVal;
                }
                $data['itour_ism_landscapes_list'] = $newItourismLandscapesList;
            }
        }

        //加载视图		
        $this->load->view('ticket/index', $data);
    }

    //旅行社门票
    public function agency() {
        $landscapesModel = $this->load->model('landscapes');
        $ticketTemplatesModel = $this->load->model('ticketTemplates');
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'relate' => 'thumbnail,poi,organization',
            'with' => 'districts',
            'order' => $landscapesModel->table . '.updated_at DESC',
            'filter' => array(
                $landscapesModel->table . '.deleted_at' => NULL
            )
        );

        $get = $this->getGet();

        //提交时间 按照更新时间检索
        if (!empty($get['updated_at']) && isset($get['updated_at'])) {
            $timeFilter = explode(' - ', $get['updated_at']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$landscapesModel->table . '.updated_at|between'] = $timeFilter;
        }

        //状态
        if (!empty($get['status']) && isset($get['status'])) {
            $param['filter'][$landscapesModel->table . '.status'] = $get['status'];
        }

        //名称
        if (!empty($get['landscape_name']) && isset($get['landscape_name'])) {
            $param['filter'][$landscapesModel->table . '.name|like'] = $get['landscape_name'];
        }

        //机构
        if (!empty($get['organization_name']) && isset($get['organization_name'])) {
            $organizationsModel = $this->load->model('organizations');
            $param['join'][] = array(
                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $landscapesModel->table . '.organization_id',
            );
            $param['filter'][$organizationsModel->table . '.name|like'] = $get['organization_name'];
        }

        //机构类型
        $get['organization_type'] = 'agency' ;
        if (!empty($get['organization_type']) && isset($get['organization_type'])) {
            $organizationsModel = $this->load->model('organizations');
            $param['join'][] = array(
                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $landscapesModel->table . '.organization_id',
            );
            $param['filter'][$organizationsModel->table . '.type'] = $get['organization_type'];
        }

        //接入状态
        if (!empty($get['location_hash']) && isset($get['location_hash'])) {
            if ($get['location_hash'] == 'no') {
                $param['filter'][$landscapesModel->table . '.location_hash|lethan'] = 0;
            } else {
                $param['filter'][$landscapesModel->table . '.location_hash|gthan'] = 0;
            }
        }

        $data['get'] = $get;
        $allStatus = LandscapeCommon::getLandscapeStatus();
        $data['allStatus'] = $allStatus;
        $landscapesList = $landscapesModel->commonGetList($param);

        $data['pagination'] = $this->getPagination($landscapesList['pagination']);
        $data['landscapesList'] = $landscapesList['data'];

        //TODO::查询二级票务
        $locationHashs = array();
        foreach ($data['landscapesList'] as $key => $value) {
            $filter = " source=1  AND deleted_at is null AND landscape_id=" . $value['id'];
            $order = "status DESC";
            $fields = "DISTINCT status";
            $second = $ticketTemplatesModel->getList($filter, '', $order, $fields);
            $data['landscapesList'][$key]['second'] = $second;

            //从数据中心获取接入的景区名称
            if ($value['location_hash']) {
                $locationHashs[] = $value['location_hash'];
            }
        }

        //从数据中心获取到的景区数据集合
        if ($locationHashs) {
            $itourismApiParam = array(
                'items' => $param['items'] ? $param['items'] : 9999,
                'filter' => 'hash:in_' . implode('_', $locationHashs),
            );
            $itourismApiCommon = $this->load->common('itourismApi');
            $result = $itourismApiCommon->getItourismLocationsInfo($itourismApiParam);
            $resultArr = json_decode($result, 1);
            if ($resultArr['status'] == 'succ') {
                $itourismLandscapesList = $resultArr['data']['data'];
                $newItourismLandscapesList = array();
                foreach ($itourismLandscapesList as $islKey => $islVal) {
                    $newItourismLandscapesList[$islVal['hash']] = $islVal;
                }
                $data['itour_ism_landscapes_list'] = $newItourismLandscapesList;
            }
        }

        //加载视图		
        $this->load->view('ticket/agency', $data);
    }

    //添加门票类型
    public function addTicketType() {
        $this->doAction('ticket', 'addTicketType', $this->getPost());
    }

    //编辑门票类型
    public function updateTicketType() {
        $this->doAction('ticket', 'updateTicketType', $this->getPost());
    }

    //删除门票类型
    public function deleteTicketType() {
        $this->doAction('ticket', 'deleteTicketType', $this->getGet());
    }

    /**
     * 预览一级票务
     *
     * @return void
     * @author cuiyulei
     * */
    public function preview() {
        $id = intval($this->getGet('id'));
        $ticketTemplatesModel = $this->load->model('ticketTemplates');
        $data = $ticketTemplatesModel->getScenicTicketDetail($id, 'all');
        $data['pageType'] = __FUNCTION__;
        $this->load->view('ticket/preview', $data);
    }

    /**
     * 票种类型
     *
     * @return void
     * @author cuiyulei
     * */
    public function type() {
        //页码
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'filter' => array(
                'deleted_at' => null
            )
        );

        $data = $this->_getTicketTypeInfo($param);

        //加载视图
        $this->load->view('ticket/type', $data);
    }

    //获取门票类型列表
    public function _getTicketTypeInfo($param = array()) {
        $ticketCommon = $this->load->common('ticket');
        $ticketTypeInfo = $ticketCommon->getTicketType($param);
        $data['ticketTypeInfo'] = $ticketTypeInfo['data'];
        $data['pagination'] = $this->getPagination($ticketTypeInfo);
        return $data;
    }

    //获取弹出层内容
    public function getModalJump() {
        $status = $this->getGet('status');
        $landscapeId = $this->getGet('id');
        $ticketTemplatesModel = $this->load->model('ticketTemplates');
        $landscapesModel = $this->load->model('landscapes');
        $unionTicketModel = $this->load->model('ticketTemplatesUnion');
        $filter = 'deleted_at is null and status=\'' . $status . '\' and (( landscape_id=' . $landscapeId . ' and source=1 ) or (landscape_id=' . $landscapeId . ' and source=2 and ticket_type=5))  ';
        $ticketTemplatesList = $ticketTemplatesModel->getList($filter);
        $data['list'] = $ticketTemplatesList;
        $data['landscape'] = $landscapesModel->getID($landscapeId);
        $newFilter = 'deleted_at is null and status=\'' . $status . '\' and (landscape_id=' . $landscapeId . ' and ticket_type=5 and status=\'unaudited\') ';
        $unionTicketList = $ticketTemplatesModel->getList($newFilter,'','','id','');
        if($unionTicketList){
			$ids = array();
	        if(is_array($unionTicketList)){
				foreach($unionTicketList as $value){
					array_push($ids,$value['id']);
				}
			}
			$ids = implode(',', $ids);
			if($ids){
				$unionList = $unionTicketModel->getList("ticket_templates_id in ($ids) and status=0",'','','ticket_templates_id','ticket_templates_id');
			}
        }
        if($unionList){
        	$unionTicketList = array();
        	if(is_array($unionList)){
        		foreach ($unionList as $newValue) {
        			array_push($unionTicketList,$newValue['ticket_templates_id']);
           		}
        	}
        }
		$unionTicketList = implode(',', $unionTicketList);
		if($unionTicketList){
			$data['union'] = $unionTicketList;
		}  
        $this->load->view('ticket/ticket_tpl', $data);
    }

    //一级票务审核
    public function landscapeVerify() {
        $post = $this->getPost();
        echo $this->doAction('landscape', 'verify', $post);
    }

    //二级票务审核
    public function ticketVerify() {
        $post = $this->getPost();
        echo $this->doAction('ticket', 'verify', $post);
    }

    //接入景区
    public function itourismLandscapes() {
        $get = $this->getGet();
        $data['get'] = $get;
        if ($get['id']) {
            $landscapeModel = $this->load->model('landscapes');
            $landscapeInfo = $landscapeModel->getID($get['id'], 'id,name,status,location_hash,organization_id');
            $landscapeInfo = $landscapeModel->getOneRelate($landscapeInfo, 'organization');
            $data['landscapeInfo'] = $landscapeInfo;
            if ($landscapeInfo['status'] != 'normal' || $landscapeInfo['organization']['status'] != 'normal' || !empty($landscapeInfo['location_hash'])) {
                $data['error_msg'] = '该一级票务不符合接入条件，请确保该一级票务的状态是正常且未接入，并且该机构的状态是启用';
            } else {
                if (!empty($get['itour_ism_landscapes_hash']) || !empty($get['itour_ism_landscapes_name'])) {
                    //页码
                    $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
                    $param = array(
                        'page' => $page,
                    );
                    $strFilter = array('location_type_id:equal_1');
                    if (!empty($get['itour_ism_landscapes_hash'])) {
                        $strFilter[] = 'hash:equal_' . $get['itour_ism_landscapes_hash'];
                    }
                    if (!empty($get['itour_ism_landscapes_name'])) {
                        $strFilter[] = 'name:like_%' . $get['itour_ism_landscapes_name'] . '%';
                    }

                    $param['filter'] = implode(',', $strFilter);
                    $itourismApiCommon = $this->load->common('itourismApi');
                    $result = $itourismApiCommon->getItourismLocationsInfo($param);
                    $resultArr = json_decode($result, 1);
                    if ($resultArr['status'] == 'succ') {
                        $data['itour_ism_landscapes_list'] = $resultArr['data']['data'];
                        $data['pagination'] = $this->getPagination($resultArr['data']['pagination']);
                    }
                }
            }
        }

        $this->load->view('ticket/itour_ism_landscapes', $data);
    }

    /**
     * 取消接入
     *
     * @return void
     * @author cuiyulei
     * */
    public function itourismLandscapeOut() {
        echo $this->doAction('landscape', 'itourismLandscapeOut', $this->getPost());
    }

    //ajax提交接入
    public function itourismLandscapeIn() {
        echo $this->doAction('landscape', 'saveItrouIsmLocationHash', $this->getPost());
    }

}
