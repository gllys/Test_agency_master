<?php

class ShowController extends Controller {

    public function actionIndex() {

        $param['id'] = $_GET['id']; //景区id
        //景区详细信息
        $lands = Landscape::api()->detail($param);
        $landspace = ApiModel::getData($lands);
        //print_r($landspace);
        //散客预订
        // TicketTemplate::api()->debug=true;
        $paramfit['scenic_id'] = $param['id'];
        $paramfit['is_fit'] = 1;
        $paramfit['state'] = 1;
        $paramfit['expire_end'] = 1;
        $paramfit['agency_id'] = Yii::app()->user->org_id;
        $tickets = TicketTemplate::api()->reserve_list($paramfit);
        $fitlist = ApiModel::getLists($tickets);
        // print_r($paramfit);
        //b print_r($fitlist);
        //团队预订
        $paramfull['is_full'] = 1;
        $paramfull['scenic_id'] = $param['id'];
        $paramfull['state'] = 1;
        $paramfull['expire_end'] = 1;
        $paramfull['agency_id'] = Yii::app()->user->org_id;
        $ticket = TicketTemplate::api()->reserve_list($paramfull);
        $fulllist = ApiModel::getLists($ticket);

        $this->render('index', compact('landspace', 'fitlist', 'fulllist'));
    }

    public function actionProduct() {
        $data = TicketTemplate::api()->ticketinfo(array('ticket_id'=>$_GET['id']));
        $ticket = ApiModel::getData($data);
        $this->render('product_view',  compact('ticket'));
    }

}
