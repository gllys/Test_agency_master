<?php
/**
 *  机构相关数据
 *
 * 2013-11-29
 *
 * @author  yuanwei
 * @version 1.0
 */

class OrganizationCommon extends BaseCommon
{
    protected $_code = array(
        '-1'  => '{"errors":{"msg":["参数不能为空"]}}',
        '-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
        '-3'  => '{"errors":{"msg":["联系人不能为空"]}}',
        '-4'  => '{"errors":{"msg":["联系人必须在2-32个字符之间"]}}',
        '-5'  => '{"errors":{"msg":["机构类别不能为空"]}}',
        '-6'  => '{"errors":{"msg":["必须是正确的邮箱格式"]}}',
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
        '-17'  => '{"errors":{"msg":["权限必须选择"]}}',

        '-21'  => '{"errors":{"msg":["null post"]}}',
        '-22'  => '{"errors":{"msg":["缺少必要的参数"]}}',
        '-23'  => '{"errors":{"msg":["错误的状态"]}}',
        '-24'  => '{"errors":{"msg":["不存在的机构信息"]}}',
        '-25'  => '{"errors":{"msg":["机构已经审核，无需重复操作"]}}',
        '-26'  => '{"errors":{"msg":["机构已经拒绝，无需重复操作"]}}',
        '-27'  => '{"errors":{"msg":["保存至数据库失败"]}}',
    );

    protected $_errorMsg ;

    //组织机构的类型
    public static $organization_type = array(
        'government' 	=>'政府机构',
        'agency' 		=> '旅行社',
        'landscape' 	=> '景区',
        'ota' 			=> 'ota'
    );

     //机构的状态
    public static $status = array(
        'normal' => '启用',
        'disable' => '停用'
    );


    static public $verifyStatus = array(
        'apply'     => '未审核',
        'checked'   => '审核通过',
        'reject'    => '审核失败'
    );

    static public $verifyStatusColor = array(
        'apply'     => 'red',
        'checked'   => 'green',
        'reject'    => 'gray'
    );

    //获取审核状态
    static public function getVerifyStatus($status)
    {
        if($status){
            return self::$verifyStatus[$status];
        }else{
            return self::$verifyStatus;
        }
    }
   
    /**
     * 搜索
     * @param array $get
     * @return array
     */
    public function getOrganization($get = array())
    {
        $organizationModel = $this->load->model('organizations');
        $poiCommon = $this->load->common('poi');
        $bankcardAccountCommon = $this->load->common('BankcardAccount');

        $param = array();
        if($get['id']){
            $param['filter']['id'] = intval($get['id']);
        }
        if($get['name']){
            $param['filter']['name|like'] = trim($get['name']);
        }

        //提交时间
        if ($get['update_time']) {
            $timeFilter    = explode(' - ', $get['update_time']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1])+86400);
            $param['filter'][$organizationModel->table.'.updated_at|between'] = $timeFilter;
        }
        //通过地区查找
        if($get['area'] && $get['area'] != '__NULL__'){
            $param['district_id'] = $get['area'];
        }elseif($get['city'] && $get['city'] != '__NULL__'){
            $param['district_id'] = $get['city'];
        }elseif($get['province'] && $get['province'] != '__NULL__'){
            $param['district_id'] = $get['province'];
        }
        if($param['district_id']){
            $param['filter'] = $organizationModel->getDistrictDeepChildFilter($param['filter'],'district_id',$param['district_id']);
        }
        //类别搜索
        if($get['type']){
            $param['filter']['type'] = trim($get['type']);
        }

        //状态搜素
        if($get['status']){
            $param['filter']['status'] = trim($get['status']);
        }

        //状态搜素
        if($get['verify_status']){
            $param['filter']['verify_status'] = trim($get['verify_status']);
        }
        

       // $param['filter']['type'] = "agency";

        
        //分销权&&供应权搜索
        if($get['power']){
            if(trim($get['power']) == 'supply'){
                $param['filter']['supply'] = 'yes';
            }elseif(trim($get['power']) == 'distribute'){
                $param['filter']['distribute'] = 'yes';
            }
        }
        if(!$get['id']){
            $param['page']  = $this->getGet('p') ? $this->getGet('p') : ($get['p'] ? $get['p'] : 1);
            $param['items'] = 10;
        }
        
        $param['order'] = 'updated_at desc';
        $param['with'] = 'districts';
        $param['relate'] = 'licence,certificate,tax,logo';
        $data = $organizationModel->commonGetList($param);
        
        foreach($data['data'] as $k => $v){
            $data['data'][$k]['poi'] = $poiCommon->getPoi($v['id']);
            $banklist = $bankcardAccountCommon->getBankAccount(array('organization_id'=>$v['id']));
            $data['data'][$k]['bank'] = $banklist['data'];
        }
        return $data;
    }

    /**
     * 保存数据
     * @param array
     * @return mixed
     */
    public function save(&$post)
    {
        // if(!$this->validate($post)){
        //     return $this->_errorMsg;
        // }
        $data = $this->saveData($post);
        $organizationModel = $this->load->model('organizations');
        $poiCommon = $this->load->common('poi');
        $poiModel = $this->load->model('poi');
        $bankcardAccountCommon = $this->load->common('BankcardAccount');
        $result = false;$array = array();

		
        //保存机构数据
        if($id = $data['id']){
            unset($data['id']);
            $orgInfo = $organizationModel->getOne(array('id' => $id));
            if ($orgInfo['name']==$data['name']) {
                unset($data['name']);
            }
            $result = $organizationModel->update($data,array('id' => $id));
            $insert_id = $id;
        }else{
            $result = $organizationModel->add($data);
            $insert_id = $organizationModel->getAddID();
            $post['id'] = $insert_id;
        }

        //关于poi数据的处理

        // if(array_filter($post['senice_name'])){
        //     if($id){
        //         $poiModel->del(array('organization_id' => $id),'',10);
        //     }
        //     foreach($post['senice_name'] as $k => $v){
        //         if($post['poi_id']){
        //             // $row['id'] = $post['poi_id'][$k]['id'];
        //         }
        //         $row['name'] = $v;
        //         $row['district_id'] = $this->getDistrictId(array('province'=>$post['provice_poi'][$k],'city'=>$post['city_poi'][$k],'area'=>$post['area_poi'][$k]));
        //         $row['organization_id'] = $insert_id;
        //         $array[] = $row;
        //     }
        // }

        //保存poi数据   这里有个bug 如果poi数据保存不成功 那机构信息已经成功了
        // if($array){
        //     $poi_result = $poiCommon->save($array);
        //     $result = array_key_exists('success',json_decode($poi_result)) ? true : false;
        // }
        //处理银行账号信息

        if($post['row']){
            foreach($post['row'] as $k => $v){
                $v['organization_id'] = $insert_id;
                if($post['bankrow'] && $post['bankrow'] == $k){
                    $v['status'] = 'normal';
                }
                $bankcardAccountCommon->save($v);
            }

        }

        if($result){
            return json_encode(array('success' => 'success'));
        }
            return $this->_getUserError(-2);

    }

    /**
     * 对保存的数据 做过滤
     * @param array
     * @return array
     */
    private function saveData($post)
    {
        $array = array();
        if($post['id']){
            $array['id'] = $post['id'];
        }else{
            $array['type'] = trim($post['type']);
        }
        $array['name']      = trim($post['name']);
        if($post['area'] && $post['area'] != '__NULL__'){
            $array['district_id'] = $post['area'];
        }elseif($post['city'] && $post['city'] != '__NULL__'){
            $array['district_id'] = $post['city'];
        }elseif($post['province'] && $post['province'] != '__NULL__'){
            $array['district_id'] = $post['province'];
        }
        $array['address']   = trim($post['address']);
        $array['contact']   = trim($post['contact']);
        $array['telephone'] = trim($post['telephone']);
        $array['email'] = trim($post['email']);
        $array['description'] = $post['description'];
        $array['status'] = $post['status'];
        if($post['supply']){
            $array['supply'] = 'yes';
        } else{
		    $array['supply'] = 'no';
		}
        if($post['distribute']){
            $array['distribute'] = 'yes';
        } else {
		    $array['distribute'] = 'no';
		}
        $array['licence_id'] = intval($post['licence_id']);
        $array['certificate_id'] = intval($post['certificate_id']);
        $array['mobile'] = trim($post['mobile']);
        $array['fax'] = trim($post['fax']);
        $array['abbreviation'] = trim($post['abbreviation']);
        $array['logo_id'] = intval($post['logo_id']);
        $array['tax_id'] = intval($post['tax_id']);
        $array['lat'] = trim($post['lat']);
        $array['lng'] = trim($post['lng']);
        $array['created_by'] = $_SESSION['backend_userinfo']['id'];
        if($post['sms_to_buyer']){
            $array['sms_to_buyer'] = 'yes';
        } else {
            $array['sms_to_buyer'] = 'no';
        }
        if($post['sms_to_agency']){
            $array['sms_to_agency'] = 'yes';
        } else {
            $array['sms_to_agency'] = 'no';
        }
        return $array;

    }

    /**
     * 验证数据
     * @param array
     * @return bool
     */
    private function validate($post)
    {
        $validation = $this->load->tool('validate');

        if(isset($post['contact'])){
            if(!$validation->validateRequired($post['contact'])){
                $this->_errorMsg = $this->_getUserError(-3);
                return false;
            }
            if(!$validation->validateLengthBetweenAnd(array('minSize'=> 2,'maxSize'=> 32,'value'=> $post['contact']))){
                $this->_errorMsg = $this->_getUserError(-4);
                return false;
            }
        }

        if($post['email']){
            if(!$validation->validateEmail($post['email'])){
                $this->_errorMsg = $this->_getUserError(-6);
                return false;
            }
        }
        if($post['telephone']){
            if(!$validation->validatePhone($post['telephone'])){
                $this->_errorMsg = $this->_getUserError(-8);
                return false;
            }
        }
        if($post['mobile']){
            if(!$validation->validatePhone($post['mobile'])){
                $this->_errorMsg = $this->_getUserError(-11);
                return false;
            }
        }
        if($post['fax']){
            if(!$validation->validatePhone($post['fax'])){
                $this->_errorMsg = $this->_getUserError(-13);
                return false;
            }
        }
        //验证权限

        if($post['type'] == 'agency'){
            if(!$post['supply'] && !$post['distribute']){
                $this->_errorMsg = $this->_getUserError(-17);
                return false;
            }
        }else{
            if(!$post['supply']){
                $this->_errorMsg = $this->_getUserError(-17);
                return false;
            }
        }

        return true;
    }

    /**
     * 传过来的省市区 确定district_id
     *
     */
    private function getDistrictId($data)
    {
        $district_id = 0;
        if($data['area'] && $data['area'] != '__NULL__'){
            $district_id =  $data['area'];
        }elseif($data['city'] && $data['city'] != '__NULL__'){
            $district_id = $data['city'];
        }elseif($data['province'] && $data['province'] != '__NULL__'){
            $district_id = $data['province'];
        }
        return $district_id;
    }
    /**
     * 查询银行列表
     */
    public function getBankList()
    {
        $BankModel = $this->load->model('bank');
        $list = $BankModel->getList();
        return $list;
    }

    /**
     * 机构类型
     * @param string $type 机构类型
     * @return string or array
     */
    static public function getOrganizationType($type = '')
    {
        if($type) {
            return self::$organization_type[$type];
        } else {
            return self::$organization_type;
        }
    }

    /**
     * 审核机构
     *
     * @return void
     * @author cuiyulei
     **/
    public function verify($post)
    {
        if($post) {
            //机构id和状态
            if(!$post['id'] || !$post['status']) {
                return $this->_getUserError(-22);
            }

            //状态对应
            if(!array_key_exists($post['status'], self::getVerifyStatus())) {
                return $this->_getUserError(-23);
            }

            $organizationsModel = $this->load->model('organizations');
            $oldInfo            = $organizationsModel->getID($post['id'], 'status');
            if($oldInfo) {
                if($post['status'] == $oldInfo['status'] && $post['status'] == 'checked') {
                    return $this->_getUserError(-25);
                }

                if($post['status'] == $oldInfo['status'] && $post['status'] == 'reject') {
                    return $this->_getUserError(-26);
                }
            } else {
                return $this->_getUserError(-24);
            }

            $updateArray = array(
                'verify_by'     => $_SESSION['backend_userinfo']['id'],
                'verify_status' => $post['status'],
                'verify_at'     => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            );

            $result       = $organizationsModel->update($updateArray, array('id' => $post['id']));
            $affectedRows = $organizationsModel->affectedRows();
            if($result && $affectedRows >= 1) {

                //发送审核消息
                $messageCommon = $this->load->common('message');
                $msg = '[系统公告]汇联皆景分销后台审核贵公司机构，状态为'.self::getVerifyStatus($post['status']);
                $result = $messageCommon->send($post['id'], $msg);
                if ($result) {
                    return $result;
                } else {
                    return json_encode(array('data' => array($updateArray)));
                }
                
            } else {
                return $this->_getUserError(-27);
            }
        } else {
            return $this->_getUserError(-21);
        }
    }
}