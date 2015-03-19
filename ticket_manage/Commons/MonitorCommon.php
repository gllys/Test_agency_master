<?php

/**
 * 监管
 *
 * 2014-07-28
 *
 * @package commons
 * @author  grg
 */
class MonitorCommon extends BaseCommon
{
	protected $_code = array('-1' => '{"errors":{"msg":["post data is null"]}}', '-2' => '{"errors":{"msg":["删除消息失败"]}}', '-3' => '{"errors":{"msg":["db err"]}}', '-4' => '{"errors":{"msg":["消息内容不能为空"]}}', '-5' => '{"errors":{"msg":["请选择要发送的机构"]}}', '-6' => '{"errors":{"msg":["发送消息失败"]}}',

		'-21' => '{"errors":{"msg":["null post"]}}', '-22' => '{"errors":{"msg":["缺少必要的参数"]}}', '-23' => '{"errors":{"msg":["错误的状态"]}}', '-24' => '{"errors":{"msg":["不存在的公告信息"]}}', '-25' => '{"errors":{"msg":["公告已经审核，无需重复操作"]}}', '-26' => '{"errors":{"msg":["公告已经拒绝，无需重复操作"]}}', '-27' => '{"errors":{"msg":["保存至数据库失败"]}}',);

	public function getMonitorRelationship($get = array()) {
		$monitorModel        = $this->load->model('monitor');
		$monitorRelationship = $this->load->model('monitorRelationship');

		$param = array();

		$param['join'] = array(array('left_join' => $monitorRelationship->table . ' mr on mr.monitor_id=' . $monitorModel->table . '.id'),);
		$param['filter']['status'] = 1;
		$param['fields'] = $monitorModel->table . '.id, name, status,editable, mr.p_id';
		$data            = $monitorModel->commonGetAll($param);

		if ($get['with_scenic']) {
			$scenic = $this->getScenic();
			$data   = array_merge($data, $scenic);
			unset($scenic);
		}

		$refs = array();
		$tree = array();
		foreach ($data as $row) {
			$_ref = & $refs[$row['id']];

			$_ref['id']   = $row['id'];
			$_ref['p_id'] = $row['p_id'];
			$_ref['name'] = $row['name'];
			if (isset($row['is_scenic'])) {
				$_ref['is_scenic'] = $row['is_scenic'];
				$_ref['org_id']    = $row['org_id'];
			} else {
				$_ref['status'] = $row['status'];
				$_ref['editable'] = $row['editable'];
			}
			if ($get['name']) {
				$_ref['name'] = str_ireplace($get['name'], '<strong>' . $get['name'] . '</strong>', $_ref['name']);
			}
			if ($row['p_id'] == 0) {
				$tree[$row['id']] = & $_ref;
			} else {
				$refs[$row['p_id']]['children'][$row['id']] = & $_ref;
			}
		}
		unset($data, $refs);
		return $tree;
	}

	/**
	 * @property SettingModel $setting_mod
	 * @author grg
	 */
	public function getSetting() {
		$setting_mod = $this->load->model('setting');
		$list = $setting_mod->getList();
		$data = array();
		if ($list) {
			foreach($list as $v) {
				$data[$v['key']] = $v['value'];
			}
		}
		return $data;
	}

	public function getMonitor($get = array()) {
		$tree = $this->getMonitorRelationship($get);

		if ($get['id']) {
			$tree = $this->_select_tree($tree, $get['id']);
		}

		$page = array();
		$idx  = 0;

		$items_per_page = 15;
		if (!$get['p']) {
			$get['p'] = 1;
		}
		$data['tree'] = $this->_format_tree($tree, $idx, $get['p'], $items_per_page, $page);
		$data['data'] = $page;

		$data['pagination'] = array('items' => $idx > $items_per_page ? $items_per_page : $idx, 'count' => $idx);
		if ($get['name']) {
			$data['pagination']['params'] = array('name' => $get['name']);
		}
		unset($tree, $page);
		return $data;
	}

	/**
	 * 查找孤立的机构
	 */
	public function getMonitorNoHead($get = array()) {
		$monitorModel = $this->load->model('monitor');

		$sql = 'SELECT m.id, m.name label, m.name value FROM monitors m LEFT JOIN monitor_relationship r ON m.id = r.monitor_id WHERE p_id IS NULL ';
		if ($get['term']) {
			$sql .= ' AND m.name LIKE "%' . $get['term'] . '%"';
		}
		return $monitorModel->getListBySQL($sql);
	}

	private function _select_tree($data, $root) {
		foreach ($data as $item) {
			if ($item['id'] == $root) {
				return array($item);
			}
			if (isset($item['children'])) {
				$ret = $this->_select_tree($item['children'], $root);
				if ($ret) {
					return $ret;
				} else {
					continue;
				}
			}
		}
		return false;
	}

	private function _format_tree($data, &$idx, $p, $per, &$page) {
		foreach ($data as &$item) {
			unset($item['p_id']);
			if (!isset($item['is_scenic']) && isset($item['status'])) {
				$idx = $idx + 1;
				if ($idx > ($p - 1) * $per && $idx <= $p * $per) {
					$page[] = array('id' => $item['id'], 'name' => $item['name'], 'status' => $item['status'], 'editable' => $item['editable']);
				}
			}
			if (isset($item['children'])) {
				$item['children'] = $this->_format_tree($item['children'], $idx, $p, $per, $page);
			}
		}
		unset($item);
		$data = array_values($data);
		return $data;
	}

	public function getOneMonitor($id) {
		$monitorModel = $this->load->model('monitor');
		return $monitorModel->getMonitorById($id);
	}

	public function getScenic($get = array(), $no_monitor = false) {
		$monitorScenicModel = $this->load->model('monitorScenic');
		$param['fields']    = 'monitor_id, scenic_id';
		$monitorScenic      = $monitorScenicModel->commonGetAll($param);
		$landscapesModel    = $this->load->model('landscapes');
		$param['fields']    = 'name, organization_id';
		if (strlen($get['term'])) {
			$param['filter']['name|like'] = $get['term'];
		}
		$landscapes = $landscapesModel->commonGetAll($param);

		foreach ($monitorScenic as $scenic) {
			if ($get['monitor_id']) {
				if ($get['monitor_id'] != $scenic['monitor_id']) {
					continue;
				}
			}
			$data[$scenic['scenic_id']] = array('p_id' => $scenic['monitor_id'], 'is_scenic' => true, 'id' => 2147483647 - $scenic['scenic_id']);
		}
		$no_monitor_data = array();
		foreach ($landscapes as $landscape) {
			if (isset($data[$landscape['organization_id']])) {
				$data[$landscape['organization_id']]['name']   = $landscape['name'];
				$data[$landscape['organization_id']]['org_id'] = $landscape['organization_id'];
			} else {
				$no_monitor_data[] = array('id' => $landscape['organization_id'], 'label' => $landscape['name'], 'value' => $landscape['name']);
			}
		}
		unset($monitorScenic, $landscapes);
		return $no_monitor ? $no_monitor_data : $data;
	}

	public function getMonitorByScenicId($ids) {
		$monitorModel = $this->load->model('monitor');

		$sql = 'SELECT m.id,`name` monitor,scenic_id org_id FROM monitors m, monitor_scenic_relationship r WHERE m.id = r.monitor_id AND r.scenic_id ';
		if (is_scalar($ids)) {
			$sql .= ' = ' . $ids;
		} elseif (!empty($ids)) {
			$sql .= ' IN (' . implode(',', $ids) . ')';
		}
		$list = $monitorModel->getListBySQL($sql);
		$data = array();
		foreach ($list as $val) {
			$data[$val['org_id']] = array($val['id'], $val['monitor']);
		}
		return is_scalar($ids) ? $list : $data;

	}

	public function save(&$data) {
		$monitorModel             = $this->load->model('monitor');
		$monitorRelationshipModel = $this->load->model('monitorRelationship');
		$relation                 = $data['relation'];
		unset($data['relation']);
		if ($data['id']) {
			$id = $data['id'];
			unset($data['id']);
			$result = $monitorModel->update($data, array('id' => $id));
		} else {
			$result = $monitorModel->add($data);
			$id     = $monitorModel->getAddID();
		}
		$monitorRelationshipModel->update($relation, array('monitor_id' => $id));
		if ($monitorRelationshipModel->affectedRows() == 0) {
			$num = $monitorRelationshipModel->getCount(array('monitor_id' => $id));
			if ($num == 0) {
				$relation['monitor_id'] = $id;
				$monitorRelationshipModel->add($relation);
			}
		}
		if ($result) {
			return json_encode(array('success' => 'success'));
		}
		return $this->_getUserError(-2);

	}

	public function delete($post) {
		if ($post) {
			$post['id'] = intval($post['id']);

			$MonitorModel             = $this->load->model('Monitor');
			$monitorScenicModel       = $this->load->model('monitorScenic');
			$monitorRelationshipModel = $this->load->model('monitorRelationship');

			$MonitorModel->begin();
			if (!$MonitorModel->update(array('status' => 0), array('id' => $post['id']))) {
				$MonitorModel->rollback();
				return $this->_getUserError(-3);
			}

			if (!$monitorScenicModel->strictDelete(array('monitor_id' => $post['id']))) {
				$MonitorModel->rollback();
				return $this->_getUserError(-3);
			}

			if (!$monitorRelationshipModel->strictDelete(array('p_id' => $post['id']))) {
				$MonitorModel->rollback();
				return $this->_getUserError(-3);
			}

			if (!$monitorRelationshipModel->strictDelete(array('monitor_id' => $post['id']))) {
				$MonitorModel->rollback();
				return $this->_getUserError(-3);
			}

			return $MonitorModel->commit() ? json_encode(array('succ' => true)) : $this->_getUserError(-1);
		} else {
			return $this->_getUserError(-1);
		}
	}

	public function catchMonitor($post) {
		if ($post) {
			$monitorRelationshipModel = $this->load->model('monitorRelationship');
			$monitorRelationshipModel->begin();
			if (! $monitorRelationshipModel->strictDelete(array('monitor_id' => $post['id']))) {
				$monitorRelationshipModel->rollback();
				return $this->_getUserError(-3);
			}
			if (! $monitorRelationshipModel->add(array('monitor_id' => $post['id'], 'p_id' => $post['m_id']))) {
				$monitorRelationshipModel->rollback();
				return $this->_getUserError(-3);
			}
			return $monitorRelationshipModel->commit() ? json_encode(array('succ' => true)) : $this->_getUserError(-1);
		} else {
			return $this->_getUserError(-1);
		}
	}

	public function releaseMonitor($post) {
		if ($post) {
			$monitorRelationshipModel = $this->load->model('monitorRelationship');
			$monitorRelationshipModel->strictDelete(array('monitor_id' => $post['id'], 'p_id' => $post['p_id']));
			return json_encode(array('succ' => true));
		} else {
			return $this->_getUserError(-1);
		}
	}

	public function catchScenic($post) {
		if ($post) {
			$monitorScenicModel = $this->load->model('monitorScenic');
			$monitorScenicModel->begin();
			if (! $monitorScenicModel->strictDelete(array('scenic_id' => $post['id']))) {
				$monitorScenicModel->rollback();
				return $this->_getUserError(-3);
			}
			if (! $monitorScenicModel->add(array('scenic_id' => $post['id'], 'monitor_id' => $post['m_id']))) {
				$monitorScenicModel->rollback();
				return $this->_getUserError(-3);
			}
			return $monitorScenicModel->commit() ? json_encode(array('succ' => true)) : $this->_getUserError(-1);
		} else {
			return $this->_getUserError(-1);
		}
	}

	public function releaseScenic($post) {
		if ($post) {
			$monitorScenicModel = $this->load->model('monitorScenic');
			$monitorScenicModel->strictDelete(array('monitor_id' => $post['m_id'], 'scenic_id' => $post['id']));
			return json_encode(array('succ' => true));
		} else {
			return $this->_getUserError(-1);
		}
	}

	public function getAccount($get = array()) {
		$monitorModel        = $this->load->model('monitor');
		$monitorAccountModel = $this->load->model('monitorAccount');
		$param['type']       = intval($get['type']);

		$param[$monitorAccountModel->table.'.status'] = 1;
		if ($get['id']) {
			$param['filter']['supervise_id'] = intval($get['id']);
		}
		$param['fields'] = $monitorAccountModel->table.'.id,account,'.$monitorAccountModel->table.'.name,supervise_id,'.$monitorAccountModel->table.'.status';
		if ($get['type'] == 1) {
			$param['fields'] .= ',m.name org_name';
			$param['join'] = array(array('left_join' => $monitorModel->table . ' m on m.id=' . $monitorAccountModel->table . '.supervise_id'),);
		}
		$monitorAccount = $monitorAccountModel->commonGetList($param);
		/*if ($get['type'] == 0) {
			$landscapesModel = $this->load->model('landscapes');
			$org_ids         = array();
			foreach ($monitorAccount as $account) {
				$org_ids[$account['supervise_id']] = 1;
			}
			$term['fields']                       = 'name,organization_id';
			$term['filter']['organization_id|IN'] = '(' . array_keys($org_ids) . ')';
			$landscapes                           = $landscapesModel->commonGetAll($term);
			$ls_name                              = array();
			foreach ($landscapes as $landscape) {
				$ls_name[$landscape['organization_id']] = $landscape['name'];
			}
			foreach ($monitorAccount as &$user) {
				$user['org_name'] = $ls_name[$user['supervise_id']];
			}
			unset($user);
		}*/
		if ($get['type'] == 0) {
			$landId = $get['id'];
			$landscapesModel = $this->load->model('landscapes');
			$landscapeInfo   = $landscapesModel->getOne(array('id='.$landId));
			$monitorAccount['name'] = $landscapeInfo['name'];
		}
		$param['fields'] = 'name, organization_id';
		if ($get['term']) {
			$param['filter']['name|like'] = $get['term'];
		}
		return $monitorAccount;
	}

	public function saveAccount(&$data) {
		$monitorAccountModel = $this->load->model('monitorAccount');
		$data['password']    = md5($data['password']);
		if ($data['id']) {
			$id = $data['id'];
			unset($data['id']);
			$result = $monitorAccountModel->update($data, array('id' => $id));
		} else{
			$data['create_dateline'] = time();
			$data['create_by']       = $_SESSION['backend_userinfo']['id'];
			$result = $monitorAccountModel->add($data);
		}

		if ($result) {
			return json_encode(array('success' => 'success'));
		}
		return $this->_getUserError(-2);

	}

	public function editStatus($ids){
		$monitorAccountModel = $this->load->model('monitorAccount');
		return $monitorAccountModel->query("UPDATE ".$monitorAccountModel->table." SET `status`=if(`status`,0,1) WHERE id IN ($ids)");
	}

	public function delAccount($ids){
		$monitorAccountModel = $this->load->model('monitorAccount');
		return $monitorAccountModel->query("DELETE FROM ".$monitorAccountModel->table." WHERE id IN ($ids)");
	}
}
