<?php

class SingleController extends Controller {

    public function actionIndex() {
        $param = $_REQUEST;
        $param['type'] = isset($param['type'])?$param['type']:0;
        //$data['type_labels'] = array( '0' => '单票', '1' => '任务单','2' => '联票');
        $data['type_labels'] = array( '0' => '单票', '2' => '联票');
        $data['type'] = array_keys($data['type_labels']);
        if (!empty($params)) {
            if (isset($params['type']) && !in_array($params['type'], $data['type'])) {
                unset($params['type']);
            }
        }
        if (!empty($_REQUESTGET['jq'])) {
            $param['scenic_id'] = $_REQUEST['jq'];
        }
        //TicketTemplate::api()->debug=true;
        $param['state'] = "true";
        $param['or_id'] = YII::app()->user->org_id;
        $param['p'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 10;
        $datas = TicketTemplate::api()->lists($param);
        $lists = ApiModel::getLists($datas);
        //print_r($param);
        // print_r($lists);

        //分页
        $pagination = ApiModel::getPagination($datas);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 10; #每页显示的数目
        //print_r($param);
         $this->render('index', compact('lists', 'pages', 'param','data'));
    }

    //票编辑
    public function actionEdit() {
        $this->renderPartial('edit');
    }

    //删除票
    public function actionDel($id) {
        if (Yii::app()->request->isPostRequest) {
            $rs['id'] = $_GET['id'];
            $rs['or_id'] = YII::app()->user->org_id;
            $model = TicketTemplate::api()->delete($rs);
        }
    }

    //上下架
    public function actionDownUp() {
        if (Yii::app()->request->isPostRequest) {
            // Landscape::api()->debug = true;
            $rs['id'] = $_POST['id'];
            $rs['state'] = ($_POST['state']==1) ? 2 : 1;
            $rs['or_id'] = YII::app()->user->org_id;
            $data = TicketTemplate::api()->state($rs);
            if ($data) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('downUp');
    }

    /**
     * 特定日期价格、库存设置
     * @author grg
     */
    public function actionSpecial() {
        $data['labels'] = array('stock' => '库存', 'price' => '价格');
        $type = Yii::app()->request->getParam('day');
        $data['id'] = Yii::app()->request->getParam('id');
        $result = TicketTemplate::api()->ticketinfo(array(
            'ticket_id' => $data['id'],
        ));
        if ($result['code'] == 'succ') {
            $data['info'] = $result['body'];
        } else {
            $this->redirect('/ticket/single/');
        }
        $data['type'] = in_array($type, array_keys($data['labels'])) ? $type : 'stock';
        $result = $data['type'] == 'stock' ? Ticketdreserve::api()->lists(array(
                    'ticket_template_id' => $data['id']
                )) : Ticketdprice::api()->lists(array(
                    'ticket_template_id' => $data['id']
        ));
        $day_tickets = $color_day_tickets = array();
        if ($result['code'] == 'succ') {
            foreach ($result['body']['data'] as $item) {
                $day_tickets[$item['date']] = $data['type'] == 'stock' ? (int) $item['reserve'] : doubleval($item['price']);
            }

            $tickets = array_values($day_tickets);
            sort($tickets);
            $tickets = array_unique($tickets);
            $idx = 1;
            $colors = array();
            foreach ($tickets as $value) {
                $colors[$value] = '.clv' . $idx;
                $idx = $idx == 18 ? 1 : $idx + 1;
            }
            unset($value);
            $color_day_tickets = array();
            foreach ($day_tickets as $date => &$ticket) {
                isset($color_day_tickets['' . $ticket . $colors[$ticket]]) ? $color_day_tickets['' . $ticket . $colors[$ticket]][] = $date : $color_day_tickets['' . $ticket . $colors[$ticket]] = array($date);
                $ticket = array($ticket, $colors[$ticket]);
            }
            unset($ticket);
            uksort($color_day_tickets, function($a, $b) {
                $a = intval($a);
                $b = intval($b);
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            });
        }


        $data['day_tickets'] = $day_tickets;
        $data['color_day_tickets'] = $color_day_tickets;
        $this->render('special', $data);
    }

    public function actionSpecial_Bind() {
        $t_id = Yii::app()->request->getParam('t_id');
        $type = Yii::app()->request->getParam('type');
        $date = Yii::app()->request->getParam('date');
        $quantity = Yii::app()->request->getParam('quantity');

        $date = explode(',', $date);
        if (doubleval($quantity) <= 0 || count($date) == 0) {
            exit(0);
        }

        if ($type == 'stock') {
            $result = Ticketdreserve::api()->set(array(
                'ticket_template_id' => (int) $t_id,
                'reserve' => intval($quantity),
                'days' => implode(',', $date),
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account
                    ), 0);
        } else {
            $result = Ticketdprice::api()->set(array(
                'ticket_template_id' => (int) $t_id,
                'price' => doubleval($quantity),
                'days' => implode(',', $date),
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account
                    ), 0);
        }


        if ($result['code'] == 'succ') {
            exit(1);
        }
        exit(0);
    }
    
    
    
    
     //优惠规则
     public function actionRule() {
         $id = $_REQUEST['id'];
         $param['supplier_id'] = Yii::app()->user->org_id;
         $lists = Ticketdiscountrule::api()->lists($param);
         $list = ApiModel::getLists($lists);
        $this->renderPartial('rule',compact('id','list'));
    }
    
    
     //添加规则
     public function actionLimitrule() {
         $id = $_REQUEST['id'];
         $this->renderPartial('limitrule',compact('id'));
    }
    
    //查找限制分销商
     public function actionLimit() {
         if(Yii::app()->request->isPostRequest){
            $field['supplier_id'] = Yii::app()->user->org_id;
             $field['type'] = $_POST['type'];
            $lists = Ticketorgnamelist::api()->lists($field);
            $list = ApiModel::getLists($lists);
            $html = '';
            if(!empty($list)){
                foreach($list as $item){
                    $html = $html .'<option value="'.$item['id'].'">'.$item['name'].'</option>';
                }
            }
           echo json_encode($html);
           Yii::app()->end();
         }
     }
    //限制分销商
     public function actionNamelist(){
         if(Yii::app()->request->isPostRequest){
            $field['id'] = $_POST['id'];
            $field['or_id'] =Yii::app()->user->org_id;
            $field['namelist_id'] = $_POST['namelist_id'];
            $data = TicketTemplate::api()->update($field);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            }else{
                $this->_end(1, $data['message']);
            }
        }
     }
     //优惠规则
     public function actionRuleadd(){
         if(Yii::app()->request->isPostRequest){
            $field['id'] = $_POST['id'];
            $field['or_id'] =Yii::app()->user->org_id;
            $field['discount_id'] = $_POST['discount_id'];
            if(trim($field['discount_id'])==""){
                $this->_end(1, "请选择优惠规则！");
            }
            $data = TicketTemplate::api()->update($field);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            }else{
                $this->_end(1, $data['message']);
            }
        }
     }

     //返仓
     public function actionGetback(){
         if(Yii::app()->request->isPostRequest){
            $field['id'] = $_POST['id'];
            $field['or_id'] =Yii::app()->user->org_id;
            $field['state'] = 0;
            $data = TicketTemplate::api()->state($field);
      //print_r($data);exit;
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            }else{
                $this->_end(1, $data['message']);
            }
        }
     }
     
     
     
     
     
     
}
