<?php

class SingleController extends Controller {

    private $types = array('+', '-', '%+', '%-');

    public function actionIndex() {
        //绑定供应商的所有景区
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
	$param['fields'] = "landscape_id";
        $param['items'] = 100000;
        $data = Landorg::api()->lists($param,true);
        $supplyLans = ApiModel::getLists($data);
		
        //得到门票列表
        $param = array();
        //$param['state'] = 1;
        $param['or_id'] = YII::app()->user->org_id;
        $param['p'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $param['items'] = 15;
        $param['state'] = '1,2';
        $param['show_policy_name'] = '1';  //是否显示策略名称，1是，0否
        if (!empty($_GET['scenic_id'])) {
            $param['scenic_id'] = $_GET['scenic_id'];
        }

        if (!empty($_GET['up']) && empty($_GET['down'])) {
            $param['state'] = 1;
        }

        if (empty($_GET['up']) && !empty($_GET['down'])) {
            $param['state'] = 2;
        }

        $datas = Tickettemplate::api()->lists($param);
        $lists = ApiModel::getLists($datas);
        $lists = $this->_getLan($lists);
        //print_r($lists);
        //分页
        $pagination = ApiModel::getPagination($datas);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
        $this->render('index', compact('lists', 'pages', 'supplyLans'));
    }

    /*
     * 获取景区名字
     * @return string
     */
    private function _getlan($lists = array()){
        if (!isset($singleLans)) {
            //得到所有景点信息
            $ids = PublicFunHelper::arrayKey($lists, 'scenic_id');
            $param = array();
            $param['ids'] = join(',', $ids);
            $param['items'] = 100000;
            $param['fields'] = 'id,name';
            $data = Landscape::api()->lists($param,true,30);
            $singleLans = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
        }
        if(is_array($lists)){
            foreach($lists as $key => $value){
                $_lans = explode(',', $value['scenic_id']);
                $lan_name = '';
                foreach ($_lans as $id) {
                    if (!empty($singleLans[$id])) {
                        $lan_name .= $singleLans[$id]['name'] . ' ';
                    }
                }
                $lists[$key]['lan_name'] = $lan_name;
            }
            return $lists;
        }
		return array();
    }

    //添加产品
    public function actionAdd() {
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;

            //提前时间数   散客
            if(isset($_REQUEST['fat_price']) && $_REQUEST['fat_price'] != ""){
                $param['fat_scheduled'] = intval($_REQUEST['fat_scheduled']) * 3600 * 24;
                $arr = $_REQUEST['fat_scheduledtime'] ? explode(':', $_REQUEST['fat_scheduledtime']) : explode(':', '0:0');
                $param['fat_scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
                $param['fat_scheduled_time'] = $param['fat_scheduled'] + $param['fat_scheduledtime'];
            }
            //团客
            if(isset($_REQUEST['group_price']) && $_REQUEST['group_price'] != ""){
                $param['group_scheduled'] = intval($_REQUEST['group_scheduled']) * 3600 * 24;
                $arr = $_REQUEST['group_scheduledtime'] ? explode(':', $_REQUEST['group_scheduledtime']) : explode(':', '0:0');
                $param['group_scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
                $param['group_scheduled_time'] = $param['group_scheduled'] + $param['group_scheduledtime'];
            }

            //销售日期判断
            $param['sale_start_time'] = empty($_REQUEST['sale_start_time']) ? 0 : strtotime($_REQUEST['sale_start_time']);
            $param['sale_end_time'] = empty($_REQUEST['sale_end_time']) ? 0 : strtotime($_REQUEST['sale_end_time']. ' 23:59:59');
            if ($param['sale_start_time'] && $param['sale_end_time'] && $param['sale_start_time'] > $param['sale_end_time']) {
                $this->_end(1, '销售开始日期不得晚于销售结束日期');
            }

            if (!empty($param['sale_end_time']) && $param['sale_end_time'] > strtotime($_REQUEST['date_available'][2]. ' 23:59:59')) {
                $this->_end(1, '销售结束日期不得晚于使用结束日期');
            }


            if (strtotime($_REQUEST['date_available'][1]) <= strtotime($_REQUEST['date_available'][2])) {
                $param['date_available'] = strtotime($_REQUEST['date_available'][1] . ' 00:00:00') . ',' . strtotime($_REQUEST['date_available'][2] . ' 23:59:59');
            } else {
                $this->_end(1, '使用开始日期不得晚于使用结束日期');
            }

            if (count($_REQUEST['week_time']) > 0) {
                $param['week_time'] = implode(',', $_REQUEST['week_time']);
            } else {
                $this->_end(1, '请选择适用日期');
            }

            //短信模板
            $sms_template = '';
            if (isset($_REQUEST['sms_tem']) && $_REQUEST['sms_tem'] == 1) {
                $sms_template = htmlspecialchars_decode($_REQUEST['sms_con']) ;
                $sms_template = preg_replace(array('/<label[^>]*>/i', '/\s*<i[^>]*><\/i><\/label>/i'), array('{{{', '}}}'), $sms_template);
                $sms_template = strip_tags($sms_template);
            }

            $param['sms_template'] = $sms_template;
            $param['state'] = 1; //默认上架
            $param['base_items'] = json_encode($_REQUEST['base_items']);
            $param['organization_id'] = yii::app()->user->org_id;
            $param['user_id']= yii::app()->user->uid;
            $param['is_fit'] = empty($_REQUEST['is_fit']) ? 0 : 1;
            $param['is_full'] = empty($_REQUEST['is_full']) ? 0 : 1;

            $data = Tickettemplate::api()->addGenerate($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }

        //得到景区列表
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
		$param['fields'] = "landscape_id";
        $param['items'] = 100000;
        $data = Landorg::api()->lists($param);
        $supplyLans = ApiModel::getLists($data);

        $orgInfo = $this->getOrgInfo(); //判断是否是景旅通 partner_type

        $this->renderPartial('singleadd', compact('supplyLans','orgInfo'));
    }

    //票编辑
    public function actionEdit() {
        //add
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
			if(!empty($param['valid'])) {
				$param['valid_flag'] = 0;
			}
            //提前时间数
            //提前时间数   散客
            if(isset($_REQUEST['fat_price']) && $_REQUEST['fat_price'] != ""){
                $param['fat_scheduled'] = intval($_REQUEST['fat_scheduled']) * 3600 * 24;
                $arr = $_REQUEST['fat_scheduledtime'] ? explode(':', $_REQUEST['fat_scheduledtime']) : explode(':', '0:0');
                $param['fat_scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
                $param['fat_scheduled_time'] = $param['fat_scheduled'] + $param['fat_scheduledtime'];
            }
            //团客
            if(isset($_REQUEST['group_price']) && $_REQUEST['group_price'] != ""){
                $param['group_scheduled'] = intval($_REQUEST['group_scheduled']) * 3600 * 24;
                $arr = $_REQUEST['group_scheduledtime'] ? explode(':', $_REQUEST['group_scheduledtime']) : explode(':', '0:0');
                $param['group_scheduledtime'] = intval($arr[0]) * 3600 + intval($arr[1]) * 60;
                $param['group_scheduled_time'] = $param['group_scheduled'] + $param['group_scheduledtime'];
            }

            //销售日期判断
            $param['sale_start_time'] = empty($_REQUEST['sale_start_time']) ? 0 : strtotime($_REQUEST['sale_start_time']);
            $param['sale_end_time'] = empty($_REQUEST['sale_end_time']) ? 0 : strtotime($_REQUEST['sale_end_time']. ' 23:59:59');
            if ($param['sale_start_time'] && $param['sale_end_time'] && $param['sale_start_time'] > $param['sale_end_time']) {
                $this->_end(1, '销售开始日期不得晚于销售结束日期');
            }

            if (!empty($param['sale_end_time']) && $param['sale_end_time'] > strtotime($_REQUEST['date_available'][2]. ' 23:59:59')) {
                $this->_end(1, '销售结束日期日期不得晚于使用结束');
            }

            if (strtotime($_REQUEST['date_available'][1]) <= strtotime($_REQUEST['date_available'][2])) {
                $param['date_available'] = strtotime($_REQUEST['date_available'][1] . ' 00:00:00') . ',' . strtotime($_REQUEST['date_available'][2] . ' 23:59:59');
            } else {
                $this->_end(1, '使用开始日期不得晚于使用结束日期');
            }

            if (count($_REQUEST['week_time']) > 0) {
                $param['week_time'] = implode(',', $_REQUEST['week_time']);
            } else {
                $this->_end(1, '请选择适用日期');
            }

            //短信模板
            $sms_template = '';
            if (isset($_REQUEST['sms_tem']) && $_REQUEST['sms_tem'] == 1) {
                $sms_template = $_REQUEST['sms_con'];
                $sms_template = htmlspecialchars_decode($_REQUEST['sms_con']) ;
                $sms_template = preg_replace(array('/<label[^>]*>/i', '/\s*<i[^>]*><\/i><\/label>/i'), array('{{{', '}}}'), $sms_template);
                $sms_template = strip_tags($sms_template);
            }

            $param['sms_template'] = $sms_template;
            $param['state'] = 1; //默认上架
            $param['base_items'] = json_encode($_REQUEST['base_items']);
            $param['or_id'] = Yii::app()->user->org_id;
            $param['is_fit'] = empty($_REQUEST['is_fit']) ? 0 : 1;
            $param['is_full'] = empty($_REQUEST['is_full']) ? 0 : 1;
            //Tickettemplate::api()->debug = true;
            $data = Tickettemplate::api()->update($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        //得到门票详情
        //Tickettemplate::api()->debug= true;
        $data = Tickettemplate::api()->ticketinfo(array('ticket_id' => $_GET['ticket_id']));
        $ticket = ApiModel::getData($data);
        if (!$ticket || $ticket['organization_id'] != Yii::app()->user->org_id) {
            exit('您没有编辑权限');
        }


        //得到景区列表
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
		$param['fields'] = "landscape_id";
        $param['items'] = 100000;
        $data = Landorg::api()->lists($param);
        $supplyLans = ApiModel::getLists($data);

        $orgInfo = $this->getOrgInfo(); //判断是否是景旅通 partner_type
        $this->renderPartial('singleedit', compact('supplyLans', 'ticket','orgInfo'));
    }


    public function actionDetail() {
        //得到门票详情
        //Tickettemplate::api()->debug= true;
        $data = Tickettemplate::api()->ticketinfo(array('ticket_id' => $_GET['ticket_id']));
        $ticket = ApiModel::getData($data);

        $orgInfo = $this->getOrgInfo(); //判断是否是景旅通 partner_type

        //得到景区列表
        $this->renderPartial('singledetail', compact('ticket', 'orgInfo'));
    }

    //删除票
    public function actionDel() {
        if (Yii::app()->request->isPostRequest) {
            // Landscape::api()->debug = true;
            $param = $_POST;
            $param['or_id'] = YII::app()->user->org_id;
            $data = Tickettemplate::api()->del($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    //上下架
    public function actionDownUp() {
        if (Yii::app()->request->isPostRequest) {
            // Landscape::api()->debug = true;
            $rs['id'] = $_POST['id'];
            $rs['state'] = ($_POST['state'] == 1) ? 2 : 1;
            $rs['or_id'] = YII::app()->user->org_id;
            $data = Tickettemplate::api()->state($rs);
            if ($data) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('downUp');
    }

    /**
     * 改变状态
     * 注意：
     * - 如果产品在强制下架状态下，供应商是无法对产品进行上架的
     */
    public function actionState() {
        if (Yii::app()->request->isPostRequest) {
            // Landscape::api()->debug = true;
            
        	if(isset($_POST['id']) && strpos($_POST['id'], ',') === false) {
	        	$datas = Tickettemplate::api()->ticketinfo(array('ticket_id' => $_POST['id']));
	        	if(!empty($datas['body']['force_out'])) {
	        		$this->_end(1, '强制下架的产品不能直接上架');
	        	}
        	}
            $param = $_POST;
            $param['or_id'] = YII::app()->user->org_id;
            $data = Tickettemplate::api()->state($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    /**
     * 特定日期价格、库存设置
     * @author grg
     */
    public function actionSpecial() {
        $data['labels'] = array('stock' => '库存', 'price' => '价格');
        $type = Yii::app()->request->getParam('day');
        $data['id'] = Yii::app()->request->getParam('id');
        $result = Tickettemplate::api()->ticketinfo(array(
            'ticket_id' => $data['id'],
        ));
        if ($result['code'] == 'succ') {
            $data['info'] = $result['body'];
        } else {
            $this->redirect('/ticket/single/');
        }
        $data['type'] = in_array($type, array_keys($data['labels'])) ? $type : 'stock';
        $result = $data['type'] == 'stock' ? Ticketdreserve::api()->lists(array(
                'ticket_template_id' => $data['id']
            )) : Ticketdprice::api()->lists(array(
                'ticket_template_id' => $data['id']
        ));
        $day_tickets = $color_day_tickets = array();
        if ($result['code'] == 'succ') {
            foreach ($result['body']['data'] as $item) {
                $day_tickets[$item['date']] = $data['type'] == 'stock' ? (int) $item['reserve'] : doubleval($item['price']);
            }

            $tickets = array_values($day_tickets);
            sort($tickets);
            $tickets = array_unique($tickets);
            $idx = 1;
            $colors = array();
            foreach ($tickets as $value) {
                $colors[$value] = '.clv' . $idx;
                $idx = $idx == 18 ? 1 : $idx + 1;
            }
            unset($value);
            $color_day_tickets = array();
            foreach ($day_tickets as $date => &$ticket) {
                isset($color_day_tickets['' . $ticket . $colors[$ticket]]) ? $color_day_tickets['' . $ticket . $colors[$ticket]][] = $date : $color_day_tickets['' . $ticket . $colors[$ticket]] = array($date);
                $ticket = array($ticket, $colors[$ticket]);
            }
            unset($ticket);
            uksort($color_day_tickets, function($a, $b) {
                $a = intval($a);
                $b = intval($b);
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            });
        }


        $data['day_tickets'] = $day_tickets;
        $data['color_day_tickets'] = $color_day_tickets;
        $this->render('special', $data);
    }

    public function actionSpecial_Bind() {
        $t_id = Yii::app()->request->getParam('t_id');
        $type = Yii::app()->request->getParam('type');
        $date = Yii::app()->request->getParam('date');
        $quantity = Yii::app()->request->getParam('quantity');

        $date = explode(',', $date);
        if (doubleval($quantity) <= 0 || count($date) == 0) {
            exit(0);
        }

        if ($type == 'stock') {
            $result = Ticketdreserve::api()->set(array(
                'ticket_template_id' => (int) $t_id,
                'reserve' => intval($quantity),
                'days' => implode(',', $date),
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account
                ), 0);
        } else {
            $result = Ticketdprice::api()->set(array(
                'ticket_template_id' => (int) $t_id,
                'price' => doubleval($quantity),
                'days' => implode(',', $date),
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account
                ), 0);
        }


        if ($result['code'] == 'succ') {
            exit(1);
        }
        exit(0);
    }

    //优惠规则
    public function actionRule() {
        $id = $_REQUEST['id'];
        $param['supplier_id'] = Yii::app()->user->org_id;
        $lists = Ticketdiscountrule::api()->lists($param);
        $list = ApiModel::getLists($lists);
        $this->renderPartial('rule', compact('id', 'list'));
    }

    //添加规则
    public function actionLimitrule() {
        $id = $_REQUEST['id'];
        $this->renderPartial('limitrule', compact('id'));
    }

    //查找限制分销商
    public function actionLimit() {
        if (Yii::app()->request->isPostRequest) {
            $param['supplier_id'] = Yii::app()->user->org_id;
            $param['type'] = $_POST['type'];
            $lists = Ticketorgnamelist::api()->lists($param);
            $list = ApiModel::getLists($lists);
            $html = '';
            if (!empty($list)) {
                foreach ($list as $item) {
                    $html = $html . '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                }
            }
            echo json_encode($html);
            Yii::app()->end();
        }
    }

    //限制分销商
    public function actionNamelist() {
        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['or_id'] = Yii::app()->user->org_id;
            $param['namelist_id'] = $_POST['namelist_id'];
            $data = Tickettemplate::api()->update($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    //优惠规则
    public function actionRuleadd() {
        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['or_id'] = Yii::app()->user->org_id;
            $param['discount_id'] = $_POST['discount_id'];
            if (trim($param['discount_id']) == "") {
                $this->_end(1, "请选择优惠规则！");
            }
            $data = Tickettemplate::api()->update($param);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    //返仓
    public function actionGetback() {
        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['or_id'] = Yii::app()->user->org_id;
            $param['state'] = 0;
            $data = Tickettemplate::api()->state($param);
            //print_r($data);exit;
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    //得到基础票
    public function actionGetbase() {
        if (Yii::app()->request->isPostRequest) {
            $param['scenic_id'] = $_POST['id'];
            $param['state'] = 1;
            $param['items'] = 10000;
            $param['types'] = '1,2,3,5';
            $data = Tickettemplatebase::api()->lists($param);
            $this->_end(0, ApiModel::getLists($data));
        }
    }

    /**
     * 获取产品的日库存
     */
    public function actionInventory() {
        //票id
        $ptid = $_GET['id'];
        $rid = $_GET['rid'];
        $name = $_GET['name'];
        $begintime = date('Y-m-d');
        if (intval($rid) > 0) {
            $result = Ticketrule::api()->detail(array(
                'id' => $rid,
                'supplier_id' => Yii::app()->user->org_id,
                'show_items' => 1
            ));
            if ($result['code'] == 'succ') {
                $data = $result['body'];
                foreach ($data['rule_items'] as $item) {
                    $rule = array();
                    $rule['date'] = $item['date'];
                    $rule['s_price'] = $item['fat_price'];
                    if (strpos($rule['s_price'], '%') === 0) {
                        $rule['s_price']{0} = '';
                        $rule['s_price'] .= '%';
                    }
                    $rule['g_price'] = $item['group_price'];
                    if (strpos($rule['g_price'], '%') === 0) {
                        $rule['g_price']{0} = '';
                        $rule['g_price'] .= '%';
                    }
                    $rule['storage'] = $item['reserve'];
                    $data['rules'][] = $rule;
                }
            }
        }
        //echo $json = json_encode($arr);
//        echo '{"id":'.$id.'}';
        $this->renderPartial('singleinventory', compact('ptid', 'rid', 'name', 'begintime', 'data'));
    }

    /**
     * 获取日库存规则
     * @param int $rid 规则id
     * @return array 规则数组
     */
    private function getRule($rid) {
        if (intval($rid) > 0) {
            $result = Ticketrule::api()->detail(array(
                'id' => $rid,
                'supplier_id' => Yii::app()->user->org_id,
                'show_items' => 1
            ));
            if ($result['code'] == 'succ') {
                $data = $result['body'];
                foreach ($data['rule_items'] as $item) {
                    $rule = array();
                    $rule['date'] = $item['date'];
                    $rule['s_price'] = $item['fat_price'];
                    if (strpos($rule['s_price'], '%') === 0) {
                        $rule['s_price']{0} = '';
                        $rule['s_price'] .= '%';
                    }
                    $rule['g_price'] = $item['group_price'];
                    if (strpos($rule['g_price'], '%') === 0) {
                        $rule['g_price']{0} = '';
                        $rule['g_price'] .= '%';
                    }
                    $rule['storage'] = $item['reserve'];
                    $data['rules'][] = $rule;
                }
            }
        }
        return $data;
    }

    /**
     * 删除产品日库存规则明细
     * 编辑时用，按天删除同一规则里的数据
     */
    public function actionDelete() {
        $rid = Yii::app()->request->getParam('id');
        $date = Yii::app()->request->getParam('date');
        if (intval($rid) > 0 && strlen($date) == 10) {
            $result = Ticketrule::api()->delitem(array(
                'rule_id' => $rid,
                'days' => $date,
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account,
                'supplier_id' => Yii::app()->user->org_id
            ));
            if ($result['code'] == 'succ') {
                $rule = $this->getRule($rid);
                echo json_encode(array(
                    'code' => 200,
                    'rid' => $rid,
                    'dateSelected' => isset($rule['rules']) ? $rule['rules'] : '',
                    'message' => '保存成功'
                    ), JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            echo json_encode(array(
                'code' => 500,
                'message' => '保存失败'
                ), JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * 保存日库存设置
     */
    public function actionCommit() {
        header('Content-type: application/json');

        $params = Yii::app()->request->getParam('params');
        $ptid = Yii::app()->request->getParam('ptid');
        $rid = Yii::app()->request->getParam('rid');
        $name = Yii::app()->request->getParam('name');

        $data['supplier_id'] = Yii::app()->user->org_id;
        $data['name'] = Yii::app()->request->getParam('name');
        $data['desc'] = Yii::app()->request->getParam('desc');
        $data['user_id'] = Yii::app()->user->uid;
        $data['user_name'] = Yii::app()->user->account;
        if (intval($rid) > 0) {
            $data['id'] = intval($rid);
            Ticketrule::api()->update($data);
        } else {
            $result = Ticketrule::api()->add($data);
            if ($result['code'] == 'succ') {
                $rid = $result['body']['id'];
            } else {
                echo json_encode(array(
                    'code' => 500,
                    'message' => '保存失败' . $result['message']
                    ), JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        if ($rid > 0 && count($params) > 0) {
            $data = array();
            $data['rule_id'] = $rid;
            $data['supplier_id'] = Yii::app()->user->org_id;
            $data['days'] = implode(',', $params);
            $s_price = abs(Yii::app()->request->getParam('s_price'));
            $g_price = abs(Yii::app()->request->getParam('g_price'));
            $dateSelected = Array();
            $data['fat_price'] = $s_price;
            if ($data['fat_price'] > 0) {
                $data['fat_price'] = $this->types[Yii::app()->request->getParam('s_type')] . $data['fat_price'];
            } else {
                unset($data['fat_price']);
            }
            $data['group_price'] = $g_price;
            if ($data['group_price'] > 0) {
                $data['group_price'] = $this->types[Yii::app()->request->getParam('g_type')] . $data['group_price'];
            } else {
                unset($data['group_price']);
            }
            $data['reserve'] = Yii::app()->request->getParam('storage');
            if (intval($data['reserve']) == 0) {
                unset($data['reserve']);
            }
            foreach ($params as $one) {
                $dateSelected[] = array(
                    'date' => $one,
                    's_price' => isset($data['fat_price']) ? $data['fat_price'] : '',
                    'g_price' => isset($data['group_price']) ? $data['group_price'] : '',
                    'storage' => isset($data['reserve']) ? $data['reserve'] : ''
                );
            }
            $data['user_id'] = Yii::app()->user->uid;
            $data['user_name'] = Yii::app()->user->account;
            $result = Ticketrule::api()->setitem($data);
            if ($result['code'] == 'succ') {
                $rule = $this->getRule($rid);
                echo json_encode(array(
                    'code' => 200,
                    'id' => $ptid,
                    'rid' => $rid,
                    'name' => $name,
                    's_price' => $s_price,
                    'g_price' => $g_price,
                    'dateSelected' => isset($rule['rules']) ? $rule['rules'] : '',
                    'message' => '保存成功'
                    ), JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                echo json_encode(array(
                    'code' => 500,
                    'message' => '保存失败' . $result['message']
                    ), JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode(array(
            'code' => 200,
            'id' => $ptid,
            'rid' => $rid,
            'name' => $name,
            'message' => '保存成功'
            ), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 保存产品的日库存
     */
    public function actionSaveInvetory() {
        if (Yii::app()->request->isPostRequest) {
            $param = array();

            $param['or_id'] = yii::app()->user->org_id;
            $param['id'] = $_REQUEST['ptid'];             //票id
            $param['rule_id'] = $_REQUEST['rid'];         //日库存表id
            //Tickettemplate::api()->debug = true;
            $data = Tickettemplate::api()->update($param);
            if (ApiModel::isSucc($data)) {
                echo json_encode(array(
                    'code' => 200,
                    'id' => $param['id'],
                    'message' => '保存成功'
                    ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array(
                    'code' => 500,
                    'error' => 'error',
                    'id' => $param['id'],
                    'message' => '保存失败'
                    ), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * 清空产品表中的库存规则id
     * 删除库存规则表中此id的数据
     */
    public function actionDelInvetory() {
        if (isset($_POST['id']) && isset($_POST['rid'])) {
            $param = array();

            $param['or_id'] = yii::app()->user->org_id;
            $param['id'] = $_POST['id'];             //票id
            $param['rule_id'] = '';         //日库存表id
            $rid = $_POST['rid'];
            //Tickettemplate::api()->debug = true;
            $data = Tickettemplate::api()->update($param);
            if (ApiModel::isSucc($data)) {
                $data['id'] = intval($rid);
                $data['supplier_id'] = Yii::app()->user->org_id;
                $data['deleted'] = 1;
                $data['user_id'] = Yii::app()->user->uid;
                $data['user_name'] = Yii::app()->user->account;
                if ($rid > 0) {
                    Ticketrule::api()->update($data);
                }
                echo json_encode(array(
                    'code' => 200,
                    'id' => $param['id'],
                    'message' => '操作成功'
                    ), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array(
                    'code' => 500,
                    'error' => 'error',
                    'id' => $param['id'],
                    'message' => '操作失败'
                    ), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * 获取分销策略
     */
    public function actionPolicy() {
        //票id
        //产品的id $_GET['id'];
        //如果有策略id $_GET['policy_id']需要默认选中此策略
        $id = $_GET['id'];
        $policy_id = $_GET['policy_id'];
        $org_id = Yii::app()->user->org_id;
        $policy_name_arr = array();  //策略名
        $policy_note_arr = array();  //策略说明
        if (intval($org_id) > 0) {
            $params['supplier_id'] = $org_id;
            $params['show_all'] = 1;
            $params['show_items'] = 0;
            //获取策略列表
            $result = Ticketpolicy::api()->lists($params);

            if ($result['code'] == 'succ') {
                if (isset($result['body']['data'])) {
                    foreach ($result['body']['data'] as $onepolily) {
                        $policy_name_arr[$onepolily['id']] = $onepolily['name'];
                        $policy_note_arr[$onepolily['id']] = $onepolily['note'];
                    }
                }
            }
        }
        $this->renderPartial('singlepolicy', compact('id', 'policy_id', 'policy_name_arr', 'policy_note_arr'));
    }

    /**
     * 保存产品的销售策略
     */
    public function actionSavePolicy() {
        if (Yii::app()->request->isPostRequest) {
            $param = array();

            $param['or_id'] = yii::app()->user->org_id;
            $param['id'] = $_REQUEST['ptid'];             //票id
            $param['policy_id'] = $_REQUEST['selpol'];    //策略id
            //Tickettemplate::api()->debug = true;
            $data = Tickettemplate::api()->update($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }
    
    /**
     * 清空日库存
     */
   	public function actionClearDailyStock() {
   		if($_POST) {
   			$params = array(
   				'id' => $_POST['id'],
   				'or_id' => Yii::app()->user->org_id,
   				'rule_id' => 0,
   			);
   			$data = Tickettemplate::api()->update($params);
   			if (ApiModel::isSucc($data)) {
   				$this->_end(0, $data['message']);
   			} else {
   				$this->_end(1, $data['message']);
   			}
   		}
   	}

}
