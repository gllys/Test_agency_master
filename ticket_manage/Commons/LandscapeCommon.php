<?php

/**
 *  一级票务
 *
 * 2013-12-26
 *
 * @author  liuhe
 * @version 1.0
 */
class LandscapeCommon extends BaseCommon {

    protected $_code = array(
        '-1' => '{"errors":{"msg":["null post"]}}',
        '-2' => '{"errors":{"msg":["缺少必要的参数"]}}',
        '-3' => '{"errors":{"msg":["错误的状态"]}}',
        '-4' => '{"errors":{"msg":["不存在的一级票务信息"]}}',
        '-5' => '{"errors":{"msg":["一级票务已经审核，无需重复操作"]}}',
        '-6' => '{"errors":{"msg":["一级票务已经拒绝，无需重复操作"]}}',
        '-7' => '{"errors":{"msg":["保存至数据库失败"]}}',
        '-8' => '{"errors":{"msg":["缺少接入必要参数"]}}',
        '-9' => '{"errors":{"msg":["该一级票务不符合接入条件，请确保该一级票务的状态是正常且未接入，并且该机构的状态是启用"]}}',
        '-10' => '{"errors":{"msg":["该一级票务未进行过接入，不能取消接入"]}}',
    );
    //景区状态对应
    static public $landscapeStatus = array(
        'unaudited' => '未审核',
        'normal' => '审核通过',
        'failed' => '驳回',
    );

    /**
     * 景区状态
     * @param string $status 状态
     * @return string or array
     */
    static public function getLandscapeStatus($status = '') {
        if ($status) {
            return self::$landscapeStatus[$status];
        } else {
            return self::$landscapeStatus;
        }
    }

    //审核
    public function verify($post) {
        if ($post) {
            //一级票务id和状态
            if (!$post['id'] || !$post['status']) {
                return $this->_getUserError(-2);
            }

            //状态对应
            if (!array_key_exists($post['status'], self::getLandscapeStatus())) {
                return $this->_getUserError(-3);
            }

            $landscapesModel = $this->load->model('landscapes');
            $landscapesLastModel = $this->load->model('landscapeLastEdit');
            $oldInfo = $landscapesModel->getID($post['id'], 'status,name,organization_id,normal_before');

            //是否在审核通过后，用户修改了拥有的景区信息
            $editInfo = $landscapesLastModel->getOne(array('landscape_id' => $post['id']));
            if ($oldInfo) {
                if ($post['status'] == $oldInfo['status'] && $post['status'] == 'normal') {
                    return $this->_getUserError(-5);
                }

                if ($post['status'] == $oldInfo['status'] && $post['status'] == 'failed') {
                    return $this->_getUserError(-6);
                }
            } else {
                return $this->_getUserError(-4);
            }

            $updateArray = array(
                'id' => $post['id'],
                'status' => $post['status'],
                'audited_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            //TODO::审核通过后，更新用户拥有的景区信息为编辑后的信息
            if ($editInfo && $post['status'] == 'normal') {
                $updateArray['name'] = $editInfo['name'];
                $updateArray['landscape_level_id'] = $editInfo['landscape_level_id'];
                $updateArray['phone'] = $editInfo['phone'];
                $updateArray['district_id'] = $editInfo['district_id'];
                $updateArray['address'] = $editInfo['address'];
                $updateArray['exaddress'] = $editInfo['exaddress'];
                $updateArray['hours'] = $editInfo['hours'];
                $updateArray['biography'] = $editInfo['biography'];
                $updateArray['note'] = $editInfo['note'];
                $updateArray['transit'] = $editInfo['transit'];
                $updateArray['impower_id'] = $editInfo['impower_id'];
                $updateArray['updated_at'] = date('Y-m-d H:i:s');
                $updateArray['audited_at'] = date('Y-m-d H:i:s');
                // $updateArray['normal_before'] = 1;
            }

            //假如之前未审核通过，再次审核通过将normal_before置为1
            if($post['status'] == 'normal' && $oldInfo['normal_before'] == 0) {
                $updateArray['normal_before'] = 1;
            }

            $result = $landscapesModel->update($updateArray, array('id' => $post['id']));
            $affectedRows = $landscapesModel->affectedRows();
            if ($result && $affectedRows >= 1) {

                //TODO::审核通过后,删除修改的信息
                if ($post['status'] == 'normal') {
                    $landscapesLastModel->del(array('landscape_id' => $post['id']));
                }

                //TODO::门票审核后发送消息：
                $msg = '[系统公告]:' . $oldInfo['name'] . '完成审核，结果为' . self::getLandscapeStatus($post['status']);
                $messageCommon = $this->load->common('message');
                $result = $messageCommon->send($oldInfo['organization_id'], $msg);
                if ($result) {
                    return $result;
                } else {
                    return json_encode(array('data' => array($updateArray)));
                }
            } else {
                return $this->_getUserError(-7);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    /**
     * 取消与数据中心的接入
     *
     * @return void
     * @author cuiyulei
     * */
    public function itourismLandscapeOut($post) {
        if ($post) {
            //票务id
            if (!$post['id']) {
                return $this->_getUserError(-2);
            }

            $landscapesModel = $this->load->model('landscapes');
            $oldInfo = $landscapesModel->getID($post['id'], 'name,location_hash');
            if ($oldInfo) {
                if ($oldInfo['location_hash'] == '') {
                    return $this->_getUserError(-10);
                }
            } else {
                return $this->_getUserError(-4);
            }
            $result = $landscapesModel->update(array('location_hash' => '', 'updated_at' => date('Y-m-d H:i:s')), array('id' => $post['id']));
            $affectedRows = $landscapesModel->affectedRows();
            if ($result && $affectedRows >= 1) {
                return json_encode(array('data' => array($post)));
            } else {
                return $this->_getUserError(-7);
            }
        } else {
            return $this->_getUserError(-1);
        }
    }

    //保存接入的景区的hash
    public function saveItrouIsmLocationHash($post) {
        if ($post['id'] && $post['itour_ism_landscape_hash']) {
            $landscapeModel = $this->load->model('landscapes');
            $landscapeInfo = $landscapeModel->getID($post['id'], 'id,name,status,location_hash,organization_id');
            $landscapeInfo = $landscapeModel->getOneRelate($landscapeInfo, 'organization');
            if ($landscapeInfo['status'] != 'normal' || $landscapeInfo['organization']['status'] != 'normal' || !empty($landscapeInfo['location_hash'])) {
                return $this->_getUserError(-9);
            } else {
                $landscapeModel->update(array('location_hash' => $post['itour_ism_landscape_hash'], 'updated_at' => date('Y-m-d H:i:s')), array('id' => $post['id']));
                $affectedRows = $landscapeModel->affectedRows();
                if ($affectedRows >= 1) {
                    return json_encode(array('data' => array($post)));
                } else {
                    return $this->_getUserError(-7);
                }
            }
        } else {
            return $this->_getUserError(-8);
        }
    }

    /**
     * 取消景区关联三维景拍
     * @param array
     * @return bool
     */
    public function cancelPoi($post) {
        $model = $this->load->model('landscapes');
        $updateArray = array(
            'location_name' => '',
            'location_hash' => '',
            'location_at' => '0000-00-00 00:00:00',
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $result = $model->update($updateArray, array('id' => $post['id']));
        $affectedRows = $model->affectedRows();
        if ($result && $affectedRows >= 1) {
            return json_encode(array('data' => array($updateArray)));
        } else {
            return $this->_getUserError(-2);
        }
    }

    /**
     * 添加景区关联三维景拍
     * @param array
     * @return bool
     */
    public function addPoi($post) {
        $model = $this->load->model('landscapes');
        $updateArray = array(
            'location_name' => $post['name'],
            'location_hash' => $post['hash'],
            'location_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $result = $model->update($updateArray, array('id' => $post['id']));
        $affectedRows = $model->affectedRows();
        if ($result && $affectedRows >= 1) {
            return json_encode(array('data' => array($updateArray)));
        } else {
            return $this->_getUserError(-2);
        }
    }

    /**
     * 通过机构id或者景区id获取景区信息
     * @param  int $id 景区id
     * @return array
     */
    public function getLandscapeDetail($id) {
        $landscapesModel = $this->load->model('landscapes');
        $landscapeLastEditModel = $this->load->model('landscapeLastEdit');
        $organizationModel = $this->load->model('organizations');
        $organizationUrlModel = $this->load->model('attachments');
        $newInfo = $info = $landscapesModel->getOne(array('id' => $id));
        $model = $landscapesModel;

        //审核后编辑的就显示编辑的内容
        $lastEditExist = $landscapeLastEditModel->getOne(array('landscape_id' => $id));
        $orgEditExist = $organizationModel->getOne('id='.$id);
        if ($lastEditExist) {
            unset($lastEditExist['id']);
            $lastEditExist['id'] = $lastEditExist['landscape_id'];
            $lastEditExist['status'] = $info['status'];
            $newInfo = $lastEditExist;
            $model = $landscapeLastEditModel; 
        }
        	$orgid = $info["organization_id"];
            $orgEditExist = $organizationModel->getOne('id='.$orgid); 
            $orgLogoId = $orgEditExist['logo_id']; 
            $orgLogoEditExist = $organizationUrlModel->getOne('id='.$orgLogoId);  
            $data = array($newInfo,$orgEditExist,$orgLogoEditExist);
            return $data;
        
        if ($newInfo) {
            $newInfo = $this->_relationDetail($newInfo, $model);
            return $newInfo;
        } else {
            return false;
        }  
    }

    /**
     * 关联的详情
     * @param  array $info  景区信息
     * @param  model $model landscape or landscapelastedit
     * @return array
     * 
     */
    private function _relationDetail($info, $model) {
        $relate = 'thumbnail,level';
        $with = 'districts';
        $info = $model->getOneRelate($info, $relate);
        $info = $model->getOneWith($info, $with);
        
        //所在2级区域所有信息
        if ($firstCityCode = $info['districts'][0]['id']) {
            $secondArea = $this->getCityInfo($firstCityCode);
            $info['secondArea'] = $secondArea;
        }

        //所在3级区域所有信息
        if ($secondCityCode = $info['districts'][1]['id']) {
            $thirdArea = $this->getCityInfo($secondCityCode);
            $info['thirdArea'] = $thirdArea;
        }
        return $info;
    }

    /**
     * 加入到function.php中，方便全局调用
     * 通过parent的code获取城市信息 默认0 表示获取省级的
     * @param int $code 城市的code，default 0 
     * @return array
     */
    public function getCityInfo($code = '0') {
        $districtsModel = $this->load->model('districts');
        return $districtsModel->findChildById($code);
    }

}
