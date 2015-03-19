<?php

/**
 * 设备管理common
 *
 * 2014-03-13
 *
 * @package common
 * @author cuiyulei
 * */
class EquipmentCommon extends BaseCommon {

    protected $_code = array(
        '-1' => '{"errors":{"post":["post data is null"]}}',
        '-2' => '{"errors":{"msg":["保存至数据库失败"]}}',
        '-3' => '{"errors":{"msg":["设备编码不能为空"]}}',
        '-4' => '{"errors":{"msg":["设备类型参数非法"]}}',
        '-5' => '{"errors":{"msg":["设备编码已经存在"]}}',
        '-6' => '{"errors":{"msg":["删除设备失败"]}}',
        '-7' => '{"errors":{"msg":["您提交的编号已经存在"]}}',
        '-8' => '{"errors":{"msg":["传递的参数不合法"]}}',
        '-9' => '{"errors":{"msg":["您已绑定{landscape_name}, 请先解绑"]}}',
        '-10' => '{"errors":{"msg":["您已安装到{poi_name}, 请先解除安装"]}}',
        '-11' => '{"errors":{"msg":["不存在的子景点信息"]}}',
        '-12' => '{"errors":{"msg":["该设备尚未绑定景区信息，不能安装到子景点"]}}',
    );
    static $_equipmentType = array(
        'gate' => '闸机',
        'andriod' => '手持设备'
    );

    /**
     * 获取设备类型
     *
     * @param string $key
     *
     * @return mixed string | array 
     * @author cuiyulei
     * */
    public static function getEquipmentType($key) {
        if ($key) {
            return self::$_equipmentType[$key];
        } else {
            return self::$_equipmentType;
        }
    }

    /**
     * 保存系统配置
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function add($post) {
        if ($post) {
            if (!$post['code']) {
                return $this->_getUserError(-3);
            }

            $type = array_keys(self::$_equipmentType);
            if (!in_array($post['type'], $type)) {
                return $this->_getUserError(-4);
            }
            //转换成数据库字段
            $equipmentModel = $this->load->model('equipment');
            $count = $equipmentModel->getCount("code='" . $post['code'] . "' AND deleted_at is NULL");
            if ($count) {
                return $this->_getUserError(-5);
            }
            $postData = $this->_formEquipmentData($post, $settle);
            $equipmentModel->add($postData);
            $addId = $equipmentModel->getAddID();
            if ($addId) {
                $postData['id'] = $addId;
                return json_encode(array('data' => array($postData)));
            } else {
                return $this->_getUserError(-2);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 更新设备
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function upEquip($post) {
        if ($post) {
            $code = trim($post['code']);
            $equipmentModel = $this->load->model('equipment');
            $equip = $equipmentModel->getOne(array('code' => $code, 'deleted_at' => null));
            if ($equip && $equip['id'] != $post['equipment_id']) {
                return $this->_getUserError(-7);
            }
            $updateArray = array(
                'type' => $post['type'],
                'code' => $code,
                'name' => trim($post['name']),
                'updated_at' => date('Y-m-d H:i:s'),
                'update_from' => 'admin',
                'update_by' => $_SESSION['backend_userinfo']['id']
            );
            $result = $equipmentModel->update($updateArray, array('id' => $post['equipment_id']));
            $affectedRows = $equipmentModel->affectedRows();
            if ($result && $affectedRows) {
                $updateArray['id'] = $post['eid'];
                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-2);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 删除设备
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function delEquip($post) {
        if ($post) {
            $equipmentModel = $this->load->model('equipment');
            $updateArray = array(
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'update_from' => 'admin',
                'update_by' => $_SESSION['backend_userinfo']['id']
            );
            $result = $equipmentModel->update($updateArray, array('id' => $post['eid']));
            $affectedRows = $equipmentModel->affectedRows();
            if ($result && $affectedRows) {
                $updateArray['id'] = $post['eid'];
                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-6);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 设备管理景区
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function saveLandscape($post) {
        if ($post) {
            //TODO 绑定景区
            if (!$post['eid'] || !$post['lid']) {
                return $this->_getUserError(-8);
            }
            $equipmentModel = $this->load->model('equipment');
            $equipment = $equipmentModel->getID($post['eid']);
            $landscapeModel = $this->load->model('landscapes');

            //是否已绑定景区
            if ($equipment['landscape_id']) {
                $landscape = $landscapeModel->getID($equipment['landscape_id']);
                $msg = $this->_getUserError(-9);
                $msg = str_replace('{landscape_name}', $landscape['name'], $msg);
                return $msg;
            }

            $landscape = $landscapeModel->getID($post['lid']);

            //组织数据
            $updateArray = array(
                'organization_id' => $landscape['organization_id'],
                'landscape_id' => $post['lid'],
                'updated_at' => date('Y-m-d H:i:s'),
                'update_from' => 'admin',
                'update_by' => $_SESSION['backend_userinfo']['id'],
                'sync_id' => BaseModel::generateSyncID($landscape['organization_id']),
            );
            $result = $equipmentModel->update($updateArray, array('id' => $post['eid']));
            $affectedRows = $equipmentModel->affectedRows();
            if ($result && $affectedRows) {
                $updateArray['id'] = $post['eid'];
                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-2);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 解除关联设备
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function removeLandscape($post) {
        if ($post) {
            $equipmentModel = $this->load->model('equipment');
            $oldInfo        = $equipmentModel->getID($post['eid']);
            $updateArray = array(
                'organization_id' => 0,
                'landscape_id' => 0,
                'poi_id' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'update_from' => 'admin',
                'update_by' => $_SESSION['backend_userinfo']['id'],
                'sync_id' => BaseModel::generateSyncID($oldInfo['organization_id']),
            );
            $result = $equipmentModel->update($updateArray, array('id' => $post['eid']));
            $affectedRows = $equipmentModel->affectedRows();
            if ($result && $affectedRows) {
                $updateArray['id'] = $post['eid'];
                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-2);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 解除子景点关联设备
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function removeScenic($post) {
        if ($post) {
            $equipmentModel = $this->load->model('equipment');
            $oldInfo        = $equipmentModel->getID($post['eid']);
            $updateArray = array(
                'poi_id' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'update_from' => 'admin',
                'update_by' => $_SESSION['backend_userinfo']['id'],
                'sync_id' => BaseModel::generateSyncID($oldInfo['organization_id']),
            );
            $result = $equipmentModel->update($updateArray, array('id' => $post['eid']));
            $affectedRows = $equipmentModel->affectedRows();
            if ($result && $affectedRows) {
                $updateArray['id'] = $post['eid'];
                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-2);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 设备管理子景点
     *
     * @param array $post
     *
     * @return json
     * @author cuiyulei
     * */
    public function saveScenic($post) {
        if ($post) {
            //TODO 绑定景区
            if (!$post['eid'] || !$post['pid']) {
                return $this->_getUserError(-8);
            }
            $equipmentModel = $this->load->model('equipment');
            $poiModel = $this->load->model('poi');
            $equipment = $equipmentModel->getID($post['eid']);
            $poi = $poiModel->getID($post['pid']);

            //若景区信息为空
            if (!$equipment['landscape_id']) {
                return $this->_getUserError(-12);
            }

            $landscapeModel = $this->load->model('landscapes');
            $landscape = $landscapeModel->getID($equipment['landscape_id']);
            if (!$poi || $landscape['organization_id'] != $poi['organization_id']) {
                return $this->_getUserError(-11);
            }

            //是否已绑定景区
            if ($equipment['poi_id']) {
                $bpoi = $poiModel->getID($equipment['poi_id']);
                $msg = $this->_getUserError(-10);
                $msg = str_replace('{poi_name}', $bpoi['name'], $msg);
                return $msg;
            }

            //组织数据
            $updateArray = array(
                'poi_id' => $post['pid'],
                'updated_at' => date('Y-m-d H:i:s'),
                'update_from' => 'admin',
                'update_by' => $_SESSION['backend_userinfo']['id'],
                'sync_id' => BaseModel::generateSyncID($equipment['organization_id']),
            );
            $result = $equipmentModel->update($updateArray, array('id' => $post['eid']));
            $affectedRows = $equipmentModel->affectedRows();
            if ($result && $affectedRows) {
                $updateArray['id'] = $post['eid'];
                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-2);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 获取设备列表
     *
     * @param array $get
     *
     * @return array
     * @author cuiyulei
     * */
    public function getEquipment($get) {
        $equipmentModel = $this->load->model('equipment');

        $param = array();
        $param['filter'][$equipmentModel->table . '.deleted_at'] = null;
        $param['order'] = 'updated_at DESC';

        if ($get['id']) {
            $param['filter']['id'] = intval($get['id']);
        }

        //提交时间
        if ($get['update_time']) {
            $timeFilter = explode(' - ', $get['update_time']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$equipmentModel->table . '.updated_at|between'] = $timeFilter;
        }

        //绑定景区
        if ($get['landscape']) {
            $landscape = $get['landscape'] == 'yes' ? '.landscape_id|gthan' : '.landscape_id';
            $param['filter'][$equipmentModel->table . $landscape] = 0;
        }

        //是否安装
        if ($get['poi']) {
            $poi = $get['poi'] == 'yes' ? '.poi_id|gthan' : '.poi_id';
            $param['filter'][$equipmentModel->table . $poi] = 0;
        }

        if (!$get['id']) {
            $param['page'] = $this->getGet('p') ? $this->getGet('p') : ($get['p'] ? $get['p'] : 1);
            $param['items'] = 10;
        }

        $param['relate'] = 'landscape,poi';
        $data = $equipmentModel->commonGetList($param);

        //格式：$data['data'][]['equipment']
        return $data;
    }

    /**
     * 组织设备的数据
     *
     * @param array $post
     *
     * @return array
     * @author cuiyulei
     * */
    private function _formEquipmentData($post) {
        $data = array(
            'type' => $post['type'],
            'code' => trim($post['code']),
            'name' => trim($post['name']),
            'create_by' => $_SESSION['backend_userinfo']['id'],
            'update_by' => $_SESSION['backend_userinfo']['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $data;
    }

}

// END class 