<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-20
 * Time: 下午7:05
 */

class TicketdpriceController extends Base_Controller_Api {

    /**
     * 获取日价格记录列表
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

        $data = TicketDayPriceModel::model()->search($where,$fields);
        Lang_Msg::output(array('data'=>$data));
    }

    public  function detailAction() {
        $ticket_template_id = intval($this->body['ticket_template_id']);
        !$ticket_template_id && Lang_Msg::error('ERROR_TICKET_1');
        $where = array('ticket_template_id'=>$ticket_template_id);

        $date = trim(Tools::safeOutput($this->body['date']));
        $date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$date) && Lang_Msg::error('ERROR_DATE_2');
        $date && $where['date'] = $date;

        $data = TicketDayPriceModel::model()->search($where);
        Lang_Msg::output(reset($data));
    }

    public function setAction(){
        $operator = $this->getOperator(); //获取操作者
        $now = time();
        $data = array(
            'ticket_template_id'=>intval($this->body['ticket_template_id']),
            'fat_price'=>doubleval($this->body['fat_price']),
            'group_price'=>doubleval($this->body['group_price']),
            'setting_at'=>$now,
            'setting_by'=>$operator['user_id'],
        );
        $days = $this->body['days'];
        !$days && Lang_Msg::error("ERROR_DATE_1");
        !is_array($days) && $days = explode(',',$days);

        !$data['ticket_template_id'] &&  Lang_Msg::error("ERROR_TICKET_1");
        //!$data['fat_price'] && Lang_Msg::error("ERROR_PRICE_1");
        if(!Validate::isPrice($data['fat_price']) || !Validate::isPrice($data['group_price']))
            Lang_Msg::error("ERROR_PRICE_2");

        $TicketDayPriceModel = new TicketDayPriceModel();
        $TicketDayPriceModel->begin();
        $r = $TicketDayPriceModel->addList($data,$days);
        if($r){
            $TicketDayPriceModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>count($days),'content'=>Lang_Msg::getLang('INFO_DAY_PRICE_1').'【ticket_template_id:'.$data['ticket_template_id'].',fat_price:'.$data['fat_price'].',group_price:'.$data['group_price'].',days:'.implode(',',$days).'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketDayPriceModel->rollback();
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

        $TicketDayPriceModel = new TicketDayPriceModel();
        $TicketDayPriceModel->begin();
        $r = $TicketDayPriceModel->delete($where);
        if($r){
            $TicketDayPriceModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['DEL'],'num'=>count($days),'content'=>Lang_Msg::getLang('INFO_DAY_PRICE_2').'【ticket_template_id:'.$ticket_template_id.',days:'.implode(',',$days).'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketDayPriceModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

}