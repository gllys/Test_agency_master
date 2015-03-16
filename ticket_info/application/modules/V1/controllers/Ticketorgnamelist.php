<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-10
 * Time: 下午2:59
 * 分销商限制清单
 */


class TicketorgnamelistController extends Base_Controller_Api {

    public function listsAction(){
        $where = array();
        $order = $this->getSortRule();
        $supplier_id = intval($this->getParam('supplier_id'));
        $supplier_id && $where['supplier_id'] = $supplier_id;

        $type = intval($this->getParam('type'))?1:0;
        isset($_POST['type']) && is_numeric($_POST['type']) && in_array($type,array(0,1)) && $where['type'] = $type;

        $agency_id = intval($this->getParam('agency_id'));
        $agency_id && $where['find_in_set|EXP']='('.$agency_id.',agency_ids)';

        $name = trim(Tools::safeOutput($this->getParam('name')));
        $name && $where['name|like'] = array("%{$name}%");

        $TicketOrgNamelistModel = new TicketOrgNamelistModel();
        $this->count = $TicketOrgNamelistModel->countResult($where);
        $this->pagenation();
        $data = $this->count>0  ? $TicketOrgNamelistModel->search($where,"*",$order,$this->limit) : array();

        $result = array(
            'data'=>array_values($data),
            'pagination'=>array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items'=>$this->items,
                'total'=>$this->total,
            )
        );
        Lang_Msg::output($result);
    }

    public function detailAction(){
        $where = array();
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_NAMELIST_3"); //缺少清单ID参数

        $where['id'] = $id;
        $supplier_id && $where['supplier_id'] = $supplier_id;

        $TicketOrgNamelistModel = new TicketOrgNamelistModel();
        $detail = $TicketOrgNamelistModel->search($where);
        !$detail && Lang_Msg::error("ERROR_NAMELIST_4");
        $data = reset($detail);
        Lang_Msg::output($data);
    }

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者
        $data = array(
            'type'=> intval($this->getParam('type'))?1:0,
            'supplier_id'=> intval($this->getParam('supplier_id')),
            'agency_ids'=> trim(Tools::safeOutput($this->getParam('agency_ids'))),
            'name'=> trim(Tools::safeOutput($this->getParam('name'))),
            'note'=> trim(Tools::safeOutput($this->getParam('note'))),
            'created_by'=>$operator['user_id'],
        );

        !$data['supplier_id'] && Lang_Msg::error('ERROR_SUPPLIER_1');
        !$data['name'] && Lang_Msg::error('ERROR_NAMELIST_1');

        $TicketOrgNamelistModel = new TicketOrgNamelistModel();
        $TicketOrgNamelistModel->begin();
        $r = $TicketOrgNamelistModel->addNew($data);
        if($r){
            $TicketOrgNamelistModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_NAMELIST_1').'[ID:'.$r['id'].']【'.$r['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$r);
        }
        else{
            $TicketOrgNamelistModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }

    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者

        $where = array();
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_NAMELIST_3"); //缺少清单ID参数
        !$supplier_id && Lang_Msg::error('ERROR_SUPPLIER_1'); //缺少供应商ID参数

        $where['id'] = $id;
        $where['supplier_id'] = $supplier_id;

        $TicketOrgNamelistModel = new TicketOrgNamelistModel();
        $detail = $TicketOrgNamelistModel->search($where);
        !$detail && Lang_Msg::error("ERROR_NAMELIST_4");
        $data = reset($detail);
        $type = intval($this->getParam('type'))?1:0;
        $agency_ids = trim(Tools::safeOutput($this->getParam('agency_ids')));

        if($type!=$data['type'] || $agency_ids!=$data['agency_ids']){ //检查清单是否被使用
            $discountRules = TicketDiscountRuleModel::model()->search(array('namelist_id'=>$id));
            $discountRules && Lang_Msg::error('ERROR_NAMELIST_5');
        }

        isset($_POST['type']) && $data['type'] = $type;
        isset($_POST['agency_ids']) && $data['agency_ids'] = $agency_ids;

        $name = trim(Tools::safeOutput($this->getParam('name')));
        $note = trim(Tools::safeOutput($this->getParam('note')));
        isset($_POST['name']) && !$name && Lang_Msg::error('ERROR_NAMELIST_1');
        isset($_POST['name']) && $data['name'] = $name;
        isset($_POST['note']) && $data['note'] = $note;

        $data['updated_at']= time();

        $TicketOrgNamelistModel->begin();
        $r = $TicketOrgNamelistModel->updateById($id,$data);
        if($r){
            $TicketOrgNamelistModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['UPDATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_NAMELIST_2').'[ID:'.$id.']【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$data);
        }
        else{
            $TicketOrgNamelistModel->rollback();
            Lang_Msg::error('ERROR_OPERATE_1');
        }
    }

    public function delAction(){
        $operator = $this->getOperator(); //获取操作者
        $where = array();
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_NAMELIST_3"); //缺少清单ID参数
        !$supplier_id && Lang_Msg::error('ERROR_SUPPLIER_1'); //缺少供应商ID参数

        $where['id'] = $id;
        $where['supplier_id'] = $supplier_id;

        $TicketOrgNamelistModel = new TicketOrgNamelistModel();
        $detail = $TicketOrgNamelistModel->search($where);
        !$detail && Lang_Msg::error("ERROR_NAMELIST_4");
        $data = reset($detail);

        //检查是否被优惠规则使用
        $discountRules = TicketDiscountRuleModel::model()->search(array('namelist_id'=>$id));
        $discountRules && Lang_Msg::error('ERROR_NAMELIST_5');
        //检查是否被票使用
        $nlInTickets = TicketTemplateModel::model()->search(array('is_del'=>0,'namelist_id'=>$id),"id",null,1);
        $nlInTickets && Lang_Msg::error('ERROR_NAMELIST_6');

        $r = $TicketOrgNamelistModel->deleteById($id);
        if($r){
            $TicketOrgNamelistModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['DEL'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_NAMELIST_3').'[ID:'.$id.']【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketOrgNamelistModel->rollback();
            Lang_Msg::error('ERROR_OPERATE_1');
        }
    }

}