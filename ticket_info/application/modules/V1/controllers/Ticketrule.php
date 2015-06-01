<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-3
 * Time: 下午2:35
 * 票价格规则
 */

class TicketruleController extends Base_Controller_Api {

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者
        $data = array(
            'supplier_id'=> intval($this->getParam('supplier_id')),
            'name'=> trim(Tools::safeOutput($this->getParam('name'))),
            'desc'=> trim(Tools::safeOutput($this->getParam('desc'))),
            'created_by'=>$operator['user_id'],
        );

        !$data['supplier_id'] && Lang_Msg::error('ERROR_TKT_RULE_3');
        !$data['name'] && Lang_Msg::error('ERROR_TKT_RULE_1');

        $TicketRuleModel = new TicketRuleModel();
        $TicketRuleModel->begin();
        $r = $TicketRuleModel->addNew($data);
        if($r){
            $TicketRuleModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_TKT_RULE_1').'[rule_id:'.$r['id'].']【'.$r['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$r);
        }
        else{
            $TicketRuleModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者

        $where = array('deleted_at'=>0);
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));
        $deleted = intval($this->getParam('deleted'));

        !$id && Lang_Msg::error("ERROR_TKT_RULE_2"); //缺少规则ID参数
        !$supplier_id && Lang_Msg::error('ERROR_TKT_RULE_3');

        $where['id'] = $id;
        $where['supplier_id'] = $supplier_id;

        $TicketRuleModel = new TicketRuleModel();
        $detail = $TicketRuleModel->search($where);
        !$detail && Lang_Msg::error("ERROR_TKT_RULE_4");
        $data = reset($detail);

        $name = trim(Tools::safeOutput($this->getParam('name')));
        $desc = trim(Tools::safeOutput($this->getParam('desc')));

        isset($_POST['name']) && !$name && Lang_Msg::error('ERROR_TKT_RULE_1');

        isset($_POST['name']) && $data['name'] = $name;
        isset($_POST['desc']) && $data['desc'] = $desc;

        $nowTime = time();
        if($deleted){
            //检查价格规则是否被使用
            $ruleInTickets = TicketTemplateModel::model()->search(array('is_del'=>0,'rule_id'=>$id),"id",null,1);
            $ruleInTickets && Lang_Msg::error('ERROR_TKT_RULE_7');
            $data['deleted_at']= $nowTime;
            $operation_lang_id = 'INFO_TKT_RULE_3';
        }
        else{
            $data['updated_at']= $nowTime;
            $operation_lang_id = 'INFO_TKT_RULE_2';
        }
        $TicketRuleModel->begin();
        $r = $TicketRuleModel->update($data,array('id'=>$id));
        if($r){
            $TicketRuleModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type[($deleted?'DEL':'UPDATE')],'num'=>1,'content'=>Lang_Msg::getLang($operation_lang_id).'[ID:'.$id.']【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$data);
        }
        else{
            $TicketRuleModel->rollback();
            Lang_Msg::error('ERROR_OPERATE_1');
        }

    }

    public function listsAction(){
        $fields = trim(Tools::safeOutput($this->getParam('fields')));
        $fields = $fields ? $fields :"*"; //要获取的字段
        $fieldArr = array();
        if($fields!="*"){
            $fieldArr = explode(',',$fields);
            !in_array('id',$fieldArr) && array_unshift($fieldArr,'id');
            $fields = implode(',',$fieldArr);
        }
        $order = $this->getSortRule();

        $where = array('deleted_at'=>0);
        $supplier_id = intval($this->getParam('supplier_id'));
        !$supplier_id && Lang_Msg::error('ERROR_TKT_RULE_3');
        $where['supplier_id'] = $supplier_id;

        $TicketRuleModel = new TicketRuleModel();
        $this->count = $TicketRuleModel->countResult($where);
        $this->pagenation();
        $data = $this->count>0  ? $TicketRuleModel->search($where,$fields,$order,$this->limit) : array();

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
        $where = array('deleted_at'=>0);
        $id = intval($this->getParam('id'));
        $supplier_id = intval($this->getParam('supplier_id'));

        !$id && Lang_Msg::error("ERROR_TKT_RULE_2"); //缺少规则ID参数
        !$supplier_id && Lang_Msg::error('ERROR_TKT_RULE_3');

        $where['id'] = $id;
        $where['supplier_id'] = $supplier_id;

        $TicketRuleModel = new TicketRuleModel();
        $detail = $TicketRuleModel->search($where);
        !$detail && Lang_Msg::error("ERROR_TKT_RULE_4");
        $detail = reset($detail);
        intval($this->getParam('show_items')) && $detail['rule_items'] = TicketRuleItemModel::model()->search(array('rule_id'=>$id));
        Lang_Msg::output($detail);
    }

    public function itemsAction() {
        $rule_id = intval($this->getParam('rule_id'));
        !$rule_id && Lang_Msg::error('ERROR_TKT_RULE_2');
        $where = array('rule_id'=>$rule_id);

        $ym =  trim(Tools::safeOutput($this->getParam('ym')));
        $ym && !preg_match("/^\d{4}-\d{2}$/",$ym) && Lang_Msg::error('ERROR_YM_1');
        $ym && $where['date|BETWEEN'] = array($ym.'-01',$ym.'-31');

        $date = trim(Tools::safeOutput($this->getParam('date')));
        $date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$date) && Lang_Msg::error('ERROR_DATE_2');
        $date && $where['date'] = $date;

        $data = TicketRuleItemModel::model()->search($where);
        Lang_Msg::output(array('data'=>$data));
    }

    public function setitemAction(){
        $operator = $this->getOperator(); //获取操作者
        $supplier_id = intval($this->getParam('supplier_id'));
        !$supplier_id && Lang_Msg::error('ERROR_TKT_RULE_3');

        $data = array(
            'rule_id'=>intval($this->getParam('rule_id')),
            'fat_price'=>trim(Tools::safeOutput($this->getParam('fat_price'))),
            'group_price'=>trim(Tools::safeOutput($this->getParam('group_price'))),
            'reserve'=>intval($this->getParam('reserve')),
        );
        !TicketRuleModel::model()->search(array('id'=>$data['rule_id'],'supplier_id'=>$supplier_id)) && Lang_Msg::error('ERROR_TKT_RULE_4');

        $days = $this->getParam('days');
        !$days && Lang_Msg::error("ERROR_DATE_1");
        !is_array($days) && $days = explode(',',$days);

        !$data['rule_id'] &&  Lang_Msg::error("ERROR_TKT_RULE_2");
        !$data['fat_price'] && !$data['group_price'] && !$data['reserve'] && Lang_Msg::error("ERROR_TKT_RULE_5");

        $TicketRuleItemModel = new TicketRuleItemModel();
        $TicketRuleItemModel->begin();
        $r = $TicketRuleItemModel->addList($data,$days);
        if($r){
            $TicketRuleItemModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>count($days),'content'=>Lang_Msg::getLang('INFO_TKT_RULE_4').'【rule_id:'.$data['rule_id'].',fat_price:'.$data['fat_price'].',group_price:'.$data['group_price'].',days:'.implode(',',$days).'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketRuleItemModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    public function delitemAction(){
        $operator = $this->getOperator(); //获取操作者
        $rule_id = intval($this->getParam('rule_id'));
        $supplier_id = intval($this->getParam('supplier_id'));
        !$rule_id && Lang_Msg::error('ERROR_TKT_RULE_2');
        !$supplier_id && Lang_Msg::error('ERROR_TKT_RULE_3');

        !TicketRuleModel::model()->search(array('id'=>$rule_id,'supplier_id'=>$supplier_id)) && Lang_Msg::error('ERROR_TKT_RULE_4');

        $where = array('rule_id'=>$rule_id);
        $days = $this->getParam('days');
        (!$days || (!is_array($days) && !preg_match("/\S+/",$days))) && Lang_Msg::error("ERROR_DATE_1");
        !is_array($days) && $days = explode(',',trim($days));
        $where['date|IN'] = $days;

        $TicketRuleItemModel = new TicketRuleItemModel();
        $TicketRuleItemModel->begin();
        $r = $TicketRuleItemModel->delete($where);
        if($r){
            $TicketRuleItemModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['DEL'],'num'=>count($days),'content'=>Lang_Msg::getLang('INFO_TKT_RULE_5').'【rule_id:'.$rule_id.',days:'.implode(',',$days).'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketRuleItemModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    //更改已使用库存
    public function chgusedreserveAction(){
        $ticket_id = intval($this->body['ticket_id']);
        $rule_id = intval($this->body['rule_id']);
        $date = trim(Tools::safeOutput($this->getParam('date')));
        $nums = intval($this->body['nums']);
        $is_refund = intval($this->body['is_refund']);

        $TicketRuleItemModel = new TicketRuleItemModel();
        $info = $TicketRuleItemModel->search(array('rule_id'=>$rule_id,'date'=>$date));
        if(!$info) Lang_Msg::error("ERROR_TKT_RULE_4");
        $info = reset($info);

        $ticketDayUsedReserveKey = 'TicketRuleItem|'.$ticket_id.'|'.$rule_id.'|'.$date;
        $ticketDayUsedReserve = Cache_Redis::factory()->get($ticketDayUsedReserveKey);
        $info['used_reserve'] = intval($ticketDayUsedReserve);
        if($is_refund==0 && $info['reserve']>0 && $nums>$info['reserve']-$info['used_reserve']){
            Lang_Msg::error("ERROR_TKT_RULE_6"); //购票张数不能超出当日库存剩余数
        }

        $info['used_reserve'] = $is_refund ? $info['used_reserve']-$nums : $info['used_reserve']+$nums;
        $info['used_reserve'] = $info['used_reserve']<0 ? 0 :$info['used_reserve'];
        $r = Cache_Redis::factory()->setex($ticketDayUsedReserveKey,172800,$info['used_reserve']);
        if($r){
            Lang_Msg::output( $info );
        }
        else
            Lang_Msg::error("ERROR_OPERATE_1");
    }

}