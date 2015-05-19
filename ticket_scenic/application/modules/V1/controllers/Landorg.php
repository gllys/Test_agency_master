<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-18
 * Time: 上午9:25
 * 景区和供应商关系
 */

Class LandorgController extends Base_Controller_Api {

    public function listsAction(){
        $order = $this->getSortRule();
        $where = array('deleted_at|EXP'=>'IS NULL');

        $landscape_id = intval($this->getParam('landscape_id'));
        $organization_id = intval($this->getParam('organization_id'));

        $landscape_id>0 && $where['landscape_id'] = $landscape_id;
        $organization_id>0 && $where['organization_id'] = $organization_id;
        (!$organization_id && !$landscape_id) && Lang_Msg::error("ERROR_LAND_ORG_2"); //缺少参数：机构ID或景区ID

        $LandOrgModel = new LandOrgModel();
        $count = $LandOrgModel->countResult($where,"count(*) as count");
        $pagination = Tools::getPagination($this->getParams(),$count);
        $data = $count>0  ? $LandOrgModel->search($where,$this->getFields(),$order,$pagination['limit']) : array();

        $result = array(
            'data'=>array_values($data),
            'pagination'=>array(
                'count'=>$count,
                'current'=>$pagination['current'],
                'items'=>$pagination['items'],
                'total'=>$pagination['total'],
            )
        );
        Lang_Msg::output($result);
    }

    public function detailAction(){
        $where = array('deleted_at|EXP'=>'IS NULL');
        $id = intval($this->getParam('id'));
        !$id && Lang_Msg::error("ERROR_DETAIL_1");
        $where['id'] = $id;

        $landscape_id = intval($this->getParam('landscape_id'));
        $organization_id = intval($this->getParam('organization_id'));

        $landscape_id>0 && $where['landscape_id'] = $landscape_id;
        $organization_id>0 && $where['organization_id'] = $organization_id;
        (!$organization_id && !$landscape_id) && Lang_Msg::error("ERROR_LAND_ORG_2"); //缺少参数：机构ID或景区ID

        $detail = LandOrgModel::model()->search($where,$this->getFields());
        !$detail && Lang_Msg::error("ERROR_DETAIL_2");
        $detail = $detail[$id];
        Lang_Msg::output($detail);
    }

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者

        $data = array();
        $data['landscape_id'] = intval($this->body['landscape_id']);
        $data['organization_id'] = intval($this->body['organization_id']);
        $data['release_right'] = intval($this->body['release_right']) ? 1 : 0; //发票权：1有0无
        $data['check_right'] = intval($this->body['check_right'])==2 ? 2 : 1; //核销权：1自我2景区所有票
        $data['check_log_right'] = intval($this->body['check_log_right'])==2 ? 2 : 1; //核销记录查看权：1自我2景区所有票
        $data['scenic_manage_right'] = intval($this->body['scenic_manage_right']) ? 1 :0; //景区管理权：1有0无
        $data['poi_manage_right'] = intval($this->body['poi_manage_right']) ? 1 :0; //景点管理权：1有0无
        $data['created_by'] = $operator['user_id']; //添加者

        !$data['landscape_id'] && Lang_Msg::error("ERROR_LANDSCAPE_1"); //缺少景区ID参数
        !$data['organization_id'] && Lang_Msg::error("ERROR_SUPPLY_ORG_1"); //缺少供应商ID参数

        $LandOrgModel = new LandOrgModel();
        if($data['scenic_manage_right'] && $LandOrgModel->search(array('landscape_id'=>$data['landscape_id'],'organization_id|!='=>$data['organization_id'],'scenic_manage_right'=>1))) {
            Lang_Msg::error('ERROR_LAND_ORG_6'); //该景区管理权限已分配给其他供应商，不能重复分配
        }

        $detail = $LandOrgModel->search(array('landscape_id'=>$data['landscape_id'],'organization_id'=>$data['organization_id']));
        if($detail)
            Lang_Msg::error("ERROR_LAND_ORG_1");

        $LandOrgModel->begin();
        LandscapeModel::model()->updateByAttr(array('has_bind_org'=>1),array('id'=>$data['landscape_id']));
        $r = $LandOrgModel->addNew($data);
        if($r){
            $LandOrgModel->commit();
            LandscapeModel::model()->syncInfo($data['landscape_id']);
            Log_Landscape::model()->add(array('type'=>1,'num'=>1,'content'=>Lang_Msg::getLang('INFO_LANDORG_1').'[ID:'.$r['id'].']'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_ADD_0'), $r);
        }
        else{
            $LandOrgModel->rollback();
            Lang_Msg::error("ERROR_ADD_1");
        }
    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者

        $where = array('deleted_at|EXP'=>'IS NULL');
        $id = intval($this->body['id']);
        !$id && Lang_Msg::error("ERROR_DETAIL_1");
        $where['id'] = $id;

        $landscape_id = intval($this->body['landscape_id']);
        if($landscape_id)
            $where['landscape_id'] = $landscape_id;
        else
            Lang_Msg::error("ERROR_LAND_ORG_3"); //缺少参数：景区ID

        $LandOrgModel = new LandOrgModel();
        $detail = $LandOrgModel->search($where);
        !$detail && Lang_Msg::error("ERROR_LAND_ORG_5");
        $data = $detail[$id];

        $organization_id = intval($this->body['organization_id']);
        $organization_id && $data['organization_id']= intval($this->body['organization_id']);

        isset($_POST['release_right']) && $data['release_right'] = intval($this->body['release_right']) ? 1 : 0; //发票权：1有0无
        isset($_POST['check_right']) && $data['check_right'] = intval($this->body['check_right'])==2 ? 2 : 1; //核销权：1自我2景区所有票
        isset($_POST['check_log_right']) && $data['check_log_right'] = intval($this->body['check_log_right'])==2 ? 2 : 1; //核销记录查看权：1自我2景区所有票
        isset($_POST['scenic_manage_right']) && $data['scenic_manage_right'] = intval($this->body['scenic_manage_right']) ? 1 :0; //景区管理权：1有0无
        isset($_POST['poi_manage_right']) && $data['poi_manage_right'] = intval($this->body['poi_manage_right']) ? 1 :0; //景点管理权：1有0无

        if($organization_id && !empty($data['scenic_manage_right']) && $LandOrgModel->search(array('landscape_id'=>$landscape_id,'organization_id|!='=>$organization_id,'scenic_manage_right'=>1))) {
            Lang_Msg::error('ERROR_LAND_ORG_6'); //该景区管理权限已分配给其他供应商，不能重复分配
        }

        $deleted = intval($this->body['deleted']);
        if($deleted)
             $data['deleted_at'] = date("Y-m-d H:i:s");
        else
            $data['updated_at']= date("Y-m-d H:i:s");

        $LandOrgModel->begin();
        $r = $LandOrgModel->updateById($id,$data);
        if($r){
            $LandOrgModel->commit();
            $landscape_id && LandscapeModel::model()->syncInfo($landscape_id);
            Log_Landscape::model()->add(array('type'=>2,'num'=>1,'content'=>Lang_Msg::getLang('INFO_LANDORG_2').'[ID:'.$id.']'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_UPDATE_0'),$data);
        }
        else{
            $LandOrgModel->rollback();
            Lang_Msg::error("ERROR_UPDATE_1");
        }
    }

    public function delAction(){
        $landscape_id = intval($this->body['landscape_id']);
        if($landscape_id)
            $where['landscape_id'] = $landscape_id;
        else
            Lang_Msg::error("ERROR_LAND_ORG_3"); //缺少参数：景区ID

        $organization_id = intval($this->body['organization_id']);
        !$organization_id && Lang_Msg::error("ERROR_LAND_ORG_4");
        $where['organization_id'] = $organization_id;

        $LandOrgModel = new LandOrgModel();
        $detail = $LandOrgModel->search($where);
        !$detail && Lang_Msg::error("ERROR_DETAIL_2");

        $LandOrgModel->begin();
        $r = $LandOrgModel->delete($where);
        if($r){
            $has_landscape_tmp = LandscapeOrganizationModel::model()->search(array('landscape_id'=>$landscape_id,'deleted_at|exp'=>'IS NULL'));
            if(!$has_landscape_tmp){
                LandscapeModel::model()->updateByAttr(array('has_bind_org'=>0),array('id'=>$landscape_id));
            }
            $LandOrgModel->commit();
            Log_Landscape::model()->add(array('type'=>3,'num'=>1,'content'=>Lang_Msg::getLang('INFO_LANDORG_3')));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_DEL_0'));
        }
        else{
            $LandOrgModel->rollback();
            Lang_Msg::error('ERROR_DEL_1');
        }

    }

}