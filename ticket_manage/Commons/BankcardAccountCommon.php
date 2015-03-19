<?php
/**
 * Created by yuanwei
 * User: yuanwei
 * Date: 13-12-23
 * Time: 下午3:36
 */

class BankcardAccountCommon extends  BaseCommon{
    protected $_code = array(
        '-1'  => '{"errors":{"msg":["参数不能为空"]}}',
        '-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
        '-3'  => '{"errors":{"msg":["机构id不能为空"]}}',
        '-4'  => '{"errors":{"msg":["银行名称不能为空"]}}',
        '-5'  => '{"errors":{"msg":["账号或者卡号不能为空"]}}',
        '-6'  => '{"errors":{"msg":["账户名称不能为空"]}}',
        '-7'  => '{"errors":{"msg":["电话号码不能为空"]}}',
        '-8'  => '{"errors":{"msg":["必须是正确的电话号码格式"]}}',
        '-9'  => '{"errors":{"msg":["地区不能为空"]}}',
        '-10'  => '{"errors":{"msg":["手机号码不能为空"]}}',
        '-11'  => '{"errors":{"msg":["必须是正确的手机号码格式"]}}',
        '-12'  => '{"errors":{"msg":["传真不能为空"]}}',
        '-13'  => '{"errors":{"msg":["必须是正确的传真格式"]}}',
        '-14'  => '{"errors":{"msg":["地址不能为空"]}}',
        '-15'  => '{"errors":{"msg":["简称不能为空"]}}',
        '-16'  => '{"errors":{"msg":["参数不能为空"]}}',
        '-17'  => '{"errors":{"msg":["权限必须选择"]}}'
    );
    protected $_errorMsg;
    /**
     * 获取机构的账户信息
     */

    public function getBankAccount($get){
        $backcardAccountModel = $this->load->model('bankcardAccount');
        $param = array();
        if($get['organization_id']){
            $param['filter'] = array('organization_id' => $get['organization_id']);
        }
        $data = $backcardAccountModel->commonGetList($param);
        return $data;
    }
    /**
     * 保存账户
     */
    public function save($post){

        $backcardAccountModel = $this->load->model('bankcardAccount');
        if(!$this->validation($post)){
            return $this->_errorMsg;
        }

        $postData = array(
            'organization_id' => $post['organization_id'],
            'bank_name' => trim($post['bank_name']),
            'account' => trim($post['account']),
            'account_name' => trim($post['account_name']),
            'type' => trim($post['type']),
            'status' => $post['status'] ? $post['status']:'disable'
        );

        if($post['id']){
            if($backcardAccountModel->update($postData,$post['id'])){
                $insertId = $post['id'];
            }
        }else{
            if($backcardAccountModel->add($postData)){
               $insertId = $backcardAccountModel->getAddID();
            }
        }
        if($insertId){
            $row = $backcardAccountModel->getId($insertId);
            return json_encode(array('data'=>$row,'success' => 'success'));
        }
            return $this->_getUserError(-2);

    }
    /**
     * 验证数据
     * @param array
     * @return bool
     */
    private function validation($data){
        $validation = $this->load->tool('validate');
        if(!$validation->validateRequired($data['organization_id'])){
            $this->_errorMsg = $this->_getUserError(-3);
            return false;
        }
        if(!$validation->validateRequired($data['bank_name'])){
            $this->_errorMsg = $this->_getUserError(-4);
            return false;
        }
        if(!$validation->validateRequired($data['account'])){
            $this->_errorMsg = $this->_getUserError(-5);
            return false;
        }
        if(!$validation->validateRequired($data['account_name'])){
            $this->_errorMsg = $this->_getUserError(-6);
            return false;
        }
        return true;
    }

    /**
     * 更新状态
     */
    public function updateStatus($id,$organization_id){
        $backcardAccountModel = $this->load->model('bankcardAccount');
        $backcardAccountModel->begin();
        $backcardAccountModel->update(array('status'=>'disable'),array('organization_id'=>$organization_id));
        $backcardAccountModel->update(array('status'=>'normal',array('id'=>$id)));
        if($backcardAccountModel->commit()){
            return true;
        }else{
            return false;
        }

    }

    /**
     * 通过银行名称查找银行名称
     */
    public function getBankByName($name){
        $model = $this->load->model('bank');
        if($row = $model->get('name',$name)){
            return true;
        }else{
            return false;
        }

    }
}