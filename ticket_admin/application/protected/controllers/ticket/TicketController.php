<?php

/**
 * 景区门票
 * 模块：电子票务
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/4/15
 * Time: 2:23 PM
 * File: TicketController.php
 */

class TicketController extends Controller
{
    private $price_type = array(1 => '成人', 2 => '儿童', 3 => '老人', 4 => '团体', 5 => '学生');

    /**
     * @author grg
     */
    public function actionIndex() {
        $param = $_REQUEST;

        $param['type']        = 0;
        $param['p']           = isset($param['page']) ? $param['page'] : 1;
        $param['items']       = 20;
        $param['show_group']  = 1;
        $param['is_del']      = 0;
        $param['source_type'] = 1;

        if (isset($param['name']) && $param['name'] != '') {
            $param['poi_name'] = $param['name'];
        }

        $rs = Landscape::api()->usedList(array(
            'show_all' => 1
        ));
        $landscapes = ApiModel::getLists($rs);

        $rs    = Tickettemplatebase::api()->lists($param);
        $lists = ApiModel::getLists($rs);

        //分页
        $pagination      = ApiModel::getPagination($rs);
        $pages           = new CPagination($pagination['count']);
        $pages->pageSize = $param['items'];

        $price_type = $this->price_type;

        $this->render('index', compact('landscapes', 'lists', 'pages', 'param', 'price_type'));
    }

    /**
     * 发布窗口门票
     * @author grg
     */
    public function actionCreate() {
//        $field['landscape_ids'] = YII::app()->user->lan_id;
        $field['items']         = 100;

        $data['price_type'] = $this->price_type;

        $rs = Poi::api()->lists($field);
        if (isset($rs['code']) && $rs['code'] == 'succ' && isset($rs['body']['data'])) {
            $data['poi'] = $rs['body']['data'];
        }

        $this->render('create', $data);
    }

    /**
     * 修改窗口门票
     * @param int $gid
     * @param string $view
     * @author grg
     */
    public function actionModify($gid = null, $view = 'modify') {
        $data['price_type'] = $this->price_type;
        //票基本信息
        if (intval($gid)) {
            //获取票基本信息
            $param['type']        = 0;
            $param['gid']         = $gid;
            $param['source_type'] = 1;

            $ticket_info = Tickettemplatebase::api()->lists($param);
            if (!ApiModel::isSucc($ticket_info)) {
                $this->redirect('/product/ticket/');
            }
            $tickets = ApiModel::getLists($ticket_info);

            $data['ticket'] = reset($tickets);
            foreach ($tickets as $ticket) {
                $data['ticket']['type_tid'][$ticket['type']]   = $ticket['id'];
                $data['ticket']['type_price'][$ticket['type']] = $ticket['sale_price'];
            }
            unset($ticket);
        }
//        $field['landscape_ids'] = YII::app()->user->lan_id;
        $field['items']         = 100;

        $rs = Poi::api()->lists($field);
        if (isset($rs['code']) && $rs['code'] == 'succ' && isset($rs['body']['data'])) {
            $data['poi'] = $rs['body']['data'];
        }

        $this->render($view, $data);
    }

    /**
     * @param null $gid
     * @author grg
     */
    public function actionView($gid = null) {

        $this->actionModify($gid, 'view');
    }

    public function actionSave() {
        if (Yii::app()->request->isPostRequest) {
            $field = $_REQUEST;

//            $field['scenic_id'] = Yii::app()->user->lan_id;

            if (isset($field['gid']) && intval($field['gid']) > 0) {
                $act          = 'updateBatch';
                $field['gid'] = intval($field['gid']);
            }
            else {
                $act = 'addBatch';

//                $field['organization_id'] = Yii::app()->user->org_id;
                $field['user_id']         = Yii::app()->user->uid;
                $field['user_account']    = Yii::app()->user->account;
                $field['user_name']       = Yii::app()->user->display_name;
                if (empty($field['user_name'])) {
                    $field['user_name'] = $field['user_account'];
                }
                $field['max_buy']     = 100;
                $field['state']       = 2;//待上架
                $field['source_type'] = 1;
            }

            if (isset($_REQUEST['view_point']) && is_array($_REQUEST['view_point'])) {
                $field['view_point'] = implode(',', $_REQUEST['view_point']);
            }
            else {
                $this->_end(1, '景点不可以为空！');
            }

            //多个类型价格
            if (isset($_REQUEST['prices']) && is_array($_REQUEST['prices'])) {
                $items = array();
                if ($act == 'updateBatch') {
                    foreach ($_REQUEST['prices'] as $price) {
                        list($id, $type, $sale_price) = explode('_', $price);
                        $items[] = array(
                            'id' => intval($id),
                            'type' => intval($type),
                            'sale_price' => $sale_price
                        );
                    }
                }
                else {
                    foreach ($_REQUEST['prices'] as $price) {
                        list($type, $sale_price) = explode('_', $price);
                        $items[] = array(
                            'type' => intval($type),
                            'sale_price' => $sale_price
                        );
                    }
                }
                unset($price);
                $field['items'] = json_encode($items);
            }
            else {
                $this->_end(1, '多种类型的价格至少填写一项！');
            }

            if (isset($field['all_available']) && $field['all_available'] == 1) {
                $field['date_available'] = 0;
            }
            else {
                $a_time = strtotime($_REQUEST['date_available'][0] . ' 00:00:00');
                $b_time = strtotime($_REQUEST['date_available'][1] . ' 23:59:59');
                if ($b_time < $a_time) {
                    //交换
                    $t_time = $a_time + 86399;//23:59:59
                    $a_time = $b_time - 86399;
                    $b_time = $t_time;
                }
                $field['date_available'] = $a_time . ',' . $b_time;
            }

            if (isset($_REQUEST['week_time']) && count($_REQUEST['week_time']) > 0) {
                $field['week_time'] = implode(',', $_REQUEST['week_time']);
            }
            else {
                $this->_end(1, '适用日期不可为空！');
            }
            unset($field['prices']);
            $rs = Tickettemplatebase::api()->$act($field);
            //Tickettemplatebase::api()->debug();
            if ($rs['code'] == 'succ') {
                $this->_end(0, $rs['message']);
            }
            else {
                $this->_end(1, $rs['message']);
            }
        }
    }

    public function actionTicketToggle($gid = null, $state = null) {
        $param['gid']   = $gid;
        $param['state'] = $state;

        $result = Tickettemplatebase::api()->updateBatch($param);
        if ($result['code'] == 'succ') {
            $this->_end(0, '操作成功');
        }
        else {
            $this->_end(1, $result['message']);
        }
    }

    //删除
    public function actionTicketDelete($gid = null) {
        $param['gid']    = $gid;
        $param['is_del'] = 1;

        $result = Tickettemplatebase::api()->updateBatch($param);
        if ($result['code'] == 'succ') {
//            unset($param['is_del']);
//            $param['type']      = 0;
////            $param['scenic_id'] = YII::app()->user->lan_id;
//
//            $ticket_info = Tickettemplatebase::api()->lists($param);
//            if (!ApiModel::isSucc($ticket_info)) {
//                $this->redirect('/product/ticket/');
//            }
//            $tickets = ApiModel::getLists($ticket_info);
//
//            $ids = array();
//            foreach ($tickets as $ticket) {
//                $ids[] = $ticket['id'];
//            }
//            $priceItems = PriceTemplatesItems::model()->deleteAllByAttributes(array('ticket_templates_id' => $ids));
//            if ($priceItems >= 0) {
//                $this->_end(0, '删除成功');
//            }
            $this->_end(0, '删除成功');
        }
        else {
            $this->_end(1, $result['message']);
        }
    }

}
