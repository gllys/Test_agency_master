<?php

class VerificationController extends Base_Controller_Api {
    /*
     * 查询要核销的订单
     * @author chentao
     * 2014-11-06 zqf 修改
     * */

    public function listsAction() {
        //查询方式 1=》身份证  2=》手机号 3=》订单号
        $type = intval($this->body['type']);
        !in_array($type, array(1, 2, 3)) && $type = 3;

        $id = trim(Tools::safeOutput($this->body['id']));
        !$id && Lang_Msg::error('ERROR_VERIFY_2'); //请输入要检票的手机号、身份证号或订单号
        //景点
        $poi_id = intval($this->body['view_point']);
        $landscape_id = intval($this->body['landscape_id']);
        $supplier_id = intval($this->body['supplier_id']);
        $equipment_code = trim(Tools::safeOutput($this->body['equipment_code']));
        if ($equipment_code) {
            $equipmentInfo = EquipmentModel::model()->getDevice($equipment_code);
            if ($equipmentInfo) {
                $landscape_id = $equipmentInfo['landscape_id'];
                $poi_id = $equipmentInfo['poi_id'];
            }
        }
        $OrderModel = OrderModel::model();

        $item = array();
        switch ($type) {
            case 1:
                $items = $OrderModel->getOrderByCard($id);
                $ids = $items ? array_keys($items) : array();
                break;
            case 2:
                $items = $OrderModel->getOrderByPhone($id);
                $ids = $items ? array_keys($items) : array();
                break;
            default:
                !preg_match("/^\d+$/", $id) && Lang_Msg::error('ERROR_VERIFY_3');
                $ids[] = $id;
        }
        if (!$ids) {
            Lang_Msg::error('ERROR_VERIFY_3');
        }

        try {
            $data = $OrderModel->getOrderList($ids, $poi_id, $landscape_id, $supplier_id);
            Lang_Msg::output($data);
        } catch (Exception $e) {
            Lang_Msg::error('ERROR_GLOBAL_3');
        }
    }

    /**
     * 旧电子售票系统核销新分销平台门票
     * @return [type] [description]
     */
    public function updatev1Action() {
        $order_id = $this->body['order_id'];
        $num = intval($this->body['num']);
        if (!$order_id || $num <= 0)
            Lang_Msg::error('ERROR_GLOBAL_1');

        $this->setParam('data', json_encode(array($order_id => $num)));
        $this->setParam('channel', 1);
        $this->setParam('view_point', 0);
        $this->setParam('or_id', $this->body['organization_id']);
        $this->body = $this->getParams();

        $this->updateAction();
    }

    //使用门票
    public function updateAction() {
        $data = null === ( $this->body['data'] ) ? Tools::lsJson('0', '没有参数') : json_decode($this->body['data'], true);
        $view_point = intval($this->body['view_point']);
        $landscape_id = intval($this->body['landscape_id']);
        $record['uid'] = intval($this->body['uid']);
        $record['created_at'] = time();
        $record['equipment_code'] = trim(Tools::safeOutput($this->body['equipment_code']));
        $record['user_name'] = $this->body['user_name'] ? $this->body['user_name'] : $this->body['user_account'];
        $record['channel'] = intval($this->body['channel']);
        if ($record['equipment_code']) {
            $equipmentInfo = EquipmentModel::model()->getDevice($record['equipment_code']);
            if ($equipmentInfo) {
                $landscape_id = $equipmentInfo['landscape_id'] ? $equipmentInfo['landscape_id'] : $landscape_id;
                $view_point = $equipmentInfo['poi_id'] ? $equipmentInfo['poi_id'] : $view_point;
            }
        }
        //$view_point =  null === $view_point ? Tools::lsJson( '0', '没有view_point参数'):  $view_point ;

        $poiIds = array($view_point);
        if ($view_point > 0) {
            $poiInfo = PoiModel::model()->getInfo($view_point);
            if (!$poiInfo)
                Lang_Msg::error('ERROR_VERIFY_5');
            $landscape_id = intval($poiInfo['landscape_id']);
        } else if ($landscape_id > 0) {
            $poiList = PoiModel::model()->getPoiList($landscape_id);
            if (!$poiList)
                Lang_Msg::error('ERROR_VERIFY_5');
            $poiIds = array_keys($poiList);
        }

        if (!$landscape_id)
            Lang_Msg::error('景区信息有误');

        $now = time();
        try {
            OrderItemModel::model()->begin();
            foreach ($data as $order_id => $nums) {
                $orderInfo = OrderModel::model()->getById($order_id);
                if (!$orderInfo)
                    Lang_Msg::error('订单不存在');
                if ($equipmentInfo && $orderInfo['supplier_id'] != $equipmentInfo['organization_id'])
                    Lang_Msg::error('设备权限错误');
                !preg_match("/^\d+$/", $order_id) && Lang_Msg::error('ERROR_VERIFY_3');
                $poi_used = array();
                foreach ($poiIds as $poiId) {
                    $rt = TicketModel::model()->useTicket($order_id, $poiId, $nums);
                    if ($rt)
                        $poi_used[] = $poiId;
                }
                if ($poi_used) {
                    $orderItems = OrderItemModel::model()->setTable($order_id)->search(array('order_id' => $order_id));
                    $orderItem = reset($orderItems);
                    // 检票记录
                    $record['supplier_id'] = $orderInfo['supplier_id'];
                    $record['status'] = 1;
                    $record['record_code'] = $order_id;
                    $record['code'] = $order_id;
                    $record['num'] = $nums;
                    $record['landscape_id'] = $landscape_id;
                    $record['poi_id'] = implode(',', $poi_used);
                    $record['ticket_type_name'] = $orderItem['name'];
                    $record['tickets_code'] = $orderItem['ticket_template_id'];
                    TicketRecordModel::model()->insert($record);
                }
            }

            OrderItemModel::model()->commit();
        } catch (Exception $e) {
            Log_Base::save('hexiao', 'error:' . $e->getMessage());
            Log_Base::save('hexiao', var_export($this->body, true));
            OrderItemModel::model()->rollback();
            Lang_Msg::error('ERROR_GLOBAL_3');
        }

        $data = array();
        $data['result'] = 1;
        $data['poi_id'] = $poiIds;
        $data['landscape_id'] = $landscape_id;
        Lang_Msg::output($data);
    }

    //显示列表
    public function recordAction() {
        $where = '';
        if (isset($this->body['date'])) {
            $st = strtotime($this->body['begin_date'] . ' 00:00:01');
            $et = strtotime($this->body['end_date'] . ' 23:59:59');
            $where['created_at|between'] = array($st, $et);
        }
        if (isset($this->body['landscape_id']))
            $where['landscape_id'] = $this->body['landscape_id'];
        if (isset($this->body['view_point']))
            $where['find_in_set|exp'] = '(' . $this->body['view_point'] . ',poi_id)';
        if (isset($this->body['order_id']))
            $where['record_code'] = $this->body['order_id'];
        if (isset($this->body['channel']))
            $where['channel'] = $this->body['channel'];
        if (isset($this->body['supplier_id']))
            $where['supplier_id'] = $this->body['supplier_id'];

        $page = 1;
        if ($this->body['p']) {
            $page = intval($this->body['p']);
        }
        $limit = array();
        null !== $this->body['items'] ? $page_limit = intval($this->body['items']) : $page_limit = 15;
        if ($page > 0)
            $limit = array(( $page - 1 ) * $page_limit, $page_limit);
        $count = TicketRecordModel::model()->countResult($where);
        $total = ceil($count / $page_limit);
        $tmp = $count > 0 ? TicketRecordModel::model()->search($where, '*', ' id desc ', $limit) : null;
        $sort = array();
        foreach ($tmp as $key => $value) {
            $sort[$key] = $value;
        }

        $data['data'] = $sort;
        $data['pagination'] = array(
            'count' => $count,
            'current' => $page,
            'items' => $page_limit,
            'total' => $total,
        );
        Tools::lsJson(true, 'ok', $data);
    }

    //撤销
    public function cancelAction() {
        $id = $this->body['id'];
        $supplier_id = $this->body['supplier_id'];


        //查找该条记录
        $record = TicketRecordModel::model()->getById($id);
        if (!$record) {
            Lang_Msg::error('不存在该条记录');
        }

        if ($record['supplier_id'] != $supplier_id) {
            Lang_Msg::error('非该供应商记录，不能撤销');
        }

        if ($record['cancel_status'] == 1) {
            Lang_Msg::error('已撤销');
        }

        if (time() - $record['created_at'] > 300) {
            Lang_Msg::error('撤销时间已过期');
        }

        $poi_id = $record['poi_id'];
        if (!$poi_id) {
            $poiList = PoiModel::model()->getPoiList($record['landscape_id']);
            $poiIds = array_keys($poiList);
        } else {
            $poiIds = explode(',', $poi_id);
        }
        try {
            TicketModel::model()->begin();
            $poi_used = array();
            foreach ($poiIds as $_id) {
                $rt = TicketModel::model()->cancelTicket($record['record_code'], $_id, $record['num']);
            }
            TicketRecordModel::model()->updateByAttr(array('cancel_status' => 1, 'updated_at' => time()), array('id' => $id));
            TicketModel::model()->commit();
        } catch (Exception $e) {
            TicketModel::model()->rollback();
            Lang_Msg::error('撤销失败');
        }
        Tools::lsJson(true, 'ok', array());
    }

}
