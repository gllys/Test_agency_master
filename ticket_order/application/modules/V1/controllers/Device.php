<?php

class DeviceController extends Base_Controller_Device {

    protected $equipment;

    public function testAction() {
        echo 'h1';
    }

    /**
     * 新版本APK检查
     * @return [type] [description]
     */
    public function checkapkAction() {
        $version = $this->body['version'];
        $items = ConfigModel::model()->getConfig(array('device_version', 'device_force', 'device_url'));
        $data = array();
        $data['type'] = $version != $items['device_version'] ? 1 : 0;
        $data['force'] = $items['device_force'];
        $data['version'] = $items['device_version'];
        $data['url'] = $items['device_url'];

        Lang_Msg::device($data);
    }

    /**
     * 新版本南平APK检查
     * @return [type] [description]
     */
    public function checkNPapkAction() {
        $version = $this->body['version'];
        $items = ConfigModel::model()->getConfig(array('np_device_version', 'np_device_force', 'np_device_url'));
        $data = array();
        $data['type'] = $version != $items['np_device_version'] ? 1 : 0;
        $data['force'] = $items['np_device_force'];
        $data['version'] = $items['np_device_version'];
        $data['url'] = $items['np_device_url'];

        Lang_Msg::device($data);
    }

    private function outputList($ids, $poi_id) {
        $list = OrderModel::model()->getOrderList($ids, $poi_id, $this->equipment['landscape_id']);
        if (!$list)
            Lang_Msg::error('ERROR_DEVICE_7');
        if (count($ids) == 1) {
            $this->outputTicket(reset($list));
        }
        $data = array();
        $data['tickets'] = array();
        foreach ($list as $item) {
            $tmp = array();
            $tmp['ticket_name'] = $item['name'];
            $tmp['order_id'] = $item['order_id'];
            $tmp['owner_name'] = $item['owner_name'];
            $tmp['owner_mobile'] = $item['owner_mobile'];
            $tmp['owner_card'] = $item['owner_card'];
            $tmp['num'] = $item['nums'];
            $data['tickets'][] = $tmp;
        }
        $data['orderType'] = strlen($this->body['content']) == 11 ? 1 : 2;
        $data['content'] = $this->body['content'];
        $data['listType'] = 3;
        Lang_Msg::device($data);
    }

    private function outputTicket(&$item) {
        $data = array();
        $data['number'] = $item['nums'];
        $data['type'] = $item['name'];
        $data['source'] = 1;
        $data['listType'] = 1;
        Lang_Msg::device($data);
    }

    public function useTicket($order_id, $ticket_id, $num, $poi_id) {
        //返回验证结果
        $order = OrderModel::model()->getById($order_id);
        if (!$order)
            Lang_Msg::error('ERROR_DEVICE_7');
        $landscape_ids = explode(',', $order['landscape_ids']);
        if (!in_array($this->equipment['landscape_id'], $landscape_ids))
            Lang_Msg::error('ERROR_DEVICE_8');
        $order_item = reset(OrderItemModel::model()->setTable($order_id)->search(array('order_id' => $order_id)));
        if (!$order_item)
            Lang_Msg::error('ERROR_DEVICE_7');
        if (!$poi_id) {
            $poiList = PoiModel::model()->getPoiList($this->equipment['landscape_id']);
            $poiIds = array_keys($poiList);
        } else {
            $poiIds = explode(',', $poi_id);
        }
        try {
            $now = time();
            TicketModel::model()->begin();
            $poi_used = array();
            foreach ($poiIds as $id) {
                //如果是不是南平票
                if ($order_id != '166129187502183') {
                    $rt = TicketModel::model()->useTicket($order_id, $id, $num, $ticket_id);
                } else { //如果是南平票
                    $rt = true;
                    // 更新ORDER
                    OrderModel::model()->updateById($order_id, array('used_nums' => $order['used_nums'] + 1, 'updated_at' => $now));
                    // 更新ORDERITEM
                    OrderItemModel::model()->setTable($order_id)->updateByAttr(array('used_nums' => $order['used_nums'] + 1, 'updated_at' => $now), array('order_id' => $order_id));
                }
                if ($rt)
                    $poi_used[] = $id;
            }
            // LOG
            $log['uid'] = intval($this->body['uid']);
            $log['supplier_id'] = $this->equipment['organization_id'];
            $log['landscape_id'] = $this->equipment['landscape_id'];
            $log['poi_id'] = implode(',', $poi_used);
            $log['equipment_code'] = $this->equipment['code'];
            $log['created_at'] = time();
            $log['user_name'] = '';
            $log['channel'] = intval($this->body['channel']);
            $log['status'] = 1;
            $log['record_code'] = $order_id;
            $log['code'] = $this->body['content'];
            $log['num'] = $num;
            $log['ticket_type_name'] = $order_item['name'];
            $log['tickets_code'] = $order_item['ticket_template_id'];
            TicketRecordModel::model()->insert($log);
            TicketModel::model()->commit();
        } catch (Exception $e) {
            TicketModel::model()->rollback();
            Lang_Msg::error('ERROR_DEVICE_9');
        }

        $data = array();
        $data['orderCode'] = $order_id; //订单号
        $data['content'] = $this->body['content'];
        $data['listType'] = 2;
        $data['validation_time'] = date('Y-m-d H:i:s'); //验票时间
        $data['ticket'] = array(
            'number' => $num, //验票张数
            'source' => 1,
            'type' => $order_item['name'],
            'price' => $order_item['price'],
            'landscape_name' => $this->equipment['landscape_name'], //景区名称
            'owner_name' => $order['owner_name'], //取票人名称
            'owner_mobile' => $order['owner_mobile'], //取票人手机
            'op_user_name' => $this->equipment['code'], //操作员
            'note' => '', //备注
            'result' => '验证成功',
        );
        Lang_Msg::device($data);
    }

    public function qrcodeAction() {
        try {
            $imei = $this->body['imei'];
            $content = $this->body['content'];
            $num = intval($this->body['num']);
            if (!$imei)
                Lang_Msg::error('ERROR_DEVICE_1');
            if (!$content)
                Lang_Msg::error('ERROR_DEVICE_2');

            $this->equipment = EquipmentModel::model()->getDevice($imei);
            if (!$this->equipment)
                Lang_Msg::error('ERROR_DEVICE_3');
            if (!$this->equipment['landscape_id'])
                Lang_Msg::error('ERROR_DEVICE_4');
            $poi_id = $this->equipment['poi_id'];
            $ticket_id = 0;

            //南平一元活动
            if (strcasecmp($content, 'Np.jinglvtong.com/a1') === 0||strcasecmp($content, 'http://np.jinglvtong.com/a1') === 0) {
                $content = '166129187502183';
            }


            $len = strlen($content); //号码的长度
            if ($len == 6 || $len == 18) {
                $order_ids = OrderModel::model()->getOrderByCard($content);
                if (!$order_ids)
                    Lang_Msg::error('ERROR_DEVICE_5');
                $this->outputList(array_keys($order_ids), $poi_id);
            } else if ($len == 11) {
                $order_ids = OrderModel::model()->getOrderByPhone($content);
                if (!$order_ids)
                    Lang_Msg::error('ERROR_DEVICE_6');
                $this->outputList(array_keys($order_ids), $poi_id);
            } else {
                $type = substr($content, 0, 1);
                if ($type == 1) {
                    $order_id = $content;
                    if ($num <= 0) {
                        $this->outputList(array($order_id), $poi_id);
                    }
                } else {
                    $ticket_id = $content;
                    $ticket_info = TicketModel::model()->getById($ticket_id);
                    $order_id = $ticket_info['order_id'];
                    $num = 1;
                }

                $this->useTicket($order_id, $ticket_id, $num, $poi_id);
            }
        } catch (Exception $e) {
            Lang_Msg::error("ERROR_DEVICE_11");
        }
    }

    public function historyAction() {
        try {
            //检查设备信息
            $imei = $this->body['imei'];
            if (!$imei)
                Lang_Msg::error('ERROR_DEVICE_1');
            $this->equipment = EquipmentModel::model()->getDevice($imei);
            if (!$this->equipment)
                Lang_Msg::error('ERROR_DEVICE_3');
            if (!$this->equipment['landscape_id'])
                Lang_Msg::error('ERROR_DEVICE_4');
            $poi_id = $this->equipment['poi_id'];

            $start_time = $this->body['start_time'];
            $end_time = $this->body['end_time'];
            $code = $this->body['code'];
            $start = intval($this->body['start']);
            $count = intval($this->body['count']);

            //得到查询条件 
            $where = array();
            $where['landscape_id'] = $this->equipment['landscape_id'];
            $where['equipment_code'] = $this->equipment['code'];
            if ($start_time) {
                $start_time = strtotime($start_time);
                $where['created_at|>='] = $start_time;
            }
            if ($end_time) {
                $end_time = strtotime($end_time);
                $where['created_at|<='] = $end_time;
            }
            if ($code)
                $where['code'] = $code;
            if ($count > 0)
                $this->items = $count;
            if ($start)
                $this->current = floor($start / $this->items);
            $this->count = TicketRecordModel::model()->countResult($where);
            if ($this->count <= 0)
                Lang_Msg::error('ERROR_DEVICE_10');
            $this->pagenation();
            $list = TicketRecordModel::model()->search($where, '*', 'created_at desc', $this->limit);
            $data = array();
            $data['count'] = $this->items;
            $data['start'] = ($this->current - 1) * $this->items;
            $data['total'] = $this->count;
            foreach ($list as $item) {
                $tmp = array();
                $tmp['id'] = $item['id'];
                $tmp['code'] = $item['code'];
                $tmp['record_code'] = $item['record_code'];
                $tmp['tickets_code'] = $item['tickets_code'];
                $tmp['type'] = $item['ticket_type_name'];
                $tmp['note'] = $item['note'];
                $tmp['validation'] = $item['status'] == 1 ? '成功' : '失败';
                $tmp['validation_num'] = $item['num'];
                $tmp['validation_time'] = date('Y-m-d H:i:s', $item['created_at']);
                if ($item['cancel_status'] == 1) {
                    $tmp['btn_status'] = 2;
                } else {
                    
                    if ($item['status'] == 1 && ( time()-  $item['created_at']< 300)) { #五分钟内票可以撤销
                        $tmp['btn_status'] = 1;
                    } else {
                        $tmp['btn_status'] = 0;
                    }
                }
                $data['ticket'][] = $tmp;
            }
            // 输出
            Lang_Msg::device($data);
        } catch (Exception $e) {
            Lang_Msg::error("ERROR_DEVICE_11");
        }
    }

    public function infoAction() {
        $imei = $this->body['imei'];
        if (!$imei)
            Lang_Msg::error('ERROR_DEVICE_1');
        $equipment = EquipmentModel::model()->getDevice($imei);

        $data = array();
        $data['name'] = $equipment ? $equipment['name'] : '未添加';
        $data['organization_name'] = '未绑定';
        $data['landscape_name'] = '未绑定';
        $data['poi_name'] = '未绑定';

        if (!empty($equipment['organization_id'])) {
            $rs = OrganizationModel::model()->getInfo($equipment['organization_id']);
            if ($rs) {
                $data['organization_name'] = $rs['name'];
            }
        }

        if (!empty($equipment['landscape_id'])) {
            $rs = LandscapeModel::model()->getDetail($equipment['landscape_id']);
            if ($rs) {
                $data['landscape_name'] = $rs['name'];
            }
        }

        if (!empty($equipment['poi_id'])) {
            $rs = PoiModel::model()->getInfo($equipment['poi_id']);
            if ($rs) {
                $data['poi_name'] = $rs['name'];
            }
        }
        // 输出
        Lang_Msg::device($data);
    }

    //撤销
    public function cancelAction() {
        $imei = $this->body['imei'];
        $id = $this->body['id'];
        if (!$imei)
            Lang_Msg::error('ERROR_DEVICE_1');

        $this->equipment = EquipmentModel::model()->getDevice($imei);
        if (!$this->equipment)
            Lang_Msg::error('ERROR_DEVICE_3');
        if (!$this->equipment['landscape_id'])
            Lang_Msg::error('ERROR_DEVICE_4');
        

        //查找该条记录
        $record = TicketRecordModel::model()->getById($id);
        if (!$record) {
            Lang_Msg::error('不存在该条记录');
        }

        if ($record['landscape_id']!=$this->equipment['landscape_id']) {
            Lang_Msg::error('非该景区的历史记录，不能撤销');
        }	
        
        if ($record['cancel_status'] == 1) {
            Lang_Msg::error('已撤销');
        }

        if (time() - $record['created_at'] > 300) {
            Lang_Msg::error('撤销时间已过期');
        }

        $poi_id = $record['poi_id'];
        if (!$poi_id) {
            $poiList = PoiModel::model()->getPoiList($this->equipment['landscape_id']);
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
            TicketRecordModel::model()->updateByAttr(array('cancel_status' =>1 , 'updated_at' => time()), array('id' => $id));
            TicketModel::model()->commit();
        } catch (Exception $e) {
            TicketModel::model()->rollback();
            Lang_Msg::error('撤销失败');
        }
        Lang_Msg::device(array());
    }

}
