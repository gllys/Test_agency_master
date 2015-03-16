<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-10
 * Time: 下午6:14
 */




class TicketdiscountruleController extends Base_Controller_Api {

    public function listsAction(){
        $where = array();
        $order = $this->getSortRule();
        $supplier_id = intval($this->getParam('supplier_id'));
        $supplier_id && $where['supplier_id'] = $supplier_id;

        $agency_id = intval($this->getParam('agency_id'));
        if($agency_id){
            $nlWhere = $where;
            $nlWhere['find_in_set|EXP']='('.$agency_id.',agency_ids)';
            $nameLists = TicketOrgNamelistModel::model()->search($nlWhere);
            $namelist_ids = array_keys($nameLists);
            if($namelist_ids)
                $where['namelist_id|IN'] = $namelist_ids;
            else{
                $this->pagenation();
                $result = array(
                    'data'=>array(),
                    'pagination'=>array(  'count'=>$this->count,   'current'=>$this->current,  'items'=>$this->items,  'total'=>$this->total,)
                );
                Lang_Msg::output($result);
            }
        }
        else{
            $namelist_id = intval($this->getParam('namelist_id'));
            $namelist_id && $where['namelist_id'] = $namelist_id;
        }

        $name = trim(Tools::safeOutput($this->getParam('name')));
        $name && $where['name|like'] = array("%{$name}%");

        $TicketDiscountRuleModel = new TicketDiscountRuleModel();
        $this->count = $TicketDiscountRuleModel->countResult($where);
        $this->pagenation();
        $data = $this->count>0  ? $TicketDiscountRuleModel->search($where,"*",$order,$this->limit) : array();

        $result = array(
            'data'=>array_values($data),
            'pagination'=>array( 'count'=>$this->count, 'current'=>$this->current,  'items'=>$this->items,  'total'=>$this->total, )
        );
        Lang_Msg::output($result);
    }

    public function detailAction(){
        $where = array();
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_DISCOUNT_1"); //缺少ID参数

        $where['id'] = $id;
        $supplier_id && $where['supplier_id'] = $supplier_id;

        $TicketDiscountRuleModel = new TicketDiscountRuleModel();
        $detail = $TicketDiscountRuleModel->search($where);
        !$detail && Lang_Msg::error("ERROR_DISCOUNT_2");
        $data = reset($detail);
        Lang_Msg::output($data);
    }

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者
        $data = array(
            'supplier_id'=> intval($this->getParam('supplier_id')),
            'namelist_id'=> intval($this->getParam('namelist_id')),
            'name'=> trim(Tools::safeOutput($this->getParam('name'))),
            'note'=> trim(Tools::safeOutput($this->getParam('note'))),
            'fat_discount'=> doubleval($this->getParam('fat_discount')), //散客优惠减免
            'group_discount'=> doubleval($this->getParam('group_discount')), //团客优惠减免
            'created_by'=>$operator['user_id'],
        );

        !$data['supplier_id'] && Lang_Msg::error('ERROR_SUPPLIER_1');
        !$data['name'] && Lang_Msg::error('ERROR_DISCOUNT_3');
        !$data['namelist_id'] && Lang_Msg::error('ERROR_DISCOUNT_7');
        (!$data['fat_discount'] && !$data['group_discount']) && Lang_Msg::error('ERROR_DISCOUNT_4');

        $start_date = trim(Tools::safeOutput($this->getParam('start_date')));
        $end_date = trim(Tools::safeOutput($this->getParam('end_date')));
        !$start_date && Lang_Msg::error('ERROR_DISCOUNT_5');
        !$end_date && Lang_Msg::error('ERROR_DISCOUNT_6');
        $start_date && $data['start_date'] = strtotime($start_date);
        $end_date && $data['end_date'] = strtotime($end_date." 23:59:59");

        $start_date > $end_date && Lang_Msg::error('ERROR_DISCOUNT_8');

        $TicketDiscountRuleModel = new TicketDiscountRuleModel();
        $TicketDiscountRuleModel->begin();
        $r = $TicketDiscountRuleModel->addNew($data);
        if($r){
            $TicketDiscountRuleModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_DISCOUNT_1').'[ID:'.$r['id'].']【'.$r['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$r);
        }
        else{
            $TicketDiscountRuleModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }

    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者

        $where = array();
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_DISCOUNT_1"); //缺少优惠规则ID参数
        !$supplier_id && Lang_Msg::error('ERROR_SUPPLIER_1'); //缺少供应商ID参数

        $where['id'] = $id;
        $where['supplier_id'] = $supplier_id;

        $TicketDiscountRuleModel = new TicketDiscountRuleModel();
        $detail = $TicketDiscountRuleModel->search($where);
        !$detail && Lang_Msg::error("ERROR_DISCOUNT_2");
        $data = reset($detail);

        $name = trim(Tools::safeOutput($this->getParam('name')));
        $note = trim(Tools::safeOutput($this->getParam('note')));
        $namelist_id = intval($this->getParam('namelist_id'));
        $fat_discount = doubleval($this->getParam('fat_discount'));
        $group_discount = doubleval($this->getParam('group_discount'));
        $start_date = trim(Tools::safeOutput($this->getParam('start_date')));
        $end_date = trim(Tools::safeOutput($this->getParam('end_date')));
        isset($_POST['name']) && !$name && Lang_Msg::error('ERROR_DISCOUNT_3');
        !$namelist_id && Lang_Msg::error('ERROR_DISCOUNT_7');
        !$start_date && Lang_Msg::error('ERROR_DISCOUNT_5');
        !$end_date && Lang_Msg::error('ERROR_DISCOUNT_6');
        (!$fat_discount && !$group_discount) && Lang_Msg::error('ERROR_DISCOUNT_4');

        isset($_POST['name']) && $data['name'] = $name;
        isset($_POST['note']) && $data['note'] = $note;
        isset($_POST['namelist_id']) && $data['namelist_id'] = $namelist_id;
        isset($_POST['start_date']) && $data['start_date'] = strtotime($start_date);
        isset($_POST['end_date']) && $data['end_date'] = strtotime($end_date." 23:59:59");
        isset($_POST['fat_discount']) && $data['fat_discount'] = $fat_discount;
        isset($_POST['group_discount']) && $data['group_discount'] = $group_discount;

        $data['start_date'] > $data['end_date'] && Lang_Msg::error('ERROR_DISCOUNT_8');

        $data['updated_at']= time();

        $TicketDiscountRuleModel->begin();
        $r = $TicketDiscountRuleModel->updateById($id,$data);
        if($r){
            $TicketDiscountRuleModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['UPDATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_DISCOUNT_2').'[ID:'.$id.']【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$data);
        }
        else{
            $TicketDiscountRuleModel->rollback();
            Lang_Msg::error('ERROR_OPERATE_1');
        }
    }

    public function delAction(){
        $operator = $this->getOperator(); //获取操作者
        $where = array();
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_DISCOUNT_1"); //缺少优惠规则ID参数
        !$supplier_id && Lang_Msg::error('ERROR_SUPPLIER_1'); //缺少供应商ID参数

        $where['id'] = $id;
        $where['supplier_id'] = $supplier_id;

        $TicketDiscountRuleModel = new TicketDiscountRuleModel();
        $detail = $TicketDiscountRuleModel->search($where);
        !$detail && Lang_Msg::error("ERROR_DISCOUNT_2");
        $data = reset($detail);

        //检查是否被票使用
        $drInTickets = TicketTemplateModel::model()->search(array('is_del'=>0,'discount_id'=>$id),"id",null,1);
        $drInTickets && Lang_Msg::error('ERROR_DISCOUNT_9');

        $r = $TicketDiscountRuleModel->deleteById($id);
        if($r){
            $TicketDiscountRuleModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['DEL'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_DISCOUNT_3').'[ID:'.$id.']【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketDiscountRuleModel->rollback();
            Lang_Msg::error('ERROR_OPERATE_1');
        }
    }

}