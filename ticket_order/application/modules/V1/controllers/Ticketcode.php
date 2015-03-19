<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-8
 * Time: 下午3:07
 */

class TicketcodeController extends Base_Controller_Api {

    public function listsAction(){


    }

    public function addAction(){
        $params = $this->getOperator(); //获取操作者
        $params['distributor_id'] = intval($this->body['distributor_id']);
        $ticket_template_ids = trim(Tools::safeOutput($this->body['ticket_template_ids']));

        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        !$ticket_template_ids && Lang_Msg::error('ERROR_TKT_1'); //缺少票种ID参数

        $ticket_template_ids = explode(',',$ticket_template_ids);
        sort($ticket_template_ids);
        $params['ticket_template_ids'] = implode(',',$ticket_template_ids);

        $data = TicketCodeModel::model()->search(array('distributor_id'=>$params['distributor_id'],'ticket_template_ids'=>$params['ticket_template_ids']));
        if($data){
            Lang_Msg::output(reset($data));
        }
        else{
            $r = TicketCodeModel::model()->addNew($params);
            if($r) {
                Lang_Msg::output($r);
            }
            else
                Lang_Msg::error('ERROR_OPERATE_1');
        }
    }


    public function delAction(){
        $params = $this->getOperator(); //获取操作者

        $id = intval($this->body['id']);
        $distributor_id = intval($this->body['distributor_id']);
        $data = TicketCodeModel::model()->search(array('id'=>$id,'distributor_id'=>$distributor_id));
        if(!$data) Lang_Msg::error('ERROR_CANCELCODE_5');

        $r = TicketCodeModel::model()->updateByAttr(array('deleted_at'=>time()),array('id'=>$id,'distributor_id'=>$distributor_id));
        if($r) {
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else
            Lang_Msg::error('ERROR_OPERATE_1');
    }
}