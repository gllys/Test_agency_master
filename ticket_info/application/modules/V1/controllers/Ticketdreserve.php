<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-20
 * Time: 下午7:07
 */

class TicketdreserveController extends Base_Controller_Api {

    /**
     * 获取日库存记录列表
     * 按月、票ID查找，不分页
     */
    public function listsAction() {
        $fields = trim(Tools::safeOutput($this->body['fields']));
        $fields = $fields ? $fields :"*"; //要获取的字段

        $ticket_template_id = intval($this->body['ticket_template_id']);
        !$ticket_template_id && Lang_Msg::error('ERROR_TICKET_1');
        $where = array('ticket_template_id'=>$ticket_template_id);

        $ym =  trim(Tools::safeOutput($this->body['ym']));
        $ym && !preg_match("/^\d{4}-\d{2}$/",$ym) && Lang_Msg::error('ERROR_YM_1');
        $ym && $where['date|BETWEEN'] = array($ym.'-01',$ym.'-31');

        $date = trim(Tools::safeOutput($this->body['date']));
        $date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$date) && Lang_Msg::error('ERROR_DATE_2');
        $date && $where['date'] = $date;

        $data = TicketDayReserveModel::model()->search($where,$fields);
        Lang_Msg::output(array('data'=>$data));
    }

    public  function detailAction() {
        $ticket_template_id = intval($this->body['ticket_template_id']);
        !$ticket_template_id && Lang_Msg::error('ERROR_TICKET_1');
        $where = array('ticket_template_id'=>$ticket_template_id);

        $date = trim(Tools::safeOutput($this->body['date']));
        $date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$date) && Lang_Msg::error('ERROR_DATE_2');
        $date && $where['date'] = $date;

        $data = TicketDayReserveModel::model()->search($where);
        Lang_Msg::output(reset($data));
    }

    public function setAction(){
        $operator = $this->getOperator(); //获取操作者
        $now = time();
        $data = array(
            'ticket_template_id'=>intval($this->body['ticket_template_id']),
            'reserve'=>intval($this->body['reserve']),
            'setting_by'=>$operator['user_id'],
            'setting_at'=>$now,
        );
        $days = $this->body['days'];
        !$days && Lang_Msg::error("ERROR_DATE_1");
        !is_array($days) && $days = explode(',',$days);

        !$data['ticket_template_id'] &&  Lang_Msg::error("ERROR_TICKET_1");
        $data['reserve']<1 && Lang_Msg::error("ERROR_RESERVE_1");

        $TicketDayReserveModel = new TicketDayReserveModel();
        $TicketDayReserveModel->begin();
        $r = $TicketDayReserveModel->addList($data,$days);
        if($r){
            $TicketDayReserveModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>count($days),'content'=>Lang_Msg::getLang('INFO_DAY_RESERVE_1').'【ticket_template_id:'.$data['ticket_template_id'].',reserve:'.$data['reserve'].',days:'.implode(',',$days).'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketDayReserveModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    public function delAction(){
        $operator = $this->getOperator(); //获取操作者
        $ticket_template_id = intval($this->body['ticket_template_id']);
        !$ticket_template_id && Lang_Msg::error('ERROR_TICKET_1');
        $where = array('ticket_template_id'=>$ticket_template_id);

        $days = $this->body['days'];
        (!$days || (!is_array($days) && !preg_match("/\S+/",$days))) && Lang_Msg::error("ERROR_DATE_1");
        !is_array($days) && $days = explode(',',trim($days));
        $where['date|IN'] = $days;

        $TicketDayReserveModel = new TicketDayReserveModel();
        $TicketDayReserveModel->begin();
        $r = $TicketDayReserveModel->delete($where);
        if($r){
            $TicketDayReserveModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['DEL'],'num'=>count($days),'content'=>Lang_Msg::getLang('INFO_DAY_RESERVE_2').'【ticket_template_id:'.$ticket_template_id.',days:'.implode(',',$days).'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketDayReserveModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    //更改已使用库存
    public function chgusedreserveAction(){
        $ticket_id = intval($this->body['ticket_id']);
        $date = trim(Tools::safeOutput($this->body['date']));
        $nums = intval($this->body['nums']);
        $is_refund = intval($this->body['is_refund']);

        $TicketDayReserveModel = new TicketDayReserveModel();
        $info = $TicketDayReserveModel->search(array('ticket_template_id'=>$ticket_id,'date'=>$date));
        if(!$info) Lang_Msg::error("ERROR_RESERVE_2"); //该日不存在日库存设置
        $info = reset($info);
        $ticketDayUsedReserveKey = 'TicketDayReserveModel|'.$ticket_id.'|'.$date;
        $ticketDayUsedReserve = Cache_Redis::factory()->get($ticketDayUsedReserveKey);
        $info['used_reserve'] = intval($ticketDayUsedReserve);

        if(!$is_refund && $nums>$info['reserve']-$info['used_reserve']){
            Lang_Msg::error("ERROR_TKT_RULE_6"); //购票张数不能超出当日库存剩余数
        }

        $info['used_reserve'] = $is_refund ? $info['used_reserve']-$nums : $info['used_reserve']+$nums;
        $r = Cache_Redis::factory()->setex($ticketDayUsedReserveKey,172800,$info['used_reserve']);
        if($r){
            Lang_Msg::output( $info );
        }
        else
            Lang_Msg::error("ERROR_OPERATE_1");
    }


}