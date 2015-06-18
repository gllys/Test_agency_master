<?php
/**
 * @link
 */
use common\huilian\utils\Header;
use common\huilian\models\API;

/**
 * 供应商产品控制器
 * 系统左侧菜单[分销系统]-[供应商产品]，链接该控制器
 */

class ProductController extends Controller
{	
	private $types = array('+', '-', '%+', '%-');
	
	/**
	 * 供应商产品列表
	 * 包含所有供应商的所有产品
	 */
	public function actionIndex()
	{	
// 		Header::utf8();
		$params = $_GET;
		/*
		 * 查询上架，则：非强制下架，且上架中
		 * 查询下架，则：非强制下架，且下架中
		 * 查询强制下架，则：强制下架，且下架中
		 */
		if(!empty($params['state'])) {
			switch($params['state']) {
				case 1:
					$params['force_out'] = 0;
					break;
				case 2:
					$params['force_out'] = 0;
					break;
				
				case 3:
					$params['state'] = 2;
					$params['force_out'] = 1;
					break;
			}
		}
		
		// 注意目前供应商名称模糊查询,通过供应商接口,因Tickettemplate接口暂未提供该功能
		if(!empty($params['organization_name'])) {
			$datas = Organizations::api()->list(['name' => $params['organization_name'], 'items' => 1000, ]);
			$orgLists = ApiModel::getLists($datas);
			// 如果没有匹配到供应商，直接输出，不必再处理下去。注意，需加exit终止程序，执行。
			if(!count($orgLists)) {
				$this->render('index', ['lists' => [], ]);
				exit;
			}
			
			$params['or_id'] = '';
			foreach($orgLists as $list) {
				$params['or_id'] .= $list['id'] .',';
			}
			$params['or_id'] = rtrim($params['or_id'], ', ');
			unset($params['organization_name']);
		}

        if(!empty($params['scenic_name'])) {
            $datas = Landscape::api()->lists(array('status'=>1, 'take_from_poi'=>0, 'keyword'=>$params['scenic_name'], 'fields'=>'id', 'items'=>10000));
            $scenicLists = ApiModel::getLists($datas);
            // 如果没有匹配到景区，直接输出，不必再处理下去。注意，需加exit终止程序，执行。
            if(!count($scenicLists)) {
                $this->render('index', ['lists' => [], ]);
                exit;
            }
            $params['scenic_id'] = '';
            foreach($scenicLists as $list) {
                $params['scenic_id'] .= $list['id'] .',';
            }
            $params['scenic_id'] = rtrim($params['scenic_id'], ', ');
            unset($params['scenic_name']);
        }
		
		$params['p'] = isset($_GET['page']) ? $_GET['page'] : 1;
		$params['show_policy_name'] = 1;
		$datas = Tickettemplate::api()->lists($params);
		$lists = ApiModel::getLists($datas);
		$lists = API::simultaneous($lists, 'organization_id', 'Organizations::list', 'id', 'id', 'organization');
		$lists = API::simultaneous($lists, 'scenic_id', 'Landscape::lists', 'ids', 'id', 'landscapes');
		
// 		Header::utf8();
// 		var_dump($params);
// 		var_dump($lists) ;
// 		exit;
		$pagination = ApiModel::getPagination($datas);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = 15; #每页显示的数目
		$this->render('index', ['lists' => $lists, 'pages' => $pages, ]);
	}
	
	/**
	 * 产品内页
	 * @param integer $id 产品主键
	 */
	public function actionView($id) {
		$datas = Tickettemplate::api()->ticketinfo(['ticket_id' => $id, ]);
		$product =  ApiModel::getData($datas);
		$product['organization'] = ApiModel::getData(Organizations::api()->show(['id' => $product['organization_id'], ]));
		$product['landscapes'] = ApiModel::getLists(Landscape::api()->lists(['ids' => $product['scenic_id'], 'items' => 10000, ], true));
		if(empty($product['items'])) {
			$product['items'] = [];
		}

// 		Header::utf8();
// 		var_dump($product);
// 		exit;
		$this->render('view', [
			'product' => $product, 
		]);
	}
	
	/**
	 * 日库存
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
                'supplier_id' => $_GET['org_id'],
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
        $this->renderPartial('inventory', compact('ptid', 'rid', 'name', 'begintime', 'data'));
	}
	
	/**
	 * 保存日库存规则
	 */
	public function actionSaveRule() {
		header('Content-type: application/json');
		
		$params = Yii::app()->request->getParam('params');
		$ptid = Yii::app()->request->getParam('ptid');
		$rid = Yii::app()->request->getParam('rid');
		$name = Yii::app()->request->getParam('name');
		
		$data['supplier_id'] = Yii::app()->request->getParam('org_id');
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
			$data['supplier_id'] = Yii::app()->request->getParam('org_id');
			$data['days'] = implode(',', $params);
			$s_price = Yii::app()->request->getParam('s_price');
			$g_price = Yii::app()->request->getParam('g_price');
			$dateSelected = Array();
			$data['fat_price'] = Yii::app()->request->getParam('s_price');
			if (intval($data['fat_price']) > 0) {
				$data['fat_price'] = $this->types[Yii::app()->request->getParam('s_type')] . $data['fat_price'];
			} else {
				unset($data['fat_price']);
			}
			$data['group_price'] = Yii::app()->request->getParam('g_price');
			if (intval($data['group_price']) > 0) {
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
				$rule = $this->getRule($rid, Yii::app()->request->getParam('org_id'));
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
	 * 获取日库存规则
	 * @param int $rid 规则id
	 * @return array 规则数组
	 */
	private function getRule($rid, $orgId) {
		if (intval($rid) > 0) {
			$result = Ticketrule::api()->detail(array(
					'id' => $rid,
					'supplier_id' => $orgId,
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
	 * 保存产品的日库存
	 */
	public function actionSaveInventory() {
		if (Yii::app()->request->isPostRequest) {
			$param = array();
	
			$param['or_id'] = $_REQUEST['org_id'];
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
	 * 清空日库存
	 */
	public function actionClearInventory() {
		if($_POST) {
			$params = array(
					'id' => $_POST['id'],
					'or_id' => $_POST['org_id'],
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
	
	/**
	 * 删除产品日库存规则明细
	 * 编辑时用，按天删除同一规则里的数据
	 */
	public function actionClearSomedayInventory() {
		$rid = Yii::app()->request->getParam('id');
		$date = Yii::app()->request->getParam('date');
		$orgId = Yii::app()->request->getParam('org_id');
		if (intval($rid) > 0 && strlen($date) == 10) {
			$result = Ticketrule::api()->delitem(array(
					'rule_id' => $rid,
					'days' => $date,
					'user_id' => Yii::app()->user->uid,
					'user_name' => Yii::app()->user->account,
					'supplier_id' => $orgId,
			));
			if ($result['code'] == 'succ') {
				$rule = $this->getRule($rid, $orgId);
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
	 * 查看分销策略
	 */
	public function actionViewPolicy() {
		if (Yii::app()->request->isPostRequest) {
			$id = Yii::app()->request->getParam('id');
			$orgId = Yii::app()->request->getParam('org_id');
			$result = Ticketpolicy::api()->detail(array(
					'id' => $id,
					'supplier_id' => $orgId,
					'show_items' => 1
			));
			if(isset($result['code']) && $result['code'] == "succ"){
				$dist_arr = array();//保存分销商名称
				$html = '';      //合作的分销商
				$otherhtml = ''; //未合作分销商
				$name = isset($result['body']['name'])?$result['body']['name']:'';
				$note = isset($result['body']['note'])?$result['body']['note']:'';
				//获取分销商名称
				$dist_result = Organizations::api()->getlist(array('supply_id'=>$orgId,'show_all' => 1));
				if(isset($dist_result['code']) && $dist_result['code'] == "succ"){
					foreach($dist_result['body']['data'] as $one_distr){
						$dist_arr[$one_distr['distributor_id']] = $one_distr['distributor_name'];
					}
				}
				if(count($result['body']['items'])>0){
					//分销商列表数据
					foreach($result['body']['items'] as $distr){
						$tmp_blackname = $distr['blackname_flag']==1?'checked="checked"':'';
						$tmp_credit = $distr['credit_flag']==1?'':'checked="checked"';
						$tmp_advance = $distr['advance_flag']==1?'':'checked="checked"';
						$distributor_name = '';
						//循环分销商数组，获得未被设置规则的分销商（可能是新添加的分销商）
						if(isset($dist_arr[$distr["distributor_id"]])){
							$distributor_name = $dist_arr[$distr["distributor_id"]];
							unset($dist_arr[$distr["distributor_id"]]);
						}
						$html .='<tr>';
						$html .='<td style="width:200px;">'.$distributor_name.'</td>';
						$html .='<td style="width:116px;"><input disabled id="p_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="blackname_arr['.$distr["distributor_id"].']" '.$tmp_blackname.' class="blackgroup"></td>';
						$html .='<td style="width:200px;"><input disabled type="text" id="s_price_'.$distr["distributor_id"].'" name="s_price['.$distr["distributor_id"].']" class="spinner" value="'.$distr['fat_price'].'"></td>';
						$html .='<td style="width:200px;"><input disabled type="text" id="g_price_'.$distr["distributor_id"].'" name="g_price['.$distr["distributor_id"].']" class="spinner" value="'.$distr['group_price'].'"></td>';
						$html .='<td style="width:149px;"><input disabled id="credit_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="credit_arr['.$distr["distributor_id"].']" '.$tmp_credit.' class="creditgroup"></td>';
						$html .='<td><input disabled id="advance_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="advance_arr['.$distr["distributor_id"].']" '.$tmp_advance.' class="advancegroup" style="margin-left: 17px;"></td>';
						$html .='</tr>';
					}
					//列出未被设置规则的分销商
					foreach($dist_arr as $distr_id => $distr_name){
						$html .='<tr>';
						$html .='<td style="width:200px;">'.$distr_name.'</td>';
						$html .='<td style="width:116px;"><input disabled id="p_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="blackname_arr['.$distr_id.']" class="blackgroup"></td>';
						$html .='<td style="width:200px;"><input disabled type="text" id="s_price_'.$distr_id.'" name="s_price['.$distr_id.']" class="spinner"></td>';
						$html .='<td style="width:200px;"><input disabled type="text" id="g_price_'.$distr_id.'" name="g_price['.$distr_id.']" class="spinner"></td>';
						$html .='<td style="width:149px;"><input disabled id="credit_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="credit_arr['.$distr_id.']" class="creditgroup"></td>';
						$html .='<td><input disabled id="advance_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="advance_arr['.$distr_id.']" class="advancegroup" style="margin-left: 17px;"></td>';
						$html .='</tr>';
					}
					
					$new_blackname = $result['body']['new_blackname_flag']==1?'checked="checked"':'';
					$new_credit = $result['body']['new_credit_flag']==0?'checked="checked"':'';
					$new_advance = $result['body']['new_advance_flag']==0?'checked="checked"':'';
					$newhtml  ='<tr>';
					$newhtml .='<td style="width:200px;">新合作分销商</td>';
					$newhtml .='<td style="width:162px;"><input disabled id="p_n" type="checkbox" value="1" name="new_blackname_flag" class="new_blackname_flag" '.$new_blackname.'></td>';
					$newhtml .='<td style="width:167px;"><input disabled type="text" class="spinner" id="s_price_n" name="new_fat_price" value="'.$result['body']['new_fat_price'].'" ></td>';
					$newhtml .='<td style="width:148px;"><input disabled type="text" class="spinner" id="g_price_n" name="new_group_price" value="'.$result['body']['new_group_price'].'" ></td>';
					$newhtml .=' <td style="width:150px;"><input disabled id="credit_n" type="checkbox" value="0" name="new_credit_flag" class="new_credit_flag" '.$new_credit.'></td>';
					$newhtml .='<td><input disabled id="advance_n" type="checkbox" value="0" name="new_advance_flag" class="new_advance_flag" '.$new_advance.' ></td>';
					$newhtml .='</tr>';
					
					$tmp_blackname = $result['body']['other_blackname_flag']==1?'checked="checked"':'';
					$tmp_credit = $result['body']['other_credit_flag']==1?'':'checked="checked"';
					$tmp_advance = $result['body']['other_advance_flag']==1?'':'checked="checked"';
					$otherhtml  ='<tr style="background-color:#f7f7f7;">';
					$otherhtml .='<td style="width:200px;">未合作分销商</td>';
					$otherhtml .='<td style="width:116px;"><input disabled id="p_0" type="checkbox" value="0" name="blackname_arr[0]" '.$tmp_blackname.'></td>';
					$otherhtml .='<td style="width:200px;"><input disabled type="text" id="s_price_0" name="s_price[0]" class="spinner" value="'.$result['body']['other_fat_price'].'" ></td>';
					$otherhtml .='<td style="width:200px;"><input disabled type="text" id="g_price_0" name="g_price[0]" class="spinner" value="'.$result['body']['other_group_price'].'" ></td>';
					//                    $otherhtml .='<td style="width:149px;"><input disabled id="credit_0" type="checkbox" value="0" name="credit_arr[0]" '.$tmp_credit.'></td>';
					//                    $otherhtml .='<td><input disabled id="advance_0" type="checkbox" value="0" name="advance_arr[0]" '.$tmp_advance.'></td>';
					$otherhtml .='<td style="width:149px;"></td>';
					$otherhtml .='<td></td>';
					$otherhtml .='</tr>';
				}
				echo json_encode(array('error'=>0,'message'=>"",'data'=>$html,'otherdata'=>$otherhtml, 'newdata'=>$newhtml,'dist_id'=>$id,'name'=>$name,'note'=>$note));
			}else{
				echo json_encode(array('error'=>1,
						'message'=>isset($result['message'])?$result['message']:'数据未返回'));
			}
		}else{
			Throw new  CHttpException('404',"找不到请求的页面!");
		}
	}
	
	/**
	 * 分销策略
	 */
	public function actionPolicy() {
		//票id
		//产品的id $_GET['id'];
		//如果有策略id $_GET['policy_id']需要默认选中此策略
		$id = $_GET['id'];
		$policy_id = $_GET['policy_id'];
		$org_id = $_GET['org_id'];
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
		$this->renderPartial('policy', compact('id', 'org_id', 'policy_id', 'policy_name_arr', 'policy_note_arr'));
	}
	
	/**
	 * 保存产品的销售策略
	 */
	public function actionSavePolicy() {
		if (Yii::app()->request->isPostRequest) {
			$param = array();
	
			$param['or_id'] = $_REQUEST['org_id'];
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
	 * 强制下架
	 */
	public function actionForceOut() {
		// 如果是POST请求，则是设置强制下架或取消强制下架
		if (Yii::app()->request->isPostRequest) {
			$params = [
				'id' => $_POST['id'],
				'force_out' => $_POST['force_out'],
				'force_out_remark' => $_POST['force_out_remark'],
			];
			$data = Tickettemplate::api()->forceout($params);
			$success = ApiModel::isSucc($data);
			// 如果下架成功，需要发强制下架消息给供应商
			if($success && $params['force_out']) {
				$datas = Tickettemplate::api()->ticketinfo(['ticket_id' => $params['id'], ]);
				$product =  ApiModel::getData($datas);
				$message = [
					'content' => '您所发布的产品<' .$product['name']. '>已被强制下架，下架理由：' .$params['force_out_remark'],
					'sms_type' => 0,
					'sys_type' => 2,
					'send_source' => 0,
					'send_status' => 1,
					'send_organization' => 0,
					'receiver_organization' => $product['organization_id'],
					'receiver_organization_type' => 0,
					//'send_back' => Yii::app()->user->id,
				];
				Message::api()->add($message);
			}
			$this->_end($success, $data['message']);
			
		} else {
			$datas = Tickettemplate::api()->ticketinfo(['ticket_id' => $_GET['id'], ]);
			$product =  ApiModel::getData($datas);
			$product['organization'] = ApiModel::getData(Organizations::api()->show(['id' => $product['organization_id'], ]));
			$product['landscapes'] = ApiModel::getLists(Landscape::api()->lists(['ids' => $product['scenic_id'], 'items' => 10000, ], true));
			$this->renderPartial('forceOut', ['product' => $product, ]);
		}
	}
	
	
	
}