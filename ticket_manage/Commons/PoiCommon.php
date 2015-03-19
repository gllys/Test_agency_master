<?php

/**
 * Created by yuanwei.
 * User: yuanwei
 * Date: 13-12-20
 * Time: 上午11:19
 */
class PoiCommon extends BaseCommon {

    protected $_code = array(
        '1' => '{"success":"success"}',
        '-1' => '{"errors":{"msg":["参数不能为空"]}}',
        '-2' => '{"errors":{"msg":["保存至数据库失败"]}}',
        '-3' => '{"errors":{"msg":["景区名称不能为空"]}}',
        '-4' => '{"errors":{"msg":["机构id不能为空"]}}',
        '-5' => '{"errors":{"msg":["地区id不能为空"]}}',
        '-11' => '{"errors":{"msg":["null post"]}}',
        '-12' => '{"errors":{"msg":["缺少必要的参数"]}}',
        '-13' => '{"errors":{"msg":["错误的状态"]}}',
        '-14' => '{"errors":{"msg":["不存在的一级票务信息"]}}',
        '-15' => '{"errors":{"msg":["一级票务已经审核，无需重复操作"]}}',
        '-16' => '{"errors":{"msg":["一级票务已经拒绝，无需重复操作"]}}',
        '-17' => '{"errors":{"msg":["保存至数据库失败"]}}'
    );
    protected $_errorMsg;
    //define static status array
    static public $status = array(
        'unaudited' => '未审核',
        'normal' => '审核通过',
        'failed' => '驳回'
    );

    /**
     * 保存数据
     * @param array
     * @return bool
     */
    public function save($data) {
        if (is_array($data) && count($data) >= 1) {
            $poiModel = $this->load->model('poi');
            $poiModel->begin();

            foreach ($data as $k => $v) {
                if (!$v['id']) {
                    if (!$this->validation($v)) {
                        return $this->_errorMsg;
                    }

                    $poiModel->add($v);
                } else {
                    if (!$this->validation($v)) {

                        return $this->_errorMsg;
                    }
                    $id = $v['id'];
                    unset($v['id']);
                    $poiModel->update($v, $id);
                }
            }

            if (!$poiModel->commit()) {
                return $this->_getUserError(-2);
            }
            return $this->_getUserError(1);
        }
    }

    /**
     * verify poi
     *
     * @return void
     * @author cuiyulei
     * */
    public function verify($post) {
        if ($post) {

            //一级票务id和状态
            if (!$post['id'] || !$post['status']) {
                return $this->_getUserError(-12);
            }

            //状态对应
            if (!array_key_exists($post['status'], self::$status)) {
                return $this->_getUserError(-13);
            }

            $poiModel = $this->load->model('poi');
            $poiLastModel = $this->load->model('poiLastEdit');
            $oldInfo = $poiModel->getID($post['id'], 'status');

            //是否在审核通过后，用户修改了拥有的景区信息
            $editInfo = $poiLastModel->getOne(array('poi_id' => $post['id']));

            if ($oldInfo) {
                if ($post['status'] == $oldInfo['status'] && $post['status'] == 'normal') {
                    return $this->_getUserError(-15);
                }

                if ($post['status'] == $oldInfo['status'] && $post['status'] == 'failed') {
                    return $this->_getUserError(-16);
                }
            } else {
                return $this->_getUserError(-14);
            }

            $updateArray = array(
                'id' => $post['id'],
                'status' => $post['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'checked_at' => date('Y-m-d H:i:s'),
            );

            //TODO::审核通过后，更新用户拥有的景区信息为编辑后的信息
            if ($editInfo && $post['status'] == 'normal') {
                $updateArray['name'] = $editInfo['name'];
                $updateArray['level_id'] = $editInfo['level_id'];
                $updateArray['district_id'] = $editInfo['district_id'];
                $updateArray['updated_at'] = date('Y-m-d H:i:s');
                $updateArray['checked_at'] = date('Y-m-d H:i:s');
                $updateArray['normal_before'] = 1;
            }

            $result = $poiModel->update($updateArray, array('id' => $post['id']));
            $affectedRows = $poiModel->affectedRows();
            if ($result && $affectedRows >= 1) {

                //TODO::审核通过后,删除修改的信息
                if ($post['status'] == 'normal') {
                    $poiLastModel->del(array('poi_id' => $post['id']));
                }

                return json_encode(array('data' => array($updateArray)));
            } else {
                return $this->_getUserError(-17);
            }
        } else {
            return $this->_getUserError(-11);
        }
    }

    /**
     * 验证数据
     * @param array
     * @return bool
     */
    private function validation($data) {
        $validation = $this->load->tool('validate');
        if (!$validation->validateRequired($data['name'])) {
            $this->_errorMsg = $this->_getUserError(-3);
            return false;
        }
        if (!$validation->validateRequired($data['organization_id'])) {
            $this->_errorMsg = $this->_getUserError(-4);
            return false;
        }
        if (!$validation->validateRequired($data['district_id'])) {
            $this->_errorMsg = $this->_getUserError(-5);
            return false;
        }
        return true;
    }

    /**
     * 获取机构的景区
     * @param array
     * @return bool
     */
    public function getPoi($id) {
        $model = $this->load->model('poi');
        $param = array('filter' => array('organization_id' => $id));
        $param['with'] = 'districts';
        $poi = $model->commonGetList($param);
        return $poi;
    }

    

}
